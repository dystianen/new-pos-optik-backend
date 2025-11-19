<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePayments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'payment_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'order_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'payment_method_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'transaction_code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'paid_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addKey('payment_id', true);

        // Foreign keys
        $this->forge->addForeignKey('order_id', 'orders', 'order_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('payment_method_id', 'payment_methods', 'payment_method_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('payments');
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}
