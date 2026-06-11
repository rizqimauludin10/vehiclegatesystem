<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SatuanKerjaSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            // KOMANDO & STAF
            [
                'kode_satker'  => 'KOARMADA-II',
                'nama_singkat' => 'Koarmada II',
                'nama_panjang' => 'Komando Armada II',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'SOPS',
                'nama_singkat' => 'Sops',
                'nama_panjang' => 'Staf Operasi',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'SPERS',
                'nama_singkat' => 'Spers',
                'nama_panjang' => 'Staf Personel',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'SLOG',
                'nama_singkat' => 'Slog',
                'nama_panjang' => 'Staf Logistik',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'SREN',
                'nama_singkat' => 'Sren',
                'nama_panjang' => 'Staf Perencanaan dan Anggaran',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'SPOTEK',
                'nama_singkat' => 'Spotek',
                'nama_panjang' => 'Staf Potensi Maritim',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'SKOMLEK',
                'nama_singkat' => 'Skomlek',
                'nama_panjang' => 'Staf Komunikasi dan Elektronika',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // DINAS
            [
                'kode_satker'  => 'DISINFOLAHTA',
                'nama_singkat' => 'Disinfolahta',
                'nama_panjang' => 'Dinas Informasi dan Pengolahan Data',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'DISKES',
                'nama_singkat' => 'Diskes',
                'nama_panjang' => 'Dinas Kesehatan',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'DISHUKUM',
                'nama_singkat' => 'Dishukum',
                'nama_panjang' => 'Dinas Hukum',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'DISKOMLEK',
                'nama_singkat' => 'Diskomlek',
                'nama_panjang' => 'Dinas Komunikasi dan Elektronika',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'DISLOG',
                'nama_singkat' => 'Dislog',
                'nama_panjang' => 'Dinas Logistik',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'DISPOTMAR',
                'nama_singkat' => 'Dispotmar',
                'nama_panjang' => 'Dinas Potensi Maritim',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // SATUAN
            [
                'kode_satker'  => 'SATESKORT',
                'nama_singkat' => 'Sateskort',
                'nama_panjang' => 'Satuan Kapal Eskorta',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'SATSEL',
                'nama_singkat' => 'Satsel',
                'nama_panjang' => 'Satuan Kapal Selam',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'SATRAT',
                'nama_singkat' => 'Satrat',
                'nama_panjang' => 'Satuan Kapal Ranjau',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'SATBAN',
                'nama_singkat' => 'Satban',
                'nama_panjang' => 'Satuan Kapal Bantu',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'SATPAT',
                'nama_singkat' => 'Satpat',
                'nama_panjang' => 'Satuan Kapal Patroli',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'SATAMLAT',
                'nama_singkat' => 'Satamlat',
                'nama_panjang' => 'Satuan Amfibi dan Lintas Laut',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // LEMBAGA
            [
                'kode_satker'  => 'RUMKITAL',
                'nama_singkat' => 'Rumkital',
                'nama_panjang' => 'Rumah Sakit TNI Angkatan Laut Dr. Ramelan',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'kode_satker'  => 'LANTAMAL-V',
                'nama_singkat' => 'Lantamal V',
                'nama_panjang' => 'Pangkalan Utama TNI AL V',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        $this->db->table('satuan_kerja')->insertBatch($data);
    }
}