<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterQrcodeAndLogKeluarToKendaraan extends Migration
{
    public function up()
    {
        // ── 1. Hapus semua data QR lama ──────────────────────
        $this->db->table('qrcode')->truncate();

        // ── 2. Hapus semua data log_keluar lama ──────────────
        $this->db->table('log_keluar')->truncate();

        // ── 3. Alter tabel qrcode ─────────────────────────────
        // Drop FK lama personel_id
        $this->forge->dropForeignKey('qrcode', 'qrcode_personel_id_foreign');

        // Drop kolom personel_id
        $this->forge->dropColumn('qrcode', 'personel_id');

        // Tambah kolom kendaraan_id
        $this->forge->addColumn('qrcode', [
            'kendaraan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'after'      => 'uuid',
            ],
        ]);

        // Tambah FK kendaraan_id
        $this->forge->addForeignKey('kendaraan_id', 'kendaraan', 'id', 'CASCADE', 'CASCADE');

        // ── 4. Alter tabel log_keluar ─────────────────────────
        // Drop FK lama personel_id
        $this->forge->dropForeignKey('log_keluar', 'log_keluar_personel_id_foreign');

        // Drop kolom personel_id
        $this->forge->dropColumn('log_keluar', 'personel_id');

        // Tambah kolom kendaraan_id
        $this->forge->addColumn('log_keluar', [
            'kendaraan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'after'      => 'uuid',
            ],
        ]);

        // Tambah FK kendaraan_id
        $this->forge->addForeignKey('kendaraan_id', 'kendaraan', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        // Rollback qrcode
        $this->forge->dropForeignKey('qrcode', 'qrcode_kendaraan_id_foreign');
        $this->forge->dropColumn('qrcode', 'kendaraan_id');
        $this->forge->addColumn('qrcode', [
            'personel_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'after'      => 'uuid',
            ],
        ]);

        // Rollback log_keluar
        $this->forge->dropForeignKey('log_keluar', 'log_keluar_kendaraan_id_foreign');
        $this->forge->dropColumn('log_keluar', 'kendaraan_id');
        $this->forge->addColumn('log_keluar', [
            'personel_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'after'      => 'uuid',
            ],
        ]);
    }
}