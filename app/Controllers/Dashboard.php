<?php

namespace App\Controllers;

use App\Models\PersonelModel;
use App\Models\KendaraanModel;
use App\Models\LogKeluarModel;
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

        $data = [
            'title'           => 'Dashboard',
            'total_personel'  => $personelModel->where('status', 'aktif')->countAllResults(),
            'total_kendaraan' => $kendaraanModel->where('status', 'aktif')->countAllResults(),
            'total_petugas'   => $userModel->where('role', 'petugas')->where('status', 'aktif')->countAllResults(),
            'log_hari_ini'    => $logModel->getLogHariIni(),
            'log_terbaru'     => $logModel->getLogTerbaru(5),
        ];

        return view('dashboard/admin', $data);
    }

    // ── Petugas Dashboard ──────────────────────────────────
    public function petugasIndex()
    {
        $logModel = new LogKeluarModel();

        $data = [
            'title'        => 'Dashboard Petugas',
            'log_hari_ini' => $logModel->getLogHariIniByPetugas(session()->get('user_id')),
        ];

        return view('dashboard/petugas', $data);
    }
}