<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderCoupons extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'order_coupon_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'order_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'coupon_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'discount_amount' => [
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
        $this->forge->addKey('id', true);

        // Foreign keys
        $this->forge->addForeignKey('order_id', 'orders', 'order_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('coupon_id', 'coupons', 'coupon_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('order_coupons');
    }

    public function down()
    {
        $this->forge->dropTable('order_coupons');
    }
}
