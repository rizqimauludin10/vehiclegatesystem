<?php

namespace App\Controllers;

use App\Models\LogKeluarModel;
use App\Models\PersonelModel;
use App\Models\UserModel;
use App\Models\PangkatModel;
use App\Models\SatkerModel;

class Laporan extends BaseController
{
    protected LogKeluarModel $logModel;
    protected PersonelModel  $personelModel;
    protected UserModel      $userModel;
    protected PangkatModel   $pangkatModel;
    protected SatkerModel    $satkerModel;

    public function __construct()
    {
        $this->logModel      = new LogKeluarModel();
        $this->personelModel = new PersonelModel();
        $this->userModel     = new UserModel();
        $this->pangkatModel  = new PangkatModel();
        $this->satkerModel   = new SatkerModel();
    }

    // ──────────────────────────────────────────────────────────
    // INDEX ADMIN — Laporan Lengkap
    // GET /admin/laporan
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        $filter = [
            'tanggal_dari'   => $this->request->getGet('tanggal_dari')   ?? date('Y-m-01'),
            'tanggal_sampai' => $this->request->getGet('tanggal_sampai') ?? date('Y-m-d'),
            'petugas_id'     => $this->request->getGet('petugas_id')     ?? '',
            'shift'          => $this->request->getGet('shift')          ?? '',
            'satker_id'      => $this->request->getGet('satker_id')      ?? '',
            'pangkat_id'     => $this->request->getGet('pangkat_id')     ?? '',
            'kelompok'       => $this->request->getGet('kelompok')       ?? '',
        ];

        $logs  = $this->logModel->getLogByFilter($filter);
        $stats = $this->buildStats($logs);

        $data = [
            'title'   => 'Laporan & Rekap',
            'logs'    => $logs,
            'filter'  => $filter,
            'stats'   => $stats,
            'petugas' => $this->userModel->getAllPetugas(),
            'satker'  => $this->satkerModel->getForDropdown(),
            'pangkat' => $this->pangkatModel->getGroupedForDropdown(),
        ];

        return view('laporan/admin', $data);
    }

    // ──────────────────────────────────────────────────────────
    // INDEX PETUGAS
    // GET /petugas/laporan
    // ──────────────────────────────────────────────────────────
    public function petugasIndex(): string
    {
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        $filter = [
            'tanggal_dari'   => $tanggal,
            'tanggal_sampai' => $tanggal,
            'petugas_id'     => session()->get('user_id'),
            'shift'          => $this->request->getGet('shift')     ?? '',
            'satker_id'      => $this->request->getGet('satker_id') ?? '',
            'pangkat_id'     => $this->request->getGet('pangkat_id') ?? '',
            'kelompok'       => $this->request->getGet('kelompok')  ?? '',
        ];

        $logs  = $this->logModel->getLogByFilter($filter);
        $stats = $this->buildStats($logs);

        $data = [
            'title'   => 'Log Scan Saya',
            'logs'    => $logs,
            'filter'  => $filter,
            'stats'   => $stats,
            'tanggal' => $tanggal,
            'satker'  => $this->satkerModel->getForDropdown(),
            'pangkat' => $this->pangkatModel->getGroupedForDropdown(),
        ];

        return view('laporan/petugas', $data);
    }

    // ──────────────────────────────────────────────────────────
    // EXPORT CSV
    // GET /admin/laporan/export
    // ──────────────────────────────────────────────────────────
    public function export()
    {
        $filter = [
            'tanggal_dari'   => $this->request->getGet('tanggal_dari')   ?? date('Y-m-01'),
            'tanggal_sampai' => $this->request->getGet('tanggal_sampai') ?? date('Y-m-d'),
            'petugas_id'     => $this->request->getGet('petugas_id')     ?? '',
            'shift'          => $this->request->getGet('shift')          ?? '',
            'satker_id'      => $this->request->getGet('satker_id')      ?? '',
            'pangkat_id'     => $this->request->getGet('pangkat_id')     ?? '',
            'kelompok'       => $this->request->getGet('kelompok')       ?? '',
        ];

        $logs     = $this->logModel->getLogByFilter($filter);
        $filename = 'laporan_keluar_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM UTF-8 untuk Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header kolom
        fputcsv($output, [
            'No', 'Tanggal', 'Waktu', 'Shift',
            'NRP', 'Nama Personel',
            'Pangkat', 'Kelompok',
            'Satuan Kerja',
            'Petugas',
        ]);

        // Data rows
        foreach ($logs as $i => $log) {
            fputcsv($output, [
                $i + 1,
                date('d/m/Y', strtotime($log['timestamp_keluar'])),
                date('H:i:s', strtotime($log['timestamp_keluar'])),
                ucfirst($log['shift'] ?? '-'),
                $log['nrp']              ?? '-',
                $log['personel_nama']    ?? '-',
                $log['pangkat_singkat']  ?? '-',
                $log['pangkat_kelompok'] ?? '-',
                $log['satker_singkat']   ?? '-',
                $log['petugas_nama']     ?? '-',
            ]);
        }

        fclose($output);
        exit;
    }

    // ──────────────────────────────────────────────────────────
    // PRIVATE — Build Statistik
    // ──────────────────────────────────────────────────────────
    private function buildStats(array $logs): array
    {
        $byShift        = ['pagi' => 0, 'siang' => 0, 'malam' => 0];
        $bySatker       = [];
        $uniquePersonel = [];

        foreach ($logs as $log) {
            // By shift
            if (!empty($log['shift']) && isset($byShift[$log['shift']])) {
                $byShift[$log['shift']]++;
            }

            // By satker
            $satker = $log['satker_singkat'] ?? 'Tidak Diketahui';
            $bySatker[$satker] = ($bySatker[$satker] ?? 0) + 1;

            // Unique personel
            $uniquePersonel[$log['personel_id']] = true;
        }

        arsort($bySatker);

        return [
            'total'           => count($logs),
            'unique_personel' => count($uniquePersonel),
            'by_shift'        => $byShift,
            'by_satker'       => array_slice($bySatker, 0, 5, true),
        ];
    }
}