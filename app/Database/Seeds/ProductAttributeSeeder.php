<?php

namespace App\Database\Seeds;

use App\Models\ProductAttributeModel;
use CodeIgniter\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    public function run()
    {
        $productAttributeModel = new ProductAttributeModel();
        $data = [
            [
                'attribute_name' => 'Frame Material',
                'attribute_type' => 'select',
            ],
            [
                'attribute_name' => 'Frame Shape',
                'attribute_type' => 'select',
            ],
            [
                'attribute_name' => 'Lens Type',
                'attribute_type' => 'select',
            ],
            [
                'attribute_name' => 'Lens Material',
                'attribute_type' => 'select',
            ],
            [
                'attribute_name' => 'Frame Size (Width)',
                'attribute_type' => 'text',
            ],
            [
                'attribute_name' => 'Bridge Size',
                'attribute_type' => 'text',
            ],
            [
                'attribute_name' => 'Temple Length',
                'attribute_type' => 'text',
            ],
            [
                'attribute_name' => 'Color',
                'attribute_type' => 'select',
            ],
        ];

        // Insert multiple rows
        foreach ($data as $row) {
            $productAttributeModel->insert($row);
        }
    }
}
