<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWishlists extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'wishlist_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'customer_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'product_id' => [
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
        $this->forge->addKey('wishlist_id', true);

        // Foreign keys
        $this->forge->addForeignKey('customer_id', 'customers', 'customer_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'product_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('wishlists');
    }

    public function down()
    {
        $this->forge->dropTable('wishlists');
    }
}
