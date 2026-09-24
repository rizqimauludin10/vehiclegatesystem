<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Database Backup</h5>
        <p class="page-header-sub">
            Buat dan kelola backup database & foto
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <!-- Backup Database -->
        <form action="<?= base_url('admin/backup/create') ?>" method="POST" onsubmit="return confirmBackup('database')">
            <?= csrf_field() ?>
            <!-- Backup Database — AJAX -->
            <button type="button" class="btn-primary-custom" onclick="startBackupDatabase()">
                <i class="bi bi-database-add me-1"></i> Backup Database
            </button>
        </form>
        <!-- Backup Foto -->
        <form action="<?= base_url('admin/backup/foto') ?>" method="POST" onsubmit="return confirmBackup('foto')">
            <?= csrf_field() ?>
            <!-- Backup Foto — pakai AJAX -->
            <button type="button" class="btn-foto-custom" onclick="startBackupFoto()">
                <i class="bi bi-file-earmark-zip me-1"></i> Backup Foto
            </button>
        </form>
    </div>
</div>

<!-- Info Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-3">
        <div class="info-banner blue">
            <i class="bi bi-info-circle-fill"></i>
            <div>
                <div class="info-banner-title">Lokasi Backup</div>
                <div class="info-banner-sub">
                    <code>writable/backups/</code>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="info-banner green">
            <i class="bi bi-file-earmark-code-fill"></i>
            <div>
                <div class="info-banner-title">Backup Database</div>
                <div class="info-banner-sub">
                    <?= count(array_filter($backups, fn($b) => $b['type'] === 'database')) ?> file .sql
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="info-banner purple">
            <i class="bi bi-file-earmark-zip-fill"></i>
            <div>
                <div class="info-banner-title">Backup Foto</div>
                <div class="info-banner-sub">
                    <?= count(array_filter($backups, fn($b) => $b['type'] === 'foto')) ?> file .zip
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="info-banner amber">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <div class="info-banner-title">Rekomendasi</div>
                <div class="info-banner-sub">Backup rutin setiap hari</div>
            </div>
        </div>
    </div>
</div>
<!-- Daftar Backup -->
<div class="card-custom">
    <div class="card-custom-header">
        <div>
            <div class="card-custom-title">
                <i class="bi bi-archive me-2 text-success"></i>
                Riwayat Backup
            </div>
            <div class="card-custom-sub">
                File backup tersimpan di server
            </div>
        </div>
    </div>

    <?php if (empty($backups)): ?>
    <div class="empty-state">
        <i class="bi bi-database-slash"></i>
        <div>Belum ada file backup</div>
        <p style="font-size:.78rem;color:#9ca3af;margin-top:6px;">
            Klik "Buat Backup Sekarang" untuk membuat backup pertama
        </p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Nama File</th>
                    <th>Ukuran</th>
                    <th>Dibuat</th>
                    <th width="140">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($backups as $i => $backup): ?>
                <tr>
                    <td class="text-muted"><?= $i + 1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="backup-icon <?= $backup['type'] === 'foto' ? 'foto' : '' ?>">
                                <i class="bi <?= $backup['type'] === 'foto'
                        ? 'bi-file-earmark-zip'
                        : 'bi-file-earmark-code' ?>">
                                </i>
                            </div>
                            <div>
                                <div class="fw-500" style="font-size:.83rem;">
                                    <?= esc($backup['filename']) ?>
                                </div>
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    <?php if ($i === 0): ?>
                                    <span class="badge-latest">Terbaru</span>
                                    <?php endif; ?>
                                    <span class="badge-type <?= $backup['type'] ?>">
                                        <?= $backup['type'] === 'foto' ? '📷 Foto ZIP' : '🗄️ Database SQL' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="size-badge"><?= esc($backup['size']) ?></span>
                    </td>
                    <td style="font-size:.82rem;color:#6b7280;">
                        <?= esc($backup['created']) ?>
                    </td>
                    <td>
                        <div class="action-wrap">
                            <!-- Download — sesuai tipe -->
                            <?php
                $downloadUrl = $backup['type'] === 'foto'
                    ? base_url('admin/backup/downloadfoto/' . $backup['filename'])
                    : base_url('admin/backup/download/' . $backup['filename']);
                ?>
                            <a href="<?= $downloadUrl ?>" class="btn-action download" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                            <!-- Hapus -->
                            <form action="<?= base_url('admin/backup/delete/' . $backup['filename']) ?>" method="POST"
                                class="d-inline form-hapus-backup" data-filename="<?= esc($backup['filename']) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-action delete" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Panduan -->
<div class="guide-card mt-4">
    <div class="guide-title">
        <i class="bi bi-lightbulb me-2"></i>
        Tips Backup
    </div>
    <div class="guide-list">
        <div class="guide-item">
            <i class="bi bi-check-circle text-success"></i>
            Lakukan backup sebelum update data besar atau perubahan sistem
        </div>
        <div class="guide-item">
            <i class="bi bi-check-circle text-success"></i>
            Download file backup dan simpan di tempat lain (USB/NAS) secara berkala
        </div>
        <div class="guide-item">
            <i class="bi bi-check-circle text-success"></i>
            Hapus backup lama yang sudah tidak diperlukan untuk menghemat storage
        </div>
        <div class="guide-item">
            <i class="bi bi-check-circle text-success"></i>
            File backup berformat SQL — bisa di-restore via Sequel Ace, phpMyAdmin, atau MySQL CLI
        </div>
    </div>
</div>
<!-- Modal Progress Backup Foto -->
<div class="modal fade" id="modalBackupProgress" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-progress-content">

            <!-- Header -->
            <div class="modal-progress-header">
                <div class="modal-progress-icon" id="progressIcon">
                    <i class="bi bi-file-earmark-zip"></i>
                </div>
                <div>
                    <div class="modal-progress-title" id="progressTitle">
                        Membuat Backup Foto
                    </div>
                    <div class="modal-progress-sub" id="progressSub">
                        Mohon tunggu, jangan tutup halaman ini...
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="modal-progress-body">

                <!-- Progress Bar -->
                <div class="progress-wrap">
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" id="progressBarFill" style="width:0%"></div>
                    </div>
                    <div class="progress-percent" id="progressPercent">0%</div>
                </div>

                <!-- Pesan status -->
                <div class="progress-message" id="progressMessage">
                    Menyiapkan proses backup...
                </div>

                <!-- Detail counter -->
                <div class="progress-detail" id="progressDetail" style="display:none;">
                    <div class="detail-item">
                        <span>Personel diproses</span>
                        <strong id="detailCurrent">0</strong>
                    </div>
                    <div class="detail-item">
                        <span>Total personel</span>
                        <strong id="detailTotal">0</strong>
                    </div>
                </div>

                <!-- State: Done -->
                <div class="progress-done" id="progressDone" style="display:none;">
                    <div class="done-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="done-title">Backup Berhasil!</div>
                    <div class="done-sub" id="doneSub"></div>
                    <a href="#" id="doneDownloadBtn" class="btn-download-result">
                        <i class="bi bi-download me-1"></i>
                        Download ZIP Sekarang
                    </a>
                </div>

                <!-- State: Error -->
                <div class="progress-error" id="progressError" style="display:none;">
                    <div class="error-icon-wrap">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div class="error-title-wrap">Backup Gagal</div>
                    <div class="error-msg-wrap" id="errorMsgWrap"></div>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-progress-footer" id="progressFooter">
                <div class="footer-warning">
                    <i class="bi bi-info-circle me-1"></i>
                    Jangan tutup atau refresh halaman selama proses berjalan
                </div>
            </div>

            <!-- Footer Done -->
            <div class="modal-progress-footer" id="progressFooterDone" style="display:none;">
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Tutup & Refresh
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Modal Progress Backup Database -->
<div class="modal fade" id="modalBackupDb" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-progress-content">

            <!-- Header -->
            <div class="modal-progress-header" style="background:linear-gradient(135deg,#166534,#16a34a);">
                <div class="modal-progress-icon" id="dbProgressIcon">
                    <i class="bi bi-database-add"></i>
                </div>
                <div>
                    <div class="modal-progress-title" id="dbProgressTitle">
                        Membuat Backup Database
                    </div>
                    <div class="modal-progress-sub" id="dbProgressSub">
                        Mohon tunggu, jangan tutup halaman ini...
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="modal-progress-body">

                <!-- Progress Bar -->
                <div class="progress-wrap">
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" id="dbProgressBarFill"
                            style="width:0%;background:linear-gradient(90deg,#166534,#16a34a);">
                        </div>
                    </div>
                    <div class="progress-percent" id="dbProgressPercent" style="color:#16a34a;">0%</div>
                </div>

                <!-- Pesan status -->
                <div class="progress-message" id="dbProgressMessage">
                    Menyiapkan backup database...
                </div>

                <!-- Detail counter tabel -->
                <div class="progress-detail" id="dbProgressDetail" style="display:none;">
                    <div class="detail-item">
                        <span>Tabel diproses</span>
                        <strong id="dbDetailCurrent">0</strong>
                    </div>
                    <div class="detail-item">
                        <span>Total tabel</span>
                        <strong id="dbDetailTotal">0</strong>
                    </div>
                </div>

                <!-- State: Done -->
                <div class="progress-done" id="dbProgressDone" style="display:none;">
                    <div class="done-icon" style="color:#16a34a;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="done-title">Backup Database Berhasil!</div>
                    <div class="done-sub" id="dbDoneSub"></div>
                    <a href="#" id="dbDoneDownloadBtn" class="btn-download-result">
                        <i class="bi bi-download me-1"></i>
                        Download SQL Sekarang
                    </a>
                </div>

                <!-- State: Error -->
                <div class="progress-error" id="dbProgressError" style="display:none;">
                    <div class="error-icon-wrap">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div class="error-title-wrap">Backup Gagal</div>
                    <div class="error-msg-wrap" id="dbErrorMsg"></div>
                </div>

            </div>

            <!-- Footer running -->
            <div class="modal-progress-footer" id="dbProgressFooter">
                <div class="footer-warning">
                    <i class="bi bi-info-circle me-1"></i>
                    Jangan tutup atau refresh halaman selama proses berjalan
                </div>
            </div>

            <!-- Footer done -->
            <div class="modal-progress-footer" id="dbProgressFooterDone" style="display:none;">
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Tutup & Refresh
                </button>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<style>
.page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem
}

.page-header-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin: 0
}

.page-header-sub {
    font-size: .78rem;
    color: #9ca3af;
    margin: 2px 0 0
}

.btn-primary-custom {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: .82rem;
    font-weight: 500;
    padding: .5rem 1rem;
    border-radius: 9px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(22, 101, 52, .25)
}

.btn-primary-custom:hover {
    transform: translateY(-1px);
    color: #fff
}

/* Info Banner */
.info-banner {
    display: flex;
    align-items: center;
    gap: .85rem;
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 1rem 1.25rem;
    font-size: 1.25rem
}

.info-banner.blue {
    border-color: #bfdbfe;
    background: #eff6ff;
    color: #1d4ed8
}

.info-banner.green {
    border-color: #bbf7d0;
    background: #f0fdf4;
    color: #166534
}

.info-banner.amber {
    border-color: #fde68a;
    background: #fffbeb;
    color: #92400e
}

.info-banner-title {
    font-size: .78rem;
    font-weight: 600;
    color: #111827
}

.info-banner-sub {
    font-size: .72rem;
    color: #6b7280;
    margin-top: 1px
}

.info-banner code {
    font-size: .72rem;
    background: rgba(0, 0, 0, .06);
    padding: 1px 5px;
    border-radius: 4px
}

/* Card */
.card-custom {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    overflow: hidden
}

.card-custom-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
    flex-wrap: wrap;
    gap: .75rem
}

.card-custom-title {
    font-size: .9rem;
    font-weight: 600;
    color: #111827
}

.card-custom-sub {
    font-size: .75rem;
    color: #9ca3af
}

/* Table */
.table-custom {
    width: 100%;
    border-collapse: collapse;
    font-size: .83rem
}

.table-custom thead tr {
    background: #f9fafb
}

.table-custom th {
    padding: .65rem 1.25rem;
    font-size: .7rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap
}

.table-custom td {
    padding: .75rem 1.25rem;
    color: #374151;
    border-bottom: 1px solid #f9fafb;
    vertical-align: middle
}

.table-custom tbody tr:hover {
    background: #fafafa
}

.table-custom tbody tr:last-child td {
    border-bottom: none
}

.fw-500 {
    font-weight: 500
}

/* Foto backup button */
.btn-foto-custom {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #1d4ed8, #3b82f6);
    color: #fff;
    font-size: .82rem;
    font-weight: 500;
    padding: .5rem 1rem;
    border-radius: 9px;
    border: none;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(29, 78, 216, .25);
}

.btn-foto-custom:hover {
    transform: translateY(-1px);
}

/* Info banner purple */
.info-banner.purple {
    border-color: #ddd6fe;
    background: #f5f3ff;
    color: #5b21b6;
}

/* Backup icon types */
.backup-icon {
    width: 34px;
    height: 34px;
    background: #ecfdf5;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #16a34a;
    font-size: 1rem;
    flex-shrink: 0;
}

.backup-icon.foto {
    background: #eff6ff;
    color: #1d4ed8;
}

/* Badge type */
.badge-type {
    display: inline-block;
    font-size: .65rem;
    font-weight: 600;
    padding: 1px 7px;
    border-radius: 99px;
}

.badge-type.database {
    background: #dcfce7;
    color: #166534;
}

.badge-type.foto {
    background: #dbeafe;
    color: #1e40af;
}

/* Backup icon */
.backup-icon {
    width: 34px;
    height: 34px;
    background: #ecfdf5;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #16a34a;
    font-size: 1rem;
    flex-shrink: 0
}

/* Badges */
.badge-latest {
    display: inline-block;
    background: #dcfce7;
    color: #166534;
    font-size: .65rem;
    font-weight: 600;
    padding: 1px 7px;
    border-radius: 99px;
    margin-top: 2px
}

.size-badge {
    background: #f3f4f6;
    color: #374151;
    font-size: .75rem;
    font-weight: 500;
    padding: 2px 8px;
    border-radius: 5px;
    font-family: monospace
}

/* Actions */
.action-wrap {
    display: flex;
    align-items: center;
    gap: 5px
}

.btn-action {
    width: 30px;
    height: 30px;
    border-radius: 7px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
    text-decoration: none;
    transition: all .15s
}

.btn-action.download {
    background: #eff6ff;
    color: #1d4ed8
}

.btn-action.download:hover {
    background: #dbeafe
}

.btn-action.delete {
    background: #fef2f2;
    color: #dc2626
}

.btn-action.delete:hover {
    background: #fee2e2
}

/* Empty */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #9ca3af
}

.empty-state i {
    font-size: 2.5rem;
    display: block;
    margin-bottom: .75rem;
    opacity: .4
}

/* Guide */
.guide-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    padding: 1.25rem
}

.guide-title {
    font-size: .85rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: .85rem;
    display: flex;
    align-items: center
}

.guide-list {
    display: flex;
    flex-direction: column;
    gap: .6rem
}

.guide-item {
    display: flex;
    align-items: flex-start;
    gap: .65rem;
    font-size: .78rem;
    color: #6b7280
}

.guide-item i {
    font-size: .85rem;
    flex-shrink: 0;
    margin-top: 1px
}

/* ── Modal Progress ── */
.modal-progress-content {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, .15);
}

.modal-progress-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.35rem 1.5rem;
    background: linear-gradient(135deg, #1d4ed8, #3b82f6);
    color: #fff;
}

.modal-progress-icon {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, .15);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}

.modal-progress-title {
    font-size: .95rem;
    font-weight: 700;
    line-height: 1.2;
}

.modal-progress-sub {
    font-size: .75rem;
    opacity: .8;
    margin-top: 2px;
}

.modal-progress-body {
    padding: 1.5rem;
}

/* Progress bar */
.progress-wrap {
    display: flex;
    align-items: center;
    gap: .85rem;
    margin-bottom: 1rem;
}

.progress-bar-track {
    flex: 1;
    height: 10px;
    background: #f3f4f6;
    border-radius: 99px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #1d4ed8, #3b82f6);
    border-radius: 99px;
    transition: width .4s ease;
    position: relative;
    overflow: hidden;
}

/* Shimmer effect */
.progress-bar-fill::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg,
            transparent 0%,
            rgba(255, 255, 255, .35) 50%,
            transparent 100%);
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    from {
        transform: translateX(-100%);
    }

    to {
        transform: translateX(100%);
    }
}

.progress-percent {
    font-size: .85rem;
    font-weight: 700;
    color: #1d4ed8;
    min-width: 38px;
    text-align: right;
    flex-shrink: 0;
}

.progress-message {
    font-size: .8rem;
    color: #6b7280;
    margin-bottom: .85rem;
    min-height: 20px;
    line-height: 1.4;
}

/* Detail counter */
.progress-detail {
    background: #f9fafb;
    border: 1px solid #f3f4f6;
    border-radius: 10px;
    padding: .75rem 1rem;
    display: flex;
    flex-direction: column;
    gap: .35rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: .78rem;
    color: #6b7280;
}

.detail-item strong {
    color: #111827;
    font-size: .82rem;
}

/* Done state */
.progress-done {
    text-align: center;
    padding: .5rem 0;
}

.done-icon {
    font-size: 2.5rem;
    color: #16a34a;
    margin-bottom: .5rem;
    animation: bounceIn .4s cubic-bezier(.22, .61, .36, 1);
}

@keyframes bounceIn {
    from {
        transform: scale(0);
        opacity: 0;
    }

    to {
        transform: scale(1);
        opacity: 1;
    }
}

.done-title {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 4px;
}

.done-sub {
    font-size: .78rem;
    color: #6b7280;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.btn-download-result {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    border-radius: 10px;
    padding: .65rem 1.25rem;
    font-size: .85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
    box-shadow: 0 3px 10px rgba(22, 101, 52, .3);
}

.btn-download-result:hover {
    transform: translateY(-1px);
    color: #fff;
}

/* Error state */
.progress-error {
    text-align: center;
    padding: .5rem 0;
}

.error-icon-wrap {
    font-size: 2.5rem;
    color: #ef4444;
    margin-bottom: .5rem;
}

.error-title-wrap {
    font-size: 1rem;
    font-weight: 700;
    color: #dc2626;
    margin-bottom: 4px;
}

.error-msg-wrap {
    font-size: .78rem;
    color: #6b7280;
}

/* Footer */
.modal-progress-footer {
    padding: .85rem 1.5rem;
    background: #f9fafb;
    border-top: 1px solid #f3f4f6;
}

.footer-warning {
    font-size: .75rem;
    color: #9ca3af;
    display: flex;
    align-items: center;
}

.btn-close-modal {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #111827;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: .65rem;
    font-size: .85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
}

.btn-close-modal:hover {
    background: #1f2937;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ── Backup Database ──────────────────────────────────────
function confirmBackup(type) {
    if (type === 'database') {
        return confirm(
            'Buat backup database sekarang?\n\n' +
            'Proses ini mungkin memerlukan beberapa detik.'
        );
    }
    return true;
}

// ── Backup Foto — AJAX + Progress ───────────────────────
let pollingInterval = null;

function startBackupFoto() {
    // Reset modal ke state awal
    resetProgressModal();

    // Tampilkan modal
    const modal = new bootstrap.Modal(
        document.getElementById('modalBackupProgress')
    );
    modal.show();

    // Kirim request backup via AJAX
    fetch('<?= base_url('admin/backup/fotoajax') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: '<?= csrf_token() ?>=<?= csrf_hash() ?>',
        })
        .then(res => res.json())
        .then(data => {
            // Stop polling
            stopPolling();

            if (data.error) {
                showError(data.error);
                return;
            }

            // Tampilkan done state
            showDone(data);
        })
        .catch(err => {
            stopPolling();
            showError('Koneksi ke server terputus: ' + err.message);
        });

    // Mulai polling progress setiap 800ms
    startPolling();
}

function startPolling() {
    pollingInterval = setInterval(() => {
        fetch('<?= base_url('admin/backup/progress') ?>', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'done' || data.status === 'error') {
                    stopPolling();
                    return;
                }
                updateProgressUI(data);
            })
            .catch(() => {}); // Silent fail untuk polling
    }, 800);
}

function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

function updateProgressUI(data) {
    const percent = data.percent || 0;
    const current = data.current || 0;
    const total = data.total || 0;

    // Update progress bar
    document.getElementById('progressBarFill').style.width = percent + '%';
    document.getElementById('progressPercent').textContent = percent + '%';

    // Update pesan
    document.getElementById('progressMessage').textContent =
        data.message || 'Memproses...';

    // Tampilkan detail counter kalau total sudah ada
    if (total > 0) {
        const detail = document.getElementById('progressDetail');
        detail.style.display = 'flex';
        document.getElementById('detailCurrent').textContent = current;
        document.getElementById('detailTotal').textContent = total;
    }
}

function showDone(data) {
    // Sembunyikan progress, tampilkan done
    document.getElementById('progressBarFill').style.width = '100%';
    document.getElementById('progressPercent').textContent = '100%';
    document.getElementById('progressMessage').style.display = 'none';
    document.getElementById('progressDetail').style.display = 'none';

    // Update header
    document.getElementById('progressIcon').innerHTML =
        '<i class="bi bi-check-circle-fill"></i>';
    document.getElementById('progressIcon').style.background = 'rgba(22,197,94,.2)';
    document.getElementById('progressTitle').textContent = 'Backup Selesai!';
    document.getElementById('progressSub').textContent =
        'File ZIP berhasil dibuat';

    // Progress bar hijau
    document.getElementById('progressBarFill').style.background =
        'linear-gradient(90deg, #166534, #16a34a)';

    // Tampilkan done content
    const doneEl = document.getElementById('progressDone');
    doneEl.style.display = 'block';

    document.getElementById('doneSub').textContent =
        `${data.total} foto ter-backup · Ukuran: ${data.size}` +
        (data.skip > 0 ? ` · ${data.skip} file tidak ditemukan` : '');

    // Set URL download
    document.getElementById('doneDownloadBtn').href =
        '<?= base_url('admin/backup/downloadfoto/') ?>' + data.filename;

    // Ganti footer
    document.getElementById('progressFooter').style.display = 'none';
    document.getElementById('progressFooterDone').style.display = 'block';
}

function showError(message) {
    document.getElementById('progressMessage').style.display = 'none';
    document.getElementById('progressDetail').style.display = 'none';
    document.getElementById('progressError').style.display = 'block';
    document.getElementById('errorMsgWrap').textContent = message;

    document.getElementById('progressIcon').innerHTML =
        '<i class="bi bi-x-circle-fill"></i>';
    document.getElementById('progressIcon').style.background = 'rgba(239,68,68,.2)';
    document.getElementById('progressTitle').textContent = 'Backup Gagal';
    document.getElementById('progressSub').textContent = 'Terjadi kesalahan';

    document.getElementById('progressFooter').style.display = 'none';
    document.getElementById('progressFooterDone').style.display = 'block';
}

function resetProgressModal() {
    // Reset semua ke state awal
    document.getElementById('progressBarFill').style.width = '0%';
    document.getElementById('progressBarFill').style.background = 'linear-gradient(90deg, #1d4ed8, #3b82f6)';
    document.getElementById('progressPercent').textContent = '0%';
    document.getElementById('progressMessage').textContent = 'Menyiapkan proses backup...';
    document.getElementById('progressMessage').style.display = 'block';
    document.getElementById('progressDetail').style.display = 'none';
    document.getElementById('progressDone').style.display = 'none';
    document.getElementById('progressError').style.display = 'none';
    document.getElementById('progressIcon').innerHTML = '<i class="bi bi-file-earmark-zip"></i>';
    document.getElementById('progressIcon').style.background = 'rgba(255,255,255,.15)';
    document.getElementById('progressTitle').textContent = 'Membuat Backup Foto';
    document.getElementById('progressSub').textContent = 'Mohon tunggu, jangan tutup halaman ini...';
    document.getElementById('progressFooter').style.display = 'block';
    document.getElementById('progressFooterDone').style.display = 'none';
    document.getElementById('detailCurrent').textContent = '0';
    document.getElementById('detailTotal').textContent = '0';
}

// Event delegation untuk hapus backup
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form.classList.contains('form-hapus-backup')) {
        const filename = form.dataset.filename;
        if (!confirm(
                `Hapus file backup "${filename}"?\n\nFile yang dihapus tidak bisa dikembalikan.`
            )) {
            e.preventDefault();
        }
    }
});

// ── Backup Database AJAX + Progress ─────────────────────
let dbPollingInterval = null;

function startBackupDatabase() {
    resetDbProgressModal();

    const modal = new bootstrap.Modal(
        document.getElementById('modalBackupDb')
    );
    modal.show();

    // Kirim request backup
    fetch('<?= base_url('admin/backup/createajax') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: '<?= csrf_token() ?>=<?= csrf_hash() ?>',
        })
        .then(res => res.json())
        .then(data => {
            stopDbPolling();
            if (data.error) {
                showDbError(data.error);
                return;
            }
            showDbDone(data);
        })
        .catch(err => {
            stopDbPolling();
            showDbError('Koneksi ke server terputus: ' + err.message);
        });

    // Mulai polling progress
    startDbPolling();
}

function startDbPolling() {
    dbPollingInterval = setInterval(() => {
        fetch('<?= base_url('admin/backup/progressdb') ?>', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'done' || data.status === 'error') {
                    stopDbPolling();
                    return;
                }
                updateDbProgressUI(data);
            })
            .catch(() => {});
    }, 600);
}

function stopDbPolling() {
    if (dbPollingInterval) {
        clearInterval(dbPollingInterval);
        dbPollingInterval = null;
    }
}

function updateDbProgressUI(data) {
    const percent = data.percent || 0;
    const current = data.current || 0;
    const total = data.total || 0;

    document.getElementById('dbProgressBarFill').style.width = percent + '%';
    document.getElementById('dbProgressPercent').textContent = percent + '%';
    document.getElementById('dbProgressMessage').textContent =
        data.message || 'Memproses...';

    if (total > 0) {
        document.getElementById('dbProgressDetail').style.display = 'flex';
        document.getElementById('dbDetailCurrent').textContent = current;
        document.getElementById('dbDetailTotal').textContent = total;
    }
}

function showDbDone(data) {
    document.getElementById('dbProgressBarFill').style.width = '100%';
    document.getElementById('dbProgressPercent').textContent = '100%';
    document.getElementById('dbProgressMessage').style.display = 'none';
    document.getElementById('dbProgressDetail').style.display = 'none';

    // Update header
    document.getElementById('dbProgressIcon').innerHTML =
        '<i class="bi bi-check-circle-fill"></i>';
    document.getElementById('dbProgressIcon').style.background =
        'rgba(255,255,255,.2)';
    document.getElementById('dbProgressTitle').textContent = 'Backup Selesai!';
    document.getElementById('dbProgressSub').textContent =
        'File SQL berhasil dibuat';

    // Tampilkan done
    document.getElementById('dbProgressDone').style.display = 'block';
    document.getElementById('dbDoneSub').textContent =
        `${data.total} tabel ter-backup · Ukuran: ${data.size}`;

    document.getElementById('dbDoneDownloadBtn').href =
        '<?= base_url('admin/backup/download/') ?>' + data.filename;

    document.getElementById('dbProgressFooter').style.display = 'none';
    document.getElementById('dbProgressFooterDone').style.display = 'block';
}

function showDbError(message) {
    document.getElementById('dbProgressMessage').style.display = 'none';
    document.getElementById('dbProgressDetail').style.display = 'none';
    document.getElementById('dbProgressError').style.display = 'block';
    document.getElementById('dbErrorMsg').textContent = message;

    document.getElementById('dbProgressIcon').innerHTML =
        '<i class="bi bi-x-circle-fill"></i>';
    document.getElementById('dbProgressIcon').style.background =
        'rgba(239,68,68,.2)';
    document.getElementById('dbProgressTitle').textContent = 'Backup Gagal';
    document.getElementById('dbProgressSub').textContent = 'Terjadi kesalahan';

    document.getElementById('dbProgressFooter').style.display = 'none';
    document.getElementById('dbProgressFooterDone').style.display = 'block';
}

function resetDbProgressModal() {
    document.getElementById('dbProgressBarFill').style.width = '0%';
    document.getElementById('dbProgressBarFill').style.background =
        'linear-gradient(90deg,#166534,#16a34a)';
    document.getElementById('dbProgressPercent').textContent = '0%';
    document.getElementById('dbProgressMessage').textContent =
        'Menyiapkan backup database...';
    document.getElementById('dbProgressMessage').style.display = 'block';
    document.getElementById('dbProgressDetail').style.display = 'none';
    document.getElementById('dbProgressDone').style.display = 'none';
    document.getElementById('dbProgressError').style.display = 'none';
    document.getElementById('dbProgressIcon').innerHTML =
        '<i class="bi bi-database-add"></i>';
    document.getElementById('dbProgressIcon').style.background =
        'rgba(255,255,255,.15)';
    document.getElementById('dbProgressTitle').textContent =
        'Membuat Backup Database';
    document.getElementById('dbProgressSub').textContent =
        'Mohon tunggu, jangan tutup halaman ini...';
    document.getElementById('dbProgressFooter').style.display = 'block';
    document.getElementById('dbProgressFooterDone').style.display = 'none';
    document.getElementById('dbDetailCurrent').textContent = '0';
    document.getElementById('dbDetailTotal').textContent = '0';
}
</script>
<?= $this->endSection() ?>