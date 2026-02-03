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

            'refund_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'comment'    => 'Jumlah yang di-refund, null = full refund',
            ],

            'reason' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'additional_note' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['requested', 'request_rejected', 'return_approved', 'return_shipped', 'return_received', 'return_rejected', 'approved', 'refunded', 'expired'],
                'default'    => 'requested',
            ],

            'refund_type' => [
                'type'       => 'ENUM',
                'constraint' => ['full', 'partial'],
                'null'       => true,
                'comment'    => 'Full refund atau partial (per-item)',
            ],

            'admin_note' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'evidence_url' => [
                'type' => 'VARCHAR',
                'constraint' => 1024,
                'null' => false,
            ],

            'processed_by' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'null'       => true,
                'comment'    => 'Admin ID yang memproses refund',
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Waktu saat refund approved/rejected',
            ],

            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Primary key
        $this->forge->addKey('order_refund_id', true);

        // Indexes
        $this->forge->addKey('order_id');
        $this->forge->addKey('user_refund_account_id');
        $this->forge->addKey('status');
        $this->forge->addKey(['created_at', 'status']);

        // Foreign Keys
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
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'processed_by',
            'users',
            'user_id',
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
