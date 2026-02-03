<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderCancellationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'order_cancellation_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'order_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'reason' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'additional_note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['requested', 'approved', 'rejected'],
                'default'    => 'requested',
            ],
            'processed_by' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'null'       => true,
            ],
            'processed_at' => [
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

        // Primary Key
        $this->forge->addKey('order_cancellation_id', true);

        // Indexes
        $this->forge->addKey('order_id');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');

        // Foreign Keys
        $this->forge->addForeignKey('order_id', 'orders', 'order_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('processed_by', 'users', 'user_id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('order_cancellations');
    }

    public function down()
    {
        $this->forge->dropTable('order_cancellations');
    }
}
