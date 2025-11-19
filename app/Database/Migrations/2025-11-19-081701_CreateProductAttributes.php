<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProductAttributes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'attribute_id' => [
                'type'           => 'CHAR',
                'constraint'     => 36,
            ],
            'attribute_name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
            ],
            'attribute_type' => [
                'type'           => 'VARCHAR',
                'constraint'     => 20,
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

        $this->forge->addKey('attribute_id', true);
        $this->forge->createTable('product_attributes');
    }

    public function down()
    {
        $this->forge->dropTable('product_attributes');
    }
}
