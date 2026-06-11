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
        'uuid', 'personel_id', 'petugas_id',
        'kode_qr_used', 'timestamp_keluar',
        'shift', 'keterangan'
    ];

    // ── Helper Methods ──────────────────────────────────────

    public function getLogHariIni(): int
    {
        return $this->where('DATE(timestamp_keluar)', date('Y-m-d'))
                    ->countAllResults();
    }

    public function getLogHariIniByPetugas(int $petugasId): int
    {
        return $this->where('petugas_id', $petugasId)
                    ->where('DATE(timestamp_keluar)', date('Y-m-d'))
                    ->countAllResults();
    }

    public function getLogTerbaru(int $limit = 10): array
    {
        return $this->db->table('log_keluar l')
            ->select('
                l.*,
                p.nama as personel_nama,
                p.nrp,
                p.foto as personel_foto,

                pk.nama_pangkat,
                pk.pangkat_singkat,

                sk.kode_satker,
                sk.nama_singkat as satker_singkat,

                u.nama as petugas_nama
            ')
            ->join('personel p', 'p.id = l.personel_id')
            ->join('pangkat pk', 'pk.id = p.pangkat_id', 'left')
            ->join('satuan_kerja sk', 'sk.id = p.satker_id', 'left')
            ->join('users u', 'u.id = l.petugas_id')
            ->orderBy('l.timestamp_keluar', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function getLogTerbaruByPetugas(int $petugasId, int $limit = 10): array
    {
        return $this->db->table('log_keluar l')
            ->select('
                l.*,
                p.nama as personel_nama,
                p.nrp,
                p.foto as personel_foto,

                pk.nama_pangkat,
                pk.pangkat_singkat,

                sk.kode_satker,
                sk.nama_singkat as satker_singkat,

                u.nama as petugas_nama
            ')
            ->join('personel p', 'p.id = l.personel_id')
            ->join('pangkat pk', 'pk.id = p.pangkat_id', 'left')
            ->join('satuan_kerja sk', 'sk.id = p.satker_id', 'left')
            ->join('users u', 'u.id = l.petugas_id')
            ->where('l.petugas_id', $petugasId)
            ->orderBy('l.timestamp_keluar', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    // ──────────────────────────────────────────────────────────
    // Get log dengan filter lengkap (dipakai di laporan)
    // ──────────────────────────────────────────────────────────
    public function getLogByFilter(array $filter = []): array
    {
        $builder = $this->db->table('log_keluar l')
                            ->select('l.*,
                                p.id as personel_id,
                                p.nama as personel_nama,
                                p.nrp,
                                p.foto as personel_foto,

                                pk.nama_pangkat,
                                pk.pangkat_singkat,
                                pk.kelompok   as pangkat_kelompok,

                                sk.kode_satker,
                                sk.nama_singkat as satker_singkat,

                                u.nama as petugas_nama
                            ')
                            ->join('personel p', 'p.id = l.personel_id')
                            ->join('pangkat pk', 'pk.id = p.pangkat_id', 'left')
                            ->join('satuan_kerja sk', 'sk.id = p.satker_id', 'left')
                            ->join('users u', 'u.id = l.petugas_id');

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

        // Filter kelompok pangkat (Perwira/Bintara/Tamtama/PNS)
        if (!empty($filter['kelompok'])) {
            $builder->where('pk.kelompok', $filter['kelompok']);
        }

        return $builder->orderBy('l.timestamp_keluar', 'DESC')
                    ->get()
                    ->getResultArray();
    }

    public function saveLog(int $personelId, string $kodeQr): bool
    {
        $now  = date('Y-m-d H:i:s');
        $hour = (int) date('H');

        if ($hour >= 6 && $hour < 14) {
            $shift = 'pagi';
        } elseif ($hour >= 14 && $hour < 22) {
            $shift = 'siang';
        } else {
            $shift = 'malam';
        }

        return $this->insert([
            'uuid'             => Uuid::uuid4()->toString(),
            'personel_id'      => $personelId,
            'petugas_id'       => session()->get('user_id'),
            'kode_qr_used'     => $kodeQr,
            'timestamp_keluar' => $now,
            'shift'            => $shift,
        ]);
    }

    
}