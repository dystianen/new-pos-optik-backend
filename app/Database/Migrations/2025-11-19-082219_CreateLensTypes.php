<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLensTypes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'lens_type_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'lens_type_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'price_addon' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
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
        $this->forge->addKey('lens_type_id', true);

        // Create table
        $this->forge->createTable('lens_types');
    }

    public function down()
    {
        $this->forge->dropTable('lens_types');
    }
}
