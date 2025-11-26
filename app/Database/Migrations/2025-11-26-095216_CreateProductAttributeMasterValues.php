<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductAttributeMasterValues extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'attribute_master_id' => [
                'type'           => 'CHAR',
                'constraint'     => 36,
            ],
            'attribute_id' => [
                'type'           => 'CHAR',
                'constraint'     => 36,
            ],
            'value' => [
                'type'           => 'VARCHAR',
                'constraint'     => 100,
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
        $this->forge->addKey('attribute_master_id', true);

        // Foreign Key
        $this->forge->addForeignKey('attribute_id', 'product_attributes', 'attribute_id', 'CASCADE', 'CASCADE');

        // Create Table
        $this->forge->createTable('product_attribute_master_values');
    }

    public function down()
    {
        $this->forge->dropTable('product_attribute_master_values');
    }
}
