<?php

namespace App\Controllers;

use App\Libraries\MaintenanceMode;

/**
 * Maintenance Controller
 *
 * Nyalakan / matikan mode maintenance dari halaman admin.
 *
 * Hanya untuk Super Admin.
 *
 * Routes (group admin dengan filter role:superadmin):
 *   GET  /admin/maintenance          → index
 *   POST /admin/maintenance/toggle   → toggle
 *   GET  /admin/maintenance/preview  → preview
 */
class Maintenance extends BaseController
{
    // ──────────────────────────────────────────────────────────
    // INDEX — Halaman pengaturan maintenance
    // GET /admin/maintenance
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        return view('maintenance/index', [
            'title'       => 'Mode Maintenance',
            'state'       => MaintenanceMode::get(),
            'forcedByEnv' => MaintenanceMode::forcedByEnv(),
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // TOGGLE — Nyalakan / matikan maintenance
    // POST /admin/maintenance/toggle
    // ──────────────────────────────────────────────────────────
    public function toggle()
    {
        // Lapis pengaman kedua, selain filter route
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')
                             ->with('error', 'Hanya Super Admin yang dapat mengatur mode maintenance.');
        }

        if (MaintenanceMode::forcedByEnv()) {
            return redirect()->to('/admin/maintenance')
                             ->with('error', 'Maintenance sedang dikunci dari file .env (app.maintenance = true). Ubah di .env untuk mematikannya.');
        }

        $action = $this->request->getPost('action');

        if ($action === 'enable') {
            $rules = [
                'scope'    => 'required|in_list[semua,petugas]',
                'estimasi' => 'permit_empty|max_length[50]',
                'pesan'    => 'permit_empty|max_length[300]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                                 ->withInput()
                                 ->with('error', implode(' ', $this->validator->getErrors()));
            }

            $scope = $this->request->getPost('scope');

            $saved = MaintenanceMode::enable(
                $scope,
                trim((string) $this->request->getPost('estimasi')),
                trim((string) $this->request->getPost('pesan')),
                (string) (session()->get('user_nama') ?? 'Super Admin')
            );

            $pesanSukses = $scope === MaintenanceMode::SCOPE_PETUGAS
                ? 'Mode maintenance diaktifkan. Petugas melihat halaman maintenance, admin tetap bisa input data.'
                : 'Mode maintenance diaktifkan. Admin dan petugas melihat halaman maintenance, hanya Super Admin yang bisa masuk.';

            return $saved
                ? redirect()->to('/admin/maintenance')->with('success', $pesanSukses)
                : redirect()->to('/admin/maintenance')->with('error', 'Gagal menyimpan status. Cek permission folder writable.');
        }

        if ($action === 'disable') {
            $saved = MaintenanceMode::disable();

            return $saved
                ? redirect()->to('/admin/maintenance')->with('success', 'Mode maintenance dimatikan. Sistem kembali normal.')
                : redirect()->to('/admin/maintenance')->with('error', 'Gagal menyimpan status. Cek permission folder writable.');
        }

        return redirect()->to('/admin/maintenance')->with('error', 'Aksi tidak dikenali.');
    }

    // ──────────────────────────────────────────────────────────
    // PREVIEW — Lihat tampilan halaman maintenance
    // (admin tidak pernah melihatnya karena selalu diloloskan filter)
    // GET /admin/maintenance/preview
    // ──────────────────────────────────────────────────────────
    public function preview(): string
    {
        return view('errors/html/maintenance', ['state' => MaintenanceMode::get()]);
    }
}