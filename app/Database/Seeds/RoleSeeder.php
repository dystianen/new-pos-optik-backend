<?php

namespace App\Database\Seeds;

use App\Models\RoleModel;
use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roleModel = new RoleModel();

        $data = [
            [
                'role_name'        => 'admin',
                'role_description' => 'Administrator dengan akses penuh'
            ],
            [
                'role_name'        => 'optometrist',
                'role_description' => 'Dokter mata yang melakukan pemeriksaan'
            ],
            [
                'role_name'        => 'cashier',
                'role_description' => 'Kasir yang menangani transaksi penjualan'
            ],
            [
                'role_name'        => 'inventory',
                'role_description' => 'Staff gudang mengelola produk'
            ],
            [
                'role_name'        => 'customer',
                'role_description' => 'Customer/Buyer'
            ]
        ];

        foreach ($data as $row) {
            $roleModel->insert($row);
        }
    }
}
