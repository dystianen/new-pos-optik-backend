<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        // Membuat table notifications
        $this->forge->addField([
            'notification_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
                'comment'    => 'Jenis notifikasi: low_stock, new_order, etc',
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => false,
                'comment' => 'Pesan notifikasi yang ditampilkan',
            ],
            'related_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'null'       => true,
                'comment'    => 'ID terkait (misal: order_id atau item_id)',
            ],
            'is_read' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => 'Status notifikasi dibaca (0 = unread, 1 = read)',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
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

        $this->forge->addKey('notification_id', true);
        $this->forge->createTable('notifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('notifications', true);
    }
}
