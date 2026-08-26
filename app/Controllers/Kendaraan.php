<?php

namespace App\Controllers;

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

class Kendaraan extends BaseController
{
    protected KendaraanModel     $kendaraanModel;
    protected KendaraanFotoModel $fotoModel;
    protected PersonelModel      $personelModel;
    protected QRCodeModel        $qrcodeModel;

    // Batas maksimal foto galeri per kendaraan
    const MAX_FOTO = 6;

    public function __construct()
    {
        $this->kendaraanModel = new KendaraanModel();
        $this->fotoModel      = new KendaraanFotoModel();
        $this->personelModel  = new PersonelModel();
        $this->qrcodeModel  = new QRCodeModel();
    }

    // ──────────────────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        $byJenis = $this->kendaraanModel->countByJenis();

        // Inject thumbnail ke setiap kendaraan
        $kendaraan = $this->kendaraanModel->getAllWithPersonel();
        foreach ($kendaraan as &$k) {
            $k['thumbnail'] = $this->fotoModel->getThumbnail($k['id']);
        }

        $data = [
            'title'       => 'Management Kendaraan',
            'kendaraan'   => $kendaraan,
            'total_motor' => $byJenis['motor'],
            'total_mobil' => $byJenis['mobil'],
        ];

        return view('kendaraan/index', $data);
    }

    // ──────────────────────────────────────────────────────────
    // CREATE
    // ──────────────────────────────────────────────────────────
    public function create(): string
    {
        $personelUuid     = $this->request->getGet('personel');
        $selectedPersonel = null;

        if ($personelUuid) {
            $selectedPersonel = $this->personelModel->findByUuid($personelUuid);
        }

        $data = [
            'title'            => 'Tambah Kendaraan',
            'personelList'     => $this->personelModel->getAktif(),
            'selectedPersonel' => $selectedPersonel,
            'nextKode'         => $this->kendaraanModel->generateKode(),
            'maxFoto'          => self::MAX_FOTO,
        ];

        return view('kendaraan/create', $data);
    }

    // ──────────────────────────────────────────────────────────
    // STORE
    // ──────────────────────────────────────────────────────────
    public function store()
    {
        $rules = [
            'personel_id'     => 'required|is_natural_no_zero',
            'plat_nomor'      => 'required|max_length[20]|is_unique[kendaraan.plat_nomor]',
            'jenis'           => 'required|in_list[motor,mobil]',
            'merk'            => 'permit_empty|max_length[50]',
            'model'           => 'permit_empty|max_length[50]',
            'warna'           => 'permit_empty|max_length[30]',
            'nomor_rangka'    => 'permit_empty|max_length[50]',
            'nomor_mesin'     => 'permit_empty|max_length[50]',
            'nomor_bpkb'      => 'permit_empty|max_length[50]',
            'nomor_stnk'      => 'permit_empty|max_length[50]',
            'tahun_kendaraan' => 'permit_empty|integer|greater_than[1900]',
            'foto_stnk'       => 'permit_empty|uploaded[foto_stnk]|max_size[foto_stnk,3072]|is_image[foto_stnk]',
        ];

        $messages = [
            'plat_nomor'  => ['is_unique' => 'Plat nomor sudah terdaftar.'],
            'foto_stnk'   => ['max_size'  => 'Foto STNK maksimal 3MB.'],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        // Handle upload foto STNK
        $fotoStnkName = null;
        $fotoStnk     = $this->request->getFile('foto_stnk');
        if ($fotoStnk && $fotoStnk->isValid() && !$fotoStnk->hasMoved()) {
            $fotoStnkName = 'stnk_' . $fotoStnk->getRandomName();
            $fotoStnk->move(FCPATH . 'uploads/kendaraan/', $fotoStnkName);
        }

        // Simpan data kendaraan
        $kendaraanId = $this->kendaraanModel->insert([
            'uuid'            => Uuid::uuid4()->toString(),
            'kendaraan_kode'  => $this->kendaraanModel->generateKode(),
            'personel_id'     => $this->request->getPost('personel_id'),
            'plat_nomor'      => strtoupper(trim($this->request->getPost('plat_nomor'))),
            'jenis'           => $this->request->getPost('jenis'),
            'merk'            => trim($this->request->getPost('merk')),
            'model'           => trim($this->request->getPost('model')),
            'warna'           => trim($this->request->getPost('warna')),
            'nomor_rangka'    => strtoupper(trim($this->request->getPost('nomor_rangka'))),
            'nomor_mesin'     => strtoupper(trim($this->request->getPost('nomor_mesin'))),
            'nomor_bpkb'      => trim($this->request->getPost('nomor_bpkb')),
            'nomor_stnk'      => trim($this->request->getPost('nomor_stnk')),
            'tahun_kendaraan' => $this->request->getPost('tahun_kendaraan') ?: null,
            'foto_stnk'       => $fotoStnkName,
            'status'          => 'aktif',
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ], true);

        // Handle upload multi foto galeri
        $this->handleUploadGaleri($kendaraanId);

        // Redirect ke detail personel jika ada
        $personelId = $this->request->getPost('personel_id');
        $personel   = $this->personelModel->find($personelId);

        if ($personel) {
            return redirect()->to('/admin/personel/detail/' . $personel['uuid'])
                            ->with('success', 'Kendaraan berhasil ditambahkan.');
        }

        return redirect()->to('/admin/kendaraan')
                        ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    // ──────────────────────────────────────────────────────────
    // EDIT
    // ──────────────────────────────────────────────────────────
    public function edit(string $uuid): string
    {
        $kendaraan = $this->kendaraanModel->getDetailByUuid($uuid);

        if (!$kendaraan) {
            return redirect()->to('/admin/kendaraan')
                            ->with('error', 'Kendaraan tidak ditemukan.');
        }

        $galeri      = $this->fotoModel->getByKendaraan($kendaraan['id']);
        $fotoTersisa = self::MAX_FOTO - count($galeri);

        $data = [
            'title'        => 'Edit Kendaraan',
            'kendaraan'    => $kendaraan,
            'galeri'       => $galeri,
            'fotoTersisa'  => max(0, $fotoTersisa),
            'maxFoto'      => self::MAX_FOTO,
            'personelList' => $this->personelModel->getAktif(),
        ];

        return view('kendaraan/edit', $data);
    }

    // ──────────────────────────────────────────────────────────
    // UPDATE
    // ──────────────────────────────────────────────────────────
    public function update(string $uuid)
    {
        $kendaraan = $this->kendaraanModel->findByUuid($uuid);

        if (!$kendaraan) {
            return redirect()->to('/admin/kendaraan')
                            ->with('error', 'Kendaraan tidak ditemukan.');
        }

        $rules = [
            'personel_id'     => 'required|is_natural_no_zero',
            'plat_nomor'      => "required|max_length[20]|is_unique[kendaraan.plat_nomor,id,{$kendaraan['id']}]",
            'jenis'           => 'required|in_list[motor,mobil]',
            'merk'            => 'permit_empty|max_length[50]',
            'model'           => 'permit_empty|max_length[50]',
            'warna'           => 'permit_empty|max_length[30]',
            'nomor_rangka'    => 'permit_empty|max_length[50]',
            'nomor_mesin'     => 'permit_empty|max_length[50]',
            'nomor_bpkb'      => 'permit_empty|max_length[50]',
            'nomor_stnk'      => 'permit_empty|max_length[50]',
            'tahun_kendaraan' => 'permit_empty|integer|greater_than[1900]',
            'foto_stnk'       => 'permit_empty|uploaded[foto_stnk]|max_size[foto_stnk,3072]|is_image[foto_stnk]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        // Handle foto STNK baru
        $fotoStnkName = $kendaraan['foto_stnk'];
        $fotoStnk     = $this->request->getFile('foto_stnk');

        if ($fotoStnk && $fotoStnk->isValid() && !$fotoStnk->hasMoved()) {
            // Hapus foto STNK lama
            if ($kendaraan['foto_stnk'] && file_exists(FCPATH . 'uploads/kendaraan/' . $kendaraan['foto_stnk'])) {
                unlink(FCPATH . 'uploads/kendaraan/' . $kendaraan['foto_stnk']);
            }
            $fotoStnkName = 'stnk_' . $fotoStnk->getRandomName();
            $fotoStnk->move(FCPATH . 'uploads/kendaraan/', $fotoStnkName);
        }

        $this->kendaraanModel->update($kendaraan['id'], [
            'personel_id'     => $this->request->getPost('personel_id'),
            'plat_nomor'      => strtoupper(trim($this->request->getPost('plat_nomor'))),
            'jenis'           => $this->request->getPost('jenis'),
            'merk'            => trim($this->request->getPost('merk')),
            'model'           => trim($this->request->getPost('model')),
            'warna'           => trim($this->request->getPost('warna')),
            'nomor_rangka'    => strtoupper(trim($this->request->getPost('nomor_rangka'))),
            'nomor_mesin'     => strtoupper(trim($this->request->getPost('nomor_mesin'))),
            'nomor_bpkb'      => trim($this->request->getPost('nomor_bpkb')),
            'nomor_stnk'      => trim($this->request->getPost('nomor_stnk')),
            'tahun_kendaraan' => $this->request->getPost('tahun_kendaraan') ?: null,
            'foto_stnk'       => $fotoStnkName,
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        // Handle tambah foto galeri baru
        $this->handleUploadGaleri($kendaraan['id']);

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
    // ──────────────────────────────────────────────────────────
    public function delete(string $uuid)
    {
        $kendaraan = $this->kendaraanModel->findByUuid($uuid);

        if (!$kendaraan) {
            return redirect()->to('/admin/kendaraan')
                            ->with('error', 'Kendaraan tidak ditemukan.');
        }

        // ── Cek apakah masih ada QR aktif ────────────────────
        $qrModel = new \App\Models\QRCodeModel();
        $qrAktif = $qrModel->findAktifByKendaraan($kendaraan['id']);

        if ($qrAktif) {
            return redirect()->to('/admin/kendaraan')
                            ->with('error_qr',
                                "Kendaraan <strong>{$kendaraan['plat_nomor']}</strong> tidak dapat dihapus " .
                                "karena masih memiliki QR Code aktif. " .
                                "Nonaktifkan QR Code terlebih dahulu sebelum menghapus kendaraan."
                            );
        }

        // ── Hapus QR nonaktif yang terkait ───────────────────
        $qrModel->deleteByKendaraan($kendaraan['id']); // <-- TAMBAH INI

        // Hapus semua foto galeri
        $this->fotoModel->deleteByKendaraan($kendaraan['id']);

        // Hapus foto STNK
        if ($kendaraan['foto_stnk'] &&
            file_exists(FCPATH . 'uploads/kendaraan/' . $kendaraan['foto_stnk'])) {
            unlink(FCPATH . 'uploads/kendaraan/' . $kendaraan['foto_stnk']);
        }

        $this->kendaraanModel->delete($kendaraan['id']);

        return redirect()->to('/admin/kendaraan')
                        ->with('success', "Kendaraan {$kendaraan['plat_nomor']} berhasil dihapus.");
    }

    // ──────────────────────────────────────────────────────────
    // TOGGLE STATUS
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

        $this->kendaraanModel->update($kendaraan['id'], [
            'status'     => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/kendaraan')
                        ->with('success', "Kendaraan berhasil {$label}.");
    }

    // ──────────────────────────────────────────────────────────
    // PRIVATE — Handle Upload Multi Foto Galeri
    // ──────────────────────────────────────────────────────────
    private function handleUploadGaleri(int $kendaraanId): void
    {
        $fotos = $this->request->getFileMultiple('foto_galeri');
        if (!$fotos) return;

        $existingCount = $this->fotoModel->countByKendaraan($kendaraanId);
        $urutan        = $existingCount + 1;

        foreach ($fotos as $foto) {
            // Batas maksimal foto
            if ($existingCount >= self::MAX_FOTO) break;

            if (!$foto->isValid() || $foto->hasMoved()) continue;

            // Validasi tipe file
            $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($foto->getMimeType(), $allowedMime)) continue;

            // Validasi ukuran (2MB)
            if ($foto->getSize() > 2 * 1024 * 1024) continue;

            $namaFile = 'galeri_' . $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/kendaraan/', $namaFile);

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
    }

    // ──────────────────────────────────────────────────────────
    // HALAMAN IMPORT KENDARAAN
    // GET /admin/kendaraan/import
    // ──────────────────────────────────────────────────────────
    public function import(): string
    {
        $data = [
            'title' => 'Import Kendaraan via Excel',
        ];
        return view('kendaraan/import', $data);
    }

    // ──────────────────────────────────────────────────────────
    // DOWNLOAD TEMPLATE EXCEL
    // GET /admin/kendaraan/templateExcel
    // ──────────────────────────────────────────────────────────
    public function templateExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Kendaraan');
        // Header kolom
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
        // Header Style (Hijau, Bold, Center)
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
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(26);
        // Contoh Data
        $contohData = [
            ['123456', 'B 1234 ABC', 'mobil', 'Toyota', 'Avanza 1.5 G', 'Hitam', '2022', 'MHF123...', '1NR456...', 'BPKB-001', 'STNK-001'],
            ['789012', 'L 5678 XX',  'motor', 'Honda',  'Vario 160',     'Putih', '2023', 'MH1234...', 'JM5678...', 'BPKB-002', 'STNK-002'],
        ];
        $rowNum = 2;
        foreach ($contohData as $row) {
            $sheet->fromArray($row, NULL, "A{$rowNum}");
            $rowNum++;
        }
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $fileName = 'Template_Import_Kendaraan.xlsx';
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
        if (!in_array($ext, ['xlsx', 'xls'])) {
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
        $db = \Config\Database::connect();
        
        $successCount = 0;
        $errorList    = [];
        foreach ($sheetData as $rowIdx => $row) {
            if ($rowIdx === 1) continue; // Skip header
            $nrp        = trim($row['A'] ?? '');
            $platNomor  = strtoupper(trim($row['B'] ?? ''));
            $jenis      = strtolower(trim($row['C'] ?? ''));
            $merk       = trim($row['D'] ?? '');
            $model      = trim($row['E'] ?? '');
            $warna      = trim($row['F'] ?? '');
            $tahun      = !empty($row['G']) ? (int) $row['G'] : null;
            $noRangka   = strtoupper(trim($row['H'] ?? ''));
            $noMesin    = strtoupper(trim($row['I'] ?? ''));
            $noBpkb     = trim($row['J'] ?? '');
            $noStnk     = trim($row['K'] ?? '');
            if (empty($nrp) && empty($platNomor)) continue;
            // Validasi kolom wajib
            if (empty($nrp) || empty($platNomor) || empty($jenis)) {
                $errorList[] = "Baris {$rowIdx}: NRP, Plat Nomor, dan Jenis kendaraan wajib diisi.";
                continue;
            }
            if (!in_array($jenis, ['motor', 'mobil'])) {
                $errorList[] = "Baris {$rowIdx} (Plat {$platNomor}): Jenis harus 'motor' atau 'mobil'.";
                continue;
            }
            // Cari Personel by NRP
            $personel = $db->table('personel')->where('nrp', $nrp)->get()->getRowArray();
            if (!$personel) {
                $errorList[] = "Baris {$rowIdx}: Personel dengan NRP <strong>{$nrp}</strong> tidak ditemukan di database.";
                continue;
            }
            // Cek Plat Nomor duplikat
            $platExists = $db->table('kendaraan')->where('plat_nomor', $platNomor)->countAllResults();
            if ($platExists > 0) {
                $errorList[] = "Baris {$rowIdx}: Plat nomor <strong>{$platNomor}</strong> sudah terdaftar (dilewati).";
                continue;
            }
            // Auto Generate Kode & Insert
            $lastKodeNum++;
            $kodeKendaraan = 'KND-' . str_pad($lastKodeNum, 4, '0', STR_PAD_LEFT);
            $this->kendaraanModel->insert([
                'uuid'            => Uuid::uuid4()->toString(),
                'kendaraan_kode'  => $kodeKendaraan,
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
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $successCount++;
        }
        // Response
        if ($successCount > 0 && empty($errorList)) {
            return redirect()->to('/admin/kendaraan')->with('success', "Berhasil mengimpor <strong>{$successCount}</strong> data kendaraan.");
        } elseif ($successCount > 0 && !empty($errorList)) {
            return redirect()->to('/admin/kendaraan')
                            ->with('success', "Berhasil mengimpor <strong>{$successCount}</strong> kendaraan.")
                            ->with('import_errors', $errorList);
        } else {
            return redirect()->to('/admin/kendaraan/import')
                            ->with('error', "Tidak ada data yang berhasil diimpor.")
                            ->with('import_errors', $errorList);
        }
    }

    // ──────────────────────────────────────────────────────────
    // AJAX: Cari personel by NRP
    // ──────────────────────────────────────────────────────────
    public function cariPersonel()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $nrp = trim($this->request->getGet('nrp'));

        if (empty($nrp)) {
            return $this->response->setJSON(['found' => false, 'message' => 'NRP tidak boleh kosong.']);
        }

        $personelModel = new \App\Models\PersonelModel();

        $result = $personelModel->db->table('personel p')
            ->select('p.id, p.uuid, p.nrp, p.nama, p.jabatan, p.foto, p.status,
                    pk.pangkat_singkat, pk.nama_pangkat,
                    sk.nama_singkat as satker_singkat')
            ->join('pangkat pk',     'pk.id = p.pangkat_id', 'left')
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
            'found'   => true,
            'personel' => [
                'id'             => $result['id'],
                'nrp'            => $result['nrp'],
                'nama'           => $result['nama'],
                'pangkat'        => $result['pangkat_singkat'],
                'nama_pangkat'   => $result['nama_pangkat'],
                'jabatan'        => $result['jabatan'] ?? '-',
                'satker'         => $result['satker_singkat'],
                'foto'           => $result['foto']
                                        ? base_url('uploads/personel/' . $result['foto'])
                                        : base_url('assets/img/no-avatar.png'),
            ],
        ]);
    }
}