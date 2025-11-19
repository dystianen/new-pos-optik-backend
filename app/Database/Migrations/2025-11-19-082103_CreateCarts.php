<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCarts extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'cart_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'customer_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'abandoned', 'saved'],
                'default' => 'active',
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
        $this->forge->addKey('cart_id', true);
        $this->forge->addForeignKey('customer_id', 'customers', 'customer_id', 'SET NULL', 'CASCADE');

        // Create table
        $this->forge->createTable('carts');
    }

    public function down()
    {
        $this->forge->dropTable('carts');
    }
}
