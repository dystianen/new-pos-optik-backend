<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentMethods extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'payment_method_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'method_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'method_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
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
        $this->forge->addKey('payment_method_id', true);

        // Create table
        $this->forge->createTable('payment_methods');
    }

    public function down()
    {
        $this->forge->dropTable('payment_methods');
    }
}
