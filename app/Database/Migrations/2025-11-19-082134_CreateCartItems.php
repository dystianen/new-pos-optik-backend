<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCartItems extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'cart_item_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'cart_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'product_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'variant_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
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
        $this->forge->addKey('cart_item_id', true);

        // Foreign keys
        $this->forge->addForeignKey('cart_id', 'carts', 'cart_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'product_id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('variant_id', 'product_variants', 'variant_id', 'SET NULL', 'CASCADE');

        // Create table
        $this->forge->createTable('cart_items');
    }

    public function down()
    {
        $this->forge->dropTable('cart_items');
    }
}
