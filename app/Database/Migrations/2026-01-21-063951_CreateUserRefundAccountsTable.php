<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserRefundAccountsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_refund_account_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'customer_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
            ],
            'account_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'bank_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'account_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'is_default' => [
                'type'    => 'BOOLEAN',
                'default' => false,
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

        $this->forge->addKey('user_refund_account_id', true);
        $this->forge->addKey('customer_id');

        // Optional FK
        $this->forge->addForeignKey(
            'customer_id',
            'customers',
            'customer_id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('user_refund_accounts');
    }

    public function down()
    {
        $this->forge->dropTable('user_refund_accounts');
    }
}
