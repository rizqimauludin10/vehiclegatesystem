<?php

namespace App\Controllers;

use App\Models\QRCodeModel;
use App\Models\KendaraanModel;
use App\Models\KendaraanFotoModel;
use App\Models\PersonelModel;
use App\Models\LogKeluarModel;

/**
 * Scan Controller
 *
 * QR sekarang per KENDARAAN:
 * Scan QR → dapat kendaraan_id → JOIN ke personel
 * Response: data kendaraan spesifik + data personel pemilik
 *
 * Aturan akses:
 * - QR harus aktif
 * - Kendaraan harus berstatus 'aktif'
 * - Personel pemilik harus berstatus 'aktif'
 * - Scan yang ditolak TIDAK dicatat di log dan TIDAK mengirim data personel
 *
 * Routes:
 * GET  /petugas/scan          → index (hardware scanner)
 * POST /petugas/scan/process  → proses QR (AJAX)
 * GET  /petugas/scan/monitor  → fullscreen monitor
 * GET  /petugas/scan/mobile   → mobile scanner
 */
class Scan extends BaseController
{
    protected QRCodeModel        $qrModel;
    protected KendaraanModel     $kendaraanModel;
    protected KendaraanFotoModel $fotoModel;
    protected PersonelModel      $personelModel;
    protected LogKeluarModel     $logModel;

    public function __construct()
    {
        $this->qrModel        = new QRCodeModel();
        $this->kendaraanModel = new KendaraanModel();
        $this->fotoModel      = new KendaraanFotoModel();
        $this->personelModel  = new PersonelModel();
        $this->logModel       = new LogKeluarModel();
    }

    // ──────────────────────────────────────────────────────────
    // INDEX — Halaman Scan (Hardware Scanner)
    // GET /petugas/scan
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        return view('scan/index', [
            'title' => 'Scan QR Code',
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // MONITOR — Fullscreen Monitor View
    // GET /petugas/scan/monitor
    // ──────────────────────────────────────────────────────────
    public function monitor(): string
    {
        return view('scan/monitor', [
            'base_url'    => base_url(),
            'csrf_token'  => csrf_token(),
            'csrf_hash'   => csrf_hash(),
            'process_url' => base_url('petugas/scan/process'),
            'petugas'     => session()->get('user_nama'),
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // MOBILE — Mobile Scanner View
    // GET /petugas/scan/mobile
    // ──────────────────────────────────────────────────────────
    public function mobile(): string
    {
        return view('scan/mobile', [
            'process_url' => base_url('petugas/scan/process'),
            'csrf_token'  => csrf_token(),
            'csrf_hash'   => csrf_hash(),
            'petugas'     => session()->get('user_nama'),
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // PROCESS — Proses QR dari Scanner (AJAX)
    // POST /petugas/scan/process
    //
    // Flow:
    // 1. Cari kode_unik di tabel qrcode → dapat kendaraan_id
    // 2. Ambil data kendaraan, pastikan statusnya aktif
    // 3. Ambil data personel pemilik, pastikan statusnya aktif
    // 4. Ambil foto galeri & STNK
    // 5. Simpan log_keluar (hanya untuk scan yang berhasil)
    // 6. Return JSON: kendaraan + personel
    //
    // Format jawaban ditolak:
    //   status: 'error', code, title, message, csrf_hash
    // ──────────────────────────────────────────────────────────
    public function process()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/petugas/scan');
        }

        $kodeUnik = trim((string) $this->request->getPost('kode_unik'));

        if ($kodeUnik === '') {
            return $this->reject('QR_KOSONG', 'QR Tidak Terbaca', 'Kode QR tidak boleh kosong.');
        }

        // Kode unik di database maksimal 64 karakter; yang lebih panjang pasti bukan QR sistem ini
        if (strlen($kodeUnik) > 64) {
            return $this->reject('QR_INVALID', 'QR Tidak Dikenali', 'QR Code ini bukan milik sistem VGS.');
        }

        // ── 1. Cari QR aktif ──────────────────────────────────
        $qr = $this->qrModel->findByKodeUnik($kodeUnik);

        if (!$qr) {
            return $this->reject('QR_INVALID', 'QR Tidak Dikenali', 'QR Code tidak dikenali atau sudah tidak aktif.');
        }

        // ── 2. Ambil data kendaraan ───────────────────────────
        $kendaraan = $this->kendaraanModel->find($qr['kendaraan_id']);

        if (!$kendaraan) {
            return $this->reject('KENDARAAN_TIDAK_ADA', 'Akses Ditolak', 'Data kendaraan tidak ditemukan.');
        }

        // Tolak semua status selain 'aktif' (dibekukan, atau status lain di masa depan)
        if (($kendaraan['status'] ?? '') !== 'aktif') {
            return $this->reject(
                'KENDARAAN_DIBEKUKAN',
                'Kendaraan Dibekukan',
                "Kendaraan {$kendaraan['plat_nomor']} sedang dibekukan dan tidak dapat melintas."
            );
        }

        // ── 3. Ambil data personel pemilik (sekaligus pangkat & satker) ──
        $personel = !empty($kendaraan['personel_id'])
            ? $this->personelModel->findDetailById((int) $kendaraan['personel_id'])
            : null;

        if (!$personel) {
            return $this->reject('PERSONEL_TIDAK_ADA', 'Akses Ditolak', 'Data personel pemilik tidak ditemukan.');
        }

        if (($personel['status'] ?? '') !== 'aktif') {
            return $this->reject('PERSONEL_NONAKTIF', 'Pemilik Tidak Aktif', 'Personel pemilik kendaraan ini tidak aktif.');
        }

        // ── 4. Ambil foto galeri & STNK kendaraan ────────────
        $galeri = $this->fotoModel->getByKendaraan($kendaraan['id']);

        $galeriUrls = array_map(static function ($foto) {
            return base_url('uploads/kendaraan/' . $foto['nama_file']);
        }, $galeri);

        $thumbnailUrl = null;
        if (!empty($galeri)) {
            $thumbnailUrl = base_url('uploads/kendaraan/' . $galeri[0]['nama_file']);
        } elseif (!empty($kendaraan['foto'])) {
            $thumbnailUrl = base_url('uploads/kendaraan/' . $kendaraan['foto']);
        }

        $fotoStnkUrl = !empty($kendaraan['foto_stnk'])
            ? base_url('uploads/kendaraan/' . $kendaraan['foto_stnk'])
            : null;

        // ── 5. Simpan log keluar ──────────────────────────────
        // Kendaraan tetap diizinkan lewat walau pencatatan gagal,
        // tapi kegagalannya dicatat di writable/logs untuk diperiksa.
        try {
            if (!$this->logModel->saveLog((int) $kendaraan['id'], $kodeUnik)) {
                log_message('error', '[Scan] Gagal menyimpan log keluar untuk kendaraan ID ' . $kendaraan['id']);
            }
        } catch (\Throwable $e) {
            log_message('error', '[Scan] Error menyimpan log keluar: ' . $e->getMessage());
        }

        // ── 6. Build response ─────────────────────────────────
        return $this->response->setJSON([
            'status'    => 'success',
            'scan_time' => date('d M Y, H:i:s'),
            'petugas'   => session()->get('user_nama'),

            // Data personel pemilik
            'personel'  => [
                'nama'            => $personel['nama'],
                'nrp'             => $personel['nrp'],
                'pangkat_singkat' => $personel['pangkat_singkat'] ?? '-',
                'nama_pangkat'    => $personel['nama_pangkat']    ?? '-',
                'jabatan'         => $personel['jabatan']         ?? '',
                'satker_singkat'  => $personel['satker_singkat']  ?? '',
                'satker_panjang'  => $personel['satker_panjang']  ?? '',
                'no_hp'           => $personel['no_hp']           ?? null,
                'foto'            => !empty($personel['foto'])
                    ? base_url('uploads/personel/' . $personel['foto'])
                    : null,
            ],

            // Data kendaraan spesifik yang di-scan
            'kendaraan' => [
                'uuid'           => $kendaraan['uuid'],
                'kendaraan_kode' => $kendaraan['kendaraan_kode'],
                'plat_nomor'     => $kendaraan['plat_nomor'],
                'jenis'          => $kendaraan['jenis'],
                'merk'           => $kendaraan['merk']            ?? '',
                'model'          => $kendaraan['model']           ?? '',
                'warna'          => $kendaraan['warna']           ?? '',
                'tahun'          => $kendaraan['tahun_kendaraan'] ?? '',
                'nomor_stnk'     => $kendaraan['nomor_stnk']      ?? '',
                'nomor_rangka'   => $kendaraan['nomor_rangka']    ?? '',
                'nomor_mesin'    => $kendaraan['nomor_mesin']     ?? '',
                'status'         => $kendaraan['status'],
                'thumbnail_url'  => $thumbnailUrl,
                'galeri_urls'    => $galeriUrls,
                'foto_stnk_url'  => $fotoStnkUrl,
            ],

            // CSRF hash baru untuk request berikutnya
            'csrf_hash' => csrf_hash(),
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // PRIVATE — Jawaban scan ditolak
    // Tidak menyertakan data personel, dan tidak dicatat di log.
    // Selalu menyertakan csrf_hash supaya scan berikutnya tidak gagal.
    // ──────────────────────────────────────────────────────────
    private function reject(string $code, string $title, string $message)
    {
        return $this->response->setJSON([
            'status'    => 'error',
            'code'      => $code,
            'title'     => $title,
            'message'   => $message,
            'csrf_hash' => csrf_hash(),
        ]);
    }
}