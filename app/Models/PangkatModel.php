<?php

namespace App\Models;

use CodeIgniter\Model;

class PangkatModel extends Model
{
    protected $table            = 'pangkat';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'urutan', 'nama_pangkat', 'pangkat_singkat',
        'kategori', 'kelompok', 'golongan_militer',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ──────────────────────────────────────────────────────────
    // Get semua pangkat diurutkan by urutan ASC
    // Dikelompokkan by kelompok untuk dropdown grouped
    // ──────────────────────────────────────────────────────────
    public function getAllOrdered(): array
    {
        return $this->orderBy('urutan', 'ASC')->findAll();
    }

    // ──────────────────────────────────────────────────────────
    // Get untuk dropdown — grouped by kelompok
    // Return format: ['Perwira' => [...], 'Bintara' => [...]]
    // ──────────────────────────────────────────────────────────
    public function getGroupedForDropdown(): array
    {
        $rows   = $this->getAllOrdered();
        $result = [];

        foreach ($rows as $row) {
            $result[$row['kelompok']][] = $row;
        }

        return $result;
    }

    // ──────────────────────────────────────────────────────────
    // Get flat list untuk dropdown sederhana
    // ──────────────────────────────────────────────────────────
    public function getForDropdown(): array
    {
        return $this->select('id, nama_pangkat, pangkat_singkat, kelompok, urutan')
                    ->orderBy('urutan', 'ASC')
                    ->findAll();
    }

    // ──────────────────────────────────────────────────────────
    // Find by id — return nama_pangkat
    // ──────────────────────────────────────────────────────────
    public function getNama(int $id): string
    {
        $row = $this->select('nama_pangkat, pangkat_singkat')
                    ->find($id);
        return $row ? $row['pangkat_singkat'] . ' — ' . $row['nama_pangkat'] : '-';
    }
}