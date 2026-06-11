<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterKendaraanAddStnkFields extends Migration
{
    public function up()
    {
        // Tambah field STNK ke tabel kendaraan
        $this->forge->addColumn('kendaraan', [
            'nomor_rangka' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'warna',
            ],
            'nomor_mesin' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nomor_rangka',
            ],
            'nomor_bpkb' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nomor_mesin',
            ],
            'nomor_stnk' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nomor_bpkb',
            ],
            'tahun_kendaraan' => [
                'type'       => 'YEAR',
                'null'       => true,
                'after'      => 'nomor_stnk',
            ],
            'foto_stnk' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'tahun_kendaraan',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('kendaraan', [
            'nomor_rangka',
            'nomor_mesin',
            'nomor_bpkb',
            'nomor_stnk',
            'tahun_kendaraan',
            'foto_stnk',
        ]);
    }
}