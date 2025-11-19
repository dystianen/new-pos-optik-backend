<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductDiscounts extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'product_discount_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'product_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'discount_type' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
            ],
            'discount_value' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            'start_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATETIME',
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
        $this->forge->addKey('product_discount_id', true);

        // Foreign key
        $this->forge->addForeignKey('product_id', 'products', 'product_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('product_discounts');
    }

    public function down()
    {
        $this->forge->dropTable('product_discounts');
    }
}
