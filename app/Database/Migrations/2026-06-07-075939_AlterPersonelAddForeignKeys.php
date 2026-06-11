<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPersonelAddForeignKeys extends Migration
{
    public function up()
    {
        // 1. Tambah kolom baru pangkat_id & satker_id
        $this->forge->addColumn('personel', [
            'pangkat_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'nrp',
            ],
            'satker_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'pangkat_id',
            ],
        ]);

        // 2. Tambah FK constraint
        $this->forge->addForeignKey('pangkat_id', 'pangkat', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('satker_id', 'satuan_kerja', 'id', 'SET NULL', 'SET NULL');

        // 3. Hapus kolom lama pangkat & satuan (VARCHAR)
        $this->forge->dropColumn('personel', 'pangkat');
        $this->forge->dropColumn('personel', 'satuan');
    }

    public function down()
    {
        // Rollback: hapus FK & kolom baru, kembalikan kolom lama
        $this->forge->dropForeignKey('personel', 'personel_pangkat_id_foreign');
        $this->forge->dropForeignKey('personel', 'personel_satker_id_foreign');
        $this->forge->dropColumn('personel', 'pangkat_id');
        $this->forge->dropColumn('personel', 'satker_id');

        $this->forge->addColumn('personel', [
            'pangkat' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nrp',
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'pangkat',
            ],
        ]);
    }
}