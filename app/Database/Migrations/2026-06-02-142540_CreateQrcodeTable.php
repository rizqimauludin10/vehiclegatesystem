<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQrcodeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'personel_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'kode_unik' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
            ],
            'generated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'generated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'expired_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addUniqueKey('kode_unik');
        $this->forge->addForeignKey('personel_id', 'personel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('generated_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('qrcode');
    }

    public function down()
    {
        $this->forge->dropTable('qrcode', true);
    }
}