<?php

namespace App\Models;

use CodeIgniter\Model;

class SatkerModel extends Model
{
    protected $table            = 'satuan_kerja';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'kode_satker', 'nama_singkat', 'nama_panjang',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [];

    protected $validationMessages = [
        'kode_satker' => [
            'is_unique' => 'Kode satker sudah digunakan.',
        ],
    ];

    // ──────────────────────────────────────────────────────────
    // Get semua satker untuk dropdown
    // ──────────────────────────────────────────────────────────
    public function getForDropdown(): array
    {
        return $this->select('id, kode_satker, nama_singkat, nama_panjang')
                    ->orderBy('nama_singkat', 'ASC')
                    ->findAll();
    }

    // ──────────────────────────────────────────────────────────
    // Find by kode_satker
    // ──────────────────────────────────────────────────────────
    public function findByKode(string $kode): ?array
    {
        return $this->where('kode_satker', $kode)->first();
    }

    // ──────────────────────────────────────────────────────────
    // Get nama untuk display
    // ──────────────────────────────────────────────────────────
    public function getNama(int $id): string
    {
        $row = $this->select('kode_satker, nama_singkat')
                    ->find($id);
        return $row ? $row['kode_satker'] . ' — ' . $row['nama_singkat'] : '-';
    }
}