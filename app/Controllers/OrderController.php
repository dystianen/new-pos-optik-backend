<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CartItemModel;
use App\Models\CartItemPrescriptionModel;
use App\Models\CartModel;
use App\Models\CustomerShippingAddressModel;
use App\Models\InventoryTransactionModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\ShippingRateModel;
use CodeIgniter\API\ResponseTrait;

class OrderController extends BaseController
{
    use ResponseTrait;
    protected $orderModel, $orderItemModel, $InventoryTransactionModel, $productModel, $csaModel, $cartModel, $cartItemModel, $shippingRateModel, $cartItemPrescriptionModel;

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
    }

    // =======================
    // API FUNCTIONS
    // =======================

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

    public function checkout()
    {
        $decoded = $this->decodedToken();
        $customerId = $decoded->user_id;

        if (!$customerId) {
            return $this->respond(['status' => 401, 'message' => 'Unauthorized'], 401);
        }

        $order = $this->orderModel
            ->where('customer_id', $customerId)
            ->where('status', 'cart')
            ->first();

        if (!$order) {
            return $this->respond(['status' => 404, 'message' => 'Cart not found'], 404);
        }

        $shippingAddress = $this->request->getVar('shipping_address');
        $shippingCost = 20000;

        if (!$shippingAddress) {
            return $this->respond(['status' => 400, 'message' => 'Incomplete checkout data'], 400);
        }

        // Start DB transaction
        $this->db->transBegin();

        try {
            $orderDetails = $this->orderItemModel
                ->where('order_id', $order['order_id'])
                ->findAll();

            // Validasi stok
            foreach ($orderDetails as $item) {
                $product = $this->productModel->find($item['product_id']);

                if (!$product) {
                    $this->db->transRollback();
                    return $this->respond([
                        'status' => 400,
                        'message' => 'Product not found: ID ' . $item['product_id']
                    ], 400);
                }

                $currentStock = (int)$product['product_stock'];
                $requestedQty = (int)$item['quantity'];

                if ($requestedQty <= 0) {
                    $this->db->transRollback();
                    return $this->respond([
                        'status' => 400,
                        'message' => 'Invalid quantity for product ID: ' . $item['product_id']
                    ], 400);
                }

                if ($currentStock < $requestedQty) {
                    $this->db->transRollback();
                    return $this->respond([
                        'status' => 400,
                        'message' => 'Insufficient stock for product ID: ' . $item['product_id']
                    ], 400);
                }
            }

            $finalTotal = $order['total_price'] + $shippingCost;

            // Update order
            $this->orderModel->update($order['order_id'], [
                'address' => $shippingAddress,
                'proof_of_payment' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'pending',
                'shipping_costs' => $shippingCost,
                'grand_total' => $finalTotal,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Proses inventory dan pengurangan stok
            foreach ($orderDetails as $item) {
                $this->InventoryTransactionModel->insert([
                    'user_id' => 5,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'transaction_type' => 'out',
                    'description' => 'Checkout Order #' . $order['order_id'],
                    'transaction_date' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Kurangi stok
                $product = $this->productModel->find($item['product_id']);
                $newStock = (int)$product['product_stock'] - (int)$item['quantity'];

                $this->productModel->update($item['product_id'], [
                    'product_stock' => $newStock
                ]);
            }

            // Commit transaksi
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->respond(['status' => 500, 'message' => 'Checkout failed. Please try again.'], 500);
            }

            $this->db->transCommit();

            return $this->respond([
                'status' => 200,
                'message' => 'Checkout successful. Awaiting payment confirmation.',
                'data' => [
                    'order_id' => $order['order_id'],
                    'grand_total' => $finalTotal,
                    'items' => $orderDetails
                ]
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->respond([
                'status' => 500,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadPaymentProof()
    {
        $decoded = $this->decodedToken();
        $customerId = $decoded->user_id;

        if (!$customerId) {
            return $this->respond(['status' => 401, 'message' => 'Unauthorized'], 401);
        }
        log_message('debug', print_r($_FILES, true));

        // Cari order yang masih 'pending'
        $order = $this->orderModel
            ->where('customer_id', $customerId)
            ->where('status', 'pending')
            ->first();

        if (!$order) {
            return $this->respond([
                'status' => 404,
                'message' => 'No pending order found'
            ], 404);
        }

        $file = $this->request->getFile('proof_of_payment');

        if (!$file || !$file->isValid()) {
            return $this->respond([
                'status' => 400,
                'message' => 'Proof of payment file is required'
            ], 400);
        }

        // Simpan file ke folder uploads/payments/
        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/payments', $newName);

        // Update order
        $this->orderModel->update($order['order_id'], [
            'proof_of_payment' => 'uploads/payments/' . $newName,
            'status' => 'waiting_confirmation',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->respond([
            'status' => 200,
            'message' => 'Payment proof uploaded successfully',
            'data' => [
                'order_id' => $order['order_id'],
                'proof_of_payment' => base_url('uploads/payments/' . $newName)
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
            'status' => $this->request->getPost('status'),
        ];

        $this->orderModel->update($id, $data);
        $message = 'Order updated successfully!';

        return redirect()->to('/orders')->with('success', $message);
    }

    public function checkIfPaid()
    {
        $decoded = $this->decodedToken();
        $customerId = $decoded->user_id;

        if (!$customerId) {
            return $this->respond(['status' => 401, 'message' => 'Unauthorized'], 401);
        }

        // Ambil order terakhir (atau bisa disesuaikan)
        $order = $this->orderModel
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!$order) {
            return $this->respond(['status' => 404, 'message' => 'Order not found'], 404);
        }

        $isShipped = $order['status'] === 'paid';

        return $this->respond([
            'status' => 200,
            'message' => $isShipped ? 'Order has been shipped' : 'Order not yet shipped',
            'data' => [
                'isShipped' => $isShipped
            ]
        ]);
    }
}
