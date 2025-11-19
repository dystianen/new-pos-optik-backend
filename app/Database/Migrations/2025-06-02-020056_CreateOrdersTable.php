<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'order_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'customer_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'status_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'shipping_method_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'shipping_costs' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true
            ],
            'coupon_discount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true
            ],
            'grand_total' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'deleted_at'  => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);

        $this->forge->addPrimaryKey('order_id');
        $this->forge->addForeignKey('customer_id', 'customers', 'customer_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
