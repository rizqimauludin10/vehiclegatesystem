<?php

namespace App\Libraries;

/**
 * MaintenanceMode
 *
 * Menyimpan status maintenance di writable/maintenance.json
 * supaya bisa dinyalakan/dimatikan dari halaman admin tanpa mengubah .env.
 *
 * .env tetap berfungsi sebagai "saklar darurat":
 * kalau app.maintenance = true, maintenance selalu aktif dan tidak bisa
 * dimatikan dari halaman admin.
 */
class MaintenanceMode
{
    private const FILE = WRITEPATH . 'maintenance.json';

    public const DEFAULT_ESTIMASI = '± 15 - 30 Menit';

    /** Semua pengguna diblokir, kecuali Super Admin */
    public const SCOPE_SEMUA = 'semua';

    /** Hanya petugas yang diblokir, admin & Super Admin tetap bisa bekerja */
    public const SCOPE_PETUGAS = 'petugas';

    /** Maintenance dipaksa aktif dari .env (app.maintenance = true) */
    public static function forcedByEnv(): bool
    {
        $value = env('app.maintenance');

        return $value === true || $value === 'true' || $value === '1' || $value === 1;
    }

    /**
     * Ambil status lengkap maintenance.
     *
     * @return array{enabled: bool, scope: string, estimasi: string, pesan: string, by: ?string, since: ?string}
     */
    public static function get(): array
    {
        $state = [
            'enabled'  => false,
            'scope'    => self::SCOPE_SEMUA,
            'estimasi' => self::DEFAULT_ESTIMASI,
            'pesan'    => '',
            'by'       => null,
            'since'    => null,
        ];

        if (is_file(self::FILE)) {
            $data = json_decode((string) @file_get_contents(self::FILE), true);
            if (is_array($data)) {
                $state = array_merge($state, $data);
            }
        }

        $state['enabled'] = (bool) $state['enabled'] || self::forcedByEnv();

        return $state;
    }

    public static function isActive(): bool
    {
        return self::get()['enabled'];
    }

    /**
     * Nyalakan maintenance.
     *
     * @param string $scope  SCOPE_SEMUA atau SCOPE_PETUGAS
     */
    public static function enable(string $scope, string $estimasi, string $pesan, string $by): bool
    {
        return self::save([
            'enabled'  => true,
            'scope'    => $scope === self::SCOPE_PETUGAS ? self::SCOPE_PETUGAS : self::SCOPE_SEMUA,
            'estimasi' => $estimasi !== '' ? $estimasi : self::DEFAULT_ESTIMASI,
            'pesan'    => $pesan,
            'by'       => $by,
            'since'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Apakah user yang sedang login boleh tetap memakai aplikasi selama maintenance.
     *
     * - Super Admin  : selalu boleh
     * - Admin        : hanya kalau cakupan maintenance "Hanya petugas"
     * - Petugas/lain : tidak boleh
     */
    public static function canBypass(?string $role, bool $loggedIn): bool
    {
        if (!$loggedIn) {
            return false;
        }

        if ($role === 'superadmin') {
            return true;
        }

        if ($role === 'admin') {
            $state = self::get();
            // Kalau dikunci dari .env, admin biasa ikut diblokir
            return !self::forcedByEnv() && $state['scope'] === self::SCOPE_PETUGAS;
        }

        return false;
    }

    public static function disable(): bool
    {
        $state = self::get();

        // Estimasi & pesan terakhir tetap disimpan supaya bisa dipakai lagi
        return self::save([
            'enabled'  => false,
            'scope'    => $state['scope'],
            'estimasi' => $state['estimasi'],
            'pesan'    => $state['pesan'],
            'by'       => null,
            'since'    => null,
        ]);
    }

    /** Tulis atomik: tulis ke file sementara lalu rename */
    private static function save(array $state): bool
    {
        $tmp = self::FILE . '.tmp';

        if (@file_put_contents($tmp, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
            return false;
        }

        return @rename($tmp, self::FILE);
    }
}