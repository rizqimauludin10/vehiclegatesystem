<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RoleFilter
 *
 * Pemakaian di route: 'role:admin', 'role:petugas', 'role:superadmin', 'role:admin,petugas'
 *
 * Hierarki:
 *   superadmin → boleh mengakses semua halaman yang mengizinkan 'admin'
 *   admin      → hanya halaman 'admin'
 *   petugas    → hanya halaman 'petugas'
 *
 * Sinkron dengan database:
 *   Di setiap request, status & role akun dicek ulang ke tabel users.
 *   Akun nonaktif langsung dikeluarkan; perubahan role langsung berlaku.
 *
 * Anti redirect-loop:
 *   Kalau akses ditolak, pengguna diarahkan ke dashboard sesuai role-nya.
 *   Kalau dashboard itu sendiri ditolak (atau role tidak dikenal),
 *   filter menampilkan pesan 403, bukan redirect lagi.
 */
class RoleFilter implements FilterInterface
{
    /** Role yang otomatis mewarisi hak akses role lain */
    private const INHERITS = [
        'superadmin' => ['admin'],
    ];

    /** Halaman awal tiap role */
    private const HOME = [
        'superadmin' => 'admin/dashboard',
        'admin'      => 'admin/dashboard',
        'petugas'    => 'petugas/dashboard',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // ── Cocokkan session dengan data terbaru di database ──
        // Akun yang dinonaktifkan langsung keluar, dan perubahan role langsung berlaku,
        // tanpa menunggu pengguna logout sendiri.
        $userId = session()->get('user_id');
        $user   = $userId
            ? db_connect()->table('users')->select('role, status')->where('id', $userId)->get()->getRowArray()
            : null;

        if (!$user || $user['status'] !== 'aktif') {
            session()->remove(['logged_in', 'user_id', 'user_uuid', 'user_nama', 'username', 'role']);

            if ($request instanceof IncomingRequest && $request->isAJAX()) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON(['status' => 'error', 'message' => 'Akun Anda sudah tidak aktif. Silakan hubungi administrator.']);
            }

            return redirect()->to('/auth/login')
                            ->with('error', 'Akun Anda sudah tidak aktif. Silakan hubungi administrator.');
        }

        if ($user['role'] !== session()->get('role')) {
            session()->set('role', $user['role']);
        }

        if (empty($arguments)) {
            return;
        }

        $role = (string) session()->get('role');

        if ($this->isAllowed($role, $arguments)) {
            return;
        }

        // Request AJAX (DataTables, dll) → balas JSON, bukan redirect
        if ($request instanceof IncomingRequest && $request->isAJAX()) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON(['error' => 'Akses ditolak. Anda tidak memiliki izin.']);
        }

        helper('url');
        $path = trim(uri_string(), '/');
        $home = self::HOME[$role] ?? null;

        // Role tidak dikenal, atau halaman awalnya sendiri ditolak → hentikan, jangan redirect lagi
        if ($home === null || $path === $home) {
            return $this->forbiddenPage($role);
        }

        return redirect()->to('/' . $home)->with('error', 'Akses ditolak. Anda tidak memiliki izin.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }

    private function isAllowed(string $role, array $allowed): bool
    {
        if (in_array($role, $allowed, true)) {
            return true;
        }

        foreach (self::INHERITS[$role] ?? [] as $inheritedRole) {
            if (in_array($inheritedRole, $allowed, true)) {
                return true;
            }
        }

        return false;
    }

    private function forbiddenPage(string $role)
    {
        $roleText  = esc($role !== '' ? $role : '(kosong)');
        $logoutUrl = base_url('auth/logout');

        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="id">
            <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Akses ditolak — VGS</title>
            <style>
            body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;
                background:#f8fafc;font-family:system-ui,sans-serif;color:#0f172a;padding:1.5rem}
            .box{max-width:420px;background:#fff;border:1px solid #e2e8f0;border-radius:16px;
                padding:2rem;text-align:center}
            h1{font-size:1.15rem;margin:0 0 .5rem}
            p{font-size:.88rem;color:#64748b;line-height:1.6;margin:0 0 1.25rem}
            code{background:#f1f5f9;padding:1px 6px;border-radius:4px}
            a{display:inline-block;background:#16a34a;color:#fff;text-decoration:none;
                padding:.6rem 1.2rem;border-radius:10px;font-size:.85rem;font-weight:600}
            </style>
            </head>
            <body>
            <div class="box">
                <h1>Akses ditolak</h1>
                <p>Akun Anda (role <code>{$roleText}</code>) tidak memiliki halaman yang bisa dibuka.
                Hubungi administrator untuk memeriksa role akun Anda.</p>
                <a href="{$logoutUrl}">Logout</a>
            </div>
            </body>
            </html>
            HTML;

        return service('response')->setStatusCode(403)->setBody($html);
    }
}