<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class InStoreSalesController extends BaseController
{
    protected $orderModel;
    protected $productModel;
    protected $productVariantModel;
    protected $orderItemPrescriptionModel;
    protected $customerModel;
    protected $orderItemModel;
    protected $db;

    public function __construct()
    {
        $this->orderModel    = new \App\Models\OrderModel();
        $this->productModel  = new \App\Models\ProductModel();
        $this->productVariantModel  = new \App\Models\ProductVariantModel();
        $this->orderItemPrescriptionModel  = new \App\Models\OrderItemPrescriptionModel();
        $this->customerModel = new \App\Models\CustomerModel();
        $this->orderItemModel = new \App\Models\OrderItemModel();
        $this->db            = \Config\Database::connect();
    }

    public function index()
    {
        $currentPage = (int) ($this->request->getVar('page') ?? 1);
        $search      = $this->request->getVar('q');

        $limit  = 10;
        $offset = ($currentPage - 1) * $limit;

        // ============================
        // BASE QUERY
        // ============================
        $builder = $this->orderModel
            ->select('
            orders.order_id,
            orders.created_at,
            orders.grand_total,
            customers.customer_name,
            customers.customer_email,
            order_statuses.status_name,
            COUNT(order_items.order_item_id) as total_items
        ')
            ->join('customers', 'customers.customer_id = orders.customer_id')
            ->join('order_statuses', 'order_statuses.status_id = orders.status_id')
            ->join('order_items', 'order_items.order_id = orders.order_id', 'left')
            ->where('orders.order_type', 'offline');

        // ============================
        // SEARCH FILTER
        // ============================
        if (!empty($search)) {
            $builder->groupStart()
                ->like('orders.order_id', $search)
                ->orLike('customers.customer_name', $search)
                ->orLike('customers.customer_email', $search)
                ->orLike('order_statuses.status_name', $search)
                ->groupEnd();
        }

        // ============================
        // DATA
        // ============================
        $orders = $builder
            ->groupBy('orders.order_id')
            ->orderBy('orders.created_at', 'DESC')
            ->findAll($limit, $offset);

        // ============================
        // TOTAL ROWS (CLONE BUILDER)
        // ============================
        $countBuilder = clone $builder;
        $totalRows    = $countBuilder->countAllResults(false);

        $totalPages = ceil($totalRows / $limit);

        return view('in_store_sales/v_index', [
            'orders' => $orders,
            'search' => $search,
            'pager'  => [
                'totalPages'  => $totalPages,
                'currentPage' => $currentPage,
                'limit'       => $limit,
            ],
        ]);
    }


    public function create()
    {
        return view('in_store_sales/v_create', [
            'customers' => $this->customerModel->findAll(),
            'products'  => $this->productModel->findAll(),
        ]);
    }

    public function store()
    {
        $db = db_connect();
        $db->transStart();

        try {
            $customerId   = $this->request->getPost('customer_id');
            $items        = $this->request->getPost('items');
            $prescription = $this->request->getPost('prescription');

            if (!$customerId) {
                throw new \Exception('Customer wajib dipilih');
            }

            if (empty($items)) {
                throw new \Exception('Item tidak boleh kosong');
            }

            // ======================
            // HITUNG GRAND TOTAL
            // ======================
            $grandTotal = 0;

            foreach ($items as $item) {
                if (
                    empty($item['product_id']) ||
                    empty($item['qty']) ||
                    empty($item['price'])
                ) {
                    throw new \Exception('Data item tidak lengkap');
                }

                $price = (float) $item['price'];
                $qty   = (int) $item['qty'];

                if ($price <= 0 || $qty <= 0) {
                    throw new \Exception('Harga / Qty tidak valid');
                }

                $grandTotal += $price * $qty;
            }

            // ======================
            // INSERT ORDER
            // ======================
            $this->orderModel->insert([
                'customer_id'     => $customerId,
                'status_id'       => '8d434de4-ba22-4698-8438-8318ef3f6d8f', // COMPLETED ID STATUS
                'shipping_cost'   => 0,
                'coupon_discount' => 0,
                'grand_total'     => $grandTotal,
                'order_type'      => 'offline'
            ]);

            $orderId = $this->orderModel->getInsertID();

            // ======================
            // INSERT ORDER ITEMS
            // ======================
            foreach ($items as $item) {

                $productId = $item['product_id'];
                $variantId = $item['variant_id'] ?: null;
                $qty       = (int) $item['qty'];
                $price     = (float) $item['price'];

                $this->orderItemModel->insert([
                    'order_id'   => $orderId,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'quantity'   => $qty,
                    'price'      => $price,
                ]);

                $orderItemId = $this->orderItemModel->getInsertID();

                // ======================
                // KURANGI STOK
                // ======================
                if ($variantId) {
                    // stok variant
                    $this->productVariantModel
                        ->where('variant_id', $variantId)
                        ->set('stock', 'stock - ' . $qty, false)
                        ->update();

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
                    // stok product tanpa variant
                    $this->productModel
                        ->where('product_id', $productId)
                        ->set('product_stock', 'product_stock - ' . $qty, false)
                        ->update();
                }

                // ======================
                // PRESCRIPTION (JIKA MANUAL)
                // ======================
                if (
                    isset($prescription['type']) &&
                    $prescription['type'] === 'manual'
                ) {
                    $this->orderItemPrescriptionModel->insert([
                        'order_item_id' => $orderItemId,

                        'right_sph'  => $prescription['right']['sph'] ?? null,
                        'right_cyl'  => $prescription['right']['cyl'] ?? null,
                        'right_axis' => $prescription['right']['axis'] ?? null,
                        'pd_right'   => $prescription['right']['pd'] ?? null,

                        'left_sph'   => $prescription['left']['sph'] ?? null,
                        'left_cyl'   => $prescription['left']['cyl'] ?? null,
                        'left_axis'  => $prescription['left']['axis'] ?? null,
                        'pd_left'    => $prescription['left']['pd'] ?? null,
                    ]);
                }
            }

            $db->transComplete();

            return redirect()
                ->to(site_url('in-store-sales'))
                ->with('success', 'Transaksi berhasil disimpan');
        } catch (\Throwable $e) {
            $db->transRollback();

            log_message('error', $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

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
            ')
            ->join('customers', 'customers.customer_id = orders.customer_id', 'left')
            ->join('order_statuses', 'order_statuses.status_id = orders.status_id', 'left')
            ->where('orders.order_id', $orderId)
            ->where('orders.deleted_at', null)
            ->first();

        if (!$order) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Order not found');
        }

        // 📦 Order items
        $items = $this->orderModel->getOrderItems($orderId);

        $data = [
            'order' => $order,
            'items' => $items,
        ];

        return view('in_store_sales/v_detail', $data);
    }
}
