<?php

namespace App\Controllers;

use App\Libraries\ImageCompressor;
use App\Models\KendaraanModel;
use App\Models\KendaraanFotoModel;
use App\Models\PersonelModel;
use App\Models\QRCodeModel;
use Ramsey\Uuid\Uuid;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Kendaraan Controller
 *
 * Routes prefix: /admin/kendaraan
 */
class Kendaraan extends BaseController
{
    protected KendaraanModel     $kendaraanModel;
    protected KendaraanFotoModel $fotoModel;
    protected PersonelModel      $personelModel;
    protected QRCodeModel        $qrcodeModel;

    /** Batas maksimal foto galeri per kendaraan */
    const MAX_FOTO = 6;

    /** Format foto yang diterima */
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp'];

    /** Ukuran maksimal foto SEBELUM dikompres (15 MB) */
    private const MAX_UPLOAD_BYTES = 15 * 1024 * 1024;

    /** Tahun kendaraan paling lama yang diterima (samakan dengan atribut min di form) */
    private const TAHUN_MIN = 1950;

    public function __construct()
    {
        $this->kendaraanModel = new KendaraanModel();
        $this->fotoModel      = new KendaraanFotoModel();
        $this->personelModel  = new PersonelModel();
        $this->qrcodeModel    = new QRCodeModel();
    }

    // ──────────────────────────────────────────────────────────
    // INDEX
    // GET /admin/kendaraan
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        $byJenis = $this->kendaraanModel->countByJenis();
        $total   = $this->kendaraanModel->countAllResults();

        return view('kendaraan/index', [
            'title'       => 'Management Kendaraan',
            'total'       => $total,
            'total_motor' => $byJenis['motor'],
            'total_mobil' => $byJenis['mobil'],
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // DATATABLES SERVER-SIDE
    // POST /admin/kendaraan/datatables
    // ──────────────────────────────────────────────────────────
    public function datatables()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $draw   = (int) $this->request->getPost('draw');
        $start  = (int) $this->request->getPost('start');
        $length = (int) $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'] ?? '';
        $order  = $this->request->getPost('order')[0] ?? [];

        // Kolom sortable (index sesuai kolom di view)
        $columns = [
            2 => 'k.kendaraan_kode',
            3 => 'k.plat_nomor',
            4 => 'p.nama',
            5 => 'sk.nama_singkat',
            6 => 'k.status',
        ];

        $orderIndex = isset($order['column']) ? (int) $order['column'] : null;
        $orderCol   = ($orderIndex !== null && isset($columns[$orderIndex])) ? $columns[$orderIndex] : null;
        $orderDir   = strtolower($order['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

        $db = \Config\Database::connect();

        // Base query — thumbnail diambil lewat subquery (tanpa query per baris)
        $baseQuery = $db->table('kendaraan k')
            ->select('
                k.id, k.uuid, k.kendaraan_kode,
                k.plat_nomor, k.jenis, k.merk,
                k.model, k.warna, k.tahun_kendaraan,
                k.status,
                p.nama as personel_nama,
                p.nrp,
                pk.pangkat_singkat,
                sk.nama_singkat as satker_singkat,
                (SELECT kf.nama_file FROM kendaraan_foto kf
                 WHERE kf.kendaraan_id = k.id
                 ORDER BY kf.urutan ASC LIMIT 1) as thumb_file
            ', false)
            ->join('personel p',      'p.id = k.personel_id', 'left')
            ->join('pangkat pk',      'pk.id = p.pangkat_id', 'left')
            ->join('satuan_kerja sk', 'sk.id = p.satker_id',  'left');

        // Total semua data
        $totalAll = (clone $baseQuery)->countAllResults(false);

        // Filter search
        if (!empty($search)) {
            $baseQuery->groupStart()
                ->like('k.plat_nomor',       $search)
                ->orLike('k.kendaraan_kode', $search)
                ->orLike('k.merk',           $search)
                ->orLike('k.model',          $search)
                ->orLike('p.nama',           $search)
                ->orLike('p.nrp',            $search)
                ->orLike('sk.nama_singkat',  $search)
            ->groupEnd();
        }

        // Total setelah filter
        $totalFiltered = (clone $baseQuery)->countAllResults(false);

        // Urutan: kolom yang diklik user (kalau ada), lalu yang terakhir diubah/ditambah paling atas
        if ($orderCol) {
            $baseQuery->orderBy($orderCol, $orderDir);
        }

        $baseQuery->orderBy('COALESCE(k.updated_at, k.created_at)', 'DESC', false)
                  ->orderBy('k.id', 'DESC');

        if ($length !== -1) {
            $baseQuery->limit($length, $start);
        }

        $data = $baseQuery->get()->getResultArray();

        // Build rows
        $rows = [];
        foreach ($data as $i => $k) {
            $uuid      = esc($k['uuid']);
            $plat      = esc($k['plat_nomor']);
            $kendaraan = esc(trim($k['merk'] . ' ' . $k['model']));

            $thumbHtml = $k['thumb_file']
                ? '<img src="' . base_url('uploads/kendaraan/' . esc($k['thumb_file'])) . '" class="kendaraan-thumb" alt="">'
                : '<div class="kendaraan-thumb-placeholder ' . $k['jenis'] . '">
                    <i class="bi ' . ($k['jenis'] === 'motor' ? 'bi-bicycle' : 'bi-car-front') . '"></i>
                </div>';

            $tahun = $k['tahun_kendaraan']
                ? '<span class="text-muted" style="font-weight:400;font-size:.75rem;">(' . $k['tahun_kendaraan'] . ')</span>'
                : '';
            $warna = $k['warna']
                ? '<span class="text-muted" style="font-size:.72rem;">· ' . esc($k['warna']) . '</span>'
                : '';

            $kendaraanCol = '
                <div class="d-flex align-items-center gap-2">
                    ' . $thumbHtml . '
                    <div>
                        <div class="fw-500">' . esc($k['merk']) . ' ' . esc($k['model']) . ' ' . $tahun . '</div>
                        <span class="badge-jenis ' . $k['jenis'] . '">' . ucfirst($k['jenis']) . '</span>
                        ' . $warna . '
                    </div>
                </div>';

            $statusBadge = $k['status'] === 'aktif'
                ? '<span class="badge-status aktif"><span class="badge-dot"></span> Aktif</span>'
                : '<span class="badge-status dibekukan"><span class="badge-dot"></span> Dibekukan</span>';

            $toggleClass = $k['status'] === 'aktif' ? 'deactivate' : 'activate';
            $toggleIcon  = $k['status'] === 'aktif' ? 'bi-lock' : 'bi-unlock';

            $rows[] = [
                $start + $i + 1,
                $kendaraanCol,
                '<code class="code-tag">' . esc($k['kendaraan_kode']) . '</code>',
                '<span class="plat-nomor">' . $plat . '</span>',
                '<div class="fw-500" style="font-size:.83rem;">' . esc($k['personel_nama']) . '</div>
                <div style="font-size:.73rem;color:#9ca3af;">' . esc($k['pangkat_singkat']) . ' · ' . esc($k['nrp']) . '</div>',
                '<span style="font-size:.8rem;color:#6b7280;">' . (esc($k['satker_singkat']) ?: '-') . '</span>',
                $statusBadge,
                '<div class="action-wrap">
                    <a href="' . base_url('admin/kendaraan/edit/' . $uuid) . '" class="btn-action edit" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button"
                        class="btn-action ' . $toggleClass . ' btn-toggle-status"
                        data-uuid="' . $uuid . '"
                        data-status="' . $k['status'] . '"
                        data-plat="' . $plat . '"
                        data-kendaraan="' . $kendaraan . '"
                        title="' . ($k['status'] === 'aktif' ? 'Bekukan' : 'Aktifkan') . '">
                        <i class="bi ' . $toggleIcon . '"></i>
                    </button>
                    <button type="button"
                        class="btn-action delete btn-delete-kendaraan"
                        data-uuid="' . $uuid . '"
                        data-plat="' . $plat . '"
                        data-kendaraan="' . $kendaraan . '"
                        title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>',
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalAll,
            'recordsFiltered' => $totalFiltered,
            'data'            => $rows,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // CREATE
    // GET /admin/kendaraan/create
    // ──────────────────────────────────────────────────────────
    public function create(): string
    {
        $personelUuid     = $this->request->getGet('personel');
        $selectedPersonel = null;

        if ($personelUuid) {
            $selectedPersonel = $this->personelModel->findByUuid($personelUuid);
        }

        return view('kendaraan/create', [
            'title'            => 'Tambah Kendaraan',
            'personelList'     => $this->personelModel->getAktif(),
            'selectedPersonel' => $selectedPersonel,
            'nextKode'         => $this->kendaraanModel->generateKode(),
            'maxFoto'          => self::MAX_FOTO,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // STORE
    // POST /admin/kendaraan/store
    // ──────────────────────────────────────────────────────────
    public function store()
    {
        $rules = $this->rulesKendaraan('is_unique[kendaraan.plat_nomor]');

        $messages = [
            'plat_nomor' => ['is_unique' => 'Plat nomor sudah terdaftar.'],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        // Foto STNK (dikompres)
        [$fotoStnkName, $masalahStnk] = $this->processStnk(null);

        $uuid = Uuid::uuid4()->toString();

        // Simpan data kendaraan
        $kendaraanId = $this->kendaraanModel->insert([
            'uuid'            => $uuid,
            'kendaraan_kode'  => $this->kendaraanModel->generateKode(),
            'personel_id'     => $this->request->getPost('personel_id'),
            'plat_nomor'      => strtoupper(trim((string) $this->request->getPost('plat_nomor'))),
            'jenis'           => $this->request->getPost('jenis'),
            'merk'            => trim((string) $this->request->getPost('merk')),
            'model'           => trim((string) $this->request->getPost('model')),
            'warna'           => trim((string) $this->request->getPost('warna')),
            'nomor_rangka'    => strtoupper(trim((string) $this->request->getPost('nomor_rangka'))),
            'nomor_mesin'     => strtoupper(trim((string) $this->request->getPost('nomor_mesin'))),
            'nomor_bpkb'      => trim((string) $this->request->getPost('nomor_bpkb')),
            'nomor_stnk'      => trim((string) $this->request->getPost('nomor_stnk')),
            'tahun_kendaraan' => $this->request->getPost('tahun_kendaraan') ?: null,
            'foto_stnk'       => $fotoStnkName,
            'status'          => 'aktif',
        ], true);

        if (!$kendaraanId) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal menyimpan data kendaraan. Silakan coba lagi.');
        }

        // Foto galeri (dikompres)
        $masalah = $this->handleUploadGaleri((int) $kendaraanId);
        if ($masalahStnk) {
            array_unshift($masalah, $masalahStnk);
        }

        // Ada foto yang gagal → arahkan ke halaman edit supaya langsung terlihat & bisa diunggah ulang
        if (!empty($masalah)) {
            return redirect()->to('/admin/kendaraan/edit/' . $uuid)
                             ->with('upload_warnings', $masalah)
                             ->with('error', 'Data kendaraan tersimpan, tetapi ' . count($masalah) . ' foto gagal diunggah. Periksa detailnya di halaman ini.');
        }

        // Redirect ke detail personel jika ada
        $personel = $this->personelModel->find($this->request->getPost('personel_id'));

        if ($personel) {
            return redirect()->to('/admin/personel/detail/' . $personel['uuid'])
                             ->with('success', 'Kendaraan berhasil ditambahkan.');
        }

        return redirect()->to('/admin/kendaraan')
                         ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    // ──────────────────────────────────────────────────────────
    // EDIT
    // GET /admin/kendaraan/edit/{uuid}
    // ──────────────────────────────────────────────────────────
    public function edit(string $uuid)
    {
        $kendaraan = $this->kendaraanModel->getDetailByUuid($uuid);

        if (!$kendaraan) {
            return redirect()->to('/admin/kendaraan')
                             ->with('error', 'Kendaraan tidak ditemukan.');
        }

        $galeri      = $this->fotoModel->getByKendaraan($kendaraan['id']);
        $fotoTersisa = self::MAX_FOTO - count($galeri);

        return view('kendaraan/edit', [
            'title'        => 'Edit Kendaraan',
            'kendaraan'    => $kendaraan,
            'galeri'       => $galeri,
            'fotoTersisa'  => max(0, $fotoTersisa),
            'maxFoto'      => self::MAX_FOTO,
            'personelList' => $this->personelModel->getAktif(),
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // UPDATE
    // POST /admin/kendaraan/update/{uuid}
    // ──────────────────────────────────────────────────────────
    public function update(string $uuid)
    {
        $kendaraan = $this->kendaraanModel->findByUuid($uuid);

        if (!$kendaraan) {
            return redirect()->to('/admin/kendaraan')
                             ->with('error', 'Kendaraan tidak ditemukan.');
        }

        $rules = $this->rulesKendaraan("is_unique[kendaraan.plat_nomor,id,{$kendaraan['id']}]");

        $messages = [
            'plat_nomor' => ['is_unique' => 'Plat nomor sudah terdaftar.'],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        // Foto STNK baru (dikompres). Kalau tidak ada file baru, foto lama tetap dipakai.
        [$fotoStnkName, $masalahStnk] = $this->processStnk($kendaraan['foto_stnk']);

        $saved = $this->kendaraanModel->update($kendaraan['id'], [
            'personel_id'     => $this->request->getPost('personel_id'),
            'plat_nomor'      => strtoupper(trim((string) $this->request->getPost('plat_nomor'))),
            'jenis'           => $this->request->getPost('jenis'),
            'merk'            => trim((string) $this->request->getPost('merk')),
            'model'           => trim((string) $this->request->getPost('model')),
            'warna'           => trim((string) $this->request->getPost('warna')),
            'nomor_rangka'    => strtoupper(trim((string) $this->request->getPost('nomor_rangka'))),
            'nomor_mesin'     => strtoupper(trim((string) $this->request->getPost('nomor_mesin'))),
            'nomor_bpkb'      => trim((string) $this->request->getPost('nomor_bpkb')),
            'nomor_stnk'      => trim((string) $this->request->getPost('nomor_stnk')),
            'tahun_kendaraan' => $this->request->getPost('tahun_kendaraan') ?: null,
            'foto_stnk'       => $fotoStnkName,
        ]);

        if (!$saved) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Gagal menyimpan perubahan. Silakan coba lagi.');
        }

        // Tambah foto galeri baru (dikompres)
        $masalah = $this->handleUploadGaleri((int) $kendaraan['id']);
        if ($masalahStnk) {
            array_unshift($masalah, $masalahStnk);
        }

        if (!empty($masalah)) {
            return redirect()->to('/admin/kendaraan/edit/' . $uuid)
                             ->with('upload_warnings', $masalah)
                             ->with('error', 'Data kendaraan tersimpan, tetapi ' . count($masalah) . ' foto gagal diunggah. Periksa detailnya di halaman ini.');
        }

        return redirect()->to('/admin/kendaraan')
                         ->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    // ──────────────────────────────────────────────────────────
    // DELETE FOTO GALERI — AJAX
    // POST /admin/kendaraan/deletefoto/{foto_uuid}
    // ──────────────────────────────────────────────────────────
    public function deleteFoto(string $fotoUuid)
    {
        $deleted = $this->fotoModel->deleteByUuid($fotoUuid);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => $deleted,
                'message' => $deleted ? 'Foto berhasil dihapus.' : 'Foto tidak ditemukan.',
            ]);
        }

        return redirect()->back()
                         ->with($deleted ? 'success' : 'error',
                                $deleted ? 'Foto berhasil dihapus.' : 'Foto tidak ditemukan.');
    }

    // ──────────────────────────────────────────────────────────
    // DELETE KENDARAAN
    // POST /admin/kendaraan/delete/{uuid}
    // ──────────────────────────────────────────────────────────
    public function delete(string $uuid)
    {
        $kendaraan = $this->kendaraanModel->findByUuid($uuid);

        if (!$kendaraan) {
            return redirect()->to('/admin/kendaraan')
                             ->with('error', 'Kendaraan tidak ditemukan.');
        }

        // Tidak boleh dihapus kalau masih punya QR aktif
        if ($this->qrcodeModel->findAktifByKendaraan($kendaraan['id'])) {
            return redirect()->to('/admin/kendaraan')
                             ->with('error_qr',
                                 'Kendaraan <strong>' . esc($kendaraan['plat_nomor']) . '</strong> tidak dapat dihapus ' .
                                 'karena masih memiliki QR Code aktif. ' .
                                 'Nonaktifkan QR Code terlebih dahulu sebelum menghapus kendaraan.'
                             );
        }

        // Hapus QR nonaktif yang terkait
        $this->qrcodeModel->deleteByKendaraan($kendaraan['id']);

        // Hapus semua foto galeri
        $this->fotoModel->deleteByKendaraan($kendaraan['id']);

        // Hapus foto STNK
        if ($kendaraan['foto_stnk'] && is_file(FCPATH . 'uploads/kendaraan/' . $kendaraan['foto_stnk'])) {
            @unlink(FCPATH . 'uploads/kendaraan/' . $kendaraan['foto_stnk']);
        }

        $this->kendaraanModel->delete($kendaraan['id']);

        return redirect()->to('/admin/kendaraan')
                         ->with('success', "Kendaraan {$kendaraan['plat_nomor']} berhasil dihapus.");
    }

    // ──────────────────────────────────────────────────────────
    // TOGGLE STATUS — Aktif / Dibekukan
    // POST /admin/kendaraan/toggle/{uuid}
    //
    // Lewat Query Builder supaya updated_at TIDAK berubah
    // (posisi kendaraan di tabel tidak loncat ke atas).
    // ──────────────────────────────────────────────────────────
    public function toggle(string $uuid)
    {
        $kendaraan = $this->kendaraanModel->findByUuid($uuid);

        if (!$kendaraan) {
            return redirect()->to('/admin/kendaraan')
                             ->with('error', 'Kendaraan tidak ditemukan.');
        }

        $newStatus = $kendaraan['status'] === 'aktif' ? 'dibekukan' : 'aktif';
        $label     = $newStatus === 'aktif' ? 'diaktifkan' : 'dibekukan';

        \Config\Database::connect()->table('kendaraan')
            ->where('id', $kendaraan['id'])
            ->update(['status' => $newStatus]);

        return redirect()->to('/admin/kendaraan')
                         ->with('success', "Kendaraan berhasil {$label}.");
    }

    // ──────────────────────────────────────────────────────────
    // HALAMAN IMPORT KENDARAAN
    // GET /admin/kendaraan/import
    // ──────────────────────────────────────────────────────────
    public function import(): string
    {
        return view('kendaraan/import', [
            'title' => 'Import Kendaraan via Excel',
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // DOWNLOAD TEMPLATE EXCEL
    // GET /admin/kendaraan/templateExcel
    // ──────────────────────────────────────────────────────────
    public function templateExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Kendaraan');

        $headers = [
            'A1' => 'NRP *',
            'B1' => 'Plat Nomor *',
            'C1' => 'Jenis (motor/mobil) *',
            'D1' => 'Merk',
            'E1' => 'Model / Tipe',
            'F1' => 'Warna',
            'G1' => 'Tahun',
            'H1' => 'Nomor Rangka',
            'I1' => 'Nomor Mesin',
            'J1' => 'Nomor BPKB',
            'K1' => 'Nomor STNK',
        ];
        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '16A34A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $contohData = [
            ['123456', 'B 1234 ABC', 'mobil', 'Toyota', 'Avanza 1.5 G', 'Hitam', '2022', 'MHF123...', '1NR456...', 'BPKB-001', 'STNK-001'],
            ['789012', 'L 5678 XX',  'motor', 'Honda',  'Vario 160',    'Putih', '2023', 'MH1234...', 'JM5678...', 'BPKB-002', 'STNK-002'],
        ];
        $rowNum = 2;
        foreach ($contohData as $row) {
            $sheet->fromArray($row, null, "A{$rowNum}");
            $rowNum++;
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $fileData = ob_get_clean();

        return $this->response
                    ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                    ->setHeader('Content-Disposition', 'attachment; filename="Template_Import_Kendaraan.xlsx"')
                    ->setHeader('Cache-Control', 'max-age=0')
                    ->setBody($fileData);
    }

    // ──────────────────────────────────────────────────────────
    // PROSES IMPORT EXCEL
    // POST /admin/kendaraan/prosesImport
    // ──────────────────────────────────────────────────────────
    public function prosesImport()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->to('/admin/kendaraan/import')->with('error', 'File Excel wajib diunggah.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xlsx', 'xls'], true)) {
            return redirect()->to('/admin/kendaraan/import')->with('error', 'Format file harus berupa .xlsx atau .xls.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheetData   = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        } catch (\Exception $e) {
            return redirect()->to('/admin/kendaraan/import')->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        // Ambil urutan kode kendaraan terakhir
        $lastKendaraan = $this->kendaraanModel->select('kendaraan_kode')->orderBy('id', 'DESC')->first();
        $lastKodeNum   = $lastKendaraan ? (int) substr($lastKendaraan['kendaraan_kode'], 4) : 0;
        $db            = \Config\Database::connect();
        $tahunMax      = (int) date('Y');

        $successCount = 0;
        $errorList    = [];

        foreach ($sheetData as $rowIdx => $row) {
            if ($rowIdx === 1) {
                continue; // Skip header
            }

            $nrp       = trim((string) ($row['A'] ?? ''));
            $platNomor = strtoupper(trim((string) ($row['B'] ?? '')));
            $jenis     = strtolower(trim((string) ($row['C'] ?? '')));
            $merk      = trim((string) ($row['D'] ?? ''));
            $model     = trim((string) ($row['E'] ?? ''));
            $warna     = trim((string) ($row['F'] ?? ''));
            $tahun     = !empty($row['G']) ? (int) $row['G'] : null;
            $noRangka  = strtoupper(trim((string) ($row['H'] ?? '')));
            $noMesin   = strtoupper(trim((string) ($row['I'] ?? '')));
            $noBpkb    = trim((string) ($row['J'] ?? ''));
            $noStnk    = trim((string) ($row['K'] ?? ''));

            if ($nrp === '' && $platNomor === '') {
                continue;
            }

            // Validasi kolom wajib
            if ($nrp === '' || $platNomor === '' || $jenis === '') {
                $errorList[] = "Baris {$rowIdx}: NRP, Plat Nomor, dan Jenis kendaraan wajib diisi.";
                continue;
            }

            if (!in_array($jenis, ['motor', 'mobil'], true)) {
                $errorList[] = "Baris {$rowIdx} (Plat {$platNomor}): Jenis harus 'motor' atau 'mobil'.";
                continue;
            }

            if ($tahun !== null && ($tahun < self::TAHUN_MIN || $tahun > $tahunMax)) {
                $errorList[] = "Baris {$rowIdx} (Plat {$platNomor}): Tahun harus antara " . self::TAHUN_MIN . " dan {$tahunMax}.";
                continue;
            }

            // Cari personel by NRP
            $personel = $db->table('personel')->where('nrp', $nrp)->get()->getRowArray();
            if (!$personel) {
                $errorList[] = "Baris {$rowIdx}: Personel dengan NRP <strong>" . esc($nrp) . "</strong> tidak ditemukan di database.";
                continue;
            }

            if ($personel['status'] !== 'aktif') {
                $errorList[] = "Baris {$rowIdx}: Personel dengan NRP <strong>" . esc($nrp) . "</strong> berstatus nonaktif.";
                continue;
            }

            // Cek plat nomor duplikat
            if ($db->table('kendaraan')->where('plat_nomor', $platNomor)->countAllResults() > 0) {
                $errorList[] = "Baris {$rowIdx}: Plat nomor <strong>" . esc($platNomor) . "</strong> sudah terdaftar (dilewati).";
                continue;
            }

            // Auto generate kode & insert
            $lastKodeNum++;
            $this->kendaraanModel->insert([
                'uuid'            => Uuid::uuid4()->toString(),
                'kendaraan_kode'  => 'KND-' . str_pad((string) $lastKodeNum, 4, '0', STR_PAD_LEFT),
                'personel_id'     => $personel['id'],
                'plat_nomor'      => $platNomor,
                'jenis'           => $jenis,
                'merk'            => $merk ?: null,
                'model'           => $model ?: null,
                'warna'           => $warna ?: null,
                'nomor_rangka'    => $noRangka ?: null,
                'nomor_mesin'     => $noMesin ?: null,
                'nomor_bpkb'      => $noBpkb ?: null,
                'nomor_stnk'      => $noStnk ?: null,
                'tahun_kendaraan' => $tahun ?: null,
                'status'          => 'aktif',
            ]);
            $successCount++;
        }

        if ($successCount > 0 && empty($errorList)) {
            return redirect()->to('/admin/kendaraan')
                             ->with('success', "Berhasil mengimpor <strong>{$successCount}</strong> data kendaraan.");
        }

        if ($successCount > 0) {
            return redirect()->to('/admin/kendaraan')
                             ->with('success', "Berhasil mengimpor <strong>{$successCount}</strong> kendaraan.")
                             ->with('import_errors', $errorList);
        }

        return redirect()->to('/admin/kendaraan/import')
                         ->with('error', 'Tidak ada data yang berhasil diimpor.')
                         ->with('import_errors', $errorList);
    }

    // ──────────────────────────────────────────────────────────
    // AJAX: Cari personel by NRP
    // GET /admin/kendaraan/cariPersonel
    // ──────────────────────────────────────────────────────────
    public function cariPersonel()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $nrp = trim((string) $this->request->getGet('nrp'));

        if ($nrp === '') {
            return $this->response->setJSON(['found' => false, 'message' => 'NRP tidak boleh kosong.']);
        }

        $result = \Config\Database::connect()->table('personel p')
            ->select('p.id, p.uuid, p.nrp, p.nama, p.jabatan, p.foto, p.status,
                      pk.pangkat_singkat, pk.nama_pangkat,
                      sk.nama_singkat as satker_singkat')
            ->join('pangkat pk',      'pk.id = p.pangkat_id', 'left')
            ->join('satuan_kerja sk', 'sk.id = p.satker_id',  'left')
            ->where('p.nrp', $nrp)
            ->get()
            ->getRowArray();

        if (!$result) {
            return $this->response->setJSON(['found' => false, 'message' => 'Personel dengan NRP tersebut tidak ditemukan.']);
        }

        if ($result['status'] !== 'aktif') {
            return $this->response->setJSON(['found' => false, 'message' => 'Personel tidak aktif, tidak dapat didaftarkan kendaraan.']);
        }

        return $this->response->setJSON([
            'found'    => true,
            'personel' => [
                'id'           => $result['id'],
                'nrp'          => $result['nrp'],
                'nama'         => $result['nama'],
                'pangkat'      => $result['pangkat_singkat'],
                'nama_pangkat' => $result['nama_pangkat'],
                'jabatan'      => $result['jabatan'] ?? '-',
                'satker'       => $result['satker_singkat'],
                'foto'         => $result['foto']
                                    ? base_url('uploads/personel/' . $result['foto'])
                                    : base_url('assets/img/no-avatar.png'),
            ],
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // PRIVATE
    // ══════════════════════════════════════════════════════════

    /**
     * Aturan validasi data kendaraan (dipakai store & update).
     * Foto tidak divalidasi di sini; dicek oleh processStnk() & handleUploadGaleri().
     */
    private function rulesKendaraan(string $platUniqueRule): array
    {
        $tahunMax = (int) date('Y');

        return [
            'personel_id'     => 'required|is_natural_no_zero',
            'plat_nomor'      => "required|max_length[20]|{$platUniqueRule}",
            'jenis'           => 'required|in_list[motor,mobil]',
            'merk'            => 'permit_empty|max_length[50]',
            'model'           => 'permit_empty|max_length[50]',
            'warna'           => 'permit_empty|max_length[30]',
            'nomor_rangka'    => 'permit_empty|max_length[50]',
            'nomor_mesin'     => 'permit_empty|max_length[50]',
            'nomor_bpkb'      => 'permit_empty|max_length[50]',
            'nomor_stnk'      => 'permit_empty|max_length[50]',
            'tahun_kendaraan' => 'permit_empty|integer|greater_than_equal_to[' . self::TAHUN_MIN . ']|less_than_equal_to[' . $tahunMax . ']',
        ];
    }

    /**
     * Upload & kompres foto galeri.
     * Mengembalikan daftar masalah (kosong = semua foto berhasil).
     */
    private function handleUploadGaleri(int $kendaraanId): array
    {
        $masalah = [];
        $fotos   = $this->request->getFileMultiple('foto_galeri') ?? [];

        $existingCount = $this->fotoModel->countByKendaraan($kendaraanId);
        $urutan        = $existingCount + 1;

        foreach ($fotos as $foto) {
            if ($foto->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $nama = $foto->getClientName();

            if ($existingCount >= self::MAX_FOTO) {
                $masalah[] = "{$nama}: melebihi batas " . self::MAX_FOTO . ' foto per kendaraan.';
                continue;
            }

            if (!$foto->isValid()) {
                $masalah[] = "{$nama}: " . ImageCompressor::uploadError($foto->getError());
                continue;
            }

            if ($foto->hasMoved()) {
                continue;
            }

            if (!in_array($foto->getMimeType(), self::ALLOWED_MIME, true)) {
                $masalah[] = "{$nama}: format harus JPG, PNG, atau WebP.";
                continue;
            }

            if ($foto->getSize() > self::MAX_UPLOAD_BYTES) {
                $masalah[] = "{$nama}: ukuran lebih dari 15 MB.";
                continue;
            }

            $namaFile = 'galeri_' . pathinfo($foto->getRandomName(), PATHINFO_FILENAME) . '.jpg';

            if (!ImageCompressor::toJpeg($foto->getTempName(), FCPATH . 'uploads/kendaraan/' . $namaFile, 1600, 80)) {
                $masalah[] = "{$nama}: gagal diproses. Pastikan file adalah foto yang tidak rusak.";
                continue;
            }

            $this->fotoModel->insert([
                'uuid'         => Uuid::uuid4()->toString(),
                'kendaraan_id' => $kendaraanId,
                'nama_file'    => $namaFile,
                'urutan'       => $urutan,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);

            $urutan++;
            $existingCount++;
        }

        return $masalah;
    }

    /**
     * Upload & kompres foto STNK.
     * Mengembalikan [nama file yang dipakai, pesan masalah atau null].
     * Kalau tidak ada file baru, nama file lama dikembalikan apa adanya.
     */
    private function processStnk(?string $namaLama): array
    {
        $file = $this->request->getFile('foto_stnk');

        if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return [$namaLama, null];
        }

        $nama = 'Foto STNK (' . $file->getClientName() . ')';

        if (!$file->isValid()) {
            return [$namaLama, "{$nama}: " . ImageCompressor::uploadError($file->getError())];
        }

        if (!in_array($file->getMimeType(), self::ALLOWED_MIME, true)) {
            return [$namaLama, "{$nama}: format harus JPG, PNG, atau WebP."];
        }

        if ($file->getSize() > self::MAX_UPLOAD_BYTES) {
            return [$namaLama, "{$nama}: ukuran lebih dari 15 MB."];
        }

        $namaBaru = 'stnk_' . pathinfo($file->getRandomName(), PATHINFO_FILENAME) . '.jpg';

        // STNK dikompres lebih ringan (2000px, kualitas 85) supaya tulisannya tetap terbaca
        if (!ImageCompressor::toJpeg($file->getTempName(), FCPATH . 'uploads/kendaraan/' . $namaBaru, 2000, 85)) {
            return [$namaLama, "{$nama}: gagal diproses. Pastikan file adalah foto yang tidak rusak."];
        }

        if ($namaLama && is_file(FCPATH . 'uploads/kendaraan/' . $namaLama)) {
            @unlink(FCPATH . 'uploads/kendaraan/' . $namaLama);
        }

        return [$namaBaru, null];
    }
}