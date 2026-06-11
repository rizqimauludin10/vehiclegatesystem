<?php

namespace App\Models;

use CodeIgniter\Model;

class KendaraanFotoModel extends Model
{
    protected $table            = 'kendaraan_foto';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'uuid', 'kendaraan_id', 'nama_file', 'urutan', 'created_at'
    ];

    // ──────────────────────────────────────────────────────────
    // Get semua foto milik kendaraan, urut by urutan
    // ──────────────────────────────────────────────────────────
    public function getByKendaraan(int $kendaraanId): array
    {
        return $this->where('kendaraan_id', $kendaraanId)
                    ->orderBy('urutan', 'ASC')
                    ->findAll();
    }

    // ──────────────────────────────────────────────────────────
    // Get foto pertama (thumbnail)
    // ──────────────────────────────────────────────────────────
    public function getThumbnail(int $kendaraanId): ?array
    {
        return $this->where('kendaraan_id', $kendaraanId)
                    ->orderBy('urutan', 'ASC')
                    ->first();
    }

    // ──────────────────────────────────────────────────────────
    // Hapus semua foto milik kendaraan (+ file fisik)
    // ──────────────────────────────────────────────────────────
    public function deleteByKendaraan(int $kendaraanId): void
    {
        $fotos = $this->getByKendaraan($kendaraanId);
        foreach ($fotos as $foto) {
            $path = FCPATH . 'uploads/kendaraan/' . $foto['nama_file'];
            if (file_exists($path)) unlink($path);
        }
        $this->where('kendaraan_id', $kendaraanId)->delete();
    }

    // ──────────────────────────────────────────────────────────
    // Hapus 1 foto by uuid (+ file fisik)
    // ──────────────────────────────────────────────────────────
    public function deleteByUuid(string $uuid): bool
    {
        $foto = $this->where('uuid', $uuid)->first();
        if (!$foto) return false;

        $path = FCPATH . 'uploads/kendaraan/' . $foto['nama_file'];
        if (file_exists($path)) unlink($path);

        $this->where('uuid', $uuid)->delete();
        return true;
    }

    // ──────────────────────────────────────────────────────────
    // Count foto milik kendaraan
    // ──────────────────────────────────────────────────────────
    public function countByKendaraan(int $kendaraanId): int
    {
        return $this->where('kendaraan_id', $kendaraanId)->countAllResults();
    }
}