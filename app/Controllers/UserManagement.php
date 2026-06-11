<?php

namespace App\Controllers;

use App\Models\UserModel;
use Ramsey\Uuid\Uuid;

/**
 * UserManagement Controller
 * 
 * Handles CRUD operations for petugas accounts.
 * Only accessible by admin role (enforced via RoleFilter in Routes).
 * 
 * Routes prefix: /admin/users
 */
class UserManagement extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // ──────────────────────────────────────────────────────────
    // INDEX — Daftar Semua Petugas
    // GET /admin/users
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        $data = [
            'title'    => 'Management User Petugas',
            'petugas'  => $this->userModel->getAllPetugas(),
        ];

        return view('users/index', $data);
    }

    // ──────────────────────────────────────────────────────────
    // CREATE — Form Tambah Petugas
    // GET /admin/users/create
    // ──────────────────────────────────────────────────────────
    public function create(): string
    {
        $data = [
            'title'      => 'Tambah User Petugas',
            'validation' => \Config\Services::validation(),
        ];

        return view('users/create', $data);
    }

    // ──────────────────────────────────────────────────────────
    // STORE — Simpan Petugas Baru
    // POST /admin/users/store
    // ──────────────────────────────────────────────────────────
    public function store()
    {
        // Validasi input
        $rules = [
            'nama'             => 'required|min_length[3]|max_length[100]',
            'username'         => 'required|min_length[4]|max_length[50]|is_unique[users.username]|alpha_numeric_punct',
            'password'         => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
        ];

        $messages = [
            'username' => [
                'is_unique'           => 'Username sudah digunakan, pilih yang lain.',
                'alpha_numeric_punct' => 'Username hanya boleh huruf, angka, dan tanda baca.',
            ],
            'confirm_password' => [
                'matches' => 'Konfirmasi password tidak cocok.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        // Simpan ke database
        $this->userModel->insert([
            'uuid'       => Uuid::uuid4()->toString(),
            'nama'       => trim($this->request->getPost('nama')),
            'username'   => trim($this->request->getPost('username')),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'       => 'petugas',
            'status'     => 'aktif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/users')
                         ->with('success', 'User petugas berhasil ditambahkan.');
    }

    // ──────────────────────────────────────────────────────────
    // EDIT — Form Edit Petugas
    // GET /admin/users/edit/{uuid}
    // ──────────────────────────────────────────────────────────
    public function edit(string $uuid): string
    {
        $user = $this->userModel->findByUuid($uuid);

        if (!$user || $user['role'] !== 'petugas') {
            return redirect()->to('/admin/users')
                             ->with('error', 'User tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit User Petugas',
            'user'       => $user,
            'validation' => \Config\Services::validation(),
        ];

        return view('users/edit', $data);
    }

    // ──────────────────────────────────────────────────────────
    // UPDATE — Simpan Perubahan Data Petugas
    // POST /admin/users/update/{uuid}
    // ──────────────────────────────────────────────────────────
    public function update(string $uuid)
    {
        $user = $this->userModel->findByUuid($uuid);

        if (!$user || $user['role'] !== 'petugas') {
            return redirect()->to('/admin/users')
                             ->with('error', 'User tidak ditemukan.');
        }

        // Validasi — username unique kecuali milik user ini sendiri
        $rules = [
            'nama'     => 'required|min_length[3]|max_length[100]',
            'username' => "required|min_length[4]|max_length[50]|is_unique[users.username,id,{$user['id']}]|alpha_numeric_punct",
        ];

        $messages = [
            'username' => [
                'is_unique' => 'Username sudah digunakan, pilih yang lain.',
            ],
        ];

        // Jika isi password baru, validasi password
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $rules['password']         = 'min_length[6]';
            $rules['confirm_password'] = 'matches[password]';
            $messages['confirm_password'] = [
                'matches' => 'Konfirmasi password tidak cocok.',
            ];
        }

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        // Siapkan data update
        $updateData = [
            'nama'       => trim($this->request->getPost('nama')),
            'username'   => trim($this->request->getPost('username')),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Update password hanya jika diisi
        if (!empty($newPassword)) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $this->userModel->update($user['id'], $updateData);

        return redirect()->to('/admin/users')
                         ->with('success', 'Data petugas berhasil diperbarui.');
    }

    // ──────────────────────────────────────────────────────────
    // TOGGLE — Aktif / Nonaktifkan Akun Petugas
    // POST /admin/users/toggle/{uuid}
    // ──────────────────────────────────────────────────────────
    public function toggle(string $uuid)
    {
        $user = $this->userModel->findByUuid($uuid);

        if (!$user || $user['role'] !== 'petugas') {
            return redirect()->to('/admin/users')
                             ->with('error', 'User tidak ditemukan.');
        }

        $newStatus = $user['status'] === 'aktif' ? 'nonaktif' : 'aktif';
        $label     = $newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';

        $this->userModel->update($user['id'], [
            'status'     => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/users')
                         ->with('success', "Akun petugas berhasil {$label}.");
    }
}