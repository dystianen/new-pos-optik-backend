<?php

namespace App\Database\Seeds;

use App\Models\ShippingRateModel;
use CodeIgniter\Database\Seeder;

class ShippingRateSeeder extends Seeder
{
    public function run()
    {
        $shippingRate = new ShippingRateModel();

        $shippingMethodId = '3e08ee99-750a-4437-a3a9-922437410f6e';

        $rates = [
            ['destination' => 'Jawa Timur', 'cost' => 15000],
            ['destination' => 'Jawa Tengah', 'cost' => 17000],
            ['destination' => 'Jawa Barat', 'cost' => 20000],
            ['destination' => 'Jakarta', 'cost' => 20000],
            ['destination' => 'Bali', 'cost' => 25000],
            ['destination' => 'Sumatra', 'cost' => 35000],
            ['destination' => 'Kalimantan', 'cost' => 40000],
            ['destination' => 'Sulawesi', 'cost' => 45000],
            ['destination' => 'Papua', 'cost' => 60000],
        ];

        $data = [];

        foreach ($rates as $rate) {
            $data[] = [
                'shipping_method_id' => $shippingMethodId,
                'destination' => $rate['destination'],
                'cost' => $rate['cost'],
            ];
        }

        foreach ($data as $row) {
            $shippingRate->insert($row);
        }
    }
}
