<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderStatuses extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'status_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'status_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'status_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
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
        $this->forge->addKey('status_id', true);

        // Create table
        $this->forge->createTable('order_statuses');
    }

    public function down()
    {
        $this->forge->dropTable('order_statuses');
    }
}
