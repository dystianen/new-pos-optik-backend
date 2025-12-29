<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\PaymentMethodModel;

class PaymentMethodSeeder extends Seeder
{
  public function run()
  {
    $model = new PaymentMethodModel();

    $data = [
      [
        'payment_method_id' => '7aeb3cfe-7ab5-4adf-a1ae-66f1d583ae56',
        'method_name' => 'BCA Transfer',
        'method_type' => 'bank_transfer',
        'is_active'   => 1,
      ],
      [
        'payment_method_id' => 'b24366c0-bada-479c-a678-0e9434375a8d',
        'method_name' => 'Mandiri Transfer',
        'method_type' => 'bank_transfer',
        'is_active'   => 1,
      ],
      [
        'payment_method_id' => '581c746b-0084-4ac3-9c2e-2c00ea5d6ab7',
        'method_name' => 'Cash',
        'method_type' => 'cash',
        'is_active'   => 1,
      ],
      [
        'payment_method_id' => 'e2914263-7e0f-4e3c-9425-0958c9581215',
        'method_name' => 'Manual Transfer',
        'method_type' => 'manual_transfer',
        'is_active'   => 1,
      ],
    ];

    $model->insertBatch($data);
  }
}
