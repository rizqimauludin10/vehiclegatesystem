<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKendaraanFotoTable extends Migration
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
            'kendaraan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'nama_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'urutan' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addKey('kendaraan_id');
        $this->forge->addForeignKey('kendaraan_id', 'kendaraan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kendaraan_foto');
    }

    public function down()
    {
        $this->forge->dropTable('kendaraan_foto');
    }
}