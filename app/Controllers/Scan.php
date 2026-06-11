<?php

namespace App\Controllers;

use App\Models\QRCodeModel;
use App\Models\PersonelModel;
use App\Models\KendaraanModel;
use App\Models\KendaraanFotoModel;
use App\Models\LogKeluarModel;

class Scan extends BaseController
{
    protected QRCodeModel      $qrModel;
    protected PersonelModel    $personelModel;
    protected KendaraanModel   $kendaraanModel;
    protected KendaraanFotoModel $fotoModel;
    protected LogKeluarModel   $logModel;

    public function __construct()
    {
        $this->qrModel        = new QRCodeModel();
        $this->personelModel  = new PersonelModel();
        $this->kendaraanModel = new KendaraanModel();
        $this->fotoModel      = new KendaraanFotoModel();
        $this->logModel       = new LogKeluarModel();
    }

    // ──────────────────────────────────────────────────────────
    // INDEX — Halaman Scan
    // GET /petugas/scan
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        return view('scan/index', ['title' => 'Scan QR Code']);
    }

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

    public function mobile(): string
    {
        return view('scan/mobile', [
            'base_url'    => base_url(),
            'csrf_token'  => csrf_token(),
            'csrf_hash'   => csrf_hash(),
            'process_url' => base_url('petugas/scan/process'),
            'petugas'     => session()->get('user_nama'),
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // PROCESS — Proses QR dari Scanner Hardware
    // POST /petugas/scan/process (AJAX)
    // ──────────────────────────────────────────────────────────
    public function process()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/petugas/scan');
        }

        $kodeUnik = trim($this->request->getPost('kode_unik'));

        if (empty($kodeUnik)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Kode QR tidak boleh kosong.',
            ]);
        }

        // Cari QR aktif
        $qr = $this->qrModel->findByKodeUnik($kodeUnik);

        if (!$qr) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'QR Code tidak dikenali atau sudah tidak aktif.',
            ]);
        }

        // Ambil data personel
        // $personel = $this->personelModel->find($qr['personel_id']);
        $personel = $this->personelModel->findDetailById($qr['personel_id']);
        //dd($personel);

        if (!$personel || $personel['status'] !== 'aktif') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Personel tidak aktif atau tidak ditemukan.',
            ]);
        }

        // Ambil semua kendaraan personel beserta foto galeri & STNK
        $kendaraanList = $this->kendaraanModel->getByPersonel($personel['id']);

        $kendaraanData = [];
        foreach ($kendaraanList as $k) {
            // Ambil galeri foto
            $galeri = $this->fotoModel->getByKendaraan($k['id']);

            $galeriUrls = array_map(function ($foto) {
                return base_url('uploads/kendaraan/' . $foto['nama_file']);
            }, $galeri);

            // Foto STNK
            $fotoStnkUrl = null;
            if (!empty($k['foto_stnk'])) {
                $fotoStnkUrl = base_url('uploads/kendaraan/' . $k['foto_stnk']);
            }

            // Thumbnail (foto pertama dari galeri, fallback ke foto lama)
            $thumbnailUrl = null;
            if (!empty($galeri)) {
                $thumbnailUrl = base_url('uploads/kendaraan/' . $galeri[0]['nama_file']);
            } elseif (!empty($k['foto'])) {
                $thumbnailUrl = base_url('uploads/kendaraan/' . $k['foto']);
            }

            $kendaraanData[] = [
                'uuid'            => $k['uuid'],
                'kendaraan_kode'  => $k['kendaraan_kode'],
                'plat_nomor'      => $k['plat_nomor'],
                'jenis'           => $k['jenis'],
                'merk'            => $k['merk'] ?? '',
                'model'           => $k['model'] ?? '',
                'warna'           => $k['warna'] ?? '',
                'tahun'           => $k['tahun_kendaraan'] ?? '',
                'nomor_rangka'    => $k['nomor_rangka'] ?? '',
                'nomor_mesin'     => $k['nomor_mesin'] ?? '',
                'nomor_stnk'      => $k['nomor_stnk'] ?? '',
                'nomor_bpkb'      => $k['nomor_bpkb'] ?? '',
                'status'          => $k['status'],
                'thumbnail_url'   => $thumbnailUrl,
                'galeri_urls'     => $galeriUrls,
                'foto_stnk_url'   => $fotoStnkUrl,
            ];
        }

        // Simpan log scan
        $this->logModel->saveLog($personel['id'], $kodeUnik);
        

        // Response data
        return $this->response->setJSON([
            'status'    => 'success',
            'scan_time' => date('d M Y, H:i:s'),
            'petugas'   => session()->get('user_nama'),
            'personel' => [
                'nama'              => $personel['nama'],
                'nrp'               => $personel['nrp'],
                'pangkat_singkat' => $personel['pangkat_singkat'],

                'jabatan'           => $personel['jabatan'] ?? '',

                'satker_singkat' => $personel['satker_singkat'],

                'foto' => !empty($personel['foto'])
                    ? base_url('uploads/personel/' . $personel['foto'])
                    : null,
            ],
            'kendaraan' => $kendaraanData,
        ]);
    }
}