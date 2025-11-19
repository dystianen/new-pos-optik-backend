<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLensPrescriptions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'prescription_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'order_item_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'left_sphere' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'left_cylinder' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'left_axis' => [
                'type' => 'INT',
                'null' => true,
            ],
            'right_sphere' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'right_cylinder' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'right_axis' => [
                'type' => 'INT',
                'null' => true,
            ],
            'pd' => [
                'type' => 'INT',
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
        $this->forge->addKey('prescription_id', true);

        // Optional foreign key ke order_items
        $this->forge->addForeignKey('order_item_id', 'cart_items', 'cart_item_id', 'SET NULL', 'CASCADE');

        // Create table
        $this->forge->createTable('lens_prescriptions');
    }

    public function down()
    {
        $this->forge->dropTable('lens_prescriptions');
    }
}
