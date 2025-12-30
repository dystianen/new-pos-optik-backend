<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\ProductCategoryModel;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        $categoryModel = new ProductCategoryModel();

        $data = [
            [
                'category_name' => 'Kacamata',
                'category_description' => 'Berbagai macam kacamata pria dan wanita'
            ],
            [
                'category_name' => 'Softlens',
                'category_description' => 'Lensa kontak sehari-hari dan khusus'
            ],
            [
                'category_name' => 'Aksesoris',
                'category_description' => 'Tali kacamata, case, cleaner, dll'
            ]
        ];

        foreach ($data as $row) {
            $categoryModel->insert($row);
        }
    }
}
