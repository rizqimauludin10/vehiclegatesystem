<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePangkatTable extends Migration
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
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_pangkat' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'pangkat_singkat' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'kategori' => [
                'type'       => 'ENUM',
                'constraint' => ['Militer', 'PNS'],
            ],
            'kelompok' => [
                'type'       => 'ENUM',
                'constraint' => ['Perwira', 'Bintara', 'Tamtama', 'PNS'],
            ],
            'golongan_militer' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'default' => null,
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('urutan');
        $this->forge->addKey('kategori');
        $this->forge->addKey('kelompok');
        $this->forge->createTable('pangkat');
    }

    public function down()
    {
        $this->forge->dropTable('pangkat');
    }
}