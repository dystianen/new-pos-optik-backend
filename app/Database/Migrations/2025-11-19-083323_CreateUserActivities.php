<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserActivities extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_activity_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'customer_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'product_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'activity_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'activity_details' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('user_activity_id', true);

        // Foreign keys
        $this->forge->addForeignKey('customer_id', 'customers', 'customer_id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'product_id', 'SET NULL', 'CASCADE');

        // Create table
        $this->forge->createTable('user_activities');
    }

    public function down()
    {
        $this->forge->dropTable('user_activities');
    }
}
