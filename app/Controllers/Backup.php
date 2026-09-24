<?php

namespace App\Controllers;

use Config\Database;

/**
 * Backup Controller
 *
 * Backup database (.sql) dan backup foto (.zip) dengan progress real-time.
 *
 * Routes prefix: /admin/backup
 *   GET  /admin/backup                         → index
 *   POST /admin/backup/createajax              → createAjax   (backup database)
 *   GET  /admin/backup/progressdb              → progressDb
 *   POST /admin/backup/fotoajax                → fotoAjax     (backup foto)
 *   GET  /admin/backup/progress                → progress
 *   GET  /admin/backup/download/{file}         → download     (.sql)
 *   GET  /admin/backup/downloadfoto/{file}     → downloadFoto (.zip)
 *   POST /admin/backup/delete/{file}           → delete
 */
class Backup extends BaseController
{
    /** Folder penyimpanan backup */
    public const BACKUP_DIR = WRITEPATH . 'backups/';

    /**
     * Simpan hanya N backup terbaru per jenis (database / foto).
     * 0 = tidak ada penghapusan otomatis.
     */
    private const KEEP_LATEST = 0;

    /**
     * Ikutkan foto personel berstatus nonaktif ke dalam backup foto.
     * Folder mereka diberi akhiran _NONAKTIF supaya mudah dibedakan.
     */
    private const INCLUDE_NONAKTIF = true;

    private const PROGRESS_DB   = 'progress_db.json';
    private const PROGRESS_FOTO = 'progress.json';
    private const LOCK_FILE     = 'backup.lock';

    public function __construct()
    {
        if (!is_dir(self::BACKUP_DIR)) {
            mkdir(self::BACKUP_DIR, 0755, true);
        }
    }

    // ──────────────────────────────────────────────────────────
    // INDEX — Halaman Backup
    // GET /admin/backup
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        return view('backup/index', [
            'title'   => 'Database Backup',
            'backups' => $this->getBackupList(),
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // BACKUP DATABASE
    // ══════════════════════════════════════════════════════════

    // ──────────────────────────────────────────────────────────
    // CREATE AJAX — Backup database via AJAX + progress
    // POST /admin/backup/createajax
    // ──────────────────────────────────────────────────────────
    public function createAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request.']);
        }

        $this->prepareLongProcess();

        $lock = $this->acquireLock();
        if ($lock === null) {
            return $this->response->setJSON([
                'error' => 'Ada proses backup lain yang sedang berjalan. Tunggu sampai selesai, lalu coba lagi.',
            ]);
        }

        $progressFile = self::BACKUP_DIR . self::PROGRESS_DB;
        $filepath     = null;
        $handle       = null;

        try {
            $db       = Database::connect();
            $database = $db->getDatabase();

            $this->writeProgress($progressFile, 0, 0, 'Menyiapkan backup...');

            // Hanya tabel asli (view dilewati karena strukturnya berbeda)
            $tables = $db->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->getResultArray();
            $total  = count($tables);

            if ($total === 0) {
                throw new \RuntimeException('Database kosong, tidak ada tabel untuk di-backup.');
            }

            $filename = 'backup_' . $this->safeFileName($database) . '_' . date('Ymd_His') . '.sql';
            $filepath = self::BACKUP_DIR . $filename;

            // Tulis langsung ke file (streaming), bukan ditampung di memori
            $handle = fopen($filepath, 'wb');
            if ($handle === false) {
                throw new \RuntimeException('Gagal membuat file backup. Cek permission folder writable/backups.');
            }

            $this->write($handle,
                "-- Vehicle Gate System Database Backup\n" .
                "-- Generated : " . date('Y-m-d H:i:s') . "\n" .
                "-- Database  : {$database}\n" .
                "-- Version   : MySQL " . $db->getVersion() . "\n" .
                str_repeat('-', 60) . "\n\n" .
                "SET FOREIGN_KEY_CHECKS = 0;\n" .
                "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n" .
                "SET NAMES utf8mb4;\n\n"
            );

            $totalRows = 0;

            foreach ($tables as $idx => $tableRow) {
                $table   = (string) array_values($tableRow)[0];
                $current = $idx + 1;

                if (!preg_match('/^[A-Za-z0-9_$]+$/', $table)) {
                    throw new \RuntimeException("Nama tabel tidak valid: {$table}");
                }

                $this->writeProgress(
                    $progressFile,
                    $current,
                    $total,
                    "Dump tabel: `{$table}` ({$current}/{$total})",
                    '',
                    (int) floor(($idx / $total) * 100)
                );

                // Struktur tabel
                $create    = $db->query("SHOW CREATE TABLE `{$table}`")->getRowArray();
                $createSql = $create['Create Table'] ?? (array_values($create ?? [])[1] ?? null);

                if (empty($createSql)) {
                    throw new \RuntimeException("Gagal mengambil struktur tabel: {$table}");
                }

                $this->write($handle,
                    "-- Tabel: `{$table}`\n" .
                    "DROP TABLE IF EXISTS `{$table}`;\n" .
                    $createSql . ";\n\n"
                );

                // Isi tabel
                $totalRows += $this->dumpTableData($db, $handle, $table);
            }

            $this->write($handle,
                "SET FOREIGN_KEY_CHECKS = 1;\n\n" .
                "-- Backup selesai: " . date('Y-m-d H:i:s') . "\n"
            );

            fclose($handle);
            $handle = null;

            $size = $this->formatSize(filesize($filepath));

            $this->cleanupOldBackups('database');

            $this->writeProgress(
                $progressFile,
                $total,
                $total,
                "Backup selesai! {$total} tabel, {$totalRows} baris data ({$size})",
                $filename,
                100,
                'done'
            );

            return $this->response->setJSON([
                'success'  => true,
                'filename' => $filename,
                'size'     => $size,
                'total'    => $total,
                'rows'     => $totalRows,
            ]);
        } catch (\Throwable $e) {
            if (is_resource($handle)) {
                fclose($handle);
            }
            // Jangan tinggalkan file backup setengah jadi
            if ($filepath && is_file($filepath)) {
                @unlink($filepath);
            }

            log_message('error', '[Backup DB] ' . $e->getMessage());
            $this->writeProgress($progressFile, 0, 0, $e->getMessage(), '', 0, 'error');

            return $this->response->setJSON(['error' => $e->getMessage()]);
        } finally {
            $this->releaseLock($lock);
        }
    }

    // ──────────────────────────────────────────────────────────
    // PROGRESS DB — Cek status progress backup database
    // GET /admin/backup/progressdb
    // ──────────────────────────────────────────────────────────
    public function progressDb()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request.']);
        }

        return $this->response->setJSON($this->readProgress(self::BACKUP_DIR . self::PROGRESS_DB));
    }

    // ══════════════════════════════════════════════════════════
    // BACKUP FOTO
    // ══════════════════════════════════════════════════════════

    // ──────────────────────────────────────────────────────────
    // FOTO AJAX — Backup foto personel & kendaraan ke ZIP
    // POST /admin/backup/fotoajax
    //
    // Struktur ZIP:
    //   NAMA_SATKER/
    //     NRP_NAMA/
    //       foto_personel/foto.jpg
    //       kendaraan/PLAT/foto_utama.jpg
    //       kendaraan/PLAT/galeri/foto_01.jpg
    //       kendaraan/PLAT/stnk/stnk.jpg
    // ──────────────────────────────────────────────────────────
    public function fotoAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request.']);
        }

        if (!class_exists(\ZipArchive::class)) {
            return $this->response->setJSON(['error' => 'Ekstensi PHP ZipArchive tidak tersedia di server ini.']);
        }

        $this->prepareLongProcess();

        $lock = $this->acquireLock();
        if ($lock === null) {
            return $this->response->setJSON([
                'error' => 'Ada proses backup lain yang sedang berjalan. Tunggu sampai selesai, lalu coba lagi.',
            ]);
        }

        $progressFile = self::BACKUP_DIR . self::PROGRESS_FOTO;
        $filepath     = null;
        $zip          = null;

        try {
            $this->writeProgress($progressFile, 0, 0, 'Mengumpulkan data foto...');

            // ── 1. Susun rencana file (3 query saja, tanpa query per baris) ──
            [$plan, $missing, $totalBytes, $nonaktifCount] = $this->buildFotoPlan();

            $totalPersonel = count($plan);
            if ($totalPersonel === 0) {
                throw new \RuntimeException('Tidak ada data personel untuk di-backup.');
            }

            // ── 2. Pastikan ruang disk cukup ──
            $free = @disk_free_space(self::BACKUP_DIR);
            $need = (int) ($totalBytes * 1.05) + (10 * 1024 * 1024);
            if ($free !== false && $free < $need) {
                throw new \RuntimeException(
                    'Ruang disk tidak cukup. Dibutuhkan sekitar ' . $this->formatSize($need) .
                    ', tersedia ' . $this->formatSize((int) $free) . '. Hapus backup lama terlebih dahulu.'
                );
            }

            // ── 3. Susun ZIP ──
            $filename = 'backup_foto_' . date('Ymd_His') . '.zip';
            $filepath = self::BACKUP_DIR . $filename;

            $zip = new \ZipArchive();
            if ($zip->open($filepath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                $zip = null;
                throw new \RuntimeException('Gagal membuat file ZIP. Cek permission folder writable/backups.');
            }

            $totalFoto = 0;
            $lastWrite = 0.0;

            foreach ($plan as $i => $entry) {
                $current = $i + 1;

                foreach ($entry['files'] as [$source, $target]) {
                    if (!$zip->addFile($source, $target)) {
                        $missing[] = "Gagal ditambahkan ke ZIP: {$target}";
                        continue;
                    }
                    // Foto sudah terkompresi (JPG/PNG/WebP) → simpan apa adanya, jauh lebih cepat
                    $zip->setCompressionName($target, \ZipArchive::CM_STORE);
                    $totalFoto++;
                }

                // Batasi penulisan progress supaya tidak membebani disk
                $now = microtime(true);
                if ($current === $totalPersonel || ($now - $lastWrite) >= 0.3) {
                    $this->writeProgress(
                        $progressFile,
                        $current,
                        $totalPersonel,
                        "Mendata: {$entry['nama']} ({$current}/{$totalPersonel})",
                        '',
                        (int) floor(($current / $totalPersonel) * 40)
                    );
                    $lastWrite = $now;
                }
            }

            $zip->addFromString('README.txt', $this->buildReadme($totalFoto, $totalPersonel, $nonaktifCount, $missing));

            // ── 4. Tulis ZIP ke disk (bagian terberat) dengan progress nyata ──
            if (method_exists($zip, 'registerProgressCallback')) {
                $zip->registerProgressCallback(0.02, function (float $rate) use ($progressFile, $totalPersonel, $totalFoto) {
                    $this->writeProgress(
                        $progressFile,
                        $totalPersonel,
                        $totalPersonel,
                        "Menulis file ZIP ({$totalFoto} foto)...",
                        '',
                        40 + (int) floor($rate * 59)
                    );
                });
            } else {
                $this->writeProgress(
                    $progressFile, $totalPersonel, $totalPersonel,
                    "Menulis file ZIP ({$totalFoto} foto)...", '', 70
                );
            }

            if (!$zip->close()) {
                $zip = null;
                throw new \RuntimeException('Gagal menyimpan file ZIP. Kemungkinan disk penuh atau permission folder bermasalah.');
            }
            $zip = null;

            $size      = $this->formatSize(filesize($filepath));
            $skipCount = count($missing);

            $this->cleanupOldBackups('foto');

            $this->writeProgress(
                $progressFile,
                $totalPersonel,
                $totalPersonel,
                "Backup selesai! {$totalFoto} foto ({$size})" .
                    ($skipCount > 0 ? " — {$skipCount} file tidak ditemukan." : ''),
                $filename,
                100,
                'done'
            );

            return $this->response->setJSON([
                'success'  => true,
                'filename' => $filename,
                'size'     => $size,
                'total'    => $totalFoto,
                'skip'     => $skipCount,
            ]);
        } catch (\Throwable $e) {
            // ZipArchive baru menulis file saat close(), jadi cukup buang objeknya
            $zip = null;
            if ($filepath && is_file($filepath)) {
                @unlink($filepath);
            }

            log_message('error', '[Backup Foto] ' . $e->getMessage());
            $this->writeProgress($progressFile, 0, 0, $e->getMessage(), '', 0, 'error');

            return $this->response->setJSON(['error' => $e->getMessage()]);
        } finally {
            $this->releaseLock($lock);
        }
    }

    // ──────────────────────────────────────────────────────────
    // PROGRESS — Cek status progress backup foto
    // GET /admin/backup/progress
    // ──────────────────────────────────────────────────────────
    public function progress()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request.']);
        }

        return $this->response->setJSON($this->readProgress(self::BACKUP_DIR . self::PROGRESS_FOTO));
    }

    // ══════════════════════════════════════════════════════════
    // DOWNLOAD & DELETE
    // ══════════════════════════════════════════════════════════

    // ──────────────────────────────────────────────────────────
    // DOWNLOAD — File backup database (.sql)
    // GET /admin/backup/download/{filename}
    // ──────────────────────────────────────────────────────────
    public function download(string $filename)
    {
        return $this->streamBackup($filename, 'sql', 'application/sql');
    }

    // ──────────────────────────────────────────────────────────
    // DOWNLOAD FOTO — File backup foto (.zip)
    // GET /admin/backup/downloadfoto/{filename}
    // ──────────────────────────────────────────────────────────
    public function downloadFoto(string $filename)
    {
        return $this->streamBackup($filename, 'zip', 'application/zip');
    }

    // ──────────────────────────────────────────────────────────
    // DELETE — Hapus file backup
    // POST /admin/backup/delete/{filename}
    // ──────────────────────────────────────────────────────────
    public function delete(string $filename)
    {
        $filepath = $this->resolveBackupPath($filename, ['sql', 'zip']);

        if ($filepath === null) {
            return redirect()->to('/admin/backup')
                             ->with('error', 'File backup tidak ditemukan atau tidak valid.');
        }

        if (!@unlink($filepath)) {
            return redirect()->to('/admin/backup')
                             ->with('error', 'Gagal menghapus file. Cek permission folder writable/backups.');
        }

        return redirect()->to('/admin/backup')
                         ->with('success', 'File ' . basename($filepath) . ' berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════
    // PRIVATE — Database
    // ══════════════════════════════════════════════════════════

    /**
     * Tulis isi tabel sebagai INSERT batch langsung ke file.
     * Baris dibaca satu per satu supaya hemat memori untuk tabel besar.
     */
    private function dumpTableData($db, $handle, string $table): int
    {
        $query   = $db->query("SELECT * FROM `{$table}`");
        $columns = null;
        $batch   = [];
        $count   = 0;

        while ($row = $query->getUnbufferedRow('array')) {
            if ($columns === null) {
                $columns = '(`' . implode('`, `', array_map(
                    static fn ($c) => str_replace('`', '``', (string) $c),
                    array_keys($row)
                )) . '`)';
            }

            $values = [];
            foreach ($row as $value) {
                $values[] = $value === null
                    ? 'NULL'
                    : "'" . $db->escapeString((string) $value) . "'";
            }

            $batch[] = '(' . implode(', ', $values) . ')';
            $count++;

            if (count($batch) >= 200) {
                $this->writeInsert($handle, $table, $columns, $batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            $this->writeInsert($handle, $table, $columns, $batch);
        }

        $query->freeResult();

        if ($count > 0) {
            $this->write($handle, "\n");
        }

        return $count;
    }

    private function writeInsert($handle, string $table, string $columns, array $batch): void
    {
        $this->write($handle,
            "INSERT INTO `{$table}` {$columns} VALUES\n" .
            implode(",\n", $batch) . ";\n"
        );
    }

    /** fwrite yang langsung gagal kalau disk penuh, bukan diam-diam menghasilkan file rusak */
    private function write($handle, string $data): void
    {
        if (fwrite($handle, $data) === false) {
            throw new \RuntimeException('Gagal menulis file backup. Kemungkinan disk penuh.');
        }
    }

    // ══════════════════════════════════════════════════════════
    // PRIVATE — Foto
    // ══════════════════════════════════════════════════════════

    /**
     * Kumpulkan semua file foto yang akan dimasukkan ke ZIP.
     *
     * @return array{0: array, 1: array, 2: int, 3: int}
     *         [rencana per personel, daftar file hilang, total byte, jumlah personel nonaktif]
     */
    private function buildFotoPlan(): array
    {
        $db = Database::connect();

        $personelQuery = $db->table('personel p')
            ->select('p.id, p.nrp, p.nama, p.foto, p.status, sk.nama_singkat as satker_nama')
            ->join('satuan_kerja sk', 'sk.id = p.satker_id', 'left')
            ->orderBy('sk.nama_singkat', 'ASC')
            ->orderBy('p.nama', 'ASC');

        if (!self::INCLUDE_NONAKTIF) {
            $personelQuery->where('p.status', 'aktif');
        }

        $personelList = $personelQuery->get()->getResultArray();

        // Kendaraan dikelompokkan per personel
        $kendaraanByPersonel = [];
        $kendaraanRows = $db->table('kendaraan')
            ->select('id, personel_id, plat_nomor, foto, foto_stnk')
            ->orderBy('plat_nomor', 'ASC')
            ->get()
            ->getResultArray();
        foreach ($kendaraanRows as $k) {
            $kendaraanByPersonel[$k['personel_id']][] = $k;
        }

        // Galeri dikelompokkan per kendaraan
        $galeriByKendaraan = [];
        $galeriRows = $db->table('kendaraan_foto')
            ->select('kendaraan_id, nama_file')
            ->orderBy('kendaraan_id', 'ASC')
            ->orderBy('urutan', 'ASC')
            ->get()
            ->getResultArray();
        foreach ($galeriRows as $g) {
            $galeriByKendaraan[$g['kendaraan_id']][] = $g['nama_file'];
        }

        $personelDir  = FCPATH . 'uploads/personel/';
        $kendaraanDir = FCPATH . 'uploads/kendaraan/';

        $plan          = [];
        $missing       = [];
        $totalBytes    = 0;
        $nonaktifCount = 0;

        foreach ($personelList as $p) {
            $isNonaktif = ($p['status'] ?? 'aktif') !== 'aktif';
            if ($isNonaktif) {
                $nonaktifCount++;
            }

            $satkerFolder   = $this->sanitizeFolderName($p['satker_nama'] ?? 'TIDAK_DIKETAHUI');
            $personelFolder = $this->sanitizeFolderName($p['nrp'] . '_' . $p['nama'])
                            . ($isNonaktif ? '_NONAKTIF' : '');
            $base  = "{$satkerFolder}/{$personelFolder}";
            $label = "{$p['nrp']} {$p['nama']}";

            $files = [];

            // Foto personel
            if (!empty($p['foto'])) {
                $this->planFile($files, $missing, $totalBytes,
                    $personelDir . $p['foto'],
                    "{$base}/foto_personel/foto." . $this->extOf($p['foto']),
                    "Foto personel {$label}"
                );
            }

            // Kendaraan milik personel
            foreach ($kendaraanByPersonel[$p['id']] ?? [] as $k) {
                $kBase = "{$base}/kendaraan/" . $this->sanitizeFolderName($k['plat_nomor']);

                if (!empty($k['foto'])) {
                    $this->planFile($files, $missing, $totalBytes,
                        $kendaraanDir . $k['foto'],
                        "{$kBase}/foto_utama." . $this->extOf($k['foto']),
                        "Foto utama {$k['plat_nomor']}"
                    );
                }

                foreach ($galeriByKendaraan[$k['id']] ?? [] as $idx => $namaFile) {
                    $num = str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT);
                    $this->planFile($files, $missing, $totalBytes,
                        $kendaraanDir . $namaFile,
                        "{$kBase}/galeri/foto_{$num}." . $this->extOf($namaFile),
                        "Galeri {$k['plat_nomor']} #{$num}"
                    );
                }

                if (!empty($k['foto_stnk'])) {
                    $this->planFile($files, $missing, $totalBytes,
                        $kendaraanDir . $k['foto_stnk'],
                        "{$kBase}/stnk/stnk." . $this->extOf($k['foto_stnk']),
                        "STNK {$k['plat_nomor']}"
                    );
                }
            }

            $plan[] = ['nama' => $p['nama'], 'files' => $files];
        }

        return [$plan, $missing, $totalBytes, $nonaktifCount];
    }

    private function planFile(array &$files, array &$missing, int &$totalBytes, string $source, string $target, string $label): void
    {
        if (is_file($source)) {
            $files[]     = [$source, $target];
            $totalBytes += (int) filesize($source);
        } else {
            $missing[] = "{$label}: " . basename($source);
        }
    }

    private function extOf(string $filename): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return $ext !== '' ? $ext : 'jpg';
    }

    private function buildReadme(int $totalFoto, int $totalPersonel, int $nonaktifCount, array $missing): string
    {
        $readme  = "Vehicle Gate System — Backup Foto\n";
        $readme .= "Generated        : " . date('Y-m-d H:i:s') . "\n";
        $readme .= "Total personel   : {$totalPersonel}"
                 . ($nonaktifCount > 0 ? " (termasuk {$nonaktifCount} nonaktif)" : '') . "\n";
        $readme .= "Total foto       : {$totalFoto}\n";
        $readme .= "File tidak ada   : " . count($missing) . "\n\n";
        $readme .= "Struktur Folder:\n";
        $readme .= "  NAMA_SATKER/\n";
        $readme .= "    NRP_NAMA/                  (akhiran _NONAKTIF = personel nonaktif)\n";
        $readme .= "      foto_personel/           → foto profil personel\n";
        $readme .= "      kendaraan/\n";
        $readme .= "        PLAT_NOMOR/\n";
        $readme .= "          foto_utama.*         → foto utama kendaraan (jika ada)\n";
        $readme .= "          galeri/              → foto galeri kendaraan\n";
        $readme .= "          stnk/                → foto STNK\n";

        if (!empty($missing)) {
            $readme .= "\nFile yang tercatat di database tetapi tidak ditemukan di server:\n";
            foreach (array_slice($missing, 0, 500) as $line) {
                $readme .= "  - {$line}\n";
            }
            if (count($missing) > 500) {
                $readme .= "  ... dan " . (count($missing) - 500) . " file lainnya\n";
            }
        }

        return $readme;
    }

    /** Sanitasi nama folder di dalam ZIP */
    private function sanitizeFolderName(string $name): string
    {
        $name = preg_replace('/[\/\\\\:*?"<>|]/', '', $name);
        $name = preg_replace('/\s+/', '_', $name);
        $name = preg_replace('/[^\w\-.]/', '_', $name);
        $name = trim($name, '_.');

        return substr($name !== '' ? $name : 'UNKNOWN', 0, 60);
    }

    // ══════════════════════════════════════════════════════════
    // PRIVATE — File, download, daftar backup
    // ══════════════════════════════════════════════════════════

    /**
     * Kirim file backup ke browser sepotong-sepotong (1MB per chunk).
     * Session dilepas dulu supaya halaman lain tetap bisa dibuka selama download.
     */
    private function streamBackup(string $filename, string $ext, string $mime)
    {
        $filepath = $this->resolveBackupPath($filename, [$ext]);

        if ($filepath === null) {
            return redirect()->to('/admin/backup')
                             ->with('error', 'File backup tidak ditemukan atau tidak valid.');
        }

        session()->close();
        @set_time_limit(0);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $handle = fopen($filepath, 'rb');
        if ($handle === false) {
            http_response_code(500);
            exit('Gagal membuka file backup.');
        }

        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Content-Transfer-Encoding: binary');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        while (!feof($handle) && !connection_aborted()) {
            echo fread($handle, 1048576);
            flush();
        }

        fclose($handle);
        exit;
    }

    /**
     * Validasi nama file backup dan kembalikan path lengkapnya.
     * Hanya file berawalan "backup_" di folder backup yang diizinkan.
     */
    private function resolveBackupPath(string $filename, array $allowedExt): ?string
    {
        $filename = basename($filename);

        if (!preg_match('/^backup_[A-Za-z0-9_\-]+\.(sql|zip)$/', $filename)) {
            return null;
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            return null;
        }

        $filepath = self::BACKUP_DIR . $filename;

        return is_file($filepath) ? $filepath : null;
    }

    private function getBackupList(): array
    {
        $files = array_merge(
            glob(self::BACKUP_DIR . 'backup_*.sql') ?: [],
            glob(self::BACKUP_DIR . 'backup_*.zip') ?: []
        );

        $backups = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $mtime    = (int) filemtime($file);

            $backups[] = [
                'filename'  => $filename,
                'ext'       => pathinfo($filename, PATHINFO_EXTENSION),
                'size'      => $this->formatSize((int) filesize($file)),
                'created'   => date('d M Y, H:i:s', $mtime),
                'timestamp' => $mtime,
                'type'      => str_starts_with($filename, 'backup_foto') ? 'foto' : 'database',
            ];
        }

        usort($backups, static fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $backups;
    }

    /** Hapus backup lama sesuai KEEP_LATEST (tidak aktif kalau KEEP_LATEST = 0) */
    private function cleanupOldBackups(string $type): void
    {
        if (self::KEEP_LATEST <= 0) {
            return;
        }

        $sameType = array_values(array_filter(
            $this->getBackupList(),
            static fn ($b) => $b['type'] === $type
        ));

        foreach (array_slice($sameType, self::KEEP_LATEST) as $old) {
            @unlink(self::BACKUP_DIR . $old['filename']);
        }
    }

    // ══════════════════════════════════════════════════════════
    // PRIVATE — Proses panjang, kunci, progress
    // ══════════════════════════════════════════════════════════

    /**
     * Siapkan request untuk proses lama:
     * - Lepas kunci session supaya polling progress tidak antre
     * - Hapus batas waktu eksekusi
     * - Tetap selesaikan proses walau tab browser ditutup
     */
    private function prepareLongProcess(): void
    {
        session()->close();
        @set_time_limit(0);
        ignore_user_abort(true);
    }

    /**
     * Cegah dua backup berjalan bersamaan.
     *
     * @return resource|true|null  resource = kunci didapat, true = lanjut tanpa kunci,
     *                             null = backup lain sedang berjalan
     */
    private function acquireLock()
    {
        $handle = @fopen(self::BACKUP_DIR . self::LOCK_FILE, 'c');

        if ($handle === false) {
            // Tidak bisa membuat file kunci, lanjutkan tanpa kunci daripada gagal total
            return true;
        }

        if (!flock($handle, LOCK_EX | LOCK_NB)) {
            fclose($handle);
            return null;
        }

        return $handle;
    }

    private function releaseLock($lock): void
    {
        if (is_resource($lock)) {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /**
     * Tulis file progress secara atomik (tulis ke file sementara lalu rename),
     * supaya polling tidak pernah membaca JSON setengah jadi.
     */
    private function writeProgress(
        string $file,
        int    $current,
        int    $total,
        string $message,
        string $filename = '',
        int    $percent  = 0,
        string $status   = 'running'
    ): void {
        $json = json_encode([
            'status'   => $status,
            'current'  => $current,
            'total'    => $total,
            'percent'  => max(0, min(100, $percent)),
            'message'  => $message,
            'filename' => $filename,
        ]);

        $tmp = $file . '.tmp';
        if (@file_put_contents($tmp, $json) !== false) {
            @rename($tmp, $file);
        }
    }

    private function readProgress(string $file): array
    {
        if (!is_file($file)) {
            return [
                'status'  => 'idle',
                'percent' => 0,
                'message' => 'Tidak ada proses berjalan.',
            ];
        }

        $data = json_decode((string) @file_get_contents($file), true);

        return is_array($data)
            ? $data
            : ['status' => 'running', 'percent' => 0, 'message' => 'Memproses...'];
    }

    // ══════════════════════════════════════════════════════════
    // PRIVATE — Helper umum
    // ══════════════════════════════════════════════════════════

    private function safeFileName(string $name): string
    {
        $name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $name);
        return $name !== '' ? $name : 'database';
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}