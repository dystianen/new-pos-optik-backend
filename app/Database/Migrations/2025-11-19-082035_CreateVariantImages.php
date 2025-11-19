<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVariantImages extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'variant_image_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'variant_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'product_image_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Primary key
        $this->forge->addKey('variant_image_id', true);

        // Foreign keys
        $this->forge->addForeignKey('variant_id', 'product_variants', 'variant_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_image_id', 'product_images', 'product_image_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('variant_images');
    }

    public function down()
    {
        $this->forge->dropTable('variant_images');
    }
}
