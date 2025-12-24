<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomerShippingAddresses extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'csa_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'customer_id' => [
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
        $this->forge->addKey('csa_id', true);

        // Foreign Key ke orders
        $this->forge->addForeignKey(
            'customer_id',
            'customers',
            'customer_id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('customer_shipping_addresses');
    }

    public function down()
    {
        $this->forge->dropTable('customer_shipping_addresses');
    }
}
