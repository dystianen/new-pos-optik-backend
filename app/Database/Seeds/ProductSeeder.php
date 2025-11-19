<?php

namespace App\Database\Seeds;

use App\Models\ProductModel;
use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $productModel = new ProductModel();

        // Ambil semua category_id dari database
        $categories = $this->db->table('product_categories')->select('category_id')->get()->getResultArray();

        if (!empty($categories)) {
            // Buat mapping category berdasarkan urutan
            $frame     = $categories[0]['category_id'] ?? null;
            $lensa     = $categories[1]['category_id'] ?? null;
            $softlens  = $categories[2]['category_id'] ?? null;
            $aksesoris = $categories[3]['category_id'] ?? null;
        }

        $data = [
            [
                // product_id otomatis dibuat oleh beforeInsert
                'category_id'   => $softlens,
                'product_name'  => 'SoftLens Natural Look',
                'product_price' => 150000,
                'product_stock' => 50,
                'product_brand' => 'OptiClear',
                'product_image_url' => '/uploads/products/1749105020_0cd8fea486d980dd21a7.jpg',
                'model'         => 'NLC-123',
                'duration'      => 'Daily',
                'material'      => 'Hydrogel',
                'base_curve'    => '8.6',
                'diameter'      => '14.2',
                'power_range'   => '-1.00 to -5.00',
                'water_content' => '40',
                'uv_protection' => 'Yes',
                'color'         => 'Brown',
                'coating'       => 'Anti-dryness',
            ],
            [
                'category_id'   => $frame,
                'product_name'  => 'Frame Kacamata Classic',
                'product_price' => 350000,
                'product_stock' => 15,
                'product_brand' => 'RayBan',
                'product_image_url' => '/uploads/products/1749107117_8bf942cedb69bf1969fa.jpg',
                'model'         => 'Classic RB001',
                'duration'      => 'Daily',
                'material'      => 'Metal',
                'uv_protection' => 'No',
                'color'   => 'Black',
                'coating' => 'Anti-Rust',
            ],
            [
                'category_id'   => $frame,
                'product_name'  => 'Frame Kacamata Trendy',
                'product_price' => 420000,
                'product_stock' => 10,
                'product_brand' => 'Oakley',
                'product_image_url' => '/uploads/products/1749107080_b75632094defdc1a38d9.jpg',
                'model'         => 'TRNDY2025',
                'material'      => 'Plastic',
                'uv_protection' => 'No',
                'color'   => 'Clear',
                'coating' => 'Scratch Resistant',
            ],
            [
                'category_id'   => $lensa,
                'product_name'  => 'Lensa Anti Radiasi',
                'product_price' => 500000,
                'product_stock' => 25,
                'product_brand' => 'Essilor',
                'product_image_url' => '/uploads/products/1749106940_2cc036dad924f15c3105.jpg',
                'model'         => 'AR-Blue',
                'material'      => 'Polycarbonate',
                'power_range'   => '-2.00 to +2.00',
                'uv_protection' => 'Yes',
                'color'   => 'Clear',
                'coating' => 'Anti-Reflective',
            ],
            [
                'category_id'   => $lensa,
                'product_name'  => 'Lensa Progresif',
                'product_price' => 800000,
                'product_stock' => 12,
                'product_brand' => 'Zeiss',
                'product_image_url' => '/uploads/products/1749107278_afb9a0acacf5f81438b6.jpg',
                'model'         => 'SmartLife',
                'material'      => 'Glass',
                'power_range'   => '-1.50 to +3.50',
                'uv_protection' => 'Yes',
                'color'   => 'Clear',
                'coating' => 'Blue Cut',
            ],
        ];

        foreach ($data as $row) {
            $productModel->insert($row);
        }
    }
}
