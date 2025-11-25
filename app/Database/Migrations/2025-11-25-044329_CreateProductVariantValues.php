<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductVariantValues extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pv_value_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'variant_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'pav_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
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
        $this->forge->addKey('pv_value_id', true);

        // Foreign keys
        $this->forge->addForeignKey('variant_id', 'product_variants', 'variant_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pav_id', 'product_attribute_values', 'pav_id', 'CASCADE', 'CASCADE');

        // Create table
        $this->forge->createTable('product_variant_values');
    }

    public function down()
    {
        $this->forge->dropTable('product_variant_values');
    }
}
