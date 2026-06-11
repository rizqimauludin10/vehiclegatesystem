<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Management QR Code</h5>
        <p class="page-header-sub">Generate & kelola kartu QR Code personel</p>
    </div>
    <!-- Tombol Generate QR untuk Personel Baru -->
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
                <i class="bi bi-person-badge"></i>
            </div>
            <div>
                <div class="summary-num"><?= $totalPersonel ?></div>
                <div class="summary-label">Total Personel Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-icon" style="background:linear-gradient(135deg,#92400e,#d97706);">
                <i class="bi bi-exclamation-circle"></i>
            </div>
            <div>
                <div class="summary-num <?= $personelTanpaQR > 0 ? 'text-warning' : '' ?>">
                    <?= $personelTanpaQR ?>
                </div>
                <div class="summary-label">Personel Belum Punya QR</div>
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
            <select id="filterStatus" class="filter-select">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
            <div class="search-wrap">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Cari nama / NRP...">
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
        <table class="table-custom" id="qrTable">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Personel</th>
                    <th>NRP / Pangkat</th>
                    <th>Kode QR</th>
                    <th>Status</th>
                    <th>Di-generate</th>
                    <th width="160">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($qrList as $i => $qr): ?>
                <tr data-status="<?= $qr['status'] ?>">
                    <td class="text-muted"><?= $i + 1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if ($qr['personel_foto']): ?>
                            <img src="<?= base_url('uploads/personel/' . $qr['personel_foto']) ?>" class="avatar-photo"
                                alt="">
                            <?php else: ?>
                            <div class="avatar-initial">
                                <?= strtoupper(substr($qr['personel_nama'], 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                            <span class="fw-500"><?= esc($qr['personel_nama']) ?></span>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:.8rem;">
                            <code class="code-tag"><?= esc($qr['nrp']) ?></code>
                        </div>
                        <div style="font-size:.73rem; color:#9ca3af; margin-top:2px;">
                            <?= esc($qr['pangkat_singkat']) ?>
                        </div>
                    </td>
                    <td>
                        <code class="kode-unik-tag">
                                <?= substr($qr['kode_unik'], 0, 12) ?>...
                            </code>
                    </td>
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
                    <td style="font-size:.78rem; color:#6b7280;">
                        <?= date('d M Y, H:i', strtotime($qr['generated_at'])) ?>
                    </td>
                    <td>
                        <div class="action-wrap">
                            <?php if ($qr['status'] === 'aktif'): ?>
                            <!-- Print Card -->
                            <a href="<?= base_url('admin/qrcode/print/' . $qr['uuid']) ?>" target="_blank"
                                class="btn-action print" title="Cetak Kartu">
                                <i class="bi bi-printer"></i>
                            </a>

                            <!-- Download QR PNG ← BARU -->
                            <a href="<?= base_url('admin/qrcode/download/' . $qr['uuid']) ?>"
                                class="btn-action download" title="Download QR Code PNG">
                                <i class="bi bi-download"></i>
                            </a>
                            <!-- Regenerate -->
                            <form action="<?= base_url('admin/qrcode/generate/' . $qr['personel_uuid']) ?>"
                                method="POST" class="d-inline regenerate-form"
                                data-nama="<?= esc($qr['personel_nama']) ?>" data-nrp="<?= esc($qr['nrp']) ?>">
                                <?= csrf_field() ?>

                                <button type="button" class="btn-action regenerate btn-regenerate"
                                    title="Generate Ulang QR">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </form>
                            <!-- Revoke -->
                            <form action="<?= base_url('admin/qrcode/revoke/' . $qr['uuid']) ?>" method="POST"
                                class="d-inline revoke-form" data-nama="<?= esc($qr['personel_nama']) ?>"
                                data-nrp="<?= esc($qr['nrp']) ?>">
                                <?= csrf_field() ?>

                                <button type="button" class="btn-action revoke btn-revoke" title="Nonaktifkan QR">
                                    <i class="bi bi-shield-lock"></i>
                                </button>
                            </form>
                            <?php else: ?>
                            <!-- Regenerate untuk QR nonaktif -->
                            <form action="<?= base_url('admin/qrcode/generate/' . $qr['personel_uuid']) ?>"
                                method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-action regenerate" title="Generate QR Baru">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </form>
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
                    Generate QR Code Baru
                </div>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <form action="" method="POST" id="formGenerate">
                <?= csrf_field() ?>
                <div class="modal-body-custom">
                    <p class="modal-desc">
                        Pilih personel untuk di-generate QR Code-nya.
                        Jika personel sudah memiliki QR aktif, QR lama akan otomatis dinonaktifkan.
                    </p>
                    <label class="form-label-custom">Pilih Personel</label>
                    <select name="personel_uuid" id="selectPersonel" class="form-input" required>
                        <option value="">-- Pilih Personel --</option>
                        <?php foreach ($personelList as $p): ?>
                        <option value="<?= $p['uuid'] ?>">
                            <?= esc($p['pangkat_singkat']) ?> <?= esc($p['nama']) ?> — <?= esc($p['nrp']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
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

<div id="revokeModal" class="custom-modal">
    <div class="custom-modal-content">

        <div class="modal-icon revoke">
            <i class="bi bi-shield-lock"></i>
        </div>

        <h5>Nonaktifkan QR Code</h5>

        <p id="revokeMessage"></p>

        <div class="modal-actions">
            <button type="button" class="btn-cancel" id="cancelRevoke">
                Batal
            </button>

            <button type="button" class="btn-confirm-revoke" id="confirmRevoke">
                Nonaktifkan
            </button>
        </div>

    </div>
</div>

<div id="regenerateModal" class="custom-modal">
    <div class="custom-modal-content">

        <div class="modal-icon regenerate">
            <i class="bi bi-arrow-repeat"></i>
        </div>

        <h5>Generate Ulang QR Code</h5>

        <p id="regenerateMessage"></p>

        <div class="modal-actions">
            <button type="button" class="btn-cancel" id="cancelRegenerate">
                Batal
            </button>

            <button type="button" class="btn-confirm-regenerate" id="confirmRegenerate">
                Generate Ulang
            </button>
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
    letter-spacing: .05em
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

.fw-500 {
    font-weight: 500
}

.code-tag {
    background: #f3f4f6;
    color: #374151;
    padding: 2px 8px;
    border-radius: 5px;
    font-size: .78rem
}

.kode-unik-tag {
    background: #ecfdf5;
    color: #065f46;
    padding: 3px 8px;
    border-radius: 5px;
    font-size: .75rem;
    font-family: monospace
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

.btn-action.print {
    background: #f0fdf4;
    color: #166534
}

.btn-action.print:hover {
    background: #dcfce7
}

.btn-action.regenerate {
    background: #eff6ff;
    color: #1d4ed8
}

.btn-action.regenerate:hover {
    background: #dbeafe
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

.custom-modal {
    position: fixed;
    inset: 0;
    background: rgba(17, 24, 39, .55);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(4px);
}

.custom-modal.show {
    display: flex;
}

.custom-modal-content {
    background: #fff;
    width: 100%;
    max-width: 430px;
    border-radius: 18px;
    padding: 1.5rem;
    text-align: center;
    animation: modalPop .2s ease;
}

@keyframes modalPop {
    from {
        opacity: 0;
        transform: scale(.95);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

.modal-icon.revoke {
    width: 72px;
    height: 72px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    background: #fff7ed;
    color: #c2410c;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
}

.modal-actions {
    display: flex;
    gap: .75rem;
}

.btn-confirm-revoke {
    flex: 1;
    border: none;
    border-radius: 10px;
    background: #ea580c;
    color: white;
    font-size: .85rem;
    font-weight: 500;
    cursor: pointer;
    padding: .7rem;
}

.btn-confirm-revoke:hover {
    background: #c2410c;
}

.modal-icon.regenerate {
    width: 72px;
    height: 72px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    background: #eff6ff;
    color: #1d4ed8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
}

.btn-confirm-regenerate {
    flex: 1;
    border: none;
    border-radius: 10px;
    background: #2563eb;
    color: white;
    font-size: .85rem;
    font-weight: 500;
    cursor: pointer;
    padding: .7rem;
}

.btn-confirm-regenerate:hover {
    background: #1d4ed8;
}

.btn-action.download {
    background: #eff6ff;
    color: #1d4ed8;
}

.btn-action.download:hover {
    background: #dbeafe;
}
</style>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
// Live Search + Filter Status
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('filterStatus').addEventListener('change', filterTable);

function filterTable() {
    const keyword = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('filterStatus').value.toLowerCase();
    document.querySelectorAll('#qrTable tbody tr').forEach(row => {
        const matchText = row.textContent.toLowerCase().includes(keyword);
        const matchStatus = status === '' || row.dataset.status === status;
        row.style.display = (matchText && matchStatus) ? '' : 'none';
    });
}

// Update action form modal generate
document.getElementById('formGenerate').addEventListener('submit', function(e) {
    e.preventDefault();
    const uuid = document.getElementById('selectPersonel').value;
    if (!uuid) {
        alert('Pilih personel terlebih dahulu.');
        return;
    }
    this.action = `<?= base_url('admin/qrcode/generate/') ?>${uuid}`;
    this.submit();
});

let selectedRevokeForm = null;

const revokeModal = document.getElementById('revokeModal');
const revokeMessage = document.getElementById('revokeMessage');

document.querySelectorAll('.btn-revoke').forEach(button => {

    button.addEventListener('click', function() {

        selectedRevokeForm = this.closest('.revoke-form');

        const nama = selectedRevokeForm.dataset.nama;
        const nrp = selectedRevokeForm.dataset.nrp;

        revokeMessage.innerHTML = `
            QR Code milik personel berikut akan dinonaktifkan:

            <div style="
                margin-top:12px;
                padding:12px;
                border-radius:10px;
                background:#f9fafb;
                border:1px solid #e5e7eb;
            ">
                <div style="font-weight:600;color:#111827;">
                    ${nama}
                </div>

                <div style="
                    margin-top:4px;
                    color:#6b7280;
                    font-size:.82rem;
                ">
                    NRP: ${nrp}
                </div>
            </div>

            <div style="
                margin-top:12px;
                color:#c2410c;
                font-size:.82rem;
            ">
                Personel tidak dapat melakukan scan gate sampai QR baru dibuat.
            </div>
        `;

        revokeModal.classList.add('show');
    });

});

document.getElementById('cancelRevoke').addEventListener('click', () => {
    revokeModal.classList.remove('show');
    selectedRevokeForm = null;
});

document.getElementById('confirmRevoke').addEventListener('click', () => {

    if (selectedRevokeForm) {
        selectedRevokeForm.submit();
    }

});

window.addEventListener('click', function(e) {

    if (e.target === revokeModal) {
        revokeModal.classList.remove('show');
        selectedRevokeForm = null;
    }

});

let selectedRegenerateForm = null;

const regenerateModal = document.getElementById('regenerateModal');
const regenerateMessage = document.getElementById('regenerateMessage');

document.querySelectorAll('.btn-regenerate').forEach(button => {

    button.addEventListener('click', function() {

        selectedRegenerateForm = this.closest('.regenerate-form');

        const nama = selectedRegenerateForm.dataset.nama;
        const nrp = selectedRegenerateForm.dataset.nrp;

        regenerateMessage.innerHTML = `
            QR Code baru akan dibuat untuk personel berikut:

            <div style="
                margin-top:12px;
                padding:12px;
                border-radius:10px;
                background:#f9fafb;
                border:1px solid #e5e7eb;
            ">
                <div style="font-weight:600;color:#111827;">
                    ${nama}
                </div>

                <div style="
                    margin-top:4px;
                    color:#6b7280;
                    font-size:.82rem;
                ">
                    NRP: ${nrp}
                </div>
            </div>

            <div style="
                margin-top:12px;
                color:#1d4ed8;
                font-size:.82rem;
            ">
                QR lama akan otomatis dinonaktifkan dan digantikan QR baru.
            </div>
        `;

        regenerateModal.classList.add('show');
    });

});

document.getElementById('cancelRegenerate').addEventListener('click', () => {
    regenerateModal.classList.remove('show');
});

document.getElementById('confirmRegenerate').addEventListener('click', () => {

    if (selectedRegenerateForm) {
        selectedRegenerateForm.submit();
    }

});

window.addEventListener('click', function(e) {

    if (e.target === regenerateModal) {
        regenerateModal.classList.remove('show');
    }

});
</script>
<?= $this->endSection() ?>