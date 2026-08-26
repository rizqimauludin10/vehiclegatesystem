<?php

namespace App\Controllers;

use App\Models\QRCodeModel;
use App\Models\KendaraanModel;
use App\Models\KendaraanFotoModel;
use App\Models\PersonelModel;

use chillerlan\QRCode\QRCode as QRGenerator;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;

use Ramsey\Uuid\Uuid;

/**
 * QRCode Controller
 *
 * QR Code sekarang per KENDARAAN (bukan per personel)
 * 1 kendaraan = 1 QR aktif
 * Regenerate otomatis revoke QR lama
 *
 * Routes prefix: /admin/qrcode
 */
class QRCode extends BaseController
{
    protected QRCodeModel      $qrModel;
    protected KendaraanModel   $kendaraanModel;
    protected KendaraanFotoModel $fotoModel;
    protected PersonelModel    $personelModel;

    public function __construct()
    {
        $this->qrModel        = new QRCodeModel();
        $this->kendaraanModel = new KendaraanModel();
        $this->fotoModel      = new KendaraanFotoModel();
        $this->personelModel  = new PersonelModel();
    }

    // ──────────────────────────────────────────────────────────
    // INDEX — Daftar Semua QR Code
    // GET /admin/qrcode
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        $db = \Config\Database::connect();
        
        $data = [
            'title'              => 'Management QR Code',
            'qrList'             => $this->qrModel->getAllWithDetail(),
            'kendaraanList'      => $this->kendaraanModel->getAllWithPersonel(),
            'satkerList'         => $db->table('satuan_kerja')->orderBy('nama_singkat', 'ASC')->get()->getResultArray(),
            'totalQrAktif'       => $this->qrModel->countAktif(),
            'totalKendaraan'     => $this->kendaraanModel->countAktif(),
            'kendaraanTanpaQR'   => count($this->qrModel->getKendaraanTanpaQR()),
        ];

        return view('qrcode/index', $data);
    }

    // ──────────────────────────────────────────────────────────
    // DOWNLOAD ZIP — Download Semua QR Satker (Auto Generate jika belum ada)
    // GET /admin/qrcode/download-satker/{satker_id}
    // ──────────────────────────────────────────────────────────
    public function downloadZipBySatker(int $satkerId)
    {
        $db = \Config\Database::connect();
        $satker = $db->table('satuan_kerja')->where('id', $satkerId)->get()->getRowArray();
        if (!$satker) {
            return redirect()->to('/admin/qrcode')->with('error', 'Satuan kerja tidak ditemukan.');
        }
        // Ambil semua kendaraan aktif milik satker ini
        $kendaraanList = $this->qrModel->getKendaraanWithQrBySatker($satkerId);
        if (empty($kendaraanList)) {
            return redirect()->to('/admin/qrcode')->with('error', "Tidak ada data kendaraan aktif pada satker {$satker['nama_singkat']}.");
        }
        // Siapkan nama file zip & temporary file
        $satkerNameClean = preg_replace('/[^A-Za-z0-9_\-]/', '_', $satker['nama_singkat']);
        $zipFileName     = "QR_Satker_{$satkerNameClean}_" . date('Ymd_His') . ".zip";
        $tempZipPath     = WRITEPATH . 'uploads/temp_' . uniqid() . '.zip';
        if (!is_dir(WRITEPATH . 'uploads')) {
            mkdir(WRITEPATH . 'uploads', 0777, true);
        }
        $zip = new \ZipArchive();
        if ($zip->open($tempZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            return redirect()->to('/admin/qrcode')->with('error', 'Gagal membuat file ZIP di server.');
        }
        $userId = session()->get('user_id');
        // Loop setiap kendaraan
        foreach ($kendaraanList as $row) {
            $kodeUnik = $row['kode_unik'];
            // JIKA BELUM PUNYA QR AKTIF → OTOMATIS GENERATE & SIMPAN KE DATABASE
            if (empty($kodeUnik) || empty($row['qr_id'])) {
                $kodeUnik = $this->qrModel->generateKodeUnik();
                
                $this->qrModel->insert([
                    'uuid'         => Uuid::uuid4()->toString(),
                    'kendaraan_id' => $row['kendaraan_id'],
                    'kode_unik'    => $kodeUnik,
                    'status'       => 'aktif',
                    'generated_by' => $userId,
                    'generated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            // Format nama folder: "[NRP] - [Nama Personel]"
            $nrpClean  = !empty($row['nrp']) ? preg_replace('/[^A-Za-z0-9_\-]/', '', $row['nrp']) : 'NON-NRP';
            $namaClean = preg_replace('/[^A-Za-z0-9_\- ]/', '', $row['personel_nama']);
            $folderPersonel = trim("{$nrpClean} - {$namaClean}");
            // Format nama file: "QR_[PLAT_NOMOR].png"
            $platClean = preg_replace('/[^A-Za-z0-9_\-]/', '_', $row['plat_nomor'] ?? 'UNKNOWN');
            $fileName  = "QR_{$platClean}.png";
            // Buat gambar QR Code binary
            $imgBinary = $this->generateQRImageBinary($kodeUnik, $row['plat_nomor'] ?? '');
            // Masukkan ke folder personel di dalam ZIP
            $zip->addFromString("{$folderPersonel}/{$fileName}", $imgBinary);
        }
        $zip->close();
        // Baca file ZIP lalu hapus file temp di server
        $zipContent = file_get_contents($tempZipPath);
        @unlink($tempZipPath);
        // Download langsung
        return $this->response
                    ->setHeader('Content-Type', 'application/zip')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $zipFileName . '"')
                    ->setHeader('Content-Length', strlen($zipContent))
                    ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->setBody($zipContent);
    }

    // ──────────────────────────────────────────────────────────
    // GENERATE — Generate QR Baru untuk Kendaraan
    // POST /admin/qrcode/generate/{kendaraan_uuid}
    // ──────────────────────────────────────────────────────────
    public function generate(string $kendaraanUuid)
    {
        $kendaraan = $this->kendaraanModel->findByUuid($kendaraanUuid);

        if (!$kendaraan) {
            return redirect()->to('/admin/qrcode')
                             ->with('error', 'Kendaraan tidak ditemukan.');
        }

        // Revoke semua QR lama milik kendaraan ini
        $this->qrModel->revokeByKendaraan($kendaraan['id']);

        // Generate kode unik baru
        $kodeUnik = $this->qrModel->generateKodeUnik();

        // Simpan QR baru
        $this->qrModel->insert([
            'uuid'         => Uuid::uuid4()->toString(),
            'kendaraan_id' => $kendaraan['id'],
            'kode_unik'    => $kodeUnik,
            'status'       => 'aktif',
            'generated_by' => session()->get('user_id'),
            'generated_at' => date('Y-m-d H:i:s'),
        ]);

        // Ambil info kendaraan untuk pesan sukses
        $detail = $this->kendaraanModel->getDetailByUuid($kendaraanUuid);
        $label  = $detail
            ? "{$detail['plat_nomor']} ({$detail['personel_nama']})"
            : $kendaraan['plat_nomor'];

        return redirect()->to('/admin/qrcode')
                        ->with('success', "QR Code berhasil di-generate untuk kendaraan {$label}.");
    }

    // ──────────────────────────────────────────────────────────
// AJAX: Cari kendaraan by plat nomor
// GET /admin/qrcode/cariKendaraan
// ──────────────────────────────────────────────────────────
public function cariKendaraan()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }

    $plat = strtoupper(trim($this->request->getGet('plat')));

    if (empty($plat)) {
        return $this->response->setJSON(['found' => false, 'message' => 'Plat nomor tidak boleh kosong.']);
    }

    $result = $this->kendaraanModel->db->table('kendaraan k')
        ->select('k.id, k.uuid, k.plat_nomor, k.jenis, k.merk, k.model,
                  k.warna, k.kendaraan_kode, k.status,
                  p.nama as personel_nama, p.nrp, p.foto as personel_foto,
                  pk.pangkat_singkat,
                  sk.nama_singkat as satker_singkat')
        ->join('personel p',     'p.id = k.personel_id', 'left')
        ->join('pangkat pk',     'pk.id = p.pangkat_id', 'left')
        ->join('satuan_kerja sk', 'sk.id = p.satker_id',  'left')
        ->where('k.plat_nomor', $plat)
        ->get()
        ->getRowArray();

    if (!$result) {
        return $this->response->setJSON(['found' => false, 'message' => 'Kendaraan dengan plat nomor tersebut tidak ditemukan.']);
    }

    if ($result['status'] !== 'aktif') {
        return $this->response->setJSON(['found' => false, 'message' => 'Kendaraan tidak aktif.']);
    }

    // Cek apakah sudah punya QR aktif
    $qrAktif = $this->qrModel->findAktifByKendaraan($result['id']);

    return $this->response->setJSON([
        'found'      => true,
        'hasQR'      => $qrAktif ? true : false,
        'kendaraan'  => [
            'uuid'          => $result['uuid'],
            'plat_nomor'    => $result['plat_nomor'],
            'jenis'         => $result['jenis'],
            'merk'          => $result['merk'] ?? '',
            'model'         => $result['model'] ?? '',
            'warna'         => $result['warna'] ?? '',
            'kendaraan_kode'=> $result['kendaraan_kode'],
            'personel_nama' => $result['personel_nama'],
            'pangkat'       => $result['pangkat_singkat'],
            'nrp'           => $result['nrp'],
            'foto_personel' => $result['personel_foto']
                                ? base_url('uploads/personel/' . $result['personel_foto'])
                                : base_url('assets/img/no-avatar.png'),
        ],
    ]);
}

    // ──────────────────────────────────────────────────────────
    // DOWNLOAD — Download QR Code sebagai PNG
    // GET /admin/qrcode/download/{qr_uuid}
    // ──────────────────────────────────────────────────────────
    public function download(string $qrUuid)
    {
        $qr = $this->qrModel->findByUuid($qrUuid);

        if (!$qr || $qr['status'] !== 'aktif') {
            return redirect()->to('/admin/qrcode')
                            ->with('error', 'QR Code tidak ditemukan atau sudah tidak aktif.');
        }

        $kendaraan = $this->kendaraanModel->getDetailByUuid(
            $this->kendaraanModel->find($qr['kendaraan_id'])['uuid']
        );

        if (!$kendaraan) {
            return redirect()->to('/admin/qrcode')
                            ->with('error', 'Data kendaraan tidak ditemukan.');
        }

        // Generate QR binary PNG langsung
        $imgData  = $this->generateQRImageBinary($qr['kode_unik'], $kendaraan['plat_nomor'] ?? '');

        // Nama file: QR_{plat_nomor}_{nama_personel}.png
        $plat    = preg_replace('/[^A-Za-z0-9]/', '_', $kendaraan['plat_nomor'] ?? 'unknown');
        $nama    = preg_replace('/[^A-Za-z0-9_]/', '_', $kendaraan['personel_nama'] ?? 'personel');
        $filename = "QR_{$plat}_{$nama}.png";

        return $this->response
                    ->setHeader('Content-Type', 'image/png')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->setHeader('Content-Length', strlen($imgData))
                    ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->setBody($imgData);
    }

    // ──────────────────────────────────────────────────────────
    // REVOKE — Nonaktifkan QR
    // POST /admin/qrcode/revoke/{qr_uuid}
    // ──────────────────────────────────────────────────────────
    public function revoke(string $qrUuid)
    {
        $qr = $this->qrModel->findByUuid($qrUuid);

        if (!$qr) {
            return redirect()->to('/admin/qrcode')
                             ->with('error', 'QR Code tidak ditemukan.');
        }

        $this->qrModel->update($qr['id'], ['status' => 'nonaktif']);

        return redirect()->to('/admin/qrcode')
                         ->with('success', 'QR Code berhasil dinonaktifkan.');
    }

    // ──────────────────────────────────────────────────────────
    // PRIVATE — Generate QR Image Base64 (untuk print/tampil)
    // ──────────────────────────────────────────────────────────
    private function generateQRImage(string $kodeUnik): string
    {
        $options = new QROptions;
        $options->outputInterface = QRGdImagePNG::class;
        $options->scale           = 10;
        $options->outputBase64    = true;  // ← return base64 data URI
        $options->imageTransparent = false;
        $options->bgColor         = [255, 255, 255];

        return (new QRGenerator($options))->render($kodeUnik);
        // return: data:image/png;base64,...
    }

    // ──────────────────────────────────────────────────────────
    // PRIVATE — Generate QR Binary PNG (untuk download)
    // ──────────────────────────────────────────────────────────

    private function generateQRImageBinary(string $kodeUnik, string $platNomor = ''): string
    {
        $options = new QROptions;
        $options->outputInterface  = QRGdImagePNG::class;
        $options->scale            = 15; // ← perbesar QR (sebelumnya 10)
        $options->outputBase64     = false;
        $options->returnResource   = true;
        $options->imageTransparent = false;
        $options->bgColor          = [255, 255, 255];

        /** @var \GdImage $gdImage */
        $gdImage = (new QRGenerator($options))->render($kodeUnik);

        // Ukuran QR asli
        $qrWidth  = imagesx($gdImage);
        $qrHeight = imagesy($gdImage);

        // Padding & tinggi area teks
        $padding    = 20;
        $textAreaH  = 50;

        // Canvas baru: lebih lebar (padding kiri-kanan) + tinggi area teks bawah
        $canvasW = $qrWidth  + ($padding * 2);
        $canvasH = $qrHeight + ($padding * 2) + $textAreaH;

        $canvas = imagecreatetruecolor($canvasW, $canvasH);

        // Warna
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $black = imagecolorallocate($canvas, 0,   0,   0);
        $gray  = imagecolorallocate($canvas, 100, 100, 100);

        // Background putih
        imagefill($canvas, 0, 0, $white);

        // Tempel QR di tengah canvas dengan padding
        imagecopy($canvas, $gdImage, $padding, $padding, 0, 0, $qrWidth, $qrHeight);
        imagedestroy($gdImage);

        // Tulis teks plat nomor di bawah QR
        if (!empty($platNomor)) {

            $font = FCPATH . 'vendor/fonts/inter/files/inter-latin-700-normal.woff'; // lokasi font

            $fontSize = 26; // bebas, bisa 18,20,22,24,30 dst

            // Garis pemisah
            $lineY = $qrHeight + $padding + 8;
            imageline($canvas, $padding, $lineY, $canvasW - $padding, $lineY, $gray);

            // Area untuk teks
            $areaTop    = $lineY;
            $areaBottom = $canvasH - $padding;
            $areaHeight = $areaBottom - $areaTop;

            // Hitung bounding box teks
            $bbox = imagettfbbox($fontSize, 0, $font, $platNomor);

            $textWidth  = $bbox[2] - $bbox[0];
            $textHeight = abs($bbox[7] - $bbox[1]);

            // Posisi horizontal (center)
            $textX = ($canvasW - $textWidth) / 2;

            // Posisi vertikal (benar-benar di tengah area)
            $textY = $areaTop + (($areaHeight - $textHeight) / 2) + $textHeight;

            // Tulis teks
            imagettftext(
                $canvas,
                $fontSize,
                0,
                (int)$textX,
                (int)$textY,
                $black,
                $font,
                $platNomor
            );
        }

        

        // Dump ke binary PNG
        ob_start();
        imagepng($canvas);
        $binary = ob_get_clean();
        imagedestroy($canvas);

        return $binary;
    }
}