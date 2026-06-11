<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogKeluarTable extends Migration
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
            'petugas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'kode_qr_used' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => false,
            ],
            'timestamp_keluar' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'shift' => [
                'type'       => 'ENUM',
                'constraint' => ['pagi', 'siang', 'malam'],
                'null'       => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addForeignKey('personel_id', 'personel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('petugas_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('log_keluar');
    }

    public function down()
    {
        $this->forge->dropTable('log_keluar', true);
    }
}