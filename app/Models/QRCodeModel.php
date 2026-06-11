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
        'uuid', 'personel_id', 'kode_unik',
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
    // Find QR aktif milik personel
    // ──────────────────────────────────────────────────────────
    public function findAktifByPersonel(int $personelId): ?array
    {
        return $this->where('personel_id', $personelId)
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
    // Nonaktifkan semua QR milik personel
    // (dipanggil sebelum generate QR baru)
    // ──────────────────────────────────────────────────────────
    public function revokeByPersonel(int $personelId): void
    {
        $this->where('personel_id', $personelId)
             ->set(['status' => 'nonaktif'])
             ->update();
    }

    // ──────────────────────────────────────────────────────────
    // Get semua QR beserta data personel (JOIN)
    // ──────────────────────────────────────────────────────────
    // ──────────────────────────────────────────────────────────
// Get semua QR beserta data personel (JOIN)
// Sekarang JOIN ke pangkat & satuan_kerja juga
// ──────────────────────────────────────────────────────────
    public function getAllWithPersonel(): array
    {
        return $this->db->table('qrcode q')
                        ->select('
                            q.*,
                            p.nama        as personel_nama,
                            p.nrp,
                            p.foto        as personel_foto,
                            p.uuid        as personel_uuid,
                            pk.pangkat_singkat,
                            pk.nama_pangkat,
                            sk.nama_singkat as satker_singkat,
                            sk.kode_satker
                        ')
                        ->join('personel p',     'p.id = q.personel_id', 'left')
                        ->join('pangkat pk',      'pk.id = p.pangkat_id', 'left')
                        ->join('satuan_kerja sk', 'sk.id = p.satker_id',  'left')
                        ->orderBy('q.generated_at', 'DESC')
                        ->get()
                        ->getResultArray();
    }
    
    // ──────────────────────────────────────────────────────────
    // Get semua personel yang BELUM punya QR aktif
    // ──────────────────────────────────────────────────────────
    public function getPersonelTanpaQR(): array
    {
        return $this->db->table('personel p')
                        ->select('
                            p.*,
                            pk.pangkat_singkat,
                            pk.nama_pangkat,
                            sk.nama_singkat as satker_singkat
                        ')
                        ->join('pangkat pk',      'pk.id = p.pangkat_id', 'left')
                        ->join('satuan_kerja sk', 'sk.id = p.satker_id',  'left')
                        ->where('p.status', 'aktif')
                        ->whereNotIn('p.id', function ($builder) {
                            return $builder->select('personel_id')
                                        ->from('qrcode')
                                        ->where('status', 'aktif');
                        })
                        ->orderBy('pk.urutan', 'ASC')
                        ->orderBy('p.nama',    'ASC')
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
    // Statistik
    // ──────────────────────────────────────────────────────────
    public function countAktif(): int
    {
        return $this->where('status', 'aktif')->countAllResults();
    }
}