<?php

namespace App\Models;

use CodeIgniter\Model;

class QRCodeModel extends Model
{
    protected $table            = 'qrcode';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'uuid', 'kendaraan_id', 'kode_unik',
        'status', 'generated_by', 'generated_at', 'expired_at'
    ];

    // ──────────────────────────────────────────────────────────
    // Find by UUID
    // ──────────────────────────────────────────────────────────
    public function findByUuid(string $uuid): ?array
    {
        return $this->where('uuid', $uuid)->first();
    }

    // ──────────────────────────────────────────────────────────
    // Find QR aktif milik kendaraan
    // ──────────────────────────────────────────────────────────
    public function findAktifByKendaraan(int $kendaraanId): ?array
    {
        return $this->where('kendaraan_id', $kendaraanId)
                    ->where('status', 'aktif')
                    ->first();
    }

    // ──────────────────────────────────────────────────────────
    // Find by kode_unik (dipakai saat scan)
    // ──────────────────────────────────────────────────────────
    public function findByKodeUnik(string $kode): ?array
    {
        return $this->where('kode_unik', $kode)
                    ->where('status', 'aktif')
                    ->first();
    }

    // ──────────────────────────────────────────────────────────
    // Nonaktifkan semua QR milik kendaraan
    // (dipanggil sebelum generate QR baru)
    // ──────────────────────────────────────────────────────────
    public function revokeByKendaraan(int $kendaraanId): void
    {
        $this->where('kendaraan_id', $kendaraanId)
            ->set(['status' => 'nonaktif'])
            ->update();
    }

    // ──────────────────────────────────────────────────────────
    // Get semua QR beserta data kendaraan + personel (JOIN)
    // ──────────────────────────────────────────────────────────
    public function getAllWithDetail(): array
    {
        return $this->db->table('qrcode q')
                        ->select('
                            q.*,
                            k.plat_nomor,
                            k.jenis,
                            k.merk,
                            k.model,
                            k.warna,
                            k.kendaraan_kode,
                            k.status        as kendaraan_status,
                            k.uuid          as kendaraan_uuid,
                            p.nama          as personel_nama,
                            p.nrp,
                            p.foto          as personel_foto,
                            p.uuid          as personel_uuid,
                            pk.pangkat_singkat,
                            sk.nama_singkat as satker_singkat
                        ')
                        ->join('kendaraan k',     'k.id = q.kendaraan_id',  'left')
                        ->join('personel p',      'p.id = k.personel_id',   'left')
                        ->join('pangkat pk',       'pk.id = p.pangkat_id',   'left')
                        ->join('satuan_kerja sk',  'sk.id = p.satker_id',    'left')
                        ->orderBy('q.generated_at', 'DESC')
                        ->get()
                        ->getResultArray();
    }

    // ──────────────────────────────────────────────────────────
    // Get semua kendaraan yang BELUM punya QR aktif
    // ──────────────────────────────────────────────────────────
    public function getKendaraanTanpaQR(): array
    {
        return $this->db->table('kendaraan k')
                        ->select('
                            k.*,
                            p.nama          as personel_nama,
                            p.nrp,
                            p.uuid          as personel_uuid,
                            pk.pangkat_singkat,
                            sk.nama_singkat as satker_singkat
                        ')
                        ->join('personel p',      'p.id = k.personel_id',  'left')
                        ->join('pangkat pk',       'pk.id = p.pangkat_id',  'left')
                        ->join('satuan_kerja sk',  'sk.id = p.satker_id',   'left')
                        ->where('k.status', 'aktif')
                        ->whereNotIn('k.id', function ($builder) {
                            return $builder->select('kendaraan_id')
                                        ->from('qrcode')
                                        ->where('status', 'aktif');
                        })
                        ->orderBy('p.nama', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    // ──────────────────────────────────────────────────────────
    // Generate kode unik yang benar-benar unik di DB
    // ──────────────────────────────────────────────────────────
    public function generateKodeUnik(): string
    {
        do {
            $kode = strtoupper(bin2hex(random_bytes(16)));
        } while ($this->where('kode_unik', $kode)->first());

        return $kode;
    }

    // ──────────────────────────────────────────────────────────
    // Count QR aktif
    // ──────────────────────────────────────────────────────────
    public function countAktif(): int
    {
        return $this->where('status', 'aktif')->countAllResults();
    }

    public function deleteByKendaraan(int $kendaraanId): void
{
    $this->where('kendaraan_id', $kendaraanId)->delete();
}
}