<?php

namespace App\Controllers;

use App\Models\UserModel;
use Ramsey\Uuid\Uuid;

/**
 * AdminManagement Controller
 *
 * Kelola akun ADMIN (tambah, edit, reset password, aktif/nonaktif).
 * Hanya untuk Super Admin (route filter role:superadmin + pengecekan ulang di sini).
 *
 * Aturan:
 * - Halaman ini hanya bisa mengelola akun ber-role 'admin'.
 * - Akun Super Admin dan petugas tidak bisa disentuh dari sini.
 * - Role akun tidak bisa diubah dari sini (admin tetap admin).
 *
 * Routes prefix: /admin/admins
 */
class AdminManagement extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // ──────────────────────────────────────────────────────────
    // INDEX — Daftar akun admin
    // GET /admin/admins
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        $admins = $this->userModel
            ->where('role', 'admin')
            ->orderBy('status', 'ASC')   // aktif dulu
            ->orderBy('nama', 'ASC')
            ->findAll();

        return view('admins/index', [
            'title'  => 'Akun Admin',
            'admins' => $admins,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // CREATE — Form tambah admin
    // GET /admin/admins/create
    // ──────────────────────────────────────────────────────────
    public function create(): string
    {
        return view('admins/form', [
            'title' => 'Tambah Akun Admin',
            'admin' => null,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // STORE — Simpan admin baru
    // POST /admin/admins/store
    // ──────────────────────────────────────────────────────────
    public function store()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $rules = [
            'nama'             => 'required|min_length[3]|max_length[100]',
            'username'         => 'required|min_length[4]|max_length[50]|alpha_numeric_punct|is_unique[users.username]',
            'password'         => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (!$this->validate($rules, $this->messages())) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $saved = $this->userModel->skipValidation(true)->insert([
            'uuid'     => Uuid::uuid4()->toString(),
            'nama'     => trim((string) $this->request->getPost('nama')),
            'username' => trim((string) $this->request->getPost('username')),
            'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'     => 'admin',
            'status'   => 'aktif',
        ]);

        if (!$saved) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->userModel->errors() ?: ['Gagal menyimpan akun. Silakan coba lagi.']);
        }

        return redirect()->to('/admin/admins')
                         ->with('success', 'Akun admin berhasil ditambahkan.');
    }

    // ──────────────────────────────────────────────────────────
    // EDIT — Form edit admin
    // GET /admin/admins/edit/{uuid}
    // ──────────────────────────────────────────────────────────
    public function edit(string $uuid)
    {
        $admin = $this->findAdmin($uuid);

        if (!$admin) {
            return redirect()->to('/admin/admins')
                             ->with('error', 'Akun admin tidak ditemukan.');
        }

        return view('admins/form', [
            'title' => 'Edit Akun Admin',
            'admin' => $admin,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // UPDATE — Simpan perubahan (termasuk reset password)
    // POST /admin/admins/update/{uuid}
    // ──────────────────────────────────────────────────────────
    public function update(string $uuid)
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $admin = $this->findAdmin($uuid);

        if (!$admin) {
            return redirect()->to('/admin/admins')
                             ->with('error', 'Akun admin tidak ditemukan.');
        }

        $rules = [
            'nama'     => 'required|min_length[3]|max_length[100]',
            'username' => "required|min_length[4]|max_length[50]|alpha_numeric_punct|is_unique[users.username,id,{$admin['id']}]",
        ];

        $newPassword = (string) $this->request->getPost('password');
        if ($newPassword !== '') {
            $rules['password']         = 'min_length[8]';
            $rules['confirm_password'] = 'matches[password]';
        }

        if (!$this->validate($rules, $this->messages())) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama'     => trim((string) $this->request->getPost('nama')),
            'username' => trim((string) $this->request->getPost('username')),
        ];

        if ($newPassword !== '') {
            $data['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        if (!$this->userModel->skipValidation(true)->update($admin['id'], $data)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->userModel->errors() ?: ['Gagal menyimpan perubahan. Silakan coba lagi.']);
        }

        $pesan = $newPassword !== ''
            ? 'Data admin dan password berhasil diperbarui.'
            : 'Data admin berhasil diperbarui.';

        return redirect()->to('/admin/admins')->with('success', $pesan);
    }

    // ──────────────────────────────────────────────────────────
    // TOGGLE — Aktif / nonaktifkan akun admin
    // POST /admin/admins/toggle/{uuid}
    //
    // Akun yang dinonaktifkan langsung keluar di request berikutnya
    // (dicek oleh RoleFilter di setiap request).
    // ──────────────────────────────────────────────────────────
    public function toggle(string $uuid)
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $admin = $this->findAdmin($uuid);

        if (!$admin) {
            return redirect()->to('/admin/admins')
                             ->with('error', 'Akun admin tidak ditemukan.');
        }

        $newStatus = $admin['status'] === 'aktif' ? 'nonaktif' : 'aktif';
        $label     = $newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';

        if (!$this->userModel->skipValidation(true)->update($admin['id'], ['status' => $newStatus])) {
            return redirect()->to('/admin/admins')
                             ->with('error', 'Gagal mengubah status akun. Silakan coba lagi.');
        }

        return redirect()->to('/admin/admins')
                         ->with('success', "Akun admin {$admin['nama']} berhasil {$label}.");
    }

    // ══════════════════════════════════════════════════════════
    // PRIVATE
    // ══════════════════════════════════════════════════════════

    /** Cari akun berdasarkan UUID, hanya yang role-nya 'admin' */
    private function findAdmin(string $uuid): ?array
    {
        $user = $this->userModel->findByUuid($uuid);

        return ($user && $user['role'] === 'admin') ? $user : null;
    }

    /** Lapis pengaman kedua selain filter route */
    private function guard()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/admin/dashboard')
                             ->with('error', 'Hanya Super Admin yang dapat mengelola akun admin.');
        }

        return null;
    }

    private function messages(): array
    {
        return [
            'username' => [
                'is_unique'           => 'Username sudah digunakan, pilih yang lain.',
                'alpha_numeric_punct' => 'Username hanya boleh huruf, angka, dan tanda baca.',
            ],
            'password' => [
                'min_length' => 'Password minimal 8 karakter.',
            ],
            'confirm_password' => [
                'matches' => 'Konfirmasi password tidak cocok.',
            ],
        ];
    }
}