<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductImages extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'product_image_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'product_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'url' => [
                'type' => 'VARCHAR',
                'constraint' => 1024,
                'null' => false,
            ],
            'alt_text' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'sort_order' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'is_primary' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'size_bytes' => [
                'type' => 'INT',
                'null' => true,
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
        $this->forge->addKey('product_image_id', true);

        // Optional foreign key ke tabel products
        $this->forge->addForeignKey('product_id', 'products', 'product_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('product_images');
    }

    public function down()
    {
        $this->forge->dropTable('product_images');
    }
}
