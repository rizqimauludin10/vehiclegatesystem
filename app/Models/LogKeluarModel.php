<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class LogKeluarModel extends Model
{
    protected $table            = 'log_keluar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'uuid', 'kendaraan_id', 'petugas_id',
        'kode_qr_used', 'timestamp_keluar',
        'shift', 'keterangan'
    ];

    // ──────────────────────────────────────────────────────────
    // BASE JOIN — kendaraan + personel + pangkat + satker
    // ──────────────────────────────────────────────────────────
    private function baseJoin()
    {
        return $this->db->table('log_keluar l')
                        ->select('
                            l.*,
                            k.plat_nomor,
                            k.jenis,
                            k.merk,
                            k.model,
                            k.kendaraan_kode,
                            p.nama          as personel_nama,
                            p.nrp,
                            p.id            as personel_id,
                            p.foto          as personel_foto,
                            pk.pangkat_singkat,
                            pk.nama_pangkat,
                            pk.kelompok     as pangkat_kelompok,
                            sk.nama_singkat as satker_singkat,
                            sk.kode_satker,
                            u.nama          as petugas_nama
                        ')
                        ->join('kendaraan k',     'k.id = l.kendaraan_id',  'left')
                        ->join('personel p',      'p.id = k.personel_id',   'left')
                        ->join('pangkat pk',       'pk.id = p.pangkat_id',   'left')
                        ->join('satuan_kerja sk',  'sk.id = p.satker_id',    'left')
                        ->join('users u',          'u.id = l.petugas_id',    'left');
    }

    // ──────────────────────────────────────────────────────────
    // Get log hari ini (count) — untuk dashboard
    // ──────────────────────────────────────────────────────────
    public function getLogHariIni(): int
    {
        return $this->where('DATE(timestamp_keluar)', date('Y-m-d'))
                    ->countAllResults();
    }

    public function getLogBulanIni(): int
    {
        return $this->where(
            "DATE_FORMAT(timestamp_keluar,'%Y-%m')",
            date('Y-m')
        )->countAllResults();
    }

    // ──────────────────────────────────────────────────────────
    // Get log hari ini by petugas (count) — untuk dashboard
    // ──────────────────────────────────────────────────────────
    public function getLogHariIniByPetugas(int $petugasId): int
    {
        return $this->where('petugas_id', $petugasId)
                    ->where('DATE(timestamp_keluar)', date('Y-m-d'))
                    ->countAllResults();
    }

    // ──────────────────────────────────────────────────────────
    // Get log terbaru (untuk dashboard admin)
    // ──────────────────────────────────────────────────────────
    public function getLogTerbaru(int $limit = 10): array
    {
        return $this->baseJoin()
                    ->orderBy('l.timestamp_keluar', 'DESC')
                    ->limit($limit)
                    ->get()
                    ->getResultArray();
    }

    // ──────────────────────────────────────────────────────────
    // Get log terbaru by petugas (untuk dashboard petugas)
    // ──────────────────────────────────────────────────────────
    public function getLogTerbaruByPetugas(int $petugasId, int $limit = 10): array
    {
        return $this->baseJoin()
                    ->where('l.petugas_id', $petugasId)
                    ->orderBy('l.timestamp_keluar', 'DESC')
                    ->limit($limit)
                    ->get()
                    ->getResultArray();
    }

        // ──────────────────────────────────────────────────────────
    // Get log terbaru by petugas DENGAN PAGINATION
    // ──────────────────────────────────────────────────────────
    public function getLogTerbaruByPetugasPaginated(int $petugasId, int $perPage = 10): array
    {
        return $this->select('
                        log_keluar.*,
                        k.plat_nomor,
                        k.jenis,
                        k.merk,
                        k.model,
                        k.kendaraan_kode,
                        p.nama          as personel_nama,
                        p.nrp,
                        p.id            as personel_id,
                        p.foto          as personel_foto,
                        pk.pangkat_singkat,
                        pk.nama_pangkat,
                        pk.kelompok     as pangkat_kelompok,
                        sk.nama_singkat as satker_singkat,
                        sk.kode_satker,
                        u.nama          as petugas_nama
                    ')
                    ->join('kendaraan k',     'k.id = log_keluar.kendaraan_id',  'left')
                    ->join('personel p',      'p.id = k.personel_id',            'left')
                    ->join('pangkat pk',      'pk.id = p.pangkat_id',            'left')
                    ->join('satuan_kerja sk', 'sk.id = p.satker_id',             'left')
                    ->join('users u',         'u.id = log_keluar.petugas_id',     'left')
                    ->where('log_keluar.petugas_id', $petugasId)
                    ->where('DATE(log_keluar.timestamp_keluar)', date('Y-m-d'))
                    ->orderBy('log_keluar.timestamp_keluar', 'DESC')
                    ->paginate($perPage, 'logs');
    }

        // ──────────────────────────────────────────────────────────
    // Get SEMUA log hari ini by petugas (untuk DataTables Petugas)
    // ──────────────────────────────────────────────────────────
    public function getLogHariIniByPetugasFull(int $petugasId): array
    {
        return $this->baseJoin()
                    ->where('l.petugas_id', $petugasId)
                    ->where('DATE(l.timestamp_keluar)', date('Y-m-d'))
                    ->orderBy('l.timestamp_keluar', 'DESC')
                    ->get()
                    ->getResultArray();
    }

    // ──────────────────────────────────────────────────────────
    // Get log dengan filter lengkap (untuk laporan)
    // ──────────────────────────────────────────────────────────
    public function getLogByFilter(array $filter = []): array
    {
        $builder = $this->baseJoin();

        if (!empty($filter['tanggal_dari'])) {
            $builder->where('DATE(l.timestamp_keluar) >=', $filter['tanggal_dari']);
        }

        if (!empty($filter['tanggal_sampai'])) {
            $builder->where('DATE(l.timestamp_keluar) <=', $filter['tanggal_sampai']);
        }

        if (!empty($filter['petugas_id'])) {
            $builder->where('l.petugas_id', $filter['petugas_id']);
        }

        if (!empty($filter['shift'])) {
            $builder->where('l.shift', $filter['shift']);
        }

        if (!empty($filter['satker_id'])) {
            $builder->where('p.satker_id', $filter['satker_id']);
        }

        if (!empty($filter['pangkat_id'])) {
            $builder->where('p.pangkat_id', $filter['pangkat_id']);
        }

        if (!empty($filter['kelompok'])) {
            $builder->where('pk.kelompok', $filter['kelompok']);
        }

        // Filter by jenis kendaraan
        if (!empty($filter['jenis'])) {
            $builder->where('k.jenis', $filter['jenis']);
        }

        return $builder->orderBy('l.timestamp_keluar', 'DESC')
                    ->get()
                    ->getResultArray();
    }

    // ──────────────────────────────────────────────────────────
    // Save log scan
    // Dipanggil dari Scan Controller setelah QR berhasil di-scan
    // ──────────────────────────────────────────────────────────
    public function saveLog(int $kendaraanId, string $kodeQr): bool
    {
        $now  = date('Y-m-d H:i:s');
        $hour = (int) date('H');

        if ($hour >= 1 && $hour < 10) {
            $shift = 'pagi';
        } elseif ($hour >= 10 && $hour < 18) {
            $shift = 'siang';
        } else {
            $shift = 'malam';
        }

        return $this->insert([
            'uuid'             => Uuid::uuid4()->toString(),
            'kendaraan_id'     => $kendaraanId,
            'petugas_id'       => session()->get('user_id'),
            'kode_qr_used'     => $kodeQr,
            'timestamp_keluar' => $now,
            'shift'            => $shift,
        ]);
    }
}