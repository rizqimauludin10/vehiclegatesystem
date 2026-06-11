<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSatuanKerjaTable extends Migration
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
            'kode_satker' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_singkat' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_panjang' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
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
        $this->forge->addUniqueKey('kode_satker');
        $this->forge->createTable('satuan_kerja');
    }

    public function down()
    {
        $this->forge->dropTable('satuan_kerja');
    }
}