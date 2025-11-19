<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderItemLens extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'order_item_lens_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'order_item_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'lens_type_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'price_addon' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
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
        $this->forge->addKey('order_item_lens_id', true);

        // Foreign keys
        $this->forge->addForeignKey('order_item_id', 'order_items', 'order_item_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('lens_type_id', 'lens_types', 'lens_type_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('order_item_lens');
    }

    public function down()
    {
        $this->forge->dropTable('order_item_lens');
    }
}
