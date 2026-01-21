<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderRefundsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'order_refund_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'order_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'user_refund_account_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'null'       => true,
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

        $this->forge->addKey('order_refund_id', true);
        $this->forge->addKey('order_id');

        $this->forge->addForeignKey(
            'order_id',
            'orders',
            'order_id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'user_refund_account_id',
            'user_refund_accounts',
            'user_refund_account_id',
            'SET NULL',
            'CASCADE'
        );

        $this->forge->createTable('order_refunds');
    }

    public function down()
    {
        $this->forge->dropTable('order_refunds');
    }
}
