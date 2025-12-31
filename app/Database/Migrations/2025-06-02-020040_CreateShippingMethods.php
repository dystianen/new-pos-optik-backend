<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShippingMethods extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'shipping_method_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'provider' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'estimated_days' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
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
        $this->forge->addKey('shipping_method_id', true);

        // Create table
        $this->forge->createTable('shipping_methods');
    }

    public function down()
    {
        $this->forge->dropTable('shipping_methods');
    }
}
