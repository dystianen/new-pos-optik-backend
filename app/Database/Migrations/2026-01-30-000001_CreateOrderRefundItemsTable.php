<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderRefundItemsTable extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'order_refund_item_id' => [
        'type'       => 'CHAR',
        'constraint' => 36,
      ],

      'order_refund_id' => [
        'type'       => 'CHAR',
        'constraint' => 36,
      ],

      'order_item_id' => [
        'type'       => 'CHAR',
        'constraint' => 36,
      ],

      'qty_refunded' => [
        'type'       => 'INT',
        'constraint' => 11,
        'comment'    => 'Jumlah item yang di-refund',
      ],

      'price_per_item' => [
        'type'       => 'DECIMAL',
        'constraint' => '15,2',
        'comment'    => 'Harga per item saat di-refund',
      ],

      'subtotal_refunded' => [
        'type'       => 'DECIMAL',
        'constraint' => '15,2',
        'comment'    => 'Subtotal refund untuk item ini (qty * price)',
      ],

      'created_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],

      'updated_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
    ]);

    // Primary key
    $this->forge->addKey('order_refund_item_id', true);

    // Indexes
    $this->forge->addKey('order_refund_id');
    $this->forge->addKey('order_item_id');

    // Foreign Keys
    $this->forge->addForeignKey(
      'order_refund_id',
      'order_refunds',
      'order_refund_id',
      'CASCADE',
      'CASCADE'
    );

    $this->forge->addForeignKey(
      'order_item_id',
      'order_items',
      'order_item_id',
      'CASCADE',
      'CASCADE'
    );

    $this->forge->createTable('order_refund_items');
  }

  public function down()
  {
    $this->forge->dropTable('order_refund_items');
  }
}
