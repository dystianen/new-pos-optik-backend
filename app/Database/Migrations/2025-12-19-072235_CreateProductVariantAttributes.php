<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductVariantAttributes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pva_id' => [
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
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('pva_id', true);

        // unique agar 1 attribute hanya sekali per product
        $this->forge->addUniqueKey(['product_id', 'attribute_id']);

        // foreign key
        $this->forge->addForeignKey(
            'product_id',
            'products',
            'product_id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'attribute_id',
            'product_attributes',
            'attribute_id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('product_variant_attributes');
    }

    public function down()
    {
        $this->forge->dropTable('product_variant_attributes');
    }
}
