<?php

namespace App\Controllers;

use App\Models\PersonelModel;
use App\Models\KendaraanModel;
use App\Models\LogKeluarModel;
use App\Models\QrCodeModel;
use App\Models\PangkatModel;
use App\Models\UserModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use CodeIgniter\HTTP\RedirectResponse;

class Dashboard extends BaseController
{
    public function __construct()
{
    $this->db = \Config\Database::connect(); // ← tambah ini
}
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

        // ── Data per Satuan Kerja ────────────────────────────
        $dataSatker = $this->db->table('satuan_kerja sk')
            ->select('
                sk.kode_satker,
                sk.nama_singkat,
                sk.nama_panjang,
                COUNT(DISTINCT p.id)  as total_personel,
                COUNT(DISTINCT k.id)  as total_kendaraan,
                COUNT(DISTINCT q.id)  as total_qr_aktif
            ')
            ->join('personel p',  'p.satker_id = sk.id AND p.status = "aktif"',  'left')
            ->join('kendaraan k', 'k.personel_id = p.id AND k.status = "aktif"', 'left')
            ->join('qrcode q',    'q.kendaraan_id = k.id AND q.status = "aktif"','left')
            ->groupBy('sk.id')
            ->orderBy('total_personel', 'DESC')
            ->get()
            ->getResultArray();

        $data['data_satker'] = $dataSatker;

        // ── Metrik Monitoring Input Data Satker (BARU) ───────
        $totalSatker        = count($dataSatker);
        $satkerSudahInput   = count(array_filter($dataSatker, function($s) {
            return (int)$s['total_personel'] > 0 || (int)$s['total_kendaraan'] > 0;
        }));
        $satkerBelumInput   = $totalSatker - $satkerSudahInput;
        $pctSatkerAktif     = $totalSatker > 0 ? round(($satkerSudahInput / $totalSatker) * 100) : 0;
        $data['monitoring_input'] = [
            'total_satker'       => $totalSatker,
            'satker_sudah_input' => $satkerSudahInput,
            'satker_belum_input' => $satkerBelumInput,
            'persentase_aktif'   => $pctSatkerAktif,
        ];

        // ── Chart 5: Tren Input Data 7 Hari Terakhir ────────────
        $chartTrenInput = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = date('Y-m-d', strtotime("-{$i} days"));
            $label   = $i === 0 ? 'Hari ini' : date('d M', strtotime("-{$i} days"));

            // Menghitung data yang dibuat pada tanggal tersebut
            $inputPersonel = $personelModel->where("DATE(created_at)", $tanggal)->countAllResults();
            $inputKendaraan = $kendaraanModel->where("DATE(created_at)", $tanggal)->countAllResults();

            $chartTrenInput[] = [
                'label'     => $label,
                'personel'  => $inputPersonel,
                'kendaraan' => $inputKendaraan,
            ];
        }
        $data['chart_tren_input'] = $chartTrenInput;


        return view('dashboard/admin', $data);
    }

        // ──────────────────────────────────────────────────────────
    // EXPORT REKAP SATKER KE EXCEL (UNTUK PIMPINAN)
    // GET /admin/dashboard/export-satker
    // ──────────────────────────────────────────────────────────
    public function exportSatkerExcel()
    {
        // Ambil data satker yang sama persis dengan dashboard
        $dataSatker = $this->db->table('satuan_kerja sk')
            ->select('
                sk.id,
                sk.kode_satker,
                sk.nama_singkat,
                sk.nama_panjang,
                (SELECT COUNT(*) FROM personel p WHERE p.satker_id = sk.id AND p.status = "aktif") as total_personel,
                (SELECT COUNT(*) FROM kendaraan k JOIN personel p2 ON p2.id = k.personel_id WHERE p2.satker_id = sk.id AND k.status = "aktif" AND p2.status = "aktif") as total_kendaraan,
                (SELECT COUNT(*) FROM qrcode q JOIN kendaraan k2 ON k2.id = q.kendaraan_id JOIN personel p3 ON p3.id = k2.personel_id WHERE p3.satker_id = sk.id AND q.status = "aktif" AND k2.status = "aktif" AND p3.status = "aktif") as total_qr_aktif
            ')
            ->orderBy('total_personel', 'DESC')
            ->get()
            ->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Satker');

        // Judul Laporan
        $sheet->setCellValue('A1', 'REKAPITULASI DATA PER SATUAN KERJA');
        $sheet->setCellValue('A2', 'Dicetak pada: ' . date('d F Y, H:i') . ' WIB oleh ' . session()->get('user_nama'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB('6B7280');

        // Header Tabel di Baris 4
        $headers = [
            'A4' => 'No',
            'B4' => 'Satuan Kerja',
            'C4' => 'Nama Panjang',
            'D4' => 'Kode',
            'E4' => 'Personel',
            'F4' => 'Kendaraan',
            'G4' => 'QR Aktif',
            'H4' => 'Coverage QR (%)',
            'I4' => 'Status Input',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        // Style Header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '16A34A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A4:I4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(24);

        // Isi Data
        $rowNum = 5;
        $totalPersonelAll  = 0;
        $totalKendaraanAll = 0;
        $totalQrAll        = 0;

        foreach ($dataSatker as $i => $s) {
            $personel  = (int) $s['total_personel'];
            $kendaraan = (int) $s['total_kendaraan'];
            $qrAktif   = (int) $s['total_qr_aktif'];
            $coverage  = $kendaraan > 0 ? round(($qrAktif / $kendaraan) * 100) : 0;
            $status    = ($personel > 0 || $kendaraan > 0) ? 'Sudah Input' : 'Belum Ada Data';

            $totalPersonelAll  += $personel;
            $totalKendaraanAll += $kendaraan;
            $totalQrAll        += $qrAktif;

            $sheet->setCellValue("A{$rowNum}", $i + 1);
            $sheet->setCellValue("B{$rowNum}", $s['nama_singkat']);
            $sheet->setCellValue("C{$rowNum}", $s['nama_panjang']);
            $sheet->setCellValue("D{$rowNum}", $s['kode_satker']);
            $sheet->setCellValue("E{$rowNum}", $personel);
            $sheet->setCellValue("F{$rowNum}", $kendaraan);
            $sheet->setCellValue("G{$rowNum}", $qrAktif);
            $sheet->setCellValue("H{$rowNum}", $coverage . '%');
            $sheet->setCellValue("I{$rowNum}", $status);

            // Alignment center untuk kolom angka & status
            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$rowNum}:H{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rowNum++;
        }

        // Baris Total
        $sheet->setCellValue("A{$rowNum}", 'TOTAL KESELURUHAN');
        $sheet->mergeCells("A{$rowNum}:D{$rowNum}");
        $sheet->setCellValue("E{$rowNum}", $totalPersonelAll);
        $sheet->setCellValue("F{$rowNum}", $totalKendaraanAll);
        $sheet->setCellValue("G{$rowNum}", $totalQrAll);
        $sheet->setCellValue("H{$rowNum}", ($totalKendaraanAll > 0 ? round(($totalQrAll / $totalKendaraanAll) * 100) : 0) . '%');
        $sheet->setCellValue("I{$rowNum}", '-');

        // Style Baris Total
        $totalStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F1F5F9'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle("A{$rowNum}:I{$rowNum}")->applyFromArray($totalStyle);
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Border seluruh tabel
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ];
        $sheet->getStyle("A4:I{$rowNum}")->applyFromArray($borderStyle);

        // Auto-width kolom
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Rekap_Data_Satker_' . date('Ymd_His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $fileData = ob_get_clean();

        return $this->response
                    ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                    ->setHeader('Cache-Control', 'max-age=0')
                    ->setBody($fileData);
    }

    public function petugasIndex()
    {
        $agent = $this->request->getUserAgent();
        if ($agent->isMobile()) {
            return redirect()->to('/petugas/scan/mobile');
        }

        $userId   = session()->get('user_id');
        $logModel = new \App\Models\LogKeluarModel();

        $data = [
            'title'        => 'Dashboard Petugas Gate',
            'log_hari_ini' => $logModel->getLogHariIniByPetugas($userId),
            // Ambil seluruh log HARI INI saja milik petugas ini
            'logs'         => $logModel->getLogHariIniByPetugasFull($userId),
        ];

        return view('dashboard/petugas', $data);
    }
}