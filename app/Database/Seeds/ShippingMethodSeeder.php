<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'shipping_method_id' => '3e08ee99-750a-4437-a3a9-922437410f6e',
            'name' => 'Reguler',
            'provider' => 'Internal Courier',
            'estimated_days' => '3-5 hari',
            'is_active' => true,
        ];

        $this->db->table('shipping_methods')->insert($data);
    }
}
