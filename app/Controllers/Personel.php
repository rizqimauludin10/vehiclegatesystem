<?php

namespace App\Controllers;

use App\Models\PersonelModel;
use App\Models\KendaraanModel;
use App\Models\QRCodeModel;
use App\Models\PangkatModel;
use App\Models\SatkerModel;
use Ramsey\Uuid\Uuid;

/**
 * Personel Controller
 *
 * Handles CRUD for personel data including photo upload.
 * Only accessible by admin role.
 *
 * Routes prefix: /admin/personel
 */
class Personel extends BaseController
{
    protected PersonelModel  $personelModel;
    protected KendaraanModel $kendaraanModel;
    protected QRCodeModel    $qrCodeModel;
    protected SatkerModel    $satkerModel;
    protected PangkatModel   $pangkatModel;

    // Upload config
    protected array $uploadConfig = [
        'upload_path'   => FCPATH . 'uploads/personel/',
        'allowed_types' => 'jpg|jpeg|png|webp',
        'max_size'      => 2048, // 2MB
        'max_width'     => 2000,
        'max_height'    => 2000,
    ];

    public function __construct()
    {
        $this->personelModel  = new PersonelModel();
        $this->kendaraanModel = new KendaraanModel();
        $this->qrCodeModel    = new QRCodeModel();
        $this->satkerModel    = new SatkerModel();
        $this->pangkatModel   = new PangkatModel();
    }

    // ──────────────────────────────────────────────────────────
    // INDEX — Daftar Semua Personel
    // GET /admin/personel
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        $data = [
            'title'    => 'Management Personel',
            'personel' => $this->personelModel->getAllWithKendaraanCount(),
        ];

        return view('personel/index', $data);
    }

    // ──────────────────────────────────────────────────────────
    // CREATE — Form Tambah Personel
    // GET /admin/personel/create
    // ──────────────────────────────────────────────────────────
    public function create(): string
    {
        $pangkatModel = new PangkatModel();
        $satkerModel  = new SatkerModel();
    
        $data = [
            'title'      => 'Tambah Personel',
            'validation' => \Config\Services::validation(),
            'pangkat'    => $pangkatModel->getGroupedForDropdown(),
            'satker'     => $satkerModel->getForDropdown(),
        ];

        return view('personel/create', $data);
    }

    // ──────────────────────────────────────────────────────────
    // STORE — Simpan Personel Baru
    // POST /admin/personel/store
    // ──────────────────────────────────────────────────────────
    public function store()
    {
        $rules = [
            'nrp'     => 'required|max_length[20]|is_unique[personel.nrp]',
            'nama'    => 'required|min_length[3]|max_length[100]',
            'pangkat_id' => 'required|is_natural_no_zero',
            'satker_id'  => 'required|is_natural_no_zero',
            'jabatan' => 'permit_empty|max_length[100]',
            'foto'    => 'permit_empty|uploaded[foto]|max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        $messages = [
            'nrp'  => ['is_unique' => 'NRP sudah terdaftar di sistem.'],
            'foto' => [
                'max_size'  => 'Ukuran foto maksimal 2MB.',
                'is_image'  => 'File harus berupa gambar.',
                'mime_in'   => 'Format foto harus JPG, PNG, atau WebP.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        // Handle upload foto
        $fotoName = null;
        $foto     = $this->request->getFile('foto');

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $fotoName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/personel/', $fotoName);
        }

        $this->personelModel->insert([
            'uuid'       => Uuid::uuid4()->toString(),
            'nrp'        => strtoupper(trim($this->request->getPost('nrp'))),
            'nama'       => trim($this->request->getPost('nama')),
            'pangkat_id' => (int) $this->request->getPost('pangkat_id'),
            'satker_id'  => (int) $this->request->getPost('satker_id'),
            'jabatan'    => trim($this->request->getPost('jabatan')),
            'foto'       => $fotoName,
            'status'     => 'aktif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/personel')
                        ->with('success', 'Data personel berhasil ditambahkan.');
    }

    // ──────────────────────────────────────────────────────────
    // DETAIL — Detail Personel + Kendaraan
    // GET /admin/personel/detail/{uuid}
    // ──────────────────────────────────────────────────────────
    public function detail(string $uuid): string
    {
        $personel = $this->personelModel->getWithKendaraan($uuid);

        if (!$personel) {
            return redirect()->to('/admin/personel')
                            ->with('error', 'Personel tidak ditemukan.');
        }

        // Cek status QR
        $qr = $this->qrCodeModel->findAktifByPersonel($personel['id']);

        $data = [
            'title'    => 'Detail Personel',
            'personel' => $personel,
            'qr'       => $qr,
        ];

        return view('personel/detail', $data);
    }

    // ──────────────────────────────────────────────────────────
    // EDIT — Form Edit Personel
    // GET /admin/personel/edit/{uuid}
    // ──────────────────────────────────────────────────────────

    public function edit(string $uuid): string
    {
        $personel = $this->personelModel->findByUuid($uuid);

        if (!$personel) {
            return redirect()->to('/admin/personel')
                            ->with('error', 'Personel tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Personel',
            'personel'   => $personel,
            'validation' => \Config\Services::validation(),
            'pangkat'    => $this->pangkatModel->getGroupedForDropdown(),
            'satker'     => $this->satkerModel->getForDropdown(),
        ];

        return view('personel/edit', $data);
    }

    // ──────────────────────────────────────────────────────────
    // UPDATE — Simpan Perubahan Personel
    // POST /admin/personel/update/{uuid}
    // ──────────────────────────────────────────────────────────
    public function update(string $uuid)
    {
        $personel = $this->personelModel->findByUuid($uuid);

        if (!$personel) {
            return redirect()->to('/admin/personel')
                            ->with('error', 'Personel tidak ditemukan.');
        }

        $rules = [
            'nrp'        => "required|max_length[20]|is_unique[personel.nrp,id,{$personel['id']}]",
            'nama'       => 'required|min_length[3]|max_length[100]',
            'pangkat_id' => 'required|is_natural_no_zero',
            'satker_id'  => 'required|is_natural_no_zero',
            'jabatan'    => 'permit_empty|max_length[100]',
        ];

        // validasi foto hanya jika upload file baru
        $foto = $this->request->getFile('foto');

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $rules['foto'] =
                'max_size[foto,2048]'
                . '|is_image[foto]'
                . '|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]';
        }

        $messages = [
            'nrp' => [
                'is_unique' => 'NRP sudah digunakan personel lain.',
            ],

            'pangkat_id' => [
                'required' => 'Pangkat wajib dipilih.',
            ],

            'satker_id' => [
                'required' => 'Satker wajib dipilih.',
            ],

            'foto' => [
                'max_size' => 'Ukuran foto maksimal 2MB.',
                'is_image' => 'File harus berupa gambar.',
                'mime_in'  => 'Format foto harus JPG, PNG, atau WebP.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {

            // sementara aktifkan kalau masih troubleshooting
            //dd($this->validator->getErrors());

            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        // default pakai foto lama
        $fotoName = $personel['foto'];

        // upload foto baru
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {

            // hapus foto lama
            if (
                !empty($personel['foto']) &&
                file_exists(FCPATH . 'uploads/personel/' . $personel['foto'])
            ) {
                unlink(FCPATH . 'uploads/personel/' . $personel['foto']);
            }

            $fotoName = $foto->getRandomName();

            $foto->move(
                FCPATH . 'uploads/personel/',
                $fotoName
            );
        }

        $this->personelModel->update($personel['id'], [
            'nrp'        => strtoupper(trim($this->request->getPost('nrp'))),
            'nama'       => trim($this->request->getPost('nama')),
            'pangkat_id' => (int) $this->request->getPost('pangkat_id'),
            'satker_id'  => (int) $this->request->getPost('satker_id'),
            'jabatan'    => trim($this->request->getPost('jabatan')),
            'foto'       => $fotoName,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/personel/detail/' . $uuid)
                        ->with('success', 'Data personel berhasil diperbarui.');
    }

    // ──────────────────────────────────────────────────────────
    // DELETE — Hapus Personel
    // POST /admin/personel/delete/{uuid}
    // ──────────────────────────────────────────────────────────
    public function delete(string $uuid)
    {
        $personel = $this->personelModel->findByUuid($uuid);

        if (!$personel) {
            return redirect()->to('/admin/personel')
                            ->with('error', 'Personel tidak ditemukan.');
        }

        // Cek apakah masih punya kendaraan
        $kendaraan = $this->kendaraanModel->where('personel_id', $personel['id'])->first();
        if ($kendaraan) {
            return redirect()->to('/admin/personel')
                            ->with('error', 'Personel tidak dapat dihapus karena masih memiliki data kendaraan.');
        }

        // Hapus foto jika ada
        if ($personel['foto'] && file_exists(FCPATH . 'uploads/personel/' . $personel['foto'])) {
            unlink(FCPATH . 'uploads/personel/' . $personel['foto']);
        }

        $this->personelModel->delete($personel['id']);

        return redirect()->to('/admin/personel')
                        ->with('success', 'Data personel berhasil dihapus.');
    }

    // ──────────────────────────────────────────────────────────
    // TOGGLE STATUS — Aktif / Nonaktifkan
    // POST /admin/personel/toggle/{uuid}
    // ──────────────────────────────────────────────────────────
    public function toggle(string $uuid)
    {
        $personel = $this->personelModel->findByUuid($uuid);

        if (!$personel) {
            return redirect()->to('/admin/personel')
                            ->with('error', 'Personel tidak ditemukan.');
        }

        $newStatus = $personel['status'] === 'aktif' ? 'nonaktif' : 'aktif';
        $label     = $newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';

        $this->personelModel->update($personel['id'], [
            'status'     => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/personel')
                        ->with('success', "Personel berhasil {$label}.");
    }

    public function findDetailById(int $id): ?array
    {
        return $this->db->table('personel p')
            ->select('p.*,
                pk.nama_pangkat,
                pk.pangkat_singkat,
                pk.kelompok,
                sk.kode_satker,
                sk.nama_singkat as satker_singkat
            ')
            ->join('pangkat pk', 'pk.id = p.pangkat_id', 'left')
            ->join('satuan_kerja sk', 'sk.id = p.satker_id', 'left')
            ->where('p.id', $id)
            ->get()
            ->getRowArray();
    }

    // ──────────────────────────────────────────────────────────
    // IMPORT EXCEL — Form Upload
    // GET /admin/personel/importExcel
    // ──────────────────────────────────────────────────────────
    public function importExcel(): string
    {
        $data = [
            'title' => 'Import Personel via Excel',
        ];
        return view('personel/import_excel', $data);
    }

    // ──────────────────────────────────────────────────────────
    // PROSES IMPORT — Handle Upload & Parsing
    // POST /admin/personel/prosesImport
    // ──────────────────────────────────────────────────────────
    public function prosesImport()
    {
        $file = $this->request->getFile('file_excel');

        // Validasi file
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau tidak ditemukan.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xlsx', 'xls'])) {
            return redirect()->back()->with('error', 'Format file harus .xlsx atau .xls');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return redirect()->back()->with('error', 'Ukuran file maksimal 5MB.');
        }

        // Pindahkan file sementara
        $tmpPath = WRITEPATH . 'uploads/excel_import/';
        if (!is_dir($tmpPath)) mkdir($tmpPath, 0777, true);

        $tmpName = $file->getRandomName();
        $file->move($tmpPath, $tmpName);

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tmpPath . $tmpName);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, false);
        } catch (\Exception $e) {
            unlink($tmpPath . $tmpName);
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        // Ambil semua pangkat & satker untuk mapping nama → id
        $pangkatList = $this->pangkatModel->getForDropdown();
        $satkerList  = $this->satkerModel->getForDropdown();

        // Buat lookup: lowercase nama/singkat → id
        $pangkatMap = [];
        foreach ($pangkatList as $p) {
            $pangkatMap[strtolower(trim($p['pangkat_singkat']))] = $p['id'];
            $pangkatMap[strtolower(trim($p['nama_pangkat']))]    = $p['id'];
        }

        $satkerMap = [];
        foreach ($satkerList as $s) {
            $satkerMap[strtolower(trim($s['kode_satker']))]  = $s['id'];
            $satkerMap[strtolower(trim($s['nama_singkat']))] = $s['id'];
        }

        $berhasil = 0;
        $dilewati = 0;
        $gagal    = [];

        // Mulai dari baris ke-2 (baris 1 = header)
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // skip header

            // Kolom: A=NRP, B=Nama, C=Pangkat, D=Jabatan, E=Satker, F=Status
            $nrp     = strtoupper(trim($row[0] ?? ''));
            $nama    = trim($row[1] ?? '');
            $pangkat = strtolower(trim($row[2] ?? ''));
            $jabatan = trim($row[3] ?? '');
            $satker  = strtolower(trim($row[4] ?? ''));
            $status  = strtolower(trim($row[5] ?? 'aktif'));

            $rowNum = $index + 1;

            // Skip baris kosong
            if (empty($nrp) && empty($nama)) continue;

            // Validasi wajib
            if (empty($nrp)) {
                $gagal[] = "Baris {$rowNum}: NRP kosong.";
                continue;
            }
            if (empty($nama)) {
                $gagal[] = "Baris {$rowNum}: Nama kosong (NRP: {$nrp}).";
                continue;
            }

            // Cek NRP duplikat di DB → skip
            $existing = $this->personelModel->where('nrp', $nrp)->first();
            if ($existing) {
                $dilewati++;
                continue;
            }

            // Mapping pangkat
            $pangkatId = $pangkatMap[$pangkat] ?? null;
            if (!$pangkatId) {
                $gagal[] = "Baris {$rowNum}: Pangkat '{$row[2]}' tidak ditemukan (NRP: {$nrp}).";
                continue;
            }

            // Mapping satker — kalau kosong pakai default 'Belum Ditentukan'
            $satkerId = null;
            if (!empty($satker)) {
                $satkerId = $satkerMap[$satker] ?? null;
                if (!$satkerId) {
                    $gagal[] = "Baris {$rowNum}: Satker '{$row[4]}' tidak ditemukan (NRP: {$nrp}).";
                    continue;
                }
            } else {
                // Pakai satker default
                $satkerId = $satkerMap['belum ditentukan'] ?? $satkerMap['unknown'] ?? null;
                if (!$satkerId) {
                    $gagal[] = "Baris {$rowNum}: Satker kosong & data default 'Belum Ditentukan' tidak ada di DB (NRP: {$nrp}).";
                    continue;
                }
            }

            // Validasi status
            if (!in_array($status, ['aktif', 'nonaktif'])) {
                $status = 'aktif';
            }

            // Insert
            try {
                $this->personelModel->insert([
                    'uuid'       => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                    'nrp'        => $nrp,
                    'nama'       => $nama,
                    'pangkat_id' => $pangkatId,
                    'satker_id'  => $satkerId,
                    'jabatan'    => $jabatan ?: null,
                    'foto'       => null,
                    'status'     => $status,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $berhasil++;
            } catch (\Exception $e) {
                $gagal[] = "Baris {$rowNum}: Gagal disimpan (NRP: {$nrp}).";
            }
        }

        // Hapus file temporary
        unlink($tmpPath . $tmpName);

        return redirect()->to('/admin/personel')
            ->with('import_result', [
                'berhasil' => $berhasil,
                'dilewati' => $dilewati,
                'gagal'    => $gagal,
            ]);
    }

    // ──────────────────────────────────────────────────────────
    // DOWNLOAD TEMPLATE EXCEL
    // GET /admin/personel/templateExcel
    // ──────────────────────────────────────────────────────────
    public function templateExcel()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Personel');

        // Header
        $headers = ['NRP', 'Nama', 'Pangkat', 'Jabatan', 'Satker', 'Status'];
        foreach ($headers as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF198754');
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
        }

        // Contoh data
        $contoh = [
            ['1234567890', 'Budi Santoso', 'Lettu', 'Komandan Regu', 'KOARMADA', 'aktif'],
            ['0987654321', 'Agus Setiawan', 'Serda', 'Anggota', 'LANAL', 'aktif'],
        ];
        foreach ($contoh as $i => $row) {
            foreach ($row as $col => $val) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . ($i + 2);
                $sheet->setCellValue($cell, $val);
            }
        }

        // Auto width kolom
        foreach (range(1, 6) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        // Sheet ke-2: Referensi Pangkat
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Ref Pangkat');
        $sheet2->setCellValue('A1', 'Pangkat Singkat');
        $sheet2->setCellValue('B1', 'Nama Pangkat');
        $sheet2->getStyle('A1:B1')->getFont()->setBold(true);

        $pangkatList = $this->pangkatModel->getForDropdown();
        foreach ($pangkatList as $i => $p) {
            $sheet2->setCellValue('A' . ($i + 2), $p['pangkat_singkat']);
            $sheet2->setCellValue('B' . ($i + 2), $p['nama_pangkat']);
        }
        $sheet2->getColumnDimension('A')->setAutoSize(true);
        $sheet2->getColumnDimension('B')->setAutoSize(true);

        // Sheet ke-3: Referensi Satker
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Ref Satker');
        $sheet3->setCellValue('A1', 'Kode Satker');
        $sheet3->setCellValue('B1', 'Nama Singkat');
        $sheet3->getStyle('A1:B1')->getFont()->setBold(true);

        $satkerList = $this->satkerModel->getForDropdown();
        foreach ($satkerList as $i => $s) {
            $sheet3->setCellValue('A' . ($i + 2), $s['kode_satker']);
            $sheet3->setCellValue('B' . ($i + 2), $s['nama_singkat']);
        }
        $sheet3->getColumnDimension('A')->setAutoSize(true);
        $sheet3->getColumnDimension('B')->setAutoSize(true);

        // Output
        $spreadsheet->setActiveSheetIndex(0);
        $filename = 'template_import_personel.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    
}