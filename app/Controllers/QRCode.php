<?php

namespace App\Controllers;

use App\Models\QRCodeModel;
use App\Models\PersonelModel;
use App\Models\KendaraanModel;
use chillerlan\QRCode\QRCode as QRGenerator;
use chillerlan\QRCode\QROptions;
use Ramsey\Uuid\Uuid;


/**
 * QRCode Controller
 *
 * Handles QR Code generation, printing, and revocation.
 * QR is generated per PERSONEL (not per kendaraan).
 * One personel = one active QR at a time.
 * Regenerating auto-revokes the old QR.
 *
 * Routes prefix: /admin/qrcode
 */
class QRCode extends BaseController
{
    protected QRCodeModel   $qrModel;
    protected PersonelModel $personelModel;
    protected KendaraanModel $kendaraanModel;

    public function __construct()
    {
        $this->qrModel        = new QRCodeModel();
        $this->personelModel  = new PersonelModel();
        $this->kendaraanModel = new KendaraanModel();
    }

    // ──────────────────────────────────────────────────────────
    // INDEX — Daftar Semua QR Code
    // GET /admin/qrcode
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        $personelModel = new PersonelModel();

        $data = [
            'title'             => 'Management QR Code',
            'qrList'            => $this->qrModel->getAllWithPersonel(),
            'personelList'      => $personelModel->getAktif(),
            'totalQrAktif'      => $this->qrModel->countAktif(),
            'totalPersonel'     => $personelModel->countAktif(),
            'personelTanpaQR'   => count($this->qrModel->getPersonelTanpaQR()),
        ];

        return view('qrcode/index', $data);
    }

    // ──────────────────────────────────────────────────────────
    // GENERATE — Generate QR Baru untuk Personel
    // POST /admin/qrcode/generate/{personel_uuid}
    // Otomatis revoke QR lama jika ada
    // ──────────────────────────────────────────────────────────
    public function generate(string $personelUuid)
    {
        $personel = $this->personelModel->findByUuid($personelUuid);

        if (!$personel) {
            return redirect()->to('/admin/qrcode')
                             ->with('error', 'Personel tidak ditemukan.');
        }

        // Revoke semua QR lama milik personel ini
        $this->qrModel->revokeByPersonel($personel['id']);

        // Generate kode unik baru
        $kodeUnik = $this->qrModel->generateKodeUnik();

        // Simpan QR baru
        $this->qrModel->insert([
            'uuid'         => Uuid::uuid4()->toString(),
            'personel_id'  => $personel['id'],
            'kode_unik'    => $kodeUnik,
            'status'       => 'aktif',
            'generated_by' => session()->get('user_id'),
            'generated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/qrcode')
                         ->with('success', "QR Code berhasil di-generate untuk {$personel['nama']}.");
    }

    // ──────────────────────────────────────────────────────────
    // PRINT CARD — Halaman Cetak Kartu QR
    // GET /admin/qrcode/print/{qr_uuid}
    // ──────────────────────────────────────────────────────────
    public function printCard(string $qrUuid): string
    {
        $qr = $this->qrModel->findByUuid($qrUuid);

        if (!$qr || $qr['status'] !== 'aktif') {
            return redirect()->to('/admin/qrcode')
                            ->with('error', 'QR Code tidak ditemukan atau sudah tidak aktif.');
        }

        $personel   = $this->personelModel->getWithKendaraan(
            $this->personelModel->find($qr['personel_id'])['uuid']
        );

        // Generate QR image sebagai base64
        $qrImage = $this->generateQRImage($qr['kode_unik']);

        $data = [
            'title'    => 'Cetak Kartu QR',
            'qr'       => $qr,
            'personel' => $personel,
            'qrImage'  => $qrImage,
        ];

        return view('qrcode/print', $data);
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
    // PRIVATE — Generate QR Image (Base64 PNG)
    // ──────────────────────────────────────────────────────────
    private function generateQRImage(string $kodeUnik): string
    {
        $options = new QROptions;
        $options->version      = 5;
        $options->outputType   = 'png';
        $options->eccLevel     = 'H';
        $options->scale        = 10;
        $options->imageBase64  = true;
        $options->bgColor      = [255, 255, 255];
        $options->moduleValues = [
            // finder
            1536 => [0, 51, 0],
            6    => [255, 255, 255],
            // alignment
            2048 => [0, 51, 0],
            8    => [255, 255, 255],
            // timing
            3072 => [0, 51, 0],
            12   => [255, 255, 255],
            // data
            1024 => [0, 0, 0],
            4    => [255, 255, 255],
        ];

        return (new QRGenerator($options))->render($kodeUnik);
    }
    
    public function download(string $qrUuid)
    {
        $qr = $this->qrModel->findByUuid($qrUuid);

        if (!$qr || $qr['status'] !== 'aktif') {
            return redirect()->to('/admin/qrcode')
                ->with('error', 'QR Code tidak ditemukan atau sudah tidak aktif.');
        }

        $personel = $this->personelModel->find($qr['personel_id']);

        if (!$personel) {
            return redirect()->to('/admin/qrcode')
                ->with('error', 'Data personel tidak ditemukan.');
        }

        // Ambil QR SVG Base64
        $svgData = $this->generateQRImage($qr['kode_unik']);

        // Pisahkan header dan data base64
        if (strpos($svgData, ',') !== false) {
            [, $svgData] = explode(',', $svgData, 2);
        }

        // Decode menjadi SVG XML asli
        $svgData = base64_decode($svgData);

        $nrp  = $personel['nrp'] ?? 'unknown';
        $nama = preg_replace('/[^A-Za-z0-9_]/', '_', $personel['nama'] ?? 'personel');

        $filename = "QR_{$nrp}_{$nama}.svg";

        return $this->response->download(
            $filename,
            $svgData
        );
    }

    // ──────────────────────────────────────────────────────────
    // Generate QR langsung sebagai binary PNG
    // Dipakai untuk download agar tidak ada masalah encoding
    // ──────────────────────────────────────────────────────────
    private function generateQRImageBinary(string $kodeUnik): string
    {
        $options = new QROptions;
        $options->version     = 5;
        $options->outputType  = 'png';
        $options->eccLevel    = 'H';
        $options->scale       = 10;
        $options->imageBase64 = false; // ← false = raw binary, bukan base64

        return (new QRGenerator($options))->render($kodeUnik);
    }
}