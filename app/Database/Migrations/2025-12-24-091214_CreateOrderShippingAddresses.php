<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderShippingAddresses extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'osa_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'order_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'recipient_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'address' => [
                'type' => 'TEXT',
            ],
            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'province' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'postal_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
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
        $this->forge->addKey('osa_id', true);

        // Foreign Key ke orders
        $this->forge->addForeignKey(
            'order_id',
            'orders',
            'order_id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('order_shipping_addresses');
    }

    public function down()
    {
        $this->forge->dropTable('order_shipping_addresses');
    }
}
