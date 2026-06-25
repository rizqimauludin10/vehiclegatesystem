<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // ── Tampilkan Halaman Login ─────────────────────────────
    public function index()
    {
        if (session()->get('logged_in')) {
            return $this->redirectByRole();
        }

        return view('auth/login');
    }

    // ── Proses Login ───────────────────────────────────────
    public function login()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Username atau password salah.');
        }

        // Set session
        session()->set([
            'logged_in'  => true,
            'user_id'    => $user['id'],
            'user_uuid'  => $user['uuid'],
            'user_nama'  => $user['nama'],
            'username'   => $user['username'],
            'role'       => $user['role'],
        ]);

        log_message('info', 'User login: ' . $username . ' | Role: ' . $user['role']);

        return $this->redirectByRole();
    }

    // ── Logout ─────────────────────────────────────────────
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'Berhasil logout.');
    }

    // ── Redirect Berdasarkan Role ──────────────────────────
    private function redirectByRole(): \CodeIgniter\HTTP\RedirectResponse
    {
        $role = session()->get('role');

        if ($role === 'petugas' && $this->isMobileDevice()) {
            // Petugas login dari HP/tablet → langsung ke mobile scanner
            return redirect()->to('/petugas/scan/mobile');
        }

        switch ($role) {
            case 'admin':
                return redirect()->to('/admin/dashboard');
            case 'petugas':
                return redirect()->to('/petugas/dashboard');
            default:
                return redirect()->to('/auth/login');
        }
    }

    // ── Deteksi Mobile / Tablet ────────────────────────────
    private function isMobileDevice(): bool
    {
        $userAgent = $this->request->getUserAgent();

        // Deteksi berdasarkan User-Agent string
        $mobileKeywords = [
            'Mobile', 'Android', 'iPhone', 'iPad',
            'iPod', 'BlackBerry', 'Windows Phone',
            'Opera Mini', 'IEMobile', 'Tablet',
        ];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }
}