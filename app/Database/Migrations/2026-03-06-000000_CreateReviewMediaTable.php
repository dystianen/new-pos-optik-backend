<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReviewMediaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'review_media_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'review_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'file_url' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 20, // e.g., 'image' or 'video'
                'null' => false,
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

        $this->forge->addKey('review_media_id', true);
        $this->forge->addForeignKey('review_id', 'reviews', 'review_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('review_media');
    }

    public function down()
    {
        $this->forge->dropTable('review_media');
    }
}
