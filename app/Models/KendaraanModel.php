<?php

namespace App\Models;

use CodeIgniter\Model;

class KendaraanModel extends Model
{
    protected $table            = 'kendaraan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'uuid', 'kendaraan_kode', 'personel_id',
        'plat_nomor', 'jenis', 'merk', 'model',
        'warna', 'nomor_rangka', 'nomor_mesin',
        'nomor_bpkb', 'nomor_stnk', 'tahun_kendaraan',
        'foto', 'foto_stnk', 'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [];

    protected $validationMessages = [
        'plat_nomor' => [
            'is_unique' => 'Plat nomor sudah terdaftar di sistem.',
        ],
    ];

    // ──────────────────────────────────────────────────────────
    // BASE JOIN — reusable query builder
    // Selalu JOIN personel + pangkat + satuan_kerja
    // ──────────────────────────────────────────────────────────
    private function baseJoin()
    {
        return $this->db->table('kendaraan k')
                        ->select('
                            k.*,
                            p.nama       as personel_nama,
                            p.nrp,
                            p.uuid       as personel_uuid,
                            pk.pangkat_singkat,
                            pk.nama_pangkat,
                            pk.kelompok  as pangkat_kelompok,
                            sk.nama_singkat as satker_singkat,
                            sk.kode_satker
                        ')
                        ->join('personel p',      'p.id = k.personel_id', 'left')
                        ->join('pangkat pk',       'pk.id = p.pangkat_id', 'left')
                        ->join('satuan_kerja sk',  'sk.id = p.satker_id',  'left');
    }

    // ──────────────────────────────────────────────────────────
    // Find by UUID
    // ──────────────────────────────────────────────────────────
    public function findByUuid(string $uuid): ?array
    {
        return $this->where('uuid', $uuid)->first();
    }

    // ──────────────────────────────────────────────────────────
    // Find by kendaraan_kode
    // ──────────────────────────────────────────────────────────
    public function findByKode(string $kode): ?array
    {
        return $this->where('kendaraan_kode', $kode)->first();
    }

    // ──────────────────────────────────────────────────────────
    // Get kendaraan by personel_id (tanpa JOIN — simpel)
    // ──────────────────────────────────────────────────────────
    public function getByPersonel(int $personelId): array
    {
        return $this->where('personel_id', $personelId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    // ──────────────────────────────────────────────────────────
    // Auto generate kendaraan_kode — format KND-0001
    // ──────────────────────────────────────────────────────────
    public function generateKode(): string
    {
        $last = $this->select('kendaraan_kode')
                     ->orderBy('id', 'DESC')
                     ->first();

        if (!$last) return 'KND-0001';

        $num = (int) substr($last['kendaraan_kode'], 4);
        return 'KND-' . str_pad($num + 1, 4, '0', STR_PAD_LEFT);
    }

    // ──────────────────────────────────────────────────────────
    // Get semua kendaraan + data personel lengkap (untuk index)
    // ──────────────────────────────────────────────────────────
    public function getAllWithPersonel(): array
    {
        return $this->baseJoin()
                    ->orderBy('k.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
    }

    // ──────────────────────────────────────────────────────────
    // Get detail kendaraan by UUID + data personel lengkap
    // ──────────────────────────────────────────────────────────
    public function getDetailByUuid(string $uuid): ?array
    {
        $result = $this->baseJoin()
                       ->where('k.uuid', $uuid)
                       ->get()
                       ->getRowArray();

        return $result ?: null;
    }

    // ──────────────────────────────────────────────────────────
    // Get kendaraan + foto galeri + foto STNK (detail lengkap)
    // ──────────────────────────────────────────────────────────
    public function getDetailLengkap(string $uuid): ?array
    {
        $kendaraan = $this->getDetailByUuid($uuid);
        if (!$kendaraan) return null;

        $fotoModel = new KendaraanFotoModel();
        $kendaraan['galeri']    = $fotoModel->getByKendaraan($kendaraan['id']);
        $kendaraan['thumbnail'] = $fotoModel->getThumbnail($kendaraan['id']);

        return $kendaraan;
    }

    // ──────────────────────────────────────────────────────────
    // Get kendaraan by personel + thumbnail foto
    // (dipakai di detail personel)
    // ──────────────────────────────────────────────────────────
    public function getByPersonelWithFoto(int $personelId): array
    {
        $kendaraan = $this->where('personel_id', $personelId)
                          ->orderBy('created_at', 'ASC')
                          ->findAll();

        $fotoModel = new KendaraanFotoModel();
        foreach ($kendaraan as &$k) {
            $k['galeri']    = $fotoModel->getByKendaraan($k['id']);
            $k['thumbnail'] = $fotoModel->getThumbnail($k['id']);
        }

        return $kendaraan;
    }

    // ──────────────────────────────────────────────────────────
    // Count kendaraan aktif
    // ──────────────────────────────────────────────────────────
    public function countAktif(): int
    {
        return $this->where('status', 'aktif')->countAllResults();
    }

    // ──────────────────────────────────────────────────────────
    // Count by jenis (motor / mobil)
    // ──────────────────────────────────────────────────────────
    public function countByJenis(): array
    {
        $result = $this->db->table('kendaraan')
                           ->select('jenis, COUNT(*) as total')
                           ->groupBy('jenis')
                           ->get()
                           ->getResultArray();

        $counts = ['motor' => 0, 'mobil' => 0];
        foreach ($result as $row) {
            $counts[$row['jenis']] = (int) $row['total'];
        }

        return $counts;
    }
}