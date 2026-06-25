<?php

namespace App\Controllers;

use App\Models\PersonelModel;
use App\Models\KendaraanModel;
use App\Models\LogKeluarModel;
use App\Models\QrCodeModel;
use App\Models\PangkatModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    // ── Admin Dashboard ────────────────────────────────────
    public function index()
    {
        $personelModel  = new PersonelModel();
        $kendaraanModel = new KendaraanModel();
        $logModel       = new LogKeluarModel();
        $userModel      = new UserModel();
        $qrModel        = new QRCodeModel();
        $pangkatModel   = new \App\Models\PangkatModel();

        // ── Stat cards ──────────────────────────────────────
        $data = [
            'title'              => 'Dashboard',
            'total_personel'     => $personelModel->where('status','aktif')->countAllResults(),
            'total_kendaraan'    => $kendaraanModel->where('status','aktif')->countAllResults(),
            'total_petugas'      => $userModel->where('role','petugas')->where('status','aktif')->countAllResults(),
            'log_hari_ini'       => $logModel->getLogHariIni(),
            'log_bulan_ini'      => $logModel->getLogBulanIni(),
            'total_qr_aktif'     => $qrModel->countAktif(),
            'kendaraan_tanpa_qr' => count($qrModel->getKendaraanTanpaQR()),
            'total_pangkat'      => $pangkatModel->countAllResults(),
        ];

        // ── Chart 1: Scan 7 hari terakhir ───────────────────
        $chartHarian = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = date('Y-m-d', strtotime("-{$i} days"));
            $label   = $i === 0 ? 'Hari ini' : date('d M', strtotime("-{$i} days"));
            $total   = $logModel->where('DATE(timestamp_keluar)', $tanggal)->countAllResults();
            $chartHarian[] = ['label' => $label, 'total' => $total];
        }
        $data['chart_harian'] = $chartHarian;

        // ── Chart 2: Distribusi shift bulan ini ─────────────
        $bulanIni = date('Y-m');
        $data['chart_shift'] = [
            'pagi'  => $logModel->where('shift', 'pagi')
                                ->where("DATE_FORMAT(timestamp_keluar,'%Y-%m')", $bulanIni)
                                ->countAllResults(),
            'siang' => $logModel->where('shift', 'siang')
                                ->where("DATE_FORMAT(timestamp_keluar,'%Y-%m')", $bulanIni)
                                ->countAllResults(),
            'malam' => $logModel->where('shift', 'malam')
                                ->where("DATE_FORMAT(timestamp_keluar,'%Y-%m')", $bulanIni)
                                ->countAllResults(),
        ];

        // ── Chart 3: Top 5 satker bulan ini ─────────────────
        $topSatker = $logModel->db->table('log_keluar l')
            ->select('sk.nama_singkat as label, COUNT(l.id) as total')
            ->join('kendaraan k',     'k.id = l.kendaraan_id',  'left')
            ->join('personel p',      'p.id = k.personel_id',   'left')
            ->join('satuan_kerja sk', 'sk.id = p.satker_id',    'left')
            ->where("DATE_FORMAT(l.timestamp_keluar,'%Y-%m')", $bulanIni)
            ->groupBy('p.satker_id')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $data['chart_satker'] = $topSatker;

        // ── Chart 4: Personel keluar bulan ini by kelompok pangkat ──
        $chartPangkat = $logModel->db->table('log_keluar l')
        ->select('p2.kelompok as label, COUNT(DISTINCT p.id) as total')
        ->join('kendaraan k',  'k.id = l.kendaraan_id',  'left')
        ->join('personel p',   'p.id = k.personel_id',   'left')
        ->join('pangkat p2',   'p2.id = p.pangkat_id',   'left')
        ->where("DATE_FORMAT(l.timestamp_keluar,'%Y-%m')", $bulanIni)
        ->where('p2.kelompok IS NOT NULL') 
        ->groupBy('p2.kelompok')
        ->orderBy('p2.kelompok', 'ASC')
        ->get()
        ->getResultArray();

        $data['chart_pangkat'] = $chartPangkat;

        return view('dashboard/admin', $data);
    }

    public function petugasIndex(): string
    {
        // Kalau akses dashboard dari mobile → arahkan ke scanner
        $userAgent      = \Config\Services::request()->getUserAgent();
        $mobileKeywords = ['Mobile','Android','iPhone','iPad','iPod','Tablet','Windows Phone'];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return redirect()->to('/petugas/scan/mobile');
            }
        }

        $logModel = new \App\Models\LogKeluarModel();

        $data = [
            'title'        => 'Dashboard Petugas',
            'log_hari_ini' => $logModel->getLogHariIniByPetugas(session()->get('user_id')),
        ];

        return view('dashboard/petugas', $data);
    }
}