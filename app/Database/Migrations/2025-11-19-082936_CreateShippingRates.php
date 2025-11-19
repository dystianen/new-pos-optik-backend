<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShippingRates extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'rate_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'shipping_method_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'destination' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => false,
            ],
            'cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
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
        $this->forge->addKey('rate_id', true);

        // Foreign key ke shipping_methods
        $this->forge->addForeignKey('shipping_method_id', 'shipping_methods', 'shipping_method_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('shipping_rates');
    }

    public function down()
    {
        $this->forge->dropTable('shipping_rates');
    }
}
