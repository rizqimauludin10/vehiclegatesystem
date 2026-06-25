<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Management QR Code</h5>
        <p class="page-header-sub">Generate & kelola QR Code per kendaraan</p>
    </div>
    <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalGenerate">
        <i class="bi bi-qr-code me-1"></i> Generate QR Baru
    </button>
</div>

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-icon" style="background:linear-gradient(135deg,#166534,#16a34a);">
                <i class="bi bi-qr-code"></i>
            </div>
            <div>
                <div class="summary-num"><?= $totalQrAktif ?></div>
                <div class="summary-label">QR Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-icon" style="background:linear-gradient(135deg,#1d4ed8,#3b82f6);">
                <i class="bi bi-car-front"></i>
            </div>
            <div>
                <div class="summary-num"><?= $totalKendaraan ?></div>
                <div class="summary-label">Total Kendaraan Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-icon" style="background:linear-gradient(135deg,#92400e,#d97706);">
                <i class="bi bi-exclamation-circle"></i>
            </div>
            <div>
                <div class="summary-num <?= $kendaraanTanpaQR > 0 ? 'text-warning' : '' ?>">
                    <?= $kendaraanTanpaQR ?>
                </div>
                <div class="summary-label">Kendaraan Belum Punya QR</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel QR -->
<div class="card-custom">
    <div class="card-custom-header">
        <div>
            <div class="card-custom-title">
                <i class="bi bi-qr-code me-2 text-success"></i>Daftar QR Code
            </div>
            <div class="card-custom-sub"><?= count($qrList) ?> QR terdaftar</div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Filter Status -->
            <select id="filterStatus" class="filter-select">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
            <!-- Search -->
            <div class="search-wrap">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Cari plat / nama personel...">
            </div>
        </div>
    </div>

    <?php if (empty($qrList)): ?>
    <div class="empty-state">
        <i class="bi bi-qr-code"></i>
        <div>Belum ada QR Code yang di-generate</div>
        <button class="btn-primary-custom mt-3" data-bs-toggle="modal" data-bs-target="#modalGenerate">
            Generate QR Sekarang
        </button>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table-custom" id="qrTable" style="width:100%">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Kendaraan</th>
                    <th>Pemilik</th>
                    <th>Kode QR</th>
                    <th>Status</th>
                    <th>Di-generate</th>
                    <th width="160">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($qrList as $qr): ?>
                <tr data-status="<?= $qr['status'] ?>">
                    <td class="text-muted row-num"></td>

                    <!-- Kendaraan -->
                    <td>
                        <div class="fw-500">
                            <?= esc($qr['plat_nomor']) ?>
                        </div>
                        <div style="font-size:.75rem;color:#9ca3af;margin-top:1px;">
                            <span class="badge-jenis <?= $qr['jenis'] ?? '' ?>">
                                <?= ucfirst($qr['jenis'] ?? '-') ?>
                            </span>
                            <?= esc($qr['merk'] ?? '') ?> <?= esc($qr['model'] ?? '') ?>
                        </div>
                        <div style="font-size:.72rem;color:#9ca3af;">
                            <code class="code-tag"><?= esc($qr['kendaraan_kode'] ?? '-') ?></code>
                        </div>
                    </td>

                    <!-- Pemilik -->
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if (!empty($qr['personel_foto'])): ?>
                            <img src="<?= base_url('uploads/personel/' . $qr['personel_foto']) ?>" class="avatar-photo"
                                alt="">
                            <?php else: ?>
                            <div class="avatar-initial">
                                <?= strtoupper(substr($qr['personel_nama'] ?? '?', 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                            <div>
                                <div class="fw-500" style="font-size:.83rem;">
                                    <?= esc($qr['personel_nama'] ?? '-') ?>
                                </div>
                                <div style="font-size:.73rem;color:#9ca3af;">
                                    <?= esc($qr['pangkat_singkat'] ?? '') ?>
                                    · <code><?= esc($qr['nrp'] ?? '') ?></code>
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Kode QR -->
                    <td>
                        <code class="kode-unik-tag">
                                <?= substr($qr['kode_unik'], 0, 12) ?>...
                            </code>
                    </td>

                    <!-- Status -->
                    <td>
                        <?php if ($qr['status'] === 'aktif'): ?>
                        <span class="badge-status aktif">
                            <span class="badge-dot"></span> Aktif
                        </span>
                        <?php else: ?>
                        <span class="badge-status nonaktif">
                            <span class="badge-dot"></span> Nonaktif
                        </span>
                        <?php endif; ?>
                    </td>

                    <!-- Generated -->
                    <td style="font-size:.78rem;color:#6b7280;">
                        <?= date('d M Y, H:i', strtotime($qr['generated_at'])) ?>
                    </td>

                    <!-- Aksi -->
                    <td>
                        <div class="action-wrap">
                            <?php if ($qr['status'] === 'aktif'): ?>
                            <!-- Download QR PNG -->
                            <a href="<?= base_url('admin/qrcode/download/' . $qr['uuid']) ?>"
                                class="btn-action download" title="Download QR PNG">
                                <i class="bi bi-download"></i>
                            </a>
                            <!-- Regenerate -->
                            <button type="button" class="btn-action regenerate" title="Regenerate QR"
                                onclick="konfirmasiRegenerate('<?= base_url('admin/qrcode/generate/' . $qr['kendaraan_uuid']) ?>','<?= esc($qr['plat_nomor']) ?>')">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <!-- Revoke -->
                            <button type="button" class="btn-action revoke" title="Nonaktifkan QR"
                                onclick="konfirmasiRevoke('<?= base_url('admin/qrcode/revoke/' . $qr['uuid']) ?>','<?= esc($qr['plat_nomor']) ?>')">
                                <i class="bi bi-x-circle"></i>
                            </button>
                            <?php else: ?>
                            <!-- Regenerate untuk QR nonaktif -->
                            <!-- Regenerate untuk QR nonaktif -->
                            <button type="button" class="btn-action regenerate" title="Generate QR Baru"
                                onclick="konfirmasiRegenerate('<?= base_url('admin/qrcode/generate/' . $qr['kendaraan_uuid']) ?>','<?= esc($qr['plat_nomor']) ?>')">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Generate QR Baru -->
<div class="modal fade" id="modalGenerate" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-custom">
            <div class="modal-header-custom">
                <div class="modal-title-custom">
                    <i class="bi bi-qr-code me-2 text-success"></i>
                    Generate QR Code
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <form action="" method="POST" id="formGenerate">
                <?= csrf_field() ?>
                <div class="modal-body-custom">
                    <p class="modal-desc">
                        Ketik plat nomor kendaraan lalu tekan <strong>Enter</strong> untuk mencari.
                    </p>

                    <!-- Input Plat -->
                    <label class="form-label-custom">Plat Nomor Kendaraan</label>
                    <div style="position:relative;">
                        <input type="text" id="inputPlat" class="form-input plat-input" placeholder="Contoh: B 1234 ABC"
                            autocomplete="off" maxlength="15" style="text-transform:uppercase;">
                        <span id="platSpinner"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#9ca3af;display:none;">
                            <i class="bi bi-arrow-repeat"
                                style="animation:spin .7s linear infinite;display:inline-block;"></i>
                        </span>
                    </div>

                    <!-- Error -->
                    <div id="platError" style="display:none;margin-top:6px;font-size:.8rem;color:#dc2626;">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        <span id="platErrorMsg"></span>
                    </div>

                    <!-- Card Hasil -->
                    <div id="kendaraanCard"
                        style="display:none;margin-top:12px;border:1.5px solid #16a34a; border-radius:10px;background:#f0fff4;padding:12px 14px;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <!-- Foto Personel -->
                            <img id="kcFoto" src="" alt=""
                                style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid #16a34a;flex-shrink:0;">
                            <!-- Info -->
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:.95rem;font-weight:600;color:#111827;" id="kcPlat"></div>
                                <div style="font-size:.78rem;color:#6b7280;margin-top:2px;">
                                    <span id="kcJenis"></span> ·
                                    <span id="kcMerkModel"></span> ·
                                    <span id="kcWarna"></span>
                                </div>
                                <div style="font-size:.75rem;color:#9ca3af;margin-top:2px;">
                                    <i class="bi bi-person me-1"></i>
                                    <span id="kcPangkat"></span> <span id="kcNama"></span>
                                    · <code id="kcNrp"></code>
                                </div>
                            </div>
                            <!-- Tombol Clear -->
                            <button type="button" onclick="clearKendaraan()"
                                style="background:none;border:none;color:#9ca3af;cursor:pointer;font-size:1rem;padding:4px;flex-shrink:0;">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <!-- Warning jika sudah ada QR aktif -->
                        <div id="kcWarningQR"
                            style="display:none;margin-top:8px;padding:6px 10px; background:#fef9c3;border-radius:7px;font-size:.78rem;color:#854d0e;">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Kendaraan ini sudah memiliki QR aktif. Generate baru akan <strong>menonaktifkan QR
                                lama</strong>.
                        </div>
                    </div>

                    <!-- Hidden field uuid -->
                    <input type="hidden" name="kendaraan_uuid" id="kendaraanUuid">
                </div>
                <div class="modal-footer-custom">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-qr-code me-1"></i> Generate QR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Konfirmasi Regenerate -->
<div class="modal fade" id="modalRegenerate" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content modal-custom">
            <div class="modal-body-custom text-center" style="padding:1.75rem 1.25rem;">
                <div class="confirm-icon warning">
                    <i class="bi bi-arrow-clockwise"></i>
                </div>
                <div class="confirm-title">Generate Ulang QR?</div>
                <div class="confirm-desc">
                    QR Code lama untuk kendaraan
                    <strong id="regeneratePlat">-</strong>
                    otomatis <span class="text-warning fw-600">dinonaktifkan</span>
                    dan diganti dengan QR baru.
                </div>
            </div>
            <div class="modal-footer-custom justify-content-center gap-2">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i> Batal
                </button>
                <form id="formRegenerate" method="POST" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-confirm warning">
                        <i class="bi bi-arrow-clockwise me-1"></i> Generate Ulang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Revoke -->
<div class="modal fade" id="modalRevoke" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content modal-custom">
            <div class="modal-body-custom text-center" style="padding:1.75rem 1.25rem;">
                <div class="confirm-icon danger">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="confirm-title">Nonaktifkan QR?</div>
                <div class="confirm-desc">
                    QR Code untuk kendaraan
                    <strong id="revokePlat">-</strong>
                    akan <span class="text-danger fw-600">dinonaktifkan</span>.
                    Kendaraan tidak bisa scan masuk sampai QR baru di-generate.
                </div>
            </div>
            <div class="modal-footer-custom justify-content-center gap-2">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i> Batal
                </button>
                <form id="formRevoke" method="POST" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-confirm danger">
                        <i class="bi bi-x-circle me-1"></i> Nonaktifkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css"> -->
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

.summary-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: .85rem
}

.summary-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    flex-shrink: 0
}

.summary-num {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    line-height: 1
}

.summary-label {
    font-size: .72rem;
    color: #9ca3af;
    margin-top: 2px
}

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

.filter-select {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .38rem .75rem;
    font-size: .82rem;
    color: #374151;
    outline: none;
    cursor: pointer
}

.filter-select:focus {
    border-color: #16a34a
}

.search-wrap {
    position: relative
}

.search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: .85rem
}

.search-input {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .38rem .75rem .38rem 2rem;
    font-size: .82rem;
    width: 210px;
    outline: none
}

.search-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1)
}

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

.avatar-photo {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid #e5e7eb
}

.avatar-initial {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: .78rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center
}

.code-tag {
    background: #f3f4f6;
    color: #374151;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: .75rem
}

.kode-unik-tag {
    background: #ecfdf5;
    color: #065f46;
    padding: 3px 8px;
    border-radius: 5px;
    font-size: .75rem;
    font-family: monospace
}

.badge-jenis {
    font-size: .68rem;
    font-weight: 500;
    padding: 1px 7px;
    border-radius: 99px
}

.badge-jenis.motor {
    background: #ede9fe;
    color: #5b21b6
}

.badge-jenis.mobil {
    background: #dbeafe;
    color: #1e40af
}

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .72rem;
    font-weight: 500;
    padding: 3px 10px;
    border-radius: 99px
}

.badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0
}

.badge-status.aktif {
    background: #dcfce7;
    color: #166534
}

.badge-status.aktif .badge-dot {
    background: #16a34a
}

.badge-status.nonaktif {
    background: #f3f4f6;
    color: #6b7280
}

.badge-status.nonaktif .badge-dot {
    background: #9ca3af
}

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

.btn-action.regenerate {
    background: #f0fdf4;
    color: #166534
}

.btn-action.regenerate:hover {
    background: #dcfce7
}

.btn-action.revoke {
    background: #fef2f2;
    color: #dc2626
}

.btn-action.revoke:hover {
    background: #fee2e2
}

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

/* Modal */
.modal-custom {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, .15)
}

.modal-header-custom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.1rem 1.25rem;
    border-bottom: 1px solid #f3f4f6
}

.modal-title-custom {
    font-size: .9rem;
    font-weight: 600;
    color: #111827
}

.btn-close-custom {
    background: none;
    border: none;
    cursor: pointer;
    color: #9ca3af;
    font-size: 1.1rem;
    padding: 0;
    line-height: 1
}

.btn-close-custom:hover {
    color: #374151
}

.modal-body-custom {
    padding: 1.25rem
}

.modal-desc {
    font-size: .82rem;
    color: #6b7280;
    margin-bottom: 1rem;
    line-height: 1.6
}

.form-label-custom {
    display: block;
    font-size: .8rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px
}

.form-input {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 9px;
    padding: .6rem .85rem;
    font-size: .85rem;
    color: #111827;
    outline: none;
    transition: all .15s
}

.form-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1)
}

.modal-footer-custom {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .75rem;
    padding: 1rem 1.25rem;
    border-top: 1px solid #f3f4f6
}

.btn-cancel {
    font-size: .82rem;
    color: #6b7280;
    text-decoration: none;
    padding: .5rem .9rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #fff;
    cursor: pointer
}

.btn-submit {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: .82rem;
    font-weight: 500;
    padding: .5rem 1.1rem;
    border-radius: 9px;
    border: none;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(22, 101, 52, .25)
}

.btn-submit:hover {
    transform: translateY(-1px)
}

/* ── Confirm Modal ── */
.confirm-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin: 0 auto 1rem;
}

.confirm-icon.warning {
    background: #fef9c3;
    color: #d97706;
    border: 2px solid #fde68a;
}

.confirm-icon.danger {
    background: #fee2e2;
    color: #dc2626;
    border: 2px solid #fecaca;
}

.confirm-title {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: .5rem;
}

.confirm-desc {
    font-size: .82rem;
    color: #6b7280;
    line-height: 1.6;
}

.btn-confirm {
    display: inline-flex;
    align-items: center;
    font-size: .82rem;
    font-weight: 600;
    padding: .5rem 1.1rem;
    border-radius: 9px;
    border: none;
    cursor: pointer;
    transition: all .2s;
}

.btn-confirm.warning {
    background: linear-gradient(135deg, #d97706, #f59e0b);
    color: #fff;
    box-shadow: 0 2px 8px rgba(217, 119, 6, .3);
}

.btn-confirm.warning:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(217, 119, 6, .4);
}

.btn-confirm.danger {
    background: linear-gradient(135deg, #dc2626, #ef4444);
    color: #fff;
    box-shadow: 0 2px 8px rgba(220, 38, 38, .3);
}

.btn-confirm.danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(220, 38, 38, .4);
}

.fw-600 {
    font-weight: 600;
}

/* ── DataTables Override ── */
div.dataTables_wrapper div.dataTables_length select,
div.dataTables_wrapper div.dataTables_filter input {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .35rem .65rem;
    font-size: .82rem;
    outline: none;
}

div.dataTables_wrapper div.dataTables_filter input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1);
}

div.dataTables_wrapper div.dataTables_length label,
div.dataTables_wrapper div.dataTables_filter label,
div.dataTables_wrapper div.dataTables_info {
    font-size: .78rem;
    color: #6b7280;
}

table.dataTable thead th {
    border-bottom: 1px solid #f3f4f6 !important;
}

table.dataTable.no-footer {
    border-bottom: none !important;
}

/* Sembunyikan search bawaan DataTables
       karena kita punya search + filter sendiri di header */
div.dataTables_wrapper div.dataTables_filter {
    display: none;
}

/* ── DataTables Layout ── */
.dt-top-wrap {
    padding: .75rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: 12px;
}

.dt-bottom-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .75rem 1.25rem;
    border-top: 1px solid #f3f4f6;
    flex-wrap: wrap;
    gap: 8px;
}

/* ── DataTables Override ── */
div.dataTables_wrapper div.dataTables_length label,
div.dataTables_wrapper div.dataTables_info {
    font-size: .78rem;
    color: #6b7280;
    margin: 0;
}

div.dataTables_wrapper div.dataTables_length select {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .3rem .6rem;
    font-size: .82rem;
    outline: none;
    margin: 0 4px;
}

div.dataTables_wrapper div.dataTables_filter {
    display: none;
    /* pakai search custom sendiri */
}

div.dataTables_wrapper div.dataTables_paginate .page-item .page-link {
    border: 1px solid #e5e7eb !important;
    border-radius: 7px !important;
    font-size: .78rem !important;
    padding: .28rem .62rem !important;
    min-width: 32px;
    text-align: center;
    line-height: 1.4;
    cursor: pointer;
    color: #374151 !important;
    background: #fff !important;
    transition: all .15s;
}

div.dataTables_wrapper div.dataTables_paginate .page-item .page-link:hover {
    background: #f0fdf4 !important;
    color: #166534 !important;
    border-color: #bbf7d0 !important;
}

div.dataTables_wrapper div.dataTables_paginate .page-item.active .page-link,
div.dataTables_wrapper div.dataTables_paginate .page-item.active .page-link:hover {
    background: linear-gradient(135deg, #166534, #16a34a) !important;
    color: #fff !important;
    border-color: transparent !important;
    box-shadow: 0 2px 6px rgba(22, 101, 52, .3);
}

div.dataTables_wrapper div.dataTables_paginate .page-item.disabled .page-link,
div.dataTables_wrapper div.dataTables_paginate .page-item.disabled .page-link:hover {
    background: #f9fafb !important;
    color: #d1d5db !important;
    border-color: #f3f4f6 !important;
    cursor: default;
    box-shadow: none;
}


table.dataTable thead th {
    border-bottom: 1px solid #f3f4f6 !important;
}

table.dataTable.no-footer {
    border-bottom: none !important;
}
</style>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap5.min.js"></script> -->
<script>
const CARI_KENDARAAN_URL = '<?= base_url('admin/qrcode/cariKendaraan') ?>';

<?php if (!empty($qrList)): ?>
let qrTable;

$(document).ready(function() {

    qrTable = $('#qrTable').DataTable({
        language: {
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ QR Code',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(difilter dari _MAX_ total data)',
            zeroRecords: 'Tidak ada data yang cocok',
            emptyTable: 'Belum ada QR Code',
            paginate: {
                first: '«',
                last: '»',
                next: '›',
                previous: '‹'
            },
        },
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [
            [5, 'desc']
        ], // default sort: generated_at DESC
        responsive: true,
        dom: '<"dt-top-wrap"l>rt<"dt-bottom-wrap"ip>',
        columnDefs: [{
                orderable: false,
                searchable: false,
                targets: [0]
            }, // #
            {
                orderable: false,
                searchable: false,
                targets: [6]
            }, // aksi
        ],

        // ── Penomoran berurutan per halaman ──────────────
        drawCallback: function() {
            const api = this.api();
            const start = api.page.info().start;
            api.column(0, {
                page: 'current'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = `<span style="color:#9ca3af;">${start + i + 1}</span>`;
            });
        },
    });

    // ── Search custom ────────────────────────────────────
    document.getElementById('searchInput').addEventListener('input', function() {
        qrTable.search(this.value).draw();
    });

    // ── Filter Status ────────────────────────────────────
    // Pakai custom search function berdasarkan data-status di <tr>
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        const val = document.getElementById('filterStatus').value;
        if (!val) return true;
        const row = qrTable.row(dataIndex).node();
        return $(row).data('status') === val;
    });

    document.getElementById('filterStatus').addEventListener('change', function() {
        qrTable.draw();
    });

});
<?php endif; ?>

/* ════════════════════════════════════════
   MODAL GENERATE — Cari Kendaraan by Plat
════════════════════════════════════════ */
document.getElementById('modalGenerate').addEventListener('show.bs.modal', function() {
    clearKendaraan();
    document.getElementById('inputPlat').value = '';
    document.getElementById('platError').style.display = 'none';
});

document.getElementById('inputPlat').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        cariKendaraan();
    }
});

document.getElementById('inputPlat').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

async function cariKendaraan() {
    const plat = document.getElementById('inputPlat').value.trim();
    if (!plat) return;

    document.getElementById('platError').style.display = 'none';
    document.getElementById('kendaraanCard').style.display = 'none';
    document.getElementById('kendaraanUuid').value = '';
    document.getElementById('platSpinner').style.display = 'inline';
    document.getElementById('inputPlat').disabled = true;

    try {
        const res = await fetch(`${CARI_KENDARAAN_URL}?plat=${encodeURIComponent(plat)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();

        if (data.found) {
            tampilkanKendaraan(data.kendaraan, data.hasQR);
        } else {
            document.getElementById('platErrorMsg').textContent = data.message;
            document.getElementById('platError').style.display = 'block';
        }
    } catch {
        document.getElementById('platErrorMsg').textContent = 'Terjadi kesalahan, coba lagi.';
        document.getElementById('platError').style.display = 'block';
    } finally {
        document.getElementById('platSpinner').style.display = 'none';
        document.getElementById('inputPlat').disabled = false;
    }
}

function tampilkanKendaraan(k, hasQR) {
    document.getElementById('kcFoto').src = k.foto_personel;
    document.getElementById('kcPlat').textContent = k.plat_nomor;
    document.getElementById('kcJenis').textContent = k.jenis.charAt(0).toUpperCase() + k.jenis.slice(1);
    document.getElementById('kcMerkModel').textContent = (k.merk + ' ' + k.model).trim() || '-';
    document.getElementById('kcWarna').textContent = k.warna || '-';
    document.getElementById('kcPangkat').textContent = k.pangkat;
    document.getElementById('kcNama').textContent = k.personel_nama;
    document.getElementById('kcNrp').textContent = k.nrp;
    document.getElementById('kendaraanUuid').value = k.uuid;
    document.getElementById('kcWarningQR').style.display = hasQR ? 'block' : 'none';
    document.getElementById('kendaraanCard').style.display = 'block';
    document.getElementById('inputPlat').classList.add('d-none');
}

function clearKendaraan() {
    document.getElementById('kendaraanUuid').value = '';
    document.getElementById('kendaraanCard').style.display = 'none';
    document.getElementById('inputPlat').classList.remove('d-none');
    document.getElementById('inputPlat').value = '';
    document.getElementById('platError').style.display = 'none';
    document.getElementById('inputPlat').focus();
}

// Submit form generate — hanya 1 listener
document.getElementById('formGenerate').addEventListener('submit', function(e) {
    e.preventDefault();
    const uuid = document.getElementById('kendaraanUuid').value;
    if (!uuid) {
        document.getElementById('platErrorMsg').textContent = 'Cari kendaraan terlebih dahulu.';
        document.getElementById('platError').style.display = 'block';
        return;
    }
    this.action = `<?= base_url('admin/qrcode/generate/') ?>${uuid}`;
    this.submit();
});

/* ════════════════════════════════════════
   MODAL KONFIRMASI REGENERATE & REVOKE
════════════════════════════════════════ */
function konfirmasiRegenerate(actionUrl, platNomor) {
    document.getElementById('regeneratePlat').textContent = platNomor;
    document.getElementById('formRegenerate').action = actionUrl;
    new bootstrap.Modal(document.getElementById('modalRegenerate')).show();
}

function konfirmasiRevoke(actionUrl, platNomor) {
    document.getElementById('revokePlat').textContent = platNomor;
    document.getElementById('formRevoke').action = actionUrl;
    new bootstrap.Modal(document.getElementById('modalRevoke')).show();
}
</script>
<?= $this->endSection() ?>