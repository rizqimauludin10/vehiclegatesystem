<?php

namespace App\Models;

use CodeIgniter\Model;

class PersonelModel extends Model
{
    protected $table            = 'personel';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'uuid', 'nrp', 'nama',
        'pangkat_id', 'satker_id',
        'jabatan', 'no_hp', 'foto', 'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [];

    protected $validationMessages = [
        'nrp'        => ['is_unique'  => 'NRP sudah terdaftar di sistem.'],
        'pangkat_id' => ['required'   => 'Pangkat harus dipilih.'],
        'satker_id'  => ['required'   => 'Satuan kerja harus dipilih.'],
    ];

    // ──────────────────────────────────────────────────────────
    // BASE QUERY — selalu JOIN pangkat & satker
    // ──────────────────────────────────────────────────────────
    private function baseQuery()
    {
        return $this->db->table('personel p')
                        ->select('
                            p.*,
                            pk.nama_pangkat,
                            pk.pangkat_singkat,
                            pk.kelompok,
                            pk.golongan_militer,
                            sk.kode_satker,
                            sk.nama_singkat as satker_singkat,
                            sk.nama_panjang as satker_panjang
                        ')
                        ->join('pangkat pk',      'pk.id = p.pangkat_id', 'left')
                        ->join('satuan_kerja sk',  'sk.id = p.satker_id',  'left');
    }

    // ──────────────────────────────────────────────────────────
    // Get semua personel + JOIN (untuk index)
    // ──────────────────────────────────────────────────────────
    public function getAllWithRelasi(): array
    {
        return $this->baseQuery()
                    ->orderBy('p.nama', 'ASC')
                    ->get()
                    ->getResultArray();
    }

    // ──────────────────────────────────────────────────────────
    // Get semua + jumlah kendaraan (untuk index dengan count)
    // ──────────────────────────────────────────────────────────
    public function getAllWithKendaraanCount(): array
    {
        return $this->db->table('personel p')
                        ->select('
                            p.*,
                            pk.nama_pangkat,
                            pk.pangkat_singkat,
                            pk.kelompok,
                            sk.kode_satker,
                            sk.nama_singkat as satker_singkat,
                            COUNT(k.id) as total_kendaraan
                        ')
                        ->join('pangkat pk',      'pk.id = p.pangkat_id',  'left')
                        ->join('satuan_kerja sk',  'sk.id = p.satker_id',   'left')
                        ->join('kendaraan k',      'k.personel_id = p.id',  'left')
                        ->groupBy('p.id')
                        ->orderBy('p.pangkat_id',    'ASC')
                        ->orderBy('p.updated_at',    'DESC')
                        ->get()
                        ->getResultArray();
    }

    // ──────────────────────────────────────────────────────────
    // Find by UUID + JOIN
    // ──────────────────────────────────────────────────────────
    public function findByUuid(string $uuid): ?array
    {
        $result = $this->baseQuery()
                       ->where('p.uuid', $uuid)
                       ->get()
                       ->getRowArray();

        return $result ?: null;
    }

    public function findDetailById(int $id): ?array
    {
        return $this->baseQuery()
                    ->where('p.id', $id)
                    ->get()
                    ->getRowArray();
    }

    // ──────────────────────────────────────────────────────────
    // Get personel + semua kendaraannya
    // ──────────────────────────────────────────────────────────
    public function getWithKendaraan(string $uuid): ?array
    {
        $personel = $this->findByUuid($uuid);
        if (!$personel) return null;

        $kendaraanModel        = new KendaraanModel();
        $personel['kendaraan'] = $kendaraanModel
            ->where('personel_id', $personel['id'])
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return $personel;
    }

    // ──────────────────────────────────────────────────────────
    // Get personel aktif (untuk dropdown di form kendaraan)
    // ──────────────────────────────────────────────────────────
    public function getAktif(): array
    {
        return $this->db->table('personel p')
                        ->select('p.id, p.uuid, p.nrp, p.nama, pk.pangkat_singkat')
                        ->join('pangkat pk', 'pk.id = p.pangkat_id', 'left')
                        ->where('p.status', 'aktif')
                        ->orderBy('pk.urutan', 'ASC')
                        ->orderBy('p.nama',    'ASC')
                        ->get()
                        ->getResultArray();
    }

    // ──────────────────────────────────────────────────────────
    // Get distinct satuan untuk filter laporan
    // ──────────────────────────────────────────────────────────
    public function getDistinctSatuan(): array
    {
        return $this->db->table('satuan_kerja')
                        ->select('id, kode_satker, nama_singkat')
                        ->orderBy('nama_singkat', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    // ──────────────────────────────────────────────────────────
    // Count personel aktif
    // ──────────────────────────────────────────────────────────
    public function countAktif(): int
    {
        return $this->where('status', 'aktif')->countAllResults();
    }

    // ──────────────────────────────────────────────────────────
    // Cek apakah punya QR aktif
    // ──────────────────────────────────────────────────────────
    public function hasQR(int $personelId): bool
    {
        $qrModel = new QRCodeModel();
        return (bool) $qrModel->where('personel_id', $personelId)
                               ->where('status', 'aktif')
                               ->first();
    }
}