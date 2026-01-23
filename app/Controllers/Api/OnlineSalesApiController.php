<?php

namespace App\Controllers\Api;

use App\Libraries\R2Storage;
use App\Models\CartItemModel;
use App\Models\CartItemPrescriptionModel;
use App\Models\CartModel;
use App\Models\CustomerShippingAddressModel;
use App\Models\InventoryTransactionModel;
use App\Models\NotificationModel;
use App\Models\OrderItemModel;
use App\Models\OrderItemPrescriptionModel;
use App\Models\OrderModel;
use App\Models\OrderRefundModel;
use App\Models\OrderShippingAddressModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\ProductVariantModel;
use App\Models\ShippingRateModel;
use App\Models\UserRefundAccountModel;
use CodeIgniter\API\ResponseTrait;

class OnlineSalesApiController extends BaseApiController
{
    use ResponseTrait;
    protected $orderModel, $orderItemModel, $InventoryTransactionModel, $productModel, $productVariantModel, $csaModel, $cartModel, $cartItemModel, $shippingRateModel, $cartItemPrescriptionModel, $orderShippingAddressModel, $orderItemPrescriptionModel, $paymentModel, $notificationModel, $userRefundAccountModel, $orderRefundModel, $r2;

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
        $this->notificationModel = new NotificationModel();
        $this->userRefundAccountModel = new UserRefundAccountModel();
        $this->orderRefundModel = new OrderRefundModel();
        $this->r2 = new R2Storage();
    }

    // GET /api/orders/summary-orders/(:segment)
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
            $customerName = $jwtUser->user_name;

            // 🔁 Ambil snapshot summary
            $summaryResponse = $this->summaryOrders($addressId);
            $summary = json_decode($summaryResponse->getBody(), true)['data'];
            log_message('debug', 'SUMMARY: ' . json_encode($summary));

            // ======================
            // VALIDASI STOK (MARKETPLACE)
            // ======================
            foreach ($summary['items'] as $item) {

                $productId = $item['product_id'];
                $variantId = $item['variant_id'] ?? null;
                $qty       = (int) $item['quantity'];

                if ($variantId) {
                    $variant = $db->query(
                        "SELECT * FROM product_variants WHERE variant_id = ? FOR UPDATE",
                        [$variantId]
                    )->getRowArray();

                    if (!$variant || $variant['stock'] < $qty) {
                        throw new \Exception('Stok produk tidak mencukupi');
                    }
                } else {
                    $product = $db->query(
                        "SELECT * FROM products WHERE product_id = ? FOR UPDATE",
                        [$productId]
                    )->getRowArray();

                    if (!$product || $product['product_stock'] < $qty) {
                        throw new \Exception('Stok produk tidak mencukupi');
                    }
                }
            }


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
            $orderId = $this->orderModel->getInsertID();

            $this->notificationModel->addNotification('new_order', "Pesanan baru dari {$customerName}", $orderId);
            log_message('debug', 'ORDER QUERY: ' . $this->orderModel->getLastQuery());

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
            $customerName = $jwtUser->user_name;

            // 📥 INPUT
            $orderId = $this->request->getVar('order_id');
            $payment_method_id  = $this->request->getVar('payment_method_id');
            $amount  = $this->request->getVar('amount');
            $img     = $this->request->getFile('proof');
            $accountName  = $this->request->getVar('account_name');
            $bankName  = $this->request->getVar('bank_name');
            $accountNumber  = $this->request->getVar('account_number');

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

            // ☁️ UPLOAD KE R2
            $objectUrl = $this->r2->uploadFile(
                $img->getTempName(),
                'payments/' . $orderId . '/' . $img->getRandomName()
            );

            log_message('debug', 'UPLOAD SUCCESS: ' . $objectUrl);

            // 🔍 CARI REFUND ACCOUNT CUSTOMER
            $refundAccount = $this->userRefundAccountModel
                ->where('customer_id', $customerId)
                ->first();

            if (!$refundAccount) {
                // 🚨 VALIDASI WAJIB JIKA BELUM ADA
                if (!$accountName || !$bankName || !$accountNumber) {
                    throw new \Exception('Refund account is required');
                }

                // ➕ CREATE REFUND ACCOUNT
                $userRefundAccountId = service('uuid')->uuid4()->toString();

                $this->userRefundAccountModel->insert([
                    'user_refund_account_id' => $userRefundAccountId,
                    'customer_id'            => $customerId,
                    'account_name'           => $accountName,
                    'bank_name'              => $bankName,
                    'account_number'         => $accountNumber,
                ]);
            } else {
                // ✅ PAKAI YANG SUDAH ADA
                $userRefundAccountId = $refundAccount['user_refund_account_id'];
            }

            $this->orderRefundModel->insert([
                'order_id'               => $orderId,
                'user_refund_account_id' => $userRefundAccountId,
            ]);

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
                'status_id' => '7f39039d-d2ef-46d1-93f5-8dbc0b5211fe', // contoh: WAITING_CONFIRMATION
            ]);

            $this->notificationModel->addNotification('new_order', "Pembayaran baru dari {$customerName}", $orderId);

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
            return $this->respond([
                'status'  => 401,
                'message' => 'Unauthorized'
            ], 401);
        }

        $customerId = $jwtUser->user_id;

        if (!$orderId) {
            return $this->respond([
                'status'  => 400,
                'message' => 'Order ID is required'
            ], 400);
        }

        $order = $this->orderModel
            ->where('order_id', $orderId)
            ->where('customer_id', $customerId)
            ->first();

        if (!$order) {
            return $this->respond([
                'status'  => 404,
                'message' => 'Order not found'
            ], 404);
        }

        // ============================
        // STATUS ID (SESUIKAN)
        // ============================
        $STATUS_APPROVED = 'cc46d2a8-436c-42fc-96a1-ffb537dbabed'; // PAID / APPROVED
        $STATUS_REJECTED = 'f1a3c2b4-9e77-4e8d-9b12-2c5a7e8f91ab'; // PAYMENT REJECTED

        $paymentStatus = 'pending';
        $message       = 'Payment is waiting for verification';

        if ($order['status_id'] === $STATUS_APPROVED) {
            $paymentStatus = 'approved';
            $message       = 'Payment approved';
        } elseif ($order['status_id'] === $STATUS_REJECTED) {
            $paymentStatus = 'rejected';
            $message       = 'Payment rejected';
        }

        return $this->respond([
            'status'  => 200,
            'message' => $message,
            'data'    => [
                'order_id'       => $orderId,
                'status_id'      => $order['status_id'],
                'payment_status' => $paymentStatus,
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

            $builder = $this->orderModel
                ->select("
                    orders.order_id,
                    orders.created_at AS order_date,
                    orders.grand_total,
                    orders.shipping_cost,

                    order_statuses.status_name,

                    shipping_methods.name AS shipping_method,
                    shipping_methods.estimated_days,

                    pm.method_name AS payment_method,
                    p.paid_at
                ")
                ->join('order_statuses', 'order_statuses.status_id = orders.status_id', 'left')
                ->join('shipping_methods', 'shipping_methods.shipping_method_id = orders.shipping_method_id', 'left')
                ->join(
                    '(SELECT p1.*
                    FROM payments p1
                    INNER JOIN (
                        SELECT order_id, MAX(paid_at) AS latest_paid
                        FROM payments
                        GROUP BY order_id
                    ) p2
                    ON p1.order_id = p2.order_id AND p1.paid_at = p2.latest_paid
                    ) p',
                    'p.order_id = orders.order_id',
                    'left'
                )

                ->join('payment_methods pm', 'pm.payment_method_id = p.payment_method_id', 'left')
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
                        'courier' => $order['courier'],
                        'tracking_number' => $order['tracking_number'],
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

    // POST /api/online-sales/{id}/approve
    public function approvePayment($orderId)
    {
        $this->db->transBegin();
        $session = session();

        try {
            // 1️⃣ Cegah double approve
            $order = $this->orderModel->find($orderId);

            if (!$order) {
                throw new \Exception('Order tidak ditemukan');
            }

            if ($order['status_id'] === 'cc46d2a8-436c-42fc-96a1-ffb537dbabed') {
                throw new \Exception('Order sudah diproses');
            }

            // 2️⃣ Update status → PAID / PROCESSING
            $this->orderModel->update($orderId, [
                'status_id' => 'cc46d2a8-436c-42fc-96a1-ffb537dbabed'
            ]);

            // 3️⃣ Ambil item order
            $items = $this->orderItemModel
                ->where('order_id', $orderId)
                ->findAll();

            foreach ($items as $item) {
                // 4️⃣ Insert inventory OUT
                $this->InventoryTransactionModel->insert([
                    'inventory_transaction_id' => service('uuid')->uuid4()->toString(),
                    'user_id'                  => $session->get('id'),
                    'product_id'               => $item['product_id'],
                    'variant_id'               => $item['variant_id'],
                    'transaction_type'         => 'out',
                    'reference_type'           => 'order',
                    'reference_id'             => $orderId,
                    'quantity'                 => (int)$item['quantity'],
                    'transaction_date'         => date('Y-m-d H:i:s'),
                    'description'              => 'Order payment approved'
                ]);

                // 5️⃣ Reduce stock
                if ($item['variant_id']) {
                    $this->productVariantModel
                        ->where('variant_id', $item['variant_id'])
                        ->set('stock', 'stock - ' . (int)$item['quantity'], false)
                        ->update();

                    // sync total product stock
                    $this->db->query("
                        UPDATE products p
                        SET product_stock = (
                            SELECT COALESCE(SUM(stock), 0)
                            FROM product_variants
                            WHERE product_id = p.product_id
                        )
                        WHERE p.product_id = ?
                    ", [$item['product_id']]);

                    // ✅ Cek stok variant, buat notifikasi jika kurang dari 5
                    $product = $this->productVariantModel->find($item['product_id']);
                    $variant = $this->productVariantModel->find($item['variant_id']);
                    if ($variant['stock'] < 5) {
                        $this->notificationModel->addNotification(
                            'low_stock',
                            "Stok barang '{$product['product_name']} ({$variant['variant_name']})' tinggal {$variant['stock']}",
                            $variant['variant_id']
                        );
                    }
                } else {
                    $this->productModel
                        ->where('product_id', $item['product_id'])
                        ->set('product_stock', 'product_stock - ' . (int)$item['quantity'], false)
                        ->update();

                    // ✅ Cek stok product, buat notifikasi jika kurang dari 5
                    $product = $this->productModel->find($item['product_id']);
                    if ($product['product_stock'] < 5) {
                        $this->notificationModel->addNotification(
                            'low_stock',
                            "Stok barang '{$product['product_name']}' tinggal {$product['product_stock']}",
                            $product['product_id']
                        );
                    }
                }
            }

            $this->db->transCommit();

            return redirect()->back()->with('success', 'Payment approved & stock updated');
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // POST /api/online-sales/{id}/reject
    public function rejectPayment($orderId)
    {
        $this->orderModel->update($orderId, [
            'status_id' => 'f1a3c2b4-9e77-4e8d-9b12-2c5a7e8f91ab'
        ]);

        return redirect()->back()->with('success', 'Payment rejected');
    }

    // POST /api/online-sales/{id}/ship
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

    // POST /api/online-sales/{id}/status
    public function updateStatus($orderId)
    {
        $statusId = $this->request->getVar('status_id');

        if (!$statusId) {
            return $this->respond([
                'status'  => 400,
                'message' => 'status_id is required'
            ], 400);
        }

        $order = $this->orderModel->find($orderId);

        if (!$order) {
            return $this->respond([
                'status'  => 404,
                'message' => 'Order not found'
            ], 404);
        }

        $this->orderModel->update($orderId, [
            'status_id' => $statusId
        ]);

        return $this->respond([
            'status'  => 200,
            'message' => 'Order status updated successfully',
            'data'    => [
                'order_id' => $orderId,
                'status_id' => $statusId
            ]
        ]);
    }
}
