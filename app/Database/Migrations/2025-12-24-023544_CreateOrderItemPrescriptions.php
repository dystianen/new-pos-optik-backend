<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderItemPrescriptions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'order_item_prescription_id' => ['type' => 'CHAR', 'constraint' => 36],
            'order_item_id' => ['type' => 'CHAR', 'constraint' => 36],

            'right_sph' => ['type' => 'DECIMAL', 'constraint' => '4,2', 'null' => true],
            'right_cyl' => ['type' => 'DECIMAL', 'constraint' => '4,2', 'null' => true],
            'right_axis' => ['type' => 'INT', 'null' => true],
            'right_add' => ['type' => 'DECIMAL', 'constraint' => '4,2', 'null' => true],

            'left_sph' => ['type' => 'DECIMAL', 'constraint' => '4,2', 'null' => true],
            'left_cyl' => ['type' => 'DECIMAL', 'constraint' => '4,2', 'null' => true],
            'left_axis' => ['type' => 'INT', 'null' => true],
            'left_add' => ['type' => 'DECIMAL', 'constraint' => '4,2', 'null' => true],

            'pd_single' => ['type' => 'DECIMAL', 'constraint' => '4,1', 'null' => true],
            'pd_left' => ['type' => 'DECIMAL', 'constraint' => '4,1', 'null' => true],
            'pd_right' => ['type' => 'DECIMAL', 'constraint' => '4,1', 'null' => true],

            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('order_item_prescription_id', true);
        $this->forge->addForeignKey(
            'order_item_id',
            'order_items',
            'order_item_id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('order_item_prescriptions');
    }

    public function down()
    {
        $this->forge->dropTable('order_item_prescriptions');
    }
}
