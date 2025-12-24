<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCartItemPrescriptions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'prescription_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'cart_item_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],

            // RIGHT EYE (OD)
            'right_sph' => ['type' => 'DECIMAL', 'constraint' => '4,2', 'null' => true],
            'right_cyl' => ['type' => 'DECIMAL', 'constraint' => '4,2', 'null' => true],
            'right_axis' => ['type' => 'INT', 'constraint' => 3, 'null' => true],
            'right_add' => ['type' => 'DECIMAL', 'constraint' => '4,2', 'null' => true],

            // LEFT EYE (OS)
            'left_sph' => ['type' => 'DECIMAL', 'constraint' => '4,2', 'null' => true],
            'left_cyl' => ['type' => 'DECIMAL', 'constraint' => '4,2', 'null' => true],
            'left_axis' => ['type' => 'INT', 'constraint' => 3, 'null' => true],
            'left_add' => ['type' => 'DECIMAL', 'constraint' => '4,2', 'null' => true],

            // PD
            'pd_single' => ['type' => 'DECIMAL', 'constraint' => '4,1', 'null' => true],
            'pd_left'   => ['type' => 'DECIMAL', 'constraint' => '4,1', 'null' => true],
            'pd_right'  => ['type' => 'DECIMAL', 'constraint' => '4,1', 'null' => true],

            // Metadata
            'lens_type' => [
                'type' => 'ENUM',
                'constraint' => ['single_vision', 'bifocal', 'progressive'],
                'default' => 'single_vision',
            ],
            'upload_prescription' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],

            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('prescription_id', true);
        $this->forge->addForeignKey(
            'cart_item_id',
            'cart_items',
            'cart_item_id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('cart_item_prescriptions');
    }

    public function down()
    {
        $this->forge->dropTable('cart_item_prescriptions');
    }
}
