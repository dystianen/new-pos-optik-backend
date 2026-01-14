<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\WishlistModel;

class WishlistController extends BaseController
{
    protected $wishlistModel;

    public function __construct()
    {
        $this->wishlistModel = new WishlistModel();
    }

    /**
     * GET /api/wishlist
     */
    public function index()
    {
        $jwtUser    = getJWTUser();
        $customerId = $jwtUser->user_id;

        $search = $this->request->getVar('search');
        $page   = (int) ($this->request->getVar('page') ?? 1);
        $limit  = (int) ($this->request->getVar('limit') ?? 10);

        /**
         * ==========================
         * SUBQUERY TOTAL SOLD
         * ==========================
         */
        $subQuery = $this->db->table('order_items oi')
            ->select('oi.product_id, SUM(oi.quantity) AS total_sold')
            ->join('orders o', 'o.order_id = oi.order_id')
            ->where('o.status_id', '8d434de4-ba22-4698-8438-8318ef3f6d8f')
            ->groupBy('oi.product_id');

        /**
         * ==========================
         * MAIN QUERY
         * ==========================
         */
        $builder = $this->wishlistModel
            ->select('
            p.product_id,
            p.product_name,
            p.product_price,
            p.product_stock,
            p.product_brand,
            pi.url AS product_image_url,
            COALESCE(ts.total_sold, 0) AS total_sold,
            1 AS is_wishlist
        ')
            ->join('products p', 'p.product_id = wishlists.product_id')
            ->join(
                "({$subQuery->getCompiledSelect()}) ts",
                'ts.product_id = p.product_id',
                'left'
            )
            ->join(
                'product_images pi',
                'pi.product_id = p.product_id AND pi.is_primary = 1',
                'left'
            )
            ->where('wishlists.customer_id', $customerId)
            ->where('wishlists.deleted_at', null)
            ->where('p.deleted_at', null);

        /**
         * 🔍 SEARCH
         */
        if (!empty($search)) {
            $builder->groupStart()
                ->like('p.product_name', $search)
                ->orLike('p.product_brand', $search)
                ->groupEnd();
        }

        /**
         * 📄 PAGINATION
         */
        $wishlist = $builder
            ->orderBy('wishlists.created_at', 'DESC')
            ->paginate($limit, 'wishlist', $page);

        $pager = [
            'currentPage' => $this->wishlistModel->pager->getCurrentPage('wishlist'),
            'totalPages'  => $this->wishlistModel->pager->getPageCount('wishlist'),
            'limit'       => $limit,
            'totalItems'  => $this->wishlistModel->pager->getTotal('wishlist'),
        ];

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Wishlist fetched',
            'data'    => $wishlist,
            'pager'   => $pager
        ]);
    }


    /**
     * GET /api/wishlist/count
     * ✅ Get total wishlist count untuk customer
     */
    public function count()
    {
        $jwtUser    = getJWTUser();
        $customerId = $jwtUser->user_id;

        $total = $this->wishlistModel
            ->where('customer_id', $customerId)
            ->countAllResults();

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Wishlist count fetched',
            'data'    => [
                'total' => $total
            ]
        ]);
    }

    /**
     * POST /api/wishlist/toggle
     */
    public function toggle()
    {
        $jwtUser    = getJWTUser();
        $customerId = $jwtUser->user_id;
        $productId  = $this->request->getVar('product_id');

        if (!$productId) {
            return $this->response->setJSON([
                'status'  => 400,
                'message' => 'Product ID required'
            ]);
        }

        $exists = $this->wishlistModel
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();

        if ($exists) {
            // remove
            $this->wishlistModel->delete($exists['wishlist_id']);

            // ✅ Get updated count
            $total = $this->wishlistModel
                ->where('customer_id', $customerId)
                ->countAllResults();

            return $this->response->setJSON([
                'status'  => 200,
                'message' => 'Removed from wishlist',
                'data'    => [
                    'is_wishlist' => false,
                    'total' => $total // ✅ Return total count
                ]
            ]);
        }

        // add
        $this->wishlistModel->insert([
            'customer_id' => $customerId,
            'product_id'  => $productId
        ]);

        // ✅ Get updated count
        $total = $this->wishlistModel
            ->where('customer_id', $customerId)
            ->countAllResults();

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Added to wishlist',
            'data'    => [
                'is_wishlist' => true,
                'total' => $total // ✅ Return total count
            ]
        ]);
    }
}
