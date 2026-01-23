<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CartItemModel;
use App\Models\CartItemPrescriptionModel;
use App\Models\CartModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use CodeIgniter\API\ResponseTrait;

class CartApiController extends BaseApiController
{
    use ResponseTrait;
    protected $cartModel;
    protected $cartItemModel;
    protected $orderModel;
    protected $orderItemModel;
    protected $cartItemPrescriptionModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->cartItemModel = new CartItemModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->cartItemPrescriptionModel = new CartItemPrescriptionModel();
    }

    // GET /api/cart/add-to-cart
    public function addToCart()
    {
        $db = db_connect();
        $db->transStart();

        try {
            // 🔐 JWT
            $jwtUser = getJWTUser();
            if (!$jwtUser) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Unauthorized'
                ]);
            }

            $customerId = $jwtUser->user_id;
            $payload = $this->request->getJSON(true);
            // 📥 Input
            $productId    = $payload['product_id'] ?? null;
            $variantId    = $payload['variant_id'] ?? null;
            $qty          = (int) ($payload['quantity'] ?? 0);
            $prescription = $payload['prescription'] ?? null;

            if (!$productId || $qty <= 0) {
                throw new \Exception('Invalid input');
            }

            // 🔎 Product
            $product = $db->table('products')
                ->where('product_id', $productId)
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();

            if (!$product) {
                throw new \Exception('Product not found');
            }

            // 🧮 Harga & stok
            if ($variantId) {
                $variant = $db->table('product_variants')
                    ->where('variant_id', $variantId)
                    ->where('product_id', $productId)
                    ->get()
                    ->getRowArray();

                if (!$variant) {
                    throw new \Exception('Invalid variant');
                }

                if ($variant['stock'] < $qty) {
                    throw new \Exception('Insufficient variant stock');
                }

                $price = $variant['price'];
            } else {
                if ($product['product_stock'] < $qty) {
                    throw new \Exception('Insufficient product stock');
                }

                $price = $product['product_price'];
            }

            // 🛒 Cart
            $cart = $this->cartModel
                ->where('customer_id', $customerId)
                ->where('deleted_at', null)
                ->first();

            if (!$cart) {
                $this->cartModel->insert([
                    'customer_id' => $customerId,
                ]);

                $cart = $this->cartModel
                    ->where('customer_id', $customerId)
                    ->where('deleted_at', null)
                    ->first();
            }

            $cartId = $cart['cart_id'];

            // 🧾 Cart Item
            $this->cartItemModel->insert([
                'cart_id'    => $cartId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity'   => $qty,
                'price'      => $price,
            ]);

            $cartItemId = $this->cartItemModel->getInsertID();

            // 👓 SIMPAN RESEP MATA
            if ($prescription && ($prescription['type'] ?? 'none') !== 'none') {
                $pdRight = $prescription['right']['pd'] ?? null;
                $pdLeft  = $prescription['left']['pd'] ?? null;

                $this->cartItemPrescriptionModel->insert([
                    'cart_item_id' => $cartItemId,

                    'right_sph'  => $prescription['right']['sph'] ?? null,
                    'right_cyl'  => $prescription['right']['cyl'] ?? null,
                    'right_axis' => $prescription['right']['axis'] ?? null,
                    'right_add'  => $prescription['right']['add'] ?? null,

                    'left_sph'   => $prescription['left']['sph'] ?? null,
                    'left_cyl'   => $prescription['left']['cyl'] ?? null,
                    'left_axis'  => $prescription['left']['axis'] ?? null,
                    'left_add'   => $prescription['left']['add'] ?? null,

                    'pd_single'  => null,
                    'pd_left'    => $pdLeft,
                    'pd_right'   => $pdRight,
                ]);
            }

            $db->transComplete();

            return $this->response->setJSON([
                'message' => 'Item added to cart'
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();

            return $this->response->setStatusCode(400)->setJSON([
                'message' => $e->getMessage()
            ]);
        }
    }

    // GET /api/cart
    public function listCart()
    {
        try {
            $jwtUser = getJWTUser();
            if (!$jwtUser) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Unauthorized'
                ]);
            }

            $customerId = $jwtUser->user_id;

            $cart = $this->cartModel
                ->where('customer_id', $customerId)
                ->where('deleted_at', null)
                ->first();

            if (!$cart) {
                return $this->response->setJSON([
                    'items' => [],
                    'summary' => [
                        'total_qty' => 0,
                        'total_price' => 0
                    ]
                ]);
            }

            /**
             * 🔥 LOGIC IMAGE
             * - Variant → product_variant_images
             * - Non Variant → product_images (is_primary)
             */
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

                ->join(
                    'product_variants',
                    'product_variants.variant_id = cart_items.variant_id',
                    'left'
                )

                // 🔥 VARIANT IMAGE
                ->join(
                    'product_variant_images pvi',
                    'pvi.variant_id = cart_items.variant_id 
                 AND pvi.deleted_at IS NULL',
                    'left'
                )
                ->join(
                    'product_images pvi_img',
                    'pvi_img.product_image_id = pvi.product_image_id
                        AND pvi_img.deleted_at IS NULL',
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

            // 🧮 Summary
            $totalQty = 0;
            $totalPrice = 0;

            $mappedItems = array_map(function ($item) use (&$totalQty, &$totalPrice, $prescriptions) {
                $subtotal = $item['price'] * $item['quantity'];

                $totalQty += $item['quantity'];
                $totalPrice += $subtotal;

                return [
                    'cart_item_id' => $item['cart_item_id'],
                    'product_id'   => $item['product_id'],
                    'variant_id'   => $item['variant_id'],
                    'product_name' => $item['product_name'],
                    'variant_name' => $item['variant_name'],
                    'image'        => $item['image'],
                    'price'        => (int) $item['price'],
                    'quantity'     => (int) $item['quantity'],
                    'subtotal'     => (int) $subtotal,
                    'prescription' => $prescriptions[$item['cart_item_id']] ?? null
                ];
            }, $items);

            return $this->response->setJSON([
                'status' => 200,
                'data' => [
                    'items' => $mappedItems,
                    'summary' => [
                        'total_qty'   => $totalQty,
                        'total_price' => $totalPrice
                    ]
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => $e->getMessage()
            ]);
        }
    }

    // GET /api/cart/total-cart
    public function getTotalCart()
    {
        try {
            // 🔐 JWT
            $jwtUser = getJWTUser();
            if (!$jwtUser) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Please login first to view cart.'
                ]);
            }

            $customerId = $jwtUser->user_id;

            // 🛒 Cart
            $cart = $this->cartModel
                ->where('customer_id', $customerId)
                ->where('deleted_at', null)
                ->first();

            if (!$cart) {
                return $this->response->setJSON([
                    'status' => 200,
                    'data'   => [
                        'total_items' => 0
                    ]
                ]);
            }

            // 🧮 Total Quantity
            $totalItems = $this->cartItemModel
                ->select('SUM(quantity) AS total_items')
                ->where('cart_id', $cart['cart_id'])
                ->where('deleted_at', null)
                ->get()
                ->getRow()
                ->total_items ?? 0;

            return $this->response->setJSON([
                'status' => 200,
                'data'   => [
                    'total_items' => (int) $totalItems
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => $e->getMessage()
            ]);
        }
    }

    // GET /api/cart/delete/{id}
    public function deleteCartItem($cartItemId)
    {
        $db = db_connect();
        $db->transStart();

        try {
            // 🔐 JWT
            $jwtUser = getJWTUser();
            if (!$jwtUser) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Please login first.'
                ]);
            }

            $customerId = $jwtUser->user_id;

            // 🛒 Cart
            $cart = $this->cartModel
                ->where('customer_id', $customerId)
                ->where('deleted_at', null)
                ->first();

            if (!$cart) {
                throw new \Exception('Cart not found');
            }

            // 📦 Cart Item
            $cartItem = $this->cartItemModel
                ->where('cart_item_id', $cartItemId)
                ->where('cart_id', $cart['cart_id'])
                ->where('deleted_at', null)
                ->first();

            if (!$cartItem) {
                throw new \Exception('Cart item not found');
            }

            // 🗑️ Soft delete
            $this->cartItemModel->delete($cartItemId);

            // 🧮 Recalculate total
            $summary = $this->cartItemModel
                ->select('
                    SUM(quantity) AS total_qty,
                    SUM(quantity * price) AS total_price
                ')
                ->where('cart_id', $cart['cart_id'])
                ->where('deleted_at', null)
                ->get()
                ->getRow();

            $db->transComplete();

            return $this->response->setJSON([
                'message' => 'Item removed from cart',
                'summary' => [
                    'total_qty'   => (int) ($summary->total_qty ?? 0),
                    'total_price' => (int) ($summary->total_price ?? 0)
                ]
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();

            return $this->response->setStatusCode(400)->setJSON([
                'message' => $e->getMessage()
            ]);
        }
    }
}
