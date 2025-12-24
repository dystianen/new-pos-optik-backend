<?php

namespace App\Database\Seeds;

use App\Models\OrderStatusModel;
use CodeIgniter\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    public function run()
    {
        $orderStatuseModel = new OrderStatusModel();

        $data = [
            [
                'status_code' => 'pending',
                'status_name' => 'Pending Payment',
            ],
            [
                'status_code' => 'waiting-confirmation',
                'status_name' => 'Waiting Confirmation',
            ],
            [
                'status_code' => 'paid',
                'status_name' => 'Paid',
            ],
            [
                'status_code' => 'processing',
                'status_name' => 'Processing',
            ],
            [
                'status_code' => 'shipped',
                'status_name' => 'Shipped',
            ],
            [
                'status_code' => 'completed',
                'status_name' => 'Completed',
            ],
            [
                'status_code' => 'cancelled',
                'status_name' => 'Cancelled',
            ],
            [
                'status_code' => 'refunded',
                'status_name' => 'Refunded',
            ],
        ];

        // Insert multiple rows
        foreach ($data as $row) {
            $orderStatuseModel->insert($row);
        }
    }
}
