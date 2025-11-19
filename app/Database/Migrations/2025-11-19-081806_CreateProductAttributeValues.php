<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProductAttributeValues extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pav_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'product_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'attribute_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'value' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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

        $this->forge->addKey('pav_id', true);
        $this->forge->createTable('product_attribute_values');
    }

    public function down()
    {
        $this->forge->dropTable('product_attribute_values');
    }
}
