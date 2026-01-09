<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\InventoryTransactionModel;
use App\Models\OrderModel;
use App\Models\ProductModel;

class DashboardController extends BaseController
{
    protected $customerModel, $productModel, $InventoryTransactionModel, $ordersModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->productModel = new ProductModel();
        $this->InventoryTransactionModel = new InventoryTransactionModel();
        $this->ordersModel = new OrderModel();
    }

    public function index()
    {
        $totalProducts = $this->productModel->countAllResults();
        $totalCustomers = $this->customerModel->countAllResults();

        // Total Selling dari Inventory Transactions (unit)
        $totalSellingUnits = (int) (
            $this->db->table('order_items oi')
            ->selectSum('oi.quantity', 'total_units')
            ->join('orders o', 'oi.order_id = o.order_id')
            ->join('order_statuses os', 'o.status_id = os.status_id')
            ->whereIn('os.status_code', ['processing', 'shipped', 'completed'])
            ->get()
            ->getRow()
            ->total_units
            ?? 0
        );

        // Total Selling dari Orders (rupiah)
        $totalSellingRupiah = (float) (
            $this->ordersModel
                ->selectSum('grand_total', 'total_rupiah')
                ->join('order_statuses os', 'orders.status_id = os.status_id')
                ->whereIn('os.status_code', ['processing', 'shipped', 'completed'])
                ->first()['total_rupiah']
            ?? 0
        );

        // Monthly Sales (Units)
        $monthlySalesUnits = $this->InventoryTransactionModel
            ->select("MONTH(created_at) AS month, SUM(quantity) AS total")
            ->where('transaction_type', 'out')
            ->where('YEAR(created_at)', date('Y'))
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->findAll();

        // Monthly Sales (Rupiah)
        $monthlySalesRupiah = $this->ordersModel
            ->select("MONTH(o.created_at) AS month, SUM(o.grand_total) AS total")
            ->from('orders o')
            ->join('order_statuses os', 'o.status_id = os.status_id')
            ->whereIn('os.status_name', ['paid', 'shipped'])
            ->where('YEAR(o.created_at)', date('Y'))
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->findAll();

        $unitsMap = [];
        foreach ($monthlySalesUnits as $row) {
            $unitsMap[(int)$row['month']] = (int)$row['total'];
        }

        $rupiahMap = [];
        foreach ($monthlySalesRupiah as $row) {
            $rupiahMap[(int)$row['month']] = (int)$row['total'];
        }

        $months = [];
        $unitTotals = [];
        $rupiahTotals = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('F', mktime(0, 0, 0, $i, 1));
            $unitTotals[] = $unitsMap[$i] ?? 0;
            $rupiahTotals[] = $rupiahMap[$i] ?? 0;
        }

        // Total Barang Masuk dan Keluar
        $totalIn = $this->InventoryTransactionModel
            ->selectSum('quantity')
            ->where('transaction_type', 'in')
            ->first()['quantity'] ?? 0;

        $totalOut = $this->InventoryTransactionModel
            ->selectSum('quantity')
            ->where('transaction_type', 'out')
            ->first()['quantity'] ?? 0;

        // Chart Barang Masuk / Keluar per bulan (6 bulan terakhir)
        $monthlyInOutRaw = $this->InventoryTransactionModel
            ->select("MONTH(transaction_date) AS month, 
              SUM(CASE WHEN transaction_type = 'in' THEN quantity ELSE 0 END) AS total_in, 
              SUM(CASE WHEN transaction_type = 'out' THEN quantity ELSE 0 END) AS total_out")
            ->where('YEAR(transaction_date)', date('Y'))
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->findAll();


        $inMap = [];
        $outMap = [];

        foreach ($monthlyInOutRaw as $row) {
            $inMap[(int)$row['month']] = (int)$row['total_in'];
            $outMap[(int)$row['month']] = (int)$row['total_out'];
        }

        $monthsIO = [];
        $inQuantities = [];
        $outQuantities = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthsIO[] = date('F', mktime(0, 0, 0, $i, 1));
            $inQuantities[] = $inMap[$i] ?? 0;
            $outQuantities[] = $outMap[$i] ?? 0;
        }

        $data = [
            'totalProducts' => $totalProducts,
            'totalCustomers' => $totalCustomers,
            'totalSellingUnits' => $totalSellingUnits,
            'totalSellingRupiah' => $totalSellingRupiah,
            'months' => json_encode($months),
            'unitTotals' => json_encode($unitTotals),
            'rupiahTotals' => json_encode($rupiahTotals),

            'totalIn' => $totalIn,
            'totalOut' => $totalOut,
            'monthsIO' => json_encode($monthsIO),
            'inQuantities' => json_encode($inQuantities),
            'outQuantities' => json_encode($outQuantities),
        ];

        return view('v_dashboard', $data);
    }
}
