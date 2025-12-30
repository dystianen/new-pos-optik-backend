<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\R2Storage;
use App\Models\CartItemModel;
use App\Models\CartItemPrescriptionModel;
use App\Models\CartModel;
use App\Models\CustomerShippingAddressModel;
use App\Models\InventoryTransactionModel;
use App\Models\OrderItemModel;
use App\Models\OrderItemPrescriptionModel;
use App\Models\OrderModel;
use App\Models\OrderShippingAddressModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\ShippingRateModel;
use CodeIgniter\API\ResponseTrait;

class OrderController extends BaseController
{
    use ResponseTrait;
    protected $orderModel, $orderItemModel, $InventoryTransactionModel, $productModel, $csaModel, $cartModel, $cartItemModel, $shippingRateModel, $cartItemPrescriptionModel, $orderShippingAddressModel, $orderItemPrescriptionModel, $paymentModel, $r2;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->InventoryTransactionModel = new InventoryTransactionModel();
        $this->productModel = new ProductModel();
        $this->csaModel = new CustomerShippingAddressModel();
        $this->cartModel = new CartModel();
        $this->cartItemModel = new CartItemModel();
        $this->shippingRateModel = new ShippingRateModel();
        $this->cartItemPrescriptionModel = new CartItemPrescriptionModel();
        $this->orderShippingAddressModel = new OrderShippingAddressModel();
        $this->orderItemPrescriptionModel = new OrderItemPrescriptionModel();
        $this->paymentModel = new PaymentModel();
        $this->r2 = new R2Storage();
    }

    // =======================
    // API FUNCTIONS
    // =======================

    // GET /api/summary-orders/(:segment)
    public function summaryOrders($addressId)
    {
        try {
            // 🔐 AUTH
            $jwtUser = getJWTUser();
            if (!$jwtUser) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Unauthorized'
                ]);
            }

            $customerId = $jwtUser->user_id;

            // 📍 SHIPPING ADDRESS
            $shippingAddress = $this->csaModel
                ->where('customer_id', $customerId)
                ->find($addressId);

            if (!$shippingAddress) {
                throw new \Exception('Shipping address not found');
            }

            // 🛒 CART
            $cart = $this->cartModel
                ->where('customer_id', $customerId)
                ->where('deleted_at', null)
                ->first();

            if (!$cart) {
                return $this->response->setJSON([
                    'status' => 200,
                    'data' => [
                        'shipping_address' => $shippingAddress,
                        'items' => [],
                        'shipping' => null,
                        'summary' => [
                            'subtotal' => 0,
                            'shipping_cost' => 0,
                            'total' => 0
                        ]
                    ]
                ]);
            }

            // 🛒 CART ITEMS
            $items = $this->cartItemModel
                ->select("
                cart_items.cart_item_id,
                cart_items.product_id,
                cart_items.variant_id,
                cart_items.quantity,
                cart_items.price,

                products.product_name,
                product_variants.variant_name,

                COALESCE(pvi_img.url, pi_img.url) AS image
            ")
                ->join('products', 'products.product_id = cart_items.product_id')
                ->join('product_variants', 'product_variants.variant_id = cart_items.variant_id', 'left')
                ->join(
                    'product_variant_images pvi',
                    'pvi.variant_id = cart_items.variant_id AND pvi.deleted_at IS NULL',
                    'left'
                )
                ->join(
                    'product_images pvi_img',
                    'pvi_img.product_image_id = pvi.product_image_id AND pvi_img.deleted_at IS NULL',
                    'left'
                )
                ->join(
                    'product_images pi_img',
                    'pi_img.product_id = products.product_id
                 AND pi_img.is_primary = 1
                 AND pi_img.deleted_at IS NULL',
                    'left'
                )
                ->where('cart_items.cart_id', $cart['cart_id'])
                ->where('cart_items.deleted_at', null)
                ->findAll();

            // 👓 PRESCRIPTIONS
            $cartItemIds = array_column($items, 'cart_item_id');
            $prescriptions = [];

            if (!empty($cartItemIds)) {
                $rows = $this->cartItemPrescriptionModel
                    ->whereIn('cart_item_id', $cartItemIds)
                    ->findAll();

                foreach ($rows as $row) {
                    $prescriptions[$row['cart_item_id']] = [
                        'right' => [
                            'sph'  => $row['right_sph'],
                            'cyl'  => $row['right_cyl'],
                            'axis' => $row['right_axis'],
                            'add' => $row['right_add'],
                            'pd'  => $row['pd_right'],
                        ],
                        'left' => [
                            'sph'  => $row['left_sph'],
                            'cyl'  => $row['left_cyl'],
                            'axis' => $row['left_axis'],
                            'add' => $row['left_add'],
                            'pd'   => $row['pd_left'],
                        ],
                    ];
                }
            }

            // 🧮 SUBTOTAL
            $subtotal = 0;

            $mappedItems = array_map(function ($item) use (&$subtotal, $prescriptions) {
                $itemSubtotal = $item['price'] * $item['quantity'];
                $subtotal += $itemSubtotal;

                return [
                    'cart_item_id' => $item['cart_item_id'],
                    'product_id'   => $item['product_id'],
                    'variant_id'   => $item['variant_id'],
                    'product_name' => $item['product_name'],
                    'variant_name' => $item['variant_name'],
                    'image'        => $item['image'],
                    'price'        => (int) $item['price'],
                    'quantity'     => (int) $item['quantity'],
                    'subtotal'     => (int) $itemSubtotal,
                    'prescription' => $prescriptions[$item['cart_item_id']] ?? null
                ];
            }, $items);

            // 🚚 SHIPPING COST
            $destinationText = trim(
                ($shippingAddress['city'] ?? '') . ' ' . ($shippingAddress['province'] ?? '')
            );

            $shippingRate = $this->shippingRateModel
                ->where("'$destinationText' LIKE CONCAT('%', destination, '%')", null, false)
                ->orderBy('LENGTH(destination)', 'DESC')
                ->first();

            if (!$shippingRate) {
                $shippingRate = $this->shippingRateModel
                    ->where('destination', 'Indonesia')
                    ->first();
            }

            $shippingCost = $shippingRate['cost'] ?? 0;

            // 💰 TOTAL
            $total = $subtotal + $shippingCost;

            // ✅ RESPONSE
            return $this->response->setJSON([
                'status' => 200,
                'data' => [
                    'shipping_address' => [
                        'recipient_name' => $shippingAddress['recipient_name'],
                        'phone'          => $shippingAddress['phone'],
                        'address'        => $shippingAddress['address'],
                        'city'           => $shippingAddress['city'],
                        'province'       => $shippingAddress['province'],
                        'postal_code'    => $shippingAddress['postal_code'],
                    ],

                    'items' => $mappedItems,

                    'shipping' => [
                        'service'     => 'regular',
                        'destination' => $destinationText,
                        'cost'        => (int) $shippingCost
                    ],

                    'summary' => [
                        'subtotal'      => (int) $subtotal,
                        'shipping_cost' => (int) $shippingCost,
                        'total'         => (int) $total
                    ]
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function orders()
    {
        $decoded = $this->decodedToken();
        $customerId = $decoded->user_id;

        if (!$customerId) {
            return $this->respond(['status' => 401, 'message' => 'Unauthorized'], 401);
        }

        $orders = $this->orderModel
            ->where('customer_id', $customerId)
            ->whereNotIn('status', ['cart'])
            ->orderBy('created_at', 'DESC')
            ->findAll();

        if (!$orders) {
            return $this->respond(['status' => 404, 'message' => 'No orders found'], 404);
        }

        // Ambil semua orderItems untuk setiap order
        $orderData = [];

        foreach ($orders as $order) {
            $items = $this->orderItemModel
                ->join('products', 'products.product_id = order_items.product_id')
                ->where('order_id', $order['order_id'])
                ->findAll();

            $order['items'] = $items;
            $orderData[] = $order;
        }

        return $this->respond([
            'status' => 200,
            'message' => 'Orders retrieved successfully',
            'data' => $orderData
        ]);
    }

    // POST /api/orders/submit/(:segment)
    public function submitOrder($addressId)
    {
        $db = db_connect();
        $db->transStart();

        try {
            // 🔐 AUTH
            $jwtUser = getJWTUser();
            if (!$jwtUser) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Unauthorized'
                ]);
            }

            log_message('debug', 'SUBMIT ORDER START');

            log_message('debug', 'AUTH USER: ' . json_encode($jwtUser));

            $customerId = $jwtUser->user_id;

            // 🔁 Ambil snapshot summary
            $summaryResponse = $this->summaryOrders($addressId);
            $summary = json_decode($summaryResponse->getBody(), true)['data'];
            log_message('debug', 'SUMMARY: ' . json_encode($summary));

            if (empty($summary['items'])) {
                throw new \Exception('Cart is empty');
            }

            log_message('debug', 'INSERT orders');
            $this->orderModel->insert([
                'customer_id'         => $customerId,
                'status_id'           => '2aa5c9be-906c-402c-a5fc-a16663125c3a',
                'shipping_method_id'  => '3e08ee99-750a-4437-a3a9-922437410f6e',
                'shipping_cost'       => $summary['shipping']['cost'],
                'coupon_discount'     => 0,
                'grand_total'         => $summary['summary']['total'],
            ]);
            log_message('debug', 'ORDER QUERY: ' . $this->orderModel->getLastQuery());

            $orderId = $this->orderModel->getInsertID();

            log_message('debug', 'INSERT order_shipping_addresses');
            // 📦 SHIPPING ADDRESS (snapshot)
            $this->orderShippingAddressModel->insert([
                'order_id'       => $orderId,
                'recipient_name' => $summary['shipping_address']['recipient_name'],
                'phone'          => $summary['shipping_address']['phone'],
                'address'        => $summary['shipping_address']['address'],
                'city'           => $summary['shipping_address']['city'],
                'province'       => $summary['shipping_address']['province'],
                'postal_code'    => $summary['shipping_address']['postal_code'],
            ]);
            log_message('debug', 'SHIPPING QUERY: ' . $this->orderShippingAddressModel->getLastQuery());

            // 🛍 ORDER ITEMS
            foreach ($summary['items'] as $item) {
                log_message('debug', 'INSERT order_item');
                $this->orderItemModel->insert([
                    'order_id'      => $orderId,
                    'product_id'    => $item['product_id'],
                    'variant_id'    => $item['variant_id'],
                    'quantity'      => $item['quantity'],
                    'price'         => $item['price'],
                ]);
                log_message('debug', 'ORDER ITEM QUERY: ' . $this->orderItemModel->getLastQuery());
                $orderItemId = $this->orderItemModel->getInsertID();

                // 👓 PRESCRIPTION (jika ada)
                if (!empty($item['prescription'])) {
                    $p = $item['prescription'];

                    log_message('debug', 'INSERT order_item_prescription');
                    $this->orderItemPrescriptionModel->insert([
                        'order_item_id' => $orderItemId,

                        'right_sph'   => $p['right']['sph'],
                        'right_cyl'   => $p['right']['cyl'],
                        'right_axis'  => $p['right']['axis'],
                        'right_add'   => $p['right']['add'],
                        'pd_right'    => $p['right']['pd'],

                        'left_sph'    => $p['left']['sph'],
                        'left_cyl'    => $p['left']['cyl'],
                        'left_axis'   => $p['left']['axis'],
                        'left_add'    => $p['left']['add'],
                        'pd_left'     => $p['left']['pd'],
                    ]);
                    log_message('debug', 'PRESCRIPTION QUERY: ' . $this->orderItemPrescriptionModel->getLastQuery());
                }
            }

            $cartItemIds = array_column($summary['items'], 'cart_item_id');
            $this->cartItemModel
                ->whereIn('cart_item_id', $cartItemIds)
                ->delete();

            $db->transComplete();

            return $this->response->setJSON([
                'status' => 200,
                'message' => 'Order submitted',
                'data' => [
                    'order_id' => $orderId,
                    'grand_total' => $summary['summary']['total']
                ]
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'SUBMIT ORDER ERROR');
            log_message('error', $e->getMessage());
            log_message('error', $e->getTraceAsString());

            if (method_exists($db, 'getLastQuery')) {
                log_message('error', 'LAST QUERY: ' . $db->getLastQuery());
            }

            $db->transRollback();

            return $this->response->setStatusCode(400)->setJSON([
                'message' => $e->getMessage()
            ]);
        }
    }

    // POST /api/payment
    public function uploadPaymentProof()
    {
        $db = db_connect();
        $db->transStart();

        try {
            log_message('debug', 'UPLOAD PAYMENT START');

            // 🔐 AUTH
            $jwtUser = getJWTUser();
            if (!$jwtUser) {
                throw new \Exception('Unauthorized');
            }

            $customerId = $jwtUser->user_id;

            // 📥 INPUT
            $orderId = $this->request->getVar('order_id');
            $payment_method_id  = $this->request->getVar('payment_method_id');
            $amount  = $this->request->getVar('amount');
            $img     = $this->request->getFile('proof');

            if (!$orderId || !$amount) {
                throw new \Exception('Invalid payload');
            }

            if (!$img || !$img->isValid()) {
                throw new \Exception('Invalid payment proof');
            }

            // 📦 VALIDATE ORDER OWNERSHIP
            $order = $this->orderModel
                ->where('order_id', $orderId)
                ->where('customer_id', $customerId)
                ->first();

            if (!$order) {
                throw new \Exception('Order not found');
            }

            // 🧪 FILE VALIDATION
            $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($img->getMimeType(), $allowedMime)) {
                throw new \Exception('Invalid file type');
            }

            if ($img->getSizeByUnit('mb') > 5) {
                throw new \Exception('Max file size is 5MB');
            }

            // ☁️ UPLOAD KE R2
            $objectUrl = $this->r2->uploadFile(
                $img->getTempName(),
                'payments/' . $orderId . '/' . $img->getRandomName()
            );

            log_message('debug', 'UPLOAD SUCCESS: ' . $objectUrl);

            // 💳 INSERT PAYMENT (TANPA STATUS)
            $this->paymentModel->insert([
                'order_id'           => $orderId,
                'payment_method_id'  => $payment_method_id,
                'amount'             => $amount,
                'proof'              => $objectUrl,
                'paid_at'            => date('Y-m-d H:i:s'),
            ]);

            // 🔁 UPDATE ORDER STATUS
            $this->orderModel->update($orderId, [
                'status_id' => '7f39039d-d2ef-46d1-93f5-8dbc0b5211fe',
                // contoh: WAITING_CONFIRMATION
            ]);

            $db->transComplete();

            return $this->response->setJSON([
                'status'  => 200,
                'message' => 'Payment proof uploaded successfully',
                'data' => [
                    'order_id'  => $orderId,
                    'proof_url' => $objectUrl,
                ]
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();

            log_message('error', 'UPLOAD PAYMENT ERROR');
            log_message('error', $e->getMessage());

            return $this->response->setStatusCode(400)->setJSON([
                'message' => $e->getMessage()
            ]);
        }
    }

    // GET /api/check-payment-status/(:segment)
    public function checkPaymentStatus($orderId)
    {
        // 🔐 AUTH
        $jwtUser = getJWTUser();
        if (!$jwtUser) {
            throw new \Exception('Unauthorized');
        }

        $customerId = $jwtUser->user_id;

        if (!$orderId) {
            return $this->respond([
                'status' => 400,
                'message' => 'Order ID is required'
            ], 400);
        }

        $order = $this->orderModel
            ->where('order_id', $orderId)
            ->where('customer_id', $customerId)
            ->first();

        if (!$order) {
            return $this->respond([
                'status' => 404,
                'message' => 'Order not found'
            ], 404);
        }

        // 🔑 status_id = PAID
        $isPaid = $order['status_id'] === '96755dec-2e2c-4d17-b21c-a71be60ecd91';


        return $this->respond([
            'status' => 200,
            'message' => $isPaid ? 'Order already paid' : 'Order not paid yet',
            'data' => [
                'order_id' => $orderId,
                'status_id' => $order['status_id'],
                'is_paid' => $isPaid
            ]
        ]);
    }



    // =======================
    // WEB DASHBOARD FUNCTIONS
    // =======================

    public function index()
    {
        $page = $this->request->getVar('page') ?? 1;
        $perPage = 10;
        $search = $this->request->getVar('search');

        $builder = $this->orderModel
            ->join('customers', 'customers.customer_id = orders.customer_id')
            ->orderBy('orders.created_at', 'DESC');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('customers.customer_name', $search)
                ->orLike('customers.customer_email', $search)
                ->groupEnd();
        }

        $orders = $builder->paginate($perPage, 'default', $page);

        $pager = [
            'currentPage' => $this->orderModel->pager->getCurrentPage('default'),
            'totalPages' => $this->orderModel->pager->getPageCount('default'),
            'limit' => $perPage
        ];

        return view('orders/v_index', [
            'orders' => $orders,
            'pager' => $pager,
            'search' => $search
        ]);
    }

    public function form()
    {
        $id = $this->request->getVar('id');

        if (!$id) {
            return view('orders/v_form');
        }

        $order = $this->orderModel
            ->join('customers', 'customers.customer_id = orders.customer_id')
            ->find($id);

        if (!$order) {
            return redirect()->to('/orders')->with('failed', 'Order not found.');
        }

        $orderItems = $this->orderItemModel
            ->join('products', 'products.product_id = order_items.product_id')
            ->where('order_id', $id)
            ->findAll();


        return view('orders/v_form', [
            'order' => $order,
            'orderItems' => $orderItems,
        ]);
    }

    public function save()
    {
        $id = $this->request->getVar('id');
        $data = [
            'status' => $this->request->getVar('status'),
        ];

        $this->orderModel->update($id, $data);
        $message = 'Order updated successfully!';

        return redirect()->to('/orders')->with('success', $message);
    }
}
