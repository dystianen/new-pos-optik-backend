<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'role_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'role_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'unique' => TRUE
            ],
            'role_description' => [
                'type' => 'TEXT',
                'null' => TRUE
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
                'null' => true
            ],
        ]);

        $this->forge->addKey('role_id', TRUE);
        $this->forge->createTable('roles', false);
    }

    public function down()
    {
        $this->forge->dropTable('roles', true);
    }
}
