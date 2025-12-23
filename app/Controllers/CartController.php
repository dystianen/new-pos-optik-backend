<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CartItemModel;
use App\Models\CartModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use CodeIgniter\API\ResponseTrait;

class CartController extends BaseController
{
    use ResponseTrait;
    protected $cartModel;
    protected $cartItemModel;
    protected $orderModel;
    protected $orderItemModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->cartItemModel = new CartItemModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
    }

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

            // 📥 Input
            $productId = $this->request->getVar('product_id');
            $variantId = $this->request->getVar('variant_id');
            $qty       = (int) $this->request->getVar('quantity');

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

            $cart = $this->cartModel
                ->where('customer_id', $customerId)
                ->where('deleted_at', null)
                ->first();

            if (!$cart) {
                $this->cartModel->insert([
                    'customer_id' => $customerId,
                ]);

                // ⚠️ UUID diambil dari entity hasil insert
                $cart = $this->cartModel
                    ->where('customer_id', $customerId)
                    ->where('deleted_at', null)
                    ->first();

                if (!$cart) {
                    throw new \Exception('Failed to create cart');
                }
            }

            $cartId = $cart['cart_id']; // ✅ UUID VALID

            $cartItem = $this->cartItemModel
                ->where('cart_id', $cartId)
                ->where('product_id', $productId)
                ->where('deleted_at', null)
                ->when($variantId !== null, function ($q) use ($variantId) {
                    return $q->where('variant_id', $variantId);
                })
                ->when($variantId === null, function ($q) {
                    return $q->where('variant_id', null);
                })
                ->first();

            if ($cartItem) {
                $this->cartItemModel->update($cartItem['cart_item_id'], [
                    'quantity'   => $cartItem['quantity'] + $qty,
                ]);
            } else {
                $this->cartItemModel->insert([
                    'cart_id'    => $cartId,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'quantity'   => $qty,
                    'price'      => $price,
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


    public function getCart()
    {
        $decoded = $this->decodedToken();
        $customerId = $decoded->user_id;

        if (!$customerId) {
            return $this->respond([
                'status' => 401,
                'message' => 'Please login first to view the cart.'
            ], 401);
        }

        $order = $this->orderModel
            ->where('customer_id', $customerId)
            ->where('status', 'cart')
            ->first();

        if (!$order) {
            return $this->respond([
                'status' => 404,
                'message' => 'No cart found.'
            ], 404);
        }

        $orderItems = $this->orderItemModel
            ->where('order_id', $order['order_id'])
            ->join('products', 'products.product_id = order_items.product_id')
            ->findAll();

        return $this->respond([
            'status' => 200,
            'message' => 'Cart retrieved successfully.',
            'data' => [
                'order_id' => $order['order_id'],
                'shipping_costs' => $order['shipping_costs'],
                'total_price' => $order['total_price'],
                'grand_total' => $order['grand_total'],
                'items' => $orderItems,
            ]
        ], 200);
    }

    public function getTotalCart()
    {
        $decoded = $this->decodedToken();
        $customerId = $decoded->user_id;

        if (!$customerId) {
            return $this->respond([
                'status' => 401,
                'message' => 'Please login first to view the total cart quantity.'
            ], 401);
        }

        // Ambil order dengan status cart
        $order = $this->orderModel
            ->where('customer_id', $customerId)
            ->where('status', 'cart')
            ->first();

        if (!$order) {
            // Tidak ada cart, tetap return sukses dengan 0 item
            return $this->respond([
                'status' => 200,
                'message' => 'No cart found.',
                'data' => [
                    'order_id' => null,
                    'total_items' => 0
                ]
            ]);
        }

        // Hitung total item di order tersebut
        $totalItems = $this->orderItemModel
            ->select('SUM(quantity) AS total_items')
            ->where('order_id', $order['order_id'])
            ->get()
            ->getRow()
            ->total_items;

        return $this->respond([
            'status' => 200,
            'message' => 'Total cart quantity retrieved successfully.',
            'data' => [
                'order_id' => $order['order_id'],
                'total_items' => (int) $totalItems
            ]
        ]);
    }

    public function deleteCartItem($itemId)
    {
        $decoded = $this->decodedToken();
        $customerId = $decoded->user_id;

        if (!$customerId) {
            return $this->respond([
                'status' => 401,
                'message' => 'Please login first to delete items from the cart.'
            ], 401);
        }

        // Ambil item yang akan dihapus
        $item = $this->orderItemModel->find($itemId);

        if (!$item) {
            return $this->respond([
                'status' => 404,
                'message' => 'Item not found.'
            ], 404);
        }

        // Ambil order cart milik user
        $order = $this->orderModel
            ->where('order_id', $item['order_id'])
            ->where('customer_id', $customerId)
            ->where('status', 'cart')
            ->first();

        if (!$order) {
            return $this->respond([
                'status' => 403,
                'message' => 'Unauthorized access to cart.'
            ], 403);
        }

        // Hapus item dari order_items
        $this->orderItemModel->delete($itemId);

        // Hitung ulang total_price
        $totalPrice = $this->orderItemModel
            ->select('SUM(quantity * price) AS total')
            ->where('order_id', $order['order_id'])
            ->get()
            ->getRow()
            ->total ?? 0;

        // Update total_price di order
        $this->orderModel->update($order['order_id'], ['total_price' => $totalPrice]);

        return $this->respond([
            'status' => 200,
            'message' => 'Item deleted and cart updated.',
            'data' => [
                'order_id' => $order['order_id'],
                'total_price' => $totalPrice
            ]
        ]);
    }
}
