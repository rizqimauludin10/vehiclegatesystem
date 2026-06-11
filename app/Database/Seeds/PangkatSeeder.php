<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PangkatSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            // =====================
            // PERWIRA TINGGI
            // =====================
            [
                'urutan'           => 1,
                'nama_pangkat'     => 'Laksamana',
                'pangkat_singkat'  => 'Laksamana',
                'kategori'         => 'Militer',
                'kelompok'         => 'Perwira',
                'golongan_militer' => 'Perwira Tinggi',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 2,
                'nama_pangkat'     => 'Laksamana Madya',
                'pangkat_singkat'  => 'Laksda',
                'kategori'         => 'Militer',
                'kelompok'         => 'Perwira',
                'golongan_militer' => 'Perwira Tinggi',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 3,
                'nama_pangkat'     => 'Laksamana Muda',
                'pangkat_singkat'  => 'Laksma',
                'kategori'         => 'Militer',
                'kelompok'         => 'Perwira',
                'golongan_militer' => 'Perwira Tinggi',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 4,
                'nama_pangkat'     => 'Laksamana Pertama',
                'pangkat_singkat'  => 'Lakspa',
                'kategori'         => 'Militer',
                'kelompok'         => 'Perwira',
                'golongan_militer' => 'Perwira Tinggi',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],

            // =====================
            // PERWIRA MENENGAH
            // =====================
            [
                'urutan'           => 5,
                'nama_pangkat'     => 'Kolonel',
                'pangkat_singkat'  => 'Kol',
                'kategori'         => 'Militer',
                'kelompok'         => 'Perwira',
                'golongan_militer' => 'Perwira Menengah',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 6,
                'nama_pangkat'     => 'Letnan Kolonel',
                'pangkat_singkat'  => 'Letkol',
                'kategori'         => 'Militer',
                'kelompok'         => 'Perwira',
                'golongan_militer' => 'Perwira Menengah',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 7,
                'nama_pangkat'     => 'Mayor',
                'pangkat_singkat'  => 'Mayor',
                'kategori'         => 'Militer',
                'kelompok'         => 'Perwira',
                'golongan_militer' => 'Perwira Menengah',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],

            // =====================
            // PERWIRA PERTAMA
            // =====================
            [
                'urutan'           => 8,
                'nama_pangkat'     => 'Kapten',
                'pangkat_singkat'  => 'Kapten',
                'kategori'         => 'Militer',
                'kelompok'         => 'Perwira',
                'golongan_militer' => 'Perwira Pertama',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 9,
                'nama_pangkat'     => 'Letnan Satu',
                'pangkat_singkat'  => 'Lettu',
                'kategori'         => 'Militer',
                'kelompok'         => 'Perwira',
                'golongan_militer' => 'Perwira Pertama',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 10,
                'nama_pangkat'     => 'Letnan Dua',
                'pangkat_singkat'  => 'Letda',
                'kategori'         => 'Militer',
                'kelompok'         => 'Perwira',
                'golongan_militer' => 'Perwira Pertama',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],

            // =====================
            // BINTARA TINGGI
            // =====================
            [
                'urutan'           => 11,
                'nama_pangkat'     => 'Pembantu Letnan Satu',
                'pangkat_singkat'  => 'Peltu',
                'kategori'         => 'Militer',
                'kelompok'         => 'Bintara',
                'golongan_militer' => 'Bintara Tinggi',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 12,
                'nama_pangkat'     => 'Pembantu Letnan Dua',
                'pangkat_singkat'  => 'Pelda',
                'kategori'         => 'Militer',
                'kelompok'         => 'Bintara',
                'golongan_militer' => 'Bintara Tinggi',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],

            // =====================
            // BINTARA
            // =====================
            [
                'urutan'           => 13,
                'nama_pangkat'     => 'Sersan Mayor',
                'pangkat_singkat'  => 'Serma',
                'kategori'         => 'Militer',
                'kelompok'         => 'Bintara',
                'golongan_militer' => 'Bintara',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 14,
                'nama_pangkat'     => 'Sersan Kepala',
                'pangkat_singkat'  => 'Serka',
                'kategori'         => 'Militer',
                'kelompok'         => 'Bintara',
                'golongan_militer' => 'Bintara',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 15,
                'nama_pangkat'     => 'Sersan Satu',
                'pangkat_singkat'  => 'Sertu',
                'kategori'         => 'Militer',
                'kelompok'         => 'Bintara',
                'golongan_militer' => 'Bintara',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 16,
                'nama_pangkat'     => 'Sersan Dua',
                'pangkat_singkat'  => 'Serda',
                'kategori'         => 'Militer',
                'kelompok'         => 'Bintara',
                'golongan_militer' => 'Bintara',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],

            // =====================
            // KOPRAL
            // =====================
            [
                'urutan'           => 17,
                'nama_pangkat'     => 'Kopral Kepala',
                'pangkat_singkat'  => 'Kopka',
                'kategori'         => 'Militer',
                'kelompok'         => 'Tamtama',
                'golongan_militer' => 'Kopral',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 18,
                'nama_pangkat'     => 'Kopral Satu',
                'pangkat_singkat'  => 'Koptu',
                'kategori'         => 'Militer',
                'kelompok'         => 'Tamtama',
                'golongan_militer' => 'Kopral',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 19,
                'nama_pangkat'     => 'Kopral Dua',
                'pangkat_singkat'  => 'Kopda',
                'kategori'         => 'Militer',
                'kelompok'         => 'Tamtama',
                'golongan_militer' => 'Kopral',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],

            // =====================
            // TAMTAMA — TNI AL UMUM
            // =====================
            [
                'urutan'           => 20,
                'nama_pangkat'     => 'Kelasi Kepala',
                'pangkat_singkat'  => 'Klkp',
                'kategori'         => 'Militer',
                'kelompok'         => 'Tamtama',
                'golongan_militer' => 'Tamtama',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 21,
                'nama_pangkat'     => 'Kelasi Satu',
                'pangkat_singkat'  => 'Kltu',
                'kategori'         => 'Militer',
                'kelompok'         => 'Tamtama',
                'golongan_militer' => 'Tamtama',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 22,
                'nama_pangkat'     => 'Kelasi Dua',
                'pangkat_singkat'  => 'Klda',
                'kategori'         => 'Militer',
                'kelompok'         => 'Tamtama',
                'golongan_militer' => 'Tamtama',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],

            // =====================
            // TAMTAMA — MARINIR
            // =====================
            [
                'urutan'           => 23,
                'nama_pangkat'     => 'Prajurit Kepala',
                'pangkat_singkat'  => 'Praka',
                'kategori'         => 'Militer',
                'kelompok'         => 'Tamtama',
                'golongan_militer' => 'Tamtama',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 24,
                'nama_pangkat'     => 'Prajurit Satu',
                'pangkat_singkat'  => 'Pratu',
                'kategori'         => 'Militer',
                'kelompok'         => 'Tamtama',
                'golongan_militer' => 'Tamtama',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 25,
                'nama_pangkat'     => 'Prajurit Dua',
                'pangkat_singkat'  => 'Prada',
                'kategori'         => 'Militer',
                'kelompok'         => 'Tamtama',
                'golongan_militer' => 'Tamtama',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],

            // =====================
            // PNS GOLONGAN IV
            // =====================
            [
                'urutan'           => 26,
                'nama_pangkat'     => 'Pembina Utama',
                'pangkat_singkat'  => 'IV/e',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan IV',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 27,
                'nama_pangkat'     => 'Pembina Utama Madya',
                'pangkat_singkat'  => 'IV/d',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan IV',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 28,
                'nama_pangkat'     => 'Pembina Utama Muda',
                'pangkat_singkat'  => 'IV/c',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan IV',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 29,
                'nama_pangkat'     => 'Pembina Tingkat I',
                'pangkat_singkat'  => 'IV/b',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan IV',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 30,
                'nama_pangkat'     => 'Pembina',
                'pangkat_singkat'  => 'IV/a',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan IV',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],

            // =====================
            // PNS GOLONGAN III
            // =====================
            [
                'urutan'           => 31,
                'nama_pangkat'     => 'Penata Tingkat I',
                'pangkat_singkat'  => 'III/d',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan III',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 32,
                'nama_pangkat'     => 'Penata',
                'pangkat_singkat'  => 'III/c',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan III',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 33,
                'nama_pangkat'     => 'Penata Muda Tingkat I',
                'pangkat_singkat'  => 'III/b',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan III',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 34,
                'nama_pangkat'     => 'Penata Muda',
                'pangkat_singkat'  => 'III/a',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan III',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],

            // =====================
            // PNS GOLONGAN II
            // =====================
            [
                'urutan'           => 35,
                'nama_pangkat'     => 'Pengatur Tingkat I',
                'pangkat_singkat'  => 'II/d',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan II',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 36,
                'nama_pangkat'     => 'Pengatur',
                'pangkat_singkat'  => 'II/c',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan II',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 37,
                'nama_pangkat'     => 'Pengatur Muda Tingkat I',
                'pangkat_singkat'  => 'II/b',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan II',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 38,
                'nama_pangkat'     => 'Pengatur Muda',
                'pangkat_singkat'  => 'II/a',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan II',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],

            // =====================
            // PNS GOLONGAN I
            // =====================
            [
                'urutan'           => 39,
                'nama_pangkat'     => 'Juru Tingkat I',
                'pangkat_singkat'  => 'I/d',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan I',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 40,
                'nama_pangkat'     => 'Juru',
                'pangkat_singkat'  => 'I/c',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan I',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 41,
                'nama_pangkat'     => 'Juru Muda Tingkat I',
                'pangkat_singkat'  => 'I/b',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan I',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'urutan'           => 42,
                'nama_pangkat'     => 'Juru Muda',
                'pangkat_singkat'  => 'I/a',
                'kategori'         => 'PNS',
                'kelompok'         => 'PNS',
                'golongan_militer' => 'Golongan I',
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
        ];

        $this->db->table('pangkat')->insertBatch($data);
    }
}