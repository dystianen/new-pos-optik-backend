<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoupons extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'coupon_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'discount_type' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
            ],
            'discount_value' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            'min_order_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'max_discount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'start_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'usage_limit' => [
                'type' => 'INT',
                'null' => true,
            ],
            'per_user_limit' => [
                'type' => 'INT',
                'null' => true,
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
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
        $this->forge->addKey('coupon_id', true);

        // Unique key
        $this->forge->addKey('code', false, true);

        // Create table
        $this->forge->createTable('coupons');
    }

    public function down()
    {
        $this->forge->dropTable('coupons');
    }
}
