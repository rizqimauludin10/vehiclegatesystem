<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKendaraanTable extends Migration
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
            'kendaraan_kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'personel_id' => [
                'type'     => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null'     => false,
            ],
            'plat_nomor' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['motor', 'mobil'],
                'null'       => false,
            ],
            'merk' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'model' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'warna' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'dibekukan'],
                'default'    => 'aktif',
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

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addUniqueKey('kendaraan_kode');
        $this->forge->addUniqueKey('plat_nomor');
        $this->forge->addForeignKey('personel_id', 'personel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kendaraan');
    }

    public function down()
    {
        $this->forge->dropTable('kendaraan', true);
    }
}