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
use App\Models\ProductVariantModel;
use App\Models\ShippingRateModel;
use CodeIgniter\API\ResponseTrait;

class OrderController extends BaseController
{
    use ResponseTrait;
    protected $orderModel, $orderItemModel, $InventoryTransactionModel, $productModel, $productVariantModel, $csaModel, $cartModel, $cartItemModel, $shippingRateModel, $cartItemPrescriptionModel, $orderShippingAddressModel, $orderItemPrescriptionModel, $paymentModel, $r2;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->InventoryTransactionModel = new InventoryTransactionModel();
        $this->productModel = new ProductModel();
        $this->productVariantModel = new ProductVariantModel();
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

                // ⚡ KURANGI STOK
                if ($item['variant_id']) {
                    // Jika ada varian, kurangi stok di product_variants
                    log_message('debug', 'REDUCE variant stock');
                    $this->productVariantModel
                        ->where('variant_id', $item['variant_id'])
                        ->set('stock', 'stock - ' . (int)$item['quantity'], false)
                        ->update();

                    // Kalkulasi ulang total stok product dari semua variantnya
                    log_message('debug', 'RECALCULATE product total stock');
                    $db->query("
                        UPDATE products p
                        SET p.product_stock = (
                            SELECT COALESCE(SUM(pv.stock), 0)
                            FROM product_variants pv
                            WHERE pv.product_id = p.product_id
                        )
                        WHERE p.product_id = ?
                    ", [$item['product_id']]);
                } else {
                    // Jika tidak ada varian, kurangi stok di products langsung
                    log_message('debug', 'REDUCE product stock');
                    $this->productModel
                        ->where('product_id', $item['product_id'])
                        ->set('product_stock', 'product_stock - ' . (int)$item['quantity'], false)
                        ->update();
                }

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
                // contoh: PAID
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

        // 🔑 status_id = SHIPPED
        $isPaid = $order['status_id'] === 'cc46d2a8-436c-42fc-96a1-ffb537dbabed';


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

    // GET /api/orders
    public function listOrders()
    {
        try {
            $jwtUser = getJWTUser();
            if (!$jwtUser) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Unauthorized'
                ]);
            }

            $customerId = $jwtUser->user_id;

            // Get filters from query params
            $statusId = $this->request->getVar('statusId');
            $limit = $this->request->getVar('limit') ?? 20;
            $page = $this->request->getVar('page') ?? 1;
            $offset = ($page - 1) * $limit;

            // Query orders dengan join
            $builder = $this->orderModel
                ->select("
                    orders.order_id,
                    orders.created_at AS order_date,
                    orders.grand_total,
                    orders.shipping_cost,
                    
                    order_statuses.status_name,
                    
                    shipping_methods.name AS shipping_method,
                    shipping_methods.estimated_days,
                    
                    payment_methods.method_name AS payment_method,
                    payments.paid_at
                ")
                ->join('order_statuses', 'order_statuses.status_id = orders.status_id', 'left')
                ->join('shipping_methods', 'shipping_methods.shipping_method_id = orders.shipping_method_id', 'left')
                ->join('payments', 'payments.order_id = orders.order_id', 'left')
                ->join('payment_methods', 'payment_methods.payment_method_id = payments.payment_method_id', 'left')
                ->where('orders.customer_id', $customerId)
                ->where('orders.deleted_at', null);

            if ($statusId) {
                $builder->where('orders.status_id', $statusId);
            }

            $orders = $builder
                ->orderBy('orders.created_at', 'DESC')
                ->limit($limit, $offset)
                ->findAll();

            if (empty($orders)) {
                return $this->response->setJSON([
                    'status' => 200,
                    'data' => []
                ]);
            }

            // Get order IDs
            $orderIds = array_column($orders, 'order_id');

            // 📦 Get items untuk setiap order
            $itemsGrouped = $this->getOrderItemsGrouped($orderIds);

            // 📍 Get shipping addresses
            $addressesGrouped = $this->getShippingAddressesGrouped($orderIds);

            // Map orders dengan items
            $mappedOrders = array_map(function ($order) use ($itemsGrouped, $addressesGrouped) {
                $orderId = $order['order_id'];

                return [
                    'order_id' => $orderId,
                    'order_date' => $order['order_date'],
                    'status' => $order['status_name'],
                    'items' => $itemsGrouped[$orderId] ?? [],
                    'summary' => [
                        'grand_total' => (int) $order['grand_total'],
                        'shipping_cost' => (int) $order['shipping_cost'],
                        'total_items' => count($itemsGrouped[$orderId] ?? [])
                    ],
                    'shipping' => [
                        'method' => $order['shipping_method'],
                        'rate' => (int) $order['shipping_cost'],
                        'estimated_days' => $order['estimated_days'],
                        'address' => $addressesGrouped[$orderId] ?? null
                    ],
                    'payment' => [
                        'method' => $order['payment_method'],
                        'date' => $order['paid_at']
                    ],
                ];
            }, $orders);

            return $this->response->setJSON([
                'status' => 200,
                'data' => $mappedOrders
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => $e->getMessage()
            ]);
        }
    }

    // GET /api/orders/{order_id}
    public function getOrderDetail($orderId)
    {
        try {
            $jwtUser = getJWTUser();
            if (!$jwtUser) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Unauthorized'
                ]);
            }

            $customerId = $jwtUser->user_id;

            // Get order detail
            $order = $this->orderModel
                ->select("
                    orders.*,
                    order_statuses.status_name,
                    shipping_methods.name AS shipping_method,
                    shipping_methods.estimated_days,
                    payment_methods.method_name AS payment_method,
                    payments.paid_at,
                    payments.proof
                ")
                ->join('order_statuses', 'order_statuses.status_id = orders.status_id', 'left')
                ->join('shipping_methods', 'shipping_methods.shipping_method_id = orders.shipping_method_id', 'left')
                ->join('payments', 'payments.order_id = orders.order_id', 'left')
                ->join('payment_methods', 'payment_methods.payment_method_id = payments.payment_method_id', 'left')
                ->where('orders.order_id', $orderId)
                ->where('orders.customer_id', $customerId)
                ->where('orders.deleted_at', null)
                ->first();

            if (!$order) {
                return $this->response->setStatusCode(404)->setJSON([
                    'message' => 'Order not found'
                ]);
            }

            /**
             * 🔥 GET ITEMS WITH LOGIC IMAGE
             * - Variant → product_variant_images
             * - Non Variant → product_images (is_primary)
             */
            $items = $this->orderItemModel
                ->select("
                    order_items.order_item_id,
                    order_items.product_id,
                    order_items.variant_id,
                    order_items.quantity,
                    order_items.price,
                    
                    products.product_name,
                    product_variants.variant_name,
                    
                    COALESCE(pvi_img.url, pi_img.url) AS image
                ")
                ->join('products', 'products.product_id = order_items.product_id')
                ->join('product_variants', 'product_variants.variant_id = order_items.variant_id', 'left')

                // 🔥 VARIANT IMAGE
                ->join(
                    'product_variant_images pvi',
                    'pvi.variant_id = order_items.variant_id AND pvi.deleted_at IS NULL',
                    'left'
                )
                ->join(
                    'product_images pvi_img',
                    'pvi_img.product_image_id = pvi.product_image_id AND pvi_img.deleted_at IS NULL',
                    'left'
                )

                // 🔥 PRODUCT PRIMARY IMAGE
                ->join(
                    'product_images pi_img',
                    'pi_img.product_id = products.product_id 
                     AND pi_img.is_primary = 1 
                     AND pi_img.deleted_at IS NULL',
                    'left'
                )
                ->where('order_items.order_id', $orderId)
                ->where('order_items.deleted_at', null)
                ->findAll();

            // 👓 GET PRESCRIPTIONS
            $orderItemIds = array_column($items, 'order_item_id');
            $prescriptions = [];

            if (!empty($orderItemIds)) {
                $rows = $this->orderItemPrescriptionModel
                    ->whereIn('order_item_id', $orderItemIds)
                    ->findAll();

                foreach ($rows as $row) {
                    $prescriptions[$row['order_item_id']] = [
                        'right' => [
                            'sph'  => $row['right_sph'],
                            'cyl'  => $row['right_cyl'],
                            'axis' => $row['right_axis'],
                            'add'  => $row['right_add'],
                            'pd'   => $row['pd_right'],
                        ],
                        'left' => [
                            'sph'  => $row['left_sph'],
                            'cyl'  => $row['left_cyl'],
                            'axis' => $row['left_axis'],
                            'add'  => $row['left_add'],
                            'pd'   => $row['pd_left'],
                        ],
                    ];
                }
            }

            // Map items dengan prescription
            $mappedItems = array_map(function ($item) use ($prescriptions) {
                return [
                    'order_item_id' => $item['order_item_id'],
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'product_name' => $item['product_name'],
                    'variant_name' => $item['variant_name'],
                    'image' => $item['image'],
                    'price' => (int) $item['price'],
                    'quantity' => (int) $item['quantity'],
                    'subtotal' => (int) ($item['price'] * $item['quantity']),
                    'prescription' => $prescriptions[$item['order_item_id']] ?? null
                ];
            }, $items);

            // 📍 Get shipping address
            $shippingAddress = $this->orderShippingAddressModel
                ->where('order_id', $orderId)
                ->first();

            return $this->response->setJSON([
                'status' => 200,
                'data' => [
                    'order_id' => $order['order_id'],
                    'order_date' => $order['created_at'],
                    'status' => $order['status_name'],
                    'items' => $mappedItems,
                    'summary' => [
                        'shipping_cost' => (int) $order['shipping_cost'],
                        'grand_total' => (int) $order['grand_total']
                    ],
                    'shipping' => [
                        'method' => $order['shipping_method'],
                        'rate' => (int) $order['shipping_cost'],
                        'estimated_days' => $order['estimated_days'],
                        'address' => $shippingAddress ? [
                            'recipient_name' => $shippingAddress['recipient_name'],
                            'phone' => $shippingAddress['phone'],
                            'address' => $shippingAddress['address'],
                            'city' => $shippingAddress['city'],
                            'province' => $shippingAddress['province'],
                            'postal_code' => $shippingAddress['postal_code']
                        ] : null
                    ],
                    'payment' => [
                        'method' => $order['payment_method'],
                        'proof' => $order['proof'],
                        'date' => $order['paid_at']
                    ],
                    // 'notes' => $order['notes']
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => $e->getMessage()
            ]);
        }
    }

    // Helper: Get items grouped by order_id
    private function getOrderItemsGrouped($orderIds)
    {
        $items = $this->orderItemModel
            ->select("
                order_items.order_id,
                order_items.order_item_id,
                order_items.product_id,
                order_items.variant_id,
                order_items.quantity,
                order_items.price,
                
                products.product_name,
                product_variants.variant_name,
                
                COALESCE(pvi_img.url, pi_img.url) AS image
            ")
            ->join('products', 'products.product_id = order_items.product_id')
            ->join('product_variants', 'product_variants.variant_id = order_items.variant_id', 'left')
            ->join(
                'product_variant_images pvi',
                'pvi.variant_id = order_items.variant_id AND pvi.deleted_at IS NULL',
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
            ->whereIn('order_items.order_id', $orderIds)
            ->where('order_items.deleted_at', null)
            ->findAll();

        $grouped = [];
        foreach ($items as $item) {
            $orderId = $item['order_id'];
            if (!isset($grouped[$orderId])) {
                $grouped[$orderId] = [];
            }

            $grouped[$orderId][] = [
                'order_item_id' => $item['order_item_id'],
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'variant_name' => $item['variant_name'],
                'image' => $item['image'],
                'price' => (int) $item['price'],
                'quantity' => (int) $item['quantity'],
                'subtotal' => (int) ($item['price'] * $item['quantity'])
            ];
        }

        return $grouped;
    }

    // Helper: Get shipping addresses grouped
    private function getShippingAddressesGrouped($orderIds)
    {
        $addresses = $this->orderShippingAddressModel
            ->whereIn('order_id', $orderIds)
            ->findAll();

        $grouped = [];
        foreach ($addresses as $addr) {
            $grouped[$addr['order_id']] = [
                'recipient_name' => $addr['recipient_name'],
                'phone' => $addr['phone'],
                'address' => $addr['address'],
                'city' => $addr['city'],
                'province' => $addr['province'],
                'postal_code' => $addr['postal_code']
            ];
        }

        return $grouped;
    }


    // =======================
    // WEB DASHBOARD FUNCTIONS
    // =======================

    public function index()
    {
        $currentPage = $this->request->getVar('page')
            ? (int) $this->request->getVar('page')
            : 1;

        $limit  = 10;
        $offset = ($currentPage - 1) * $limit;

        $orders = $this->orderModel
            ->select('
                orders.order_id,
                orders.created_at,
                orders.grand_total,
                customers.customer_name,
                customers.customer_email,
                order_statuses.status_name,
                COUNT(order_items.order_item_id) as total_items
            ')
            ->where('order_type', 'online')
            ->join('customers', 'customers.customer_id = orders.customer_id')
            ->join('order_statuses', 'order_statuses.status_id = orders.status_id')
            ->join('order_items', 'order_items.order_id = orders.order_id', 'left')
            ->groupBy('orders.order_id')
            ->orderBy('orders.created_at', 'DESC')
            ->findAll($limit, $offset);


        // ============================
        // TOTAL ROWS
        // ============================
        $totalRows = $this->orderModel
            ->countAllResults();

        $totalPages = ceil($totalRows / $limit);

        // ============================
        // DATA TO VIEW
        // ============================
        return view('orders/v_index', [
            'orders' => $orders,
            'pager'  => [
                'totalPages'  => $totalPages,
                'currentPage' => $currentPage,
                'limit'       => $limit,
            ],
        ]);
    }

    /**
     * DETAIL ORDER
     * GET /admin/orders/{id}
     */
    public function detail($orderId)
    {
        $order = $this->orderModel
            ->select('
            orders.order_id,
            orders.created_at AS order_date,
            orders.grand_total,
            orders.shipping_cost,
            orders.status_id,
            orders.tracking_number,
            orders.courier,

            customers.customer_name,
            customers.customer_email,

            order_statuses.status_name,
            order_statuses.status_code,

            shipping_methods.name AS shipping_method,
            shipping_methods.estimated_days
        ')
            ->join('customers', 'customers.customer_id = orders.customer_id', 'left')
            ->join('order_statuses', 'order_statuses.status_id = orders.status_id', 'left')
            ->join('shipping_methods', 'shipping_methods.shipping_method_id = orders.shipping_method_id', 'left')
            ->where('orders.order_id', $orderId)
            ->where('orders.deleted_at', null)
            ->first();

        if (!$order) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Order not found');
        }

        // 📦 Order items
        $items = $this->orderModel->getOrderItems($orderId);

        // 💳 Payment
        $payment = $this->paymentModel
            ->select('
                payments.proof,
                payments.amount,
                payments.paid_at,
                payment_methods.method_name
            ')
            ->join('payment_methods', 'payment_methods.payment_method_id = payments.payment_method_id', 'left')
            ->where('payments.order_id', $orderId)
            ->first();

        // 📍 Shipping address
        $shippingAddress = $this->orderModel->getShippingAddress($orderId);

        $data = [
            'order'           => $order,
            'items'           => $items,
            'payment'         => $payment,
            'shippingAddress' => $shippingAddress
        ];

        return view('orders/v_detail', $data);
    }


    /**
     * APPROVE PAYMENT
     * POST /admin/orders/{id}/approve
     */
    public function approvePayment($orderId)
    {
        // Update order status → PAID
        $this->orderModel->update($orderId, [
            'status_id' => 'cc46d2a8-436c-42fc-96a1-ffb537dbabed' // PROCESSING ID STATUS
        ]);

        return redirect()->back()->with('success', 'Payment approved');
    }

    /**
     * REJECT PAYMENT
     */
    public function rejectPayment($orderId)
    {
        $this->paymentModel
            ->where('order_id', $orderId)
            ->set([
                'payment_status' => 'rejected'
            ])
            ->update();

        return redirect()->back()->with('success', 'Payment rejected');
    }

    public function shipOrder($orderId)
    {
        $data = [
            'status_id'        => '4d609622-8392-469b-acd1-c7859424633a', // SHIPPED ID STATUS
            'courier'          => $this->request->getVar('courier'),
            'tracking_number'  => $this->request->getVar('tracking_number'),
            'shipped_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s')
        ];

        $this->orderModel->update($orderId, $data);
        return redirect()->back()->with('success', 'Shipping added');
    }


    /**
     * UPDATE ORDER STATUS
     */
    public function updateStatus($orderId)
    {
        $statusId = $this->request->getPost('status_id');

        $this->orderModel->update($orderId, [
            'status_id' => $statusId
        ]);

        return redirect()->back()->with('success', 'Order status updated');
    }
}
