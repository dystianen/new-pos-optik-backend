<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'role_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'user_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'user_email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => TRUE
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'deleted_at'  => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);

        $this->forge->addKey('user_id', TRUE);
        $this->forge->createTable('users');

        // Add foreign key
        $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_roles FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE users DROP FOREIGN KEY fk_users_roles');
        $this->forge->dropTable('users');
    }
}
