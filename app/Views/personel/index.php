<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashdata('import_result')): ?>
<?php $res = session()->getFlashdata('import_result'); ?>
<div class="alert-success mb-3">
    <i class="bi bi-check-circle me-2"></i>
    Import selesai — <strong><?= $res['berhasil'] ?></strong> berhasil ditambahkan,
    <strong><?= $res['dilewati'] ?></strong> dilewati (NRP duplikat).
</div>
<?php if (!empty($res['gagal'])): ?>
<div class="alert-error mb-3">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong><?= count($res['gagal']) ?> baris gagal diproses:</strong>
    <ul class="mb-0 mt-1 ps-3">
        <?php foreach ($res['gagal'] as $err): ?>
        <li><?= esc($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
<?php endif; ?>

<div class="page-header mb-4">

    <div>
        <h5 class="page-header-title">Management Personel</h5>
        <p class="page-header-sub">
            Kelola data personel dan kendaraan yang terdaftar
        </p>
    </div>

    <div class="header-actions">

        <a href="<?= base_url('admin/personel/importExcel') ?>" class="btn-secondary-custom">
            <i class="bi bi-file-earmark-excel me-1"></i>
            Import Excel
        </a>

        <a href="<?= base_url('admin/personel/create') ?>" class="btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Personel
        </a>

    </div>

</div>

<!-- Summary -->
<div class="row g-3 mb-4">
    <?php
        $totalAktif     = count(array_filter($personel, fn($p) => $p['status'] === 'aktif'));
        $totalNonaktif  = count($personel) - $totalAktif;
        $totalKendaraan = array_sum(array_column($personel, 'total_kendaraan'));
    ?>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-icon" style="background:linear-gradient(135deg,#166534,#16a34a);">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div>
                <div class="summary-num"><?= count($personel) ?></div>
                <div class="summary-label">Total Personel</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-icon" style="background:linear-gradient(135deg,#065f46,#10b981);">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div>
                <div class="summary-num"><?= $totalAktif ?></div>
                <div class="summary-label">Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-icon" style="background:linear-gradient(135deg,#1d4ed8,#3b82f6);">
                <i class="bi bi-car-front-fill"></i>
            </div>
            <div>
                <div class="summary-num"><?= $totalKendaraan ?></div>
                <div class="summary-label">Total Kendaraan</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel -->
<div class="card-custom">
    <div class="card-custom-header">
        <div>
            <div class="card-custom-title">
                <i class="bi bi-person-badge me-2 text-success"></i>Daftar Personel
            </div>
            <div class="card-custom-sub"><?= count($personel) ?> personel terdaftar</div>
        </div>
    </div>

    <?php if (empty($personel)): ?>
    <div class="empty-state">
        <i class="bi bi-person-badge"></i>
        <div>Belum ada personel terdaftar</div>
        <a href="<?= base_url('admin/personel/create') ?>" class="btn-primary-custom mt-3 d-inline-flex">
            Tambah Personel Pertama
        </a>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table id="personelTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Personel</th>
                    <th>NRP</th>
                    <th>Pangkat / Jabatan</th>
                    <th>Satuan</th>
                    <th>Kendaraan</th>
                    <th>Status</th>
                    <th width="130">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($personel as $i => $p): ?>
                <tr>
                    <td class="text-muted"><?= $i + 1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if ($p['foto']): ?>
                            <img src="<?= base_url('uploads/personel/' . $p['foto']) ?>" class="avatar-photo" alt="">
                            <?php else: ?>
                            <div class="avatar-initial">
                                <?= strtoupper(substr($p['nama'], 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                            <span class="fw-500"><?= esc($p['nama']) ?></span>
                        </div>
                    </td>
                    <td><code class="code-tag"><?= esc($p['nrp']) ?></code></td>
                    <td>
                        <div class="fw-500" style="font-size:.83rem;"><?= esc($p['pangkat_singkat']) ?></div>
                        <?php if ($p['jabatan']): ?>
                        <div style="font-size:.75rem;color:#9ca3af;"><?= esc($p['jabatan']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:.82rem;color:#6b7280;">
                        <?= esc($p['satker_singkat']) ?: '<span class="text-muted">-</span>' ?>
                    </td>
                    <td>
                        <span class="badge-kendaraan">
                            <i class="bi bi-car-front me-1"></i>
                            <?= $p['total_kendaraan'] ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($p['status'] === 'aktif'): ?>
                        <span class="badge-status aktif"><span class="badge-dot"></span> Aktif</span>
                        <?php else: ?>
                        <span class="badge-status nonaktif"><span class="badge-dot"></span> Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-wrap">
                            <a href="<?= base_url('admin/personel/detail/' . $p['uuid']) ?>" class="btn-action view"
                                title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= base_url('admin/personel/edit/' . $p['uuid']) ?>" class="btn-action edit"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?= base_url('admin/personel/toggle/' . $p['uuid']) ?>" method="POST"
                                class="d-inline" onsubmit="return confirm('Yakin ubah status personel ini?')">
                                <?= csrf_field() ?>
                                <button type="button"
                                    class="btn-action <?= $p['status'] === 'aktif' ? 'deactivate' : 'activate' ?>"
                                    title="<?= $p['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>"
                                    onclick="konfirmasiToggle('<?= base_url('admin/personel/toggle/' . $p['uuid']) ?>','<?= esc($p['nama']) ?>','<?= $p['status'] ?>')">
                                    <i class="bi <?= $p['status'] === 'aktif' ? 'bi-toggle-on' : 'bi-toggle-off' ?>">
                                    </i>
                                </button>
                            </form>
                            <?php if ($p['total_kendaraan'] == 0): ?>
                            <form action="<?= base_url('admin/personel/delete/' . $p['uuid']) ?>" method="POST"
                                class="d-inline delete-form" data-nama="<?= esc($p['nama']) ?>"
                                data-nrp="<?= esc($p['nrp']) ?>">
                                <?= csrf_field() ?>
                                <button type="button" class="btn-action delete btn-delete" title="Hapus">
                                    <i class="bi bi-trash"></i>
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

<!-- Modal Konfirmasi Toggle Status Personel -->
<div class="modal fade" id="modalToggle" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content modal-custom">
            <div class="modal-body-custom text-center" style="padding:1.75rem 1.25rem;">

                <!-- Icon — berubah sesuai status -->
                <div class="confirm-icon" id="toggleIcon">
                    <i class="bi" id="toggleIconBi"></i>
                </div>

                <div class="confirm-title" id="toggleTitle">-</div>
                <div class="confirm-desc">
                    Personel <strong id="toggleNama">-</strong>
                    akan <span id="toggleActionText">-</span>.
                    <div id="toggleWarning" style="display:none;
                        margin-top:10px;padding:8px 10px;
                        background:#fef9c3;border-radius:8px;
                        font-size:.78rem;color:#854d0e;">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Personel yang dinonaktifkan tidak dapat melakukan scan kendaraan.
                    </div>
                </div>
            </div>
            <div class="modal-footer-custom"
                style="display:flex; align-items:center; justify-content:center; gap:8px; padding:1rem 1.25rem; border-top:1px solid #f3f4f6;">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i> Batal
                </button>
                <form id="formToggle" method="POST" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-confirm" id="toggleBtn">
                        <i class="bi me-1" id="toggleBtnIcon"></i>
                        <span id="toggleBtnText">-</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="modal-icon">
            <i class="bi bi-trash"></i>
        </div>
        <h5>Hapus Personel</h5>
        <p id="deleteMessage">Apakah Anda yakin ingin menghapus personel ini?</p>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" id="cancelDelete">Batal</button>
            <button type="button" class="btn-confirm-delete" id="confirmDelete">Ya, Hapus</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<!-- <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css"> -->
<style>
/* =============================================
   BASE PAGE
============================================= */
.page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-header-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.page-header-sub {
    font-size: .78rem;
    color: #9ca3af;
    margin: 2px 0 0;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
}

.btn-secondary-custom {
    display: inline-flex;
    align-items: center;

    padding: .5rem 1rem;

    border-radius: 9px;

    font-size: .82rem;
    font-weight: 500;

    text-decoration: none;

    background: #fff;

    border: 1px solid #d1d5db;

    color: #374151;

    transition: all .2s ease;
}

.btn-secondary-custom:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #111827;
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
    box-shadow: 0 2px 8px rgba(22, 101, 52, .25);
}

.btn-primary-custom:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(22, 101, 52, .35);
    color: #fff;
}

/* =============================================
   SUMMARY CARDS
============================================= */
.summary-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: .85rem;
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
    flex-shrink: 0;
}

.summary-num {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

.summary-label {
    font-size: .72rem;
    color: #9ca3af;
    margin-top: 2px;
}

/* =============================================
   CARD
============================================= */
.card-custom {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.card-custom-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
    flex-wrap: wrap;
    gap: .75rem;
}

.card-custom-title {
    font-size: .9rem;
    font-weight: 600;
    color: #111827;
}

.card-custom-sub {
    font-size: .75rem;
    color: #9ca3af;
}

/* =============================================
   TABLE
============================================= */
.table-wrap {
    padding: 0 1.25rem 1.25rem;
}

.table-custom {
    width: 100%;
    border-collapse: collapse;
    font-size: .83rem;
}

.table-custom thead tr {
    background: #f9fafb;
}

.table-custom th {
    padding: .65rem 1rem;
    font-size: .7rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
    border-bottom: 1px solid #f3f4f6;
}

.table-custom td {
    padding: .75rem 1rem;
    color: #374151;
    border-bottom: 1px solid #f9fafb;
    vertical-align: middle;
}

.table-custom tbody tr:last-child td {
    border-bottom: none;
}

.table-custom tbody tr:hover {
    background: #fafafa;
}

/* =============================================
   TABLE CELL COMPONENTS
============================================= */
.fw-500 {
    font-weight: 500;
}

.avatar-photo {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid #e5e7eb;
}

.avatar-initial {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: .82rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.code-tag {
    background: #f3f4f6;
    color: #374151;
    padding: 2px 8px;
    border-radius: 5px;
    font-size: .78rem;
}

.badge-kendaraan {
    display: inline-flex;
    align-items: center;
    background: #eff6ff;
    color: #1d4ed8;
    font-size: .72rem;
    font-weight: 500;
    padding: 2px 9px;
    border-radius: 99px;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .72rem;
    font-weight: 500;
    padding: 3px 10px;
    border-radius: 99px;
}

.badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}

.badge-status.aktif {
    background: #dcfce7;
    color: #166534;
}

.badge-status.aktif .badge-dot {
    background: #16a34a;
}

.badge-status.nonaktif {
    background: #fee2e2;
    color: #991b1b;
}

.badge-status.nonaktif .badge-dot {
    background: #ef4444;
}

.action-wrap {
    display: flex;
    align-items: center;
    gap: 5px;
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
    transition: all .15s;
}

.btn-action.view {
    background: #f0fdf4;
    color: #166534;
}

.btn-action.view:hover {
    background: #dcfce7;
}

.btn-action.edit {
    background: #eff6ff;
    color: #1d4ed8;
}

.btn-action.edit:hover {
    background: #dbeafe;
}

.btn-action.deactivate {
    background: #fff7ed;
    color: #c2410c;
}

.btn-action.deactivate:hover {
    background: #ffedd5;
}

.btn-action.activate {
    background: #f0fdf4;
    color: #166534;
}

.btn-action.activate:hover {
    background: #dcfce7;
}

.btn-action.delete {
    background: #fef2f2;
    color: #dc2626;
}

.btn-action.delete:hover {
    background: #fee2e2;
}

/* =============================================
   EMPTY STATE
============================================= */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #9ca3af;
}

.empty-state i {
    font-size: 2.5rem;
    display: block;
    margin-bottom: .75rem;
    opacity: .4;
}

/* =============================================
   DELETE MODAL
============================================= */
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
    border-radius: 16px;
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

.modal-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    margin: 0 auto 1rem;
    background: #fef2f2;
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
}

.custom-modal h5 {
    margin-bottom: .75rem;
    color: #111827;
    font-weight: 600;
}

.custom-modal p {
    color: #6b7280;
    font-size: .9rem;
    margin-bottom: 1.5rem;
}

.modal-actions {
    display: flex;
    gap: .75rem;
}

.btn-cancel,
.btn-confirm-delete {
    flex: 1;
    border: none;
    border-radius: 10px;
    padding: .7rem;
    font-size: .85rem;
    font-weight: 500;
    cursor: pointer;
}

.btn-cancel {
    background: #f3f4f6;
    color: #374151;
}

.btn-confirm-delete {
    background: #dc2626;
    color: #fff;
}

.btn-confirm-delete:hover {
    background: #b91c1c;
}

/* ── Toggle Button ── */
.btn-action.deactivate {
    background: #fef2f2;
    color: #dc2626;
}

.btn-action.deactivate:hover {
    background: #fee2e2;
}

.btn-action.activate {
    background: #f0fdf4;
    color: #16a34a;
}

.btn-action.activate:hover {
    background: #dcfce7;
}

/* ── Confirm Modal (jika belum ada) ── */
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

.confirm-icon.danger {
    background: #fee2e2;
    color: #dc2626;
    border: 2px solid #fecaca;
}

.confirm-icon.success {
    background: #dcfce7;
    color: #16a34a;
    border: 2px solid #bbf7d0;
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

.btn-confirm.danger {
    background: linear-gradient(135deg, #dc2626, #ef4444);
    color: #fff;
    box-shadow: 0 2px 8px rgba(220, 38, 38, .3);
}

.btn-confirm.danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(220, 38, 38, .4);
}

.btn-confirm.success {
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    box-shadow: 0 2px 8px rgba(22, 101, 52, .3);
}

.btn-confirm.success:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(22, 101, 52, .4);
}

/* =============================================
   DATATABLES — TOP BAR (search kiri, length kanan)
============================================= */
div.dataTables_wrapper div.dt-top-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .75rem;
    padding: 1rem 0 .75rem;
    border-bottom: 1px solid #f3f4f6;
    margin-bottom: .25rem;
}

/* =============================================
   DATATABLES — BOTTOM BAR (info kiri, pagination kanan)
============================================= */
div.dataTables_wrapper div.dt-bottom-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .75rem;
    padding: .85rem 0 .25rem;
    border-top: 1px solid #f3f4f6;
    margin-top: .25rem;
}

/* =============================================
   SEARCH INPUT
============================================= */
div.dataTables_wrapper div.dataTables_filter {
    order: 1;
}

div.dataTables_wrapper div.dataTables_filter label {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .8rem;
    color: #6b7280;
    margin: 0;
    font-weight: 500;
}

div.dataTables_wrapper div.dataTables_filter label::before {
    content: '\1F50D';
    font-size: .85rem;
    line-height: 1;
}

div.dataTables_wrapper div.dataTables_filter input {
    width: 220px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .4rem .75rem;
    font-size: .82rem;
    color: #374151;
    background: #fff;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    margin-left: 0 !important;
}

div.dataTables_wrapper div.dataTables_filter input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1);
}

div.dataTables_wrapper div.dataTables_filter input::placeholder {
    color: #9ca3af;
}

/* =============================================
   LENGTH SELECT
============================================= */
div.dataTables_wrapper div.dataTables_length {
    order: 2;
}

div.dataTables_wrapper div.dataTables_length label {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .8rem;
    color: #6b7280;
    margin: 0;
    font-weight: 500;
    white-space: nowrap;
}

div.dataTables_wrapper div.dataTables_length select {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .38rem 2rem .38rem .65rem;
    font-size: .82rem;
    color: #374151;
    background-color: #fff;
    outline: none;
    cursor: pointer;
    transition: border-color .2s;

    /* Tambahin ini — custom arrow biar konsisten semua browser */
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .6rem center;
}

div.dataTables_wrapper div.dataTables_length select:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1);
}

/* =============================================
   INFO TEXT
============================================= */
div.dataTables_wrapper div.dataTables_info {
    font-size: .78rem;
    color: #9ca3af;
    padding: 0;
    line-height: 1.4;
}

/* =============================================
   PAGINATION
============================================= */
div.dataTables_wrapper div.dataTables_paginate {
    margin: 0;
    line-height: 1;
}

div.dataTables_wrapper div.dataTables_paginate .pagination {
    margin: 0;
    gap: 3px;
    display: flex;
    flex-wrap: wrap;
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

/* =============================================
   DATATABLES TABLE OVERRIDES
============================================= */
table.dataTable thead th,
table.dataTable thead td {
    border-bottom: 1px solid #f3f4f6 !important;
}

table.dataTable.no-footer {
    border-bottom: none !important;
}

table.dataTable thead .sorting,
table.dataTable thead .sorting_asc,
table.dataTable thead .sorting_desc {
    background-image: none !important;
    cursor: pointer;
    padding-right: 1.5rem !important;
    position: relative;
}

table.dataTable thead .sorting::after {
    content: '⇅';
    position: absolute;
    right: .5rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: .65rem;
    opacity: .3;
}

table.dataTable thead .sorting_asc::after {
    content: '↑';
    position: absolute;
    right: .5rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: .65rem;
    opacity: .7;
    color: #16a34a;
}

table.dataTable thead .sorting_desc::after {
    content: '↓';
    position: absolute;
    right: .5rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: .65rem;
    opacity: .7;
    color: #16a34a;
}

/* =============================================
   RESPONSIVE
============================================= */
@media (max-width: 576px) {
    div.dataTables_wrapper div.dt-top-wrap {
        flex-direction: column;
        align-items: stretch;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        width: 100%;
    }

    div.dataTables_wrapper div.dataTables_length label {
        justify-content: space-between;
    }

    div.dataTables_wrapper div.dt-bottom-wrap {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap5.min.js"></script> -->
<script>
$(document).ready(function() {

    $('#personelTable').DataTable({
        language: {
            search: "",
            searchPlaceholder: "Cari nama / NRP / satuan...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_–_END_ dari _TOTAL_ personel",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ data)",
            zeroRecords: "Tidak ada personel yang cocok",
            emptyTable: "Belum ada data personel",
            paginate: {
                first: "«",
                last: "»",
                next: "›",
                previous: "‹"
            }
        },

        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],

        columnDefs: [
            // Kolom # dan Aksi — tidak sortable & tidak ikut search
            {
                orderable: false,
                searchable: false,
                targets: [0, 7]
            },
            // Kolom foto/avatar — tidak sortable
            {
                orderable: false,
                targets: [1]
            },
        ],

        // Default sort: nama (kolom 1) ascending
        order: [],

        responsive: true,

        // DOM: search kiri | length kanan — tabel — info kiri | pagination kanan
        dom: '<"dt-top-wrap"fl>rt<"dt-bottom-wrap"ip>',
    });

    /* ── Delete Modal ── */
    let selectedForm = null;
    const modal = document.getElementById('deleteModal');
    const deleteMessage = document.getElementById('deleteMessage');

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            selectedForm = this.closest('.delete-form');
            const nama = selectedForm.dataset.nama;
            const nrp = selectedForm.dataset.nrp;

            deleteMessage.innerHTML = `
                Apakah Anda yakin ingin menghapus personel berikut?
                <div style="margin-top:12px;padding:12px;border-radius:10px;background:#f9fafb;border:1px solid #e5e7eb;">
                    <div style="font-weight:600;color:#111827;">${nama}</div>
                    <div style="font-size:.85rem;color:#6b7280;margin-top:4px;">NRP: ${nrp}</div>
                </div>
                <div style="margin-top:12px;color:#dc2626;font-size:.82rem;">
                    Tindakan ini tidak dapat dibatalkan.
                </div>
            `;
            modal.classList.add('show');
        });
    });

    document.getElementById('cancelDelete').addEventListener('click', () => {
        modal.classList.remove('show');
        selectedForm = null;
    });

    document.getElementById('confirmDelete').addEventListener('click', () => {
        if (selectedForm) selectedForm.submit();
    });

    // Tutup modal kalau klik backdrop
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('show');
            selectedForm = null;
        }
    });
});

function konfirmasiToggle(actionUrl, nama, statusSaat) {
    const isAktif = statusSaat === 'aktif';

    // Set form action
    document.getElementById('formToggle').action = actionUrl;

    // Set nama
    document.getElementById('toggleNama').textContent = nama;

    // Set icon modal
    const icon = document.getElementById('toggleIcon');
    const iconBi = document.getElementById('toggleIconBi');
    if (isAktif) {
        icon.className = 'confirm-icon danger';
        iconBi.className = 'bi bi-toggle-off';
    } else {
        icon.className = 'confirm-icon success';
        iconBi.className = 'bi bi-toggle-on';
    }

    // Set title
    document.getElementById('toggleTitle').textContent =
        isAktif ? 'Nonaktifkan Personel?' : 'Aktifkan Personel?';

    // Set action text
    document.getElementById('toggleActionText').textContent =
        isAktif ? 'dinonaktifkan dari sistem' : 'diaktifkan kembali';

    // Tampilkan warning hanya saat nonaktifkan
    document.getElementById('toggleWarning').style.display =
        isAktif ? 'block' : 'none';

    // Set tombol konfirmasi
    const btn = document.getElementById('toggleBtn');
    const btnIcon = document.getElementById('toggleBtnIcon');
    const btnText = document.getElementById('toggleBtnText');

    if (isAktif) {
        btn.className = 'btn-confirm danger';
        btnIcon.className = 'bi bi-toggle-off me-1';
        btnText.textContent = 'Ya, Nonaktifkan';
    } else {
        btn.className = 'btn-confirm success';
        btnIcon.className = 'bi bi-toggle-on me-1';
        btnText.textContent = 'Ya, Aktifkan';
    }

    new bootstrap.Modal(document.getElementById('modalToggle')).show();
}
</script>
<?= $this->endSection() ?>