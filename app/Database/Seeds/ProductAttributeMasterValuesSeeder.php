<?php

namespace App\Database\Seeds;

use App\Models\ProductAttributeMasterValueModel;
use CodeIgniter\Database\Seeder;

class ProductAttributeMasterValuesSeeder extends Seeder
{
  public function run()
  {
    $pamvModel = new ProductAttributeMasterValueModel();
    $data = [
      // Color
      ['attribute_id' => '331f5339-1774-4b06-9e19-bb88b603c5a2', 'value' => 'Brown'],
      ['attribute_id' => '331f5339-1774-4b06-9e19-bb88b603c5a2', 'value' => 'Orange'],
      ['attribute_id' => '331f5339-1774-4b06-9e19-bb88b603c5a2', 'value' => 'Red'],
      ['attribute_id' => '331f5339-1774-4b06-9e19-bb88b603c5a2', 'value' => 'Blue'],
      ['attribute_id' => '331f5339-1774-4b06-9e19-bb88b603c5a2', 'value' => 'Gold'],
      ['attribute_id' => '331f5339-1774-4b06-9e19-bb88b603c5a2', 'value' => 'White'],
      ['attribute_id' => '331f5339-1774-4b06-9e19-bb88b603c5a2', 'value' => 'Black'],

      // Lens Material
      ['attribute_id' => 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'value' => 'Trivex'],
      ['attribute_id' => 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'value' => 'Aspheric Lens'],
      ['attribute_id' => 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'value' => 'CR-39'],
      ['attribute_id' => 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'value' => 'High-Index Plastic (1.61 / 1.67 / 1.74)'],
      ['attribute_id' => 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'value' => 'Polycarbonate'],
      ['attribute_id' => 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'value' => 'Digital / Freeform Lens'],
      ['attribute_id' => 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'value' => 'Glass'],

      // Material
      ['attribute_id' => '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'value' => 'Stainless Steel'],
      ['attribute_id' => '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'value' => 'Polycarbonate'],
      ['attribute_id' => '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'value' => 'Titanium'],
      ['attribute_id' => '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'value' => 'Acetate'],
      ['attribute_id' => '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'value' => 'Carbon Fiber'],
      ['attribute_id' => '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'value' => 'Aluminum'],

      // Lens Type
      ['attribute_id' => 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'value' => 'Blue Light Blocking Lens'],
      ['attribute_id' => 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'value' => 'Photochromic Lens'],
      ['attribute_id' => 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'value' => 'Single Vision'],
      ['attribute_id' => 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'value' => 'Progressive Lens'],
      ['attribute_id' => 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'value' => 'Polycarbonate Lens'],

      // Frame Shape
      ['attribute_id' => 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'value' => 'Rectangle'],
      ['attribute_id' => 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'value' => 'Aviator'],
      ['attribute_id' => 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'value' => 'Cat Eye'],
      ['attribute_id' => 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'value' => 'Round'],
      ['attribute_id' => 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'value' => 'Square'],
    ];

    // Insert multiple rows
    foreach ($data as $row) {
      $pamvModel->insert($row);
    }
  }
}
