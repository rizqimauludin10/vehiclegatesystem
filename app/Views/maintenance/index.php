<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
    $active       = (bool) $state['enabled'];
    $sinceStr     = $state['since'] ? date('d M Y, H:i', strtotime($state['since'])) : null;
    $scopePetugas = ($state['scope'] ?? 'semua') === 'petugas';
    $oldScope     = old('scope', $state['scope'] ?? 'semua');
?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Mode Maintenance</h5>
        <p class="page-header-sub">Tutup akses sistem sementara saat pemeliharaan, tanpa mengubah file .env</p>
    </div>
    <a href="<?= base_url('admin/maintenance/preview') ?>" target="_blank" class="btn-outline-custom">
        <i class="bi bi-eye me-1"></i> Lihat tampilan
    </a>
</div>

<!-- Status -->
<div class="mt-status <?= $active ? 'on' : 'off' ?> mb-4">
    <div class="mt-status-icon">
        <i class="bi <?= $active ? 'bi-cone-striped' : 'bi-check2-circle' ?>"></i>
    </div>
    <div class="mt-status-body">
        <div class="mt-status-title">
            <?= $active ? 'Maintenance sedang aktif' : 'Sistem berjalan normal' ?>
        </div>
        <div class="mt-status-desc">
            <?php if ($forcedByEnv): ?>
            Dikunci dari file <code>.env</code> (<code>app.maintenance = true</code>). Ubah di <code>.env</code> untuk
            mematikannya.
            <?php elseif ($active): ?>
            Diaktifkan oleh
            <strong><?= esc($state['by'] ?? '-') ?></strong><?= $sinceStr ? ' sejak ' . esc($sinceStr) : '' ?>.
            <?php if ($scopePetugas): ?>
            Hanya petugas yang diblokir, admin tetap bisa input data.
            <?php else: ?>
            Admin dan petugas diblokir, hanya Super Admin yang bisa masuk.
            <?php endif; ?>
            <?php else: ?>
            Semua pengguna bisa mengakses aplikasi seperti biasa.
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card-custom">
    <?php if ($active): ?>

    <!-- Form MATIKAN -->
    <div class="mt-card-body">
        <div class="mt-summary">
            <div>
                <div class="mt-label">Yang diblokir</div>
                <div class="mt-value"><?= $scopePetugas ? 'Hanya petugas' : 'Admin & petugas' ?></div>
            </div>
            <div>
                <div class="mt-label">Estimasi selesai</div>
                <div class="mt-value"><?= esc($state['estimasi']) ?></div>
            </div>
            <div>
                <div class="mt-label">Pesan untuk pengguna</div>
                <div class="mt-value">
                    <?= $state['pesan'] !== '' ? esc($state['pesan']) : '<span class="text-muted">Pesan bawaan</span>' ?>
                </div>
            </div>
        </div>

        <?php if (!$forcedByEnv): ?>
        <form action="<?= base_url('admin/maintenance/toggle') ?>" method="POST"
            data-confirm-title="Matikan mode maintenance?"
            data-confirm="Semua pengguna akan bisa mengakses sistem kembali setelah memuat ulang halaman."
            data-confirm-ok="Ya, matikan" data-confirm-variant="success">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="disable">
            <button type="submit" class="mt-btn mt-btn-on">
                <i class="bi bi-play-circle me-1"></i> Matikan maintenance
            </button>
        </form>
        <?php endif; ?>
    </div>

    <?php else: ?>

    <!-- Form AKTIFKAN -->
    <form action="<?= base_url('admin/maintenance/toggle') ?>" method="POST" class="mt-card-body"
        data-confirm-title="Aktifkan mode maintenance?"
        data-confirm="Pengguna yang diblokir akan melihat halaman maintenance, dan petugas tidak bisa scan QR di gerbang."
        data-confirm-ok="Ya, aktifkan" data-confirm-variant="warning">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="enable">

        <div class="mt-label">Siapa yang diblokir?</div>
        <div class="mt-scope">
            <label class="mt-scope-option">
                <input type="radio" name="scope" value="semua" <?= $oldScope !== 'petugas' ? 'checked' : '' ?>>
                <span>
                    <span class="mt-scope-title">Admin dan petugas</span>
                    <span class="mt-scope-desc">Hanya Super Admin yang bisa masuk. Pakai saat update sistem, restore
                        database, atau pindah server.</span>
                </span>
            </label>
            <label class="mt-scope-option">
                <input type="radio" name="scope" value="petugas" <?= $oldScope === 'petugas' ? 'checked' : '' ?>>
                <span>
                    <span class="mt-scope-title">Hanya petugas</span>
                    <span class="mt-scope-desc">Admin tetap bisa input data. Pakai saat gerbang sedang tidak dijaga
                        lewat sistem.</span>
                </span>
            </label>
        </div>

        <label class="mt-label mt-3" for="estimasi">Estimasi selesai</label>
        <input type="text" id="estimasi" name="estimasi" class="mt-input" maxlength="50"
            value="<?= esc(old('estimasi', $state['estimasi'])) ?>" placeholder="Contoh: ± 15 - 30 Menit">

        <label class="mt-label mt-3" for="pesan">Pesan untuk pengguna <span class="text-muted">(opsional)</span></label>
        <textarea id="pesan" name="pesan" class="mt-input" rows="3" maxlength="300"
            placeholder="Kosongkan untuk memakai pesan bawaan"><?= esc(old('pesan', $state['pesan'])) ?></textarea>

        <div class="mt-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <span>
                Selama maintenance, petugas tidak bisa scan QR, jadi pastikan palang gerbang sudah dialihkan ke mode
                manual.
                Kalau memilih "Admin dan petugas", minta rekan menyimpan pekerjaannya dulu: form yang dikirim setelah
                maintenance menyala tidak akan tersimpan.
            </span>
        </div>

        <button type="submit" class="mt-btn mt-btn-off">
            <i class="bi bi-cone-striped me-1"></i> Aktifkan maintenance
        </button>
    </form>

    <?php endif; ?>
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

.btn-outline-custom {
    display: inline-flex;
    align-items: center;
    background: #fff;
    color: #374151;
    font-size: .82rem;
    font-weight: 500;
    padding: .48rem .95rem;
    border-radius: 9px;
    text-decoration: none;
    border: 1px solid #d1d5db;
}

.btn-outline-custom:hover {
    background: #f9fafb;
    color: #111827;
}

.card-custom {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    max-width: 640px;
}

/* Status */
.mt-status {
    display: flex;
    align-items: center;
    gap: 1rem;
    border-radius: 14px;
    padding: 1.1rem 1.25rem;
    border: 1px solid;
    max-width: 640px;
}

.mt-status.off {
    background: #f0fdf4;
    border-color: #bbf7d0;
}

.mt-status.on {
    background: #fffbeb;
    border-color: #fde68a;
}

.mt-status-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

.mt-status.off .mt-status-icon {
    background: #dcfce7;
    color: #166534;
}

.mt-status.on .mt-status-icon {
    background: #fef3c7;
    color: #b45309;
}

.mt-status-title {
    font-size: .92rem;
    font-weight: 600;
    color: #111827;
}

.mt-status-desc {
    font-size: .8rem;
    color: #6b7280;
    margin-top: 2px;
    line-height: 1.5;
}

.mt-status-desc code {
    font-size: .75rem;
    background: rgba(0, 0, 0, .06);
    padding: 1px 5px;
    border-radius: 4px;
}

/* Form */
.mt-card-body {
    padding: 1.25rem;
}

.mt-label {
    display: block;
    font-size: .78rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
}

.mt-input {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 9px;
    padding: .6rem .85rem;
    font-size: .85rem;
    color: #111827;
    outline: none;
    resize: vertical;
}

.mt-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1);
}

.mt-warning {
    display: flex;
    align-items: flex-start;
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #92400e;
    border-radius: 10px;
    padding: .7rem .9rem;
    font-size: .78rem;
    line-height: 1.5;
    margin: 1rem 0;
}

.mt-scope {
    display: flex;
    flex-direction: column;
    gap: .5rem;
}

.mt-scope-option {
    display: flex;
    align-items: flex-start;
    gap: .65rem;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: .7rem .85rem;
    cursor: pointer;
}

.mt-scope-option:has(input:checked) {
    border-color: #16a34a;
    background: #f0fdf4;
}

.mt-scope-option input {
    margin-top: 3px;
    accent-color: #16a34a;
}

.mt-scope-title {
    display: block;
    font-size: .84rem;
    font-weight: 600;
    color: #111827;
}

.mt-scope-desc {
    display: block;
    font-size: .76rem;
    color: #6b7280;
    line-height: 1.5;
    margin-top: 2px;
}

.mt-summary {
    display: grid;
    grid-template-columns: 1fr 1fr 2fr;
    gap: 1rem;
    margin-bottom: 1.25rem;
}

@media (max-width: 575.98px) {
    .mt-summary {
        grid-template-columns: 1fr;
    }
}

.mt-value {
    font-size: .88rem;
    color: #111827;
    font-weight: 500;
}

.mt-btn {
    display: inline-flex;
    align-items: center;
    border: none;
    border-radius: 9px;
    padding: .6rem 1.15rem;
    font-size: .84rem;
    font-weight: 600;
    cursor: pointer;
    color: #fff;
}

.mt-btn-off {
    background: #d97706;
}

.mt-btn-off:hover {
    background: #b45309;
}

.mt-btn-on {
    background: #16a34a;
}

.mt-btn-on:hover {
    background: #15803d;
}
</style>
<?= $this->endSection() ?>