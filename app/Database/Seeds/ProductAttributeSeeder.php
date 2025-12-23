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
                'attribute_id'   => '331f5339-1774-4b06-9e19-bb88b603c5a2',
                'attribute_name' => 'Color',
                'attribute_type' => 'dropdown',
            ],
            [
                'attribute_id'   => 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514',
                'attribute_name' => 'Lens Type',
                'attribute_type' => 'dropdown',
            ],
            [
                'attribute_id'   => 'fab9a0b6-5633-43a5-b78a-cd6523e4c406',
                'attribute_name' => 'Lens Material',
                'attribute_type' => 'dropdown',
            ],
            [
                'attribute_id'   => 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6',
                'attribute_name' => 'Frame Shape',
                'attribute_type' => 'dropdown',
            ],
            [
                'attribute_id'   => 'dbc661e8-ad9c-4dfe-8fe5-40707210c3f3',
                'attribute_name' => 'Frame Size (Width)',
                'attribute_type' => 'text',
            ],
            [
                'attribute_id'   => '77e03517-d3b5-4a73-9066-0b7c21338c0a',
                'attribute_name' => 'Frame Material',
                'attribute_type' => 'dropdown',
            ],
            [
                'attribute_id'   => '00cbc3c6-f421-4714-b509-e9770e3182d1',
                'attribute_name' => 'Temple Length',
                'attribute_type' => 'text',
            ],
            [
                'attribute_id'   => '17d811ef-8002-4db7-8cbd-6f012ad12028',
                'attribute_name' => 'Bridge Size',
                'attribute_type' => 'text',
            ],
        ];

        $productAttributeModel->insertBatch($data);
    }
}
