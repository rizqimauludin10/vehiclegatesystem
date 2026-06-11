<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Management Kendaraan</h5>
        <p class="page-header-sub">Kelola semua kendaraan personel yang terdaftar</p>
    </div>
    <a href="<?= base_url('admin/kendaraan/create') ?>" class="btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kendaraan
    </a>
</div>

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-icon" style="background:linear-gradient(135deg,#166534,#16a34a);">
                <i class="bi bi-car-front-fill"></i>
            </div>
            <div>
                <div class="summary-num"><?= count($kendaraan) ?></div>
                <div class="summary-label">Total Kendaraan</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-icon" style="background:linear-gradient(135deg,#6b21a8,#9333ea);">
                <i class="bi bi-bicycle"></i>
            </div>
            <div>
                <div class="summary-num"><?= $total_motor ?></div>
                <div class="summary-label">Motor</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-icon" style="background:linear-gradient(135deg,#1d4ed8,#3b82f6);">
                <i class="bi bi-car-front"></i>
            </div>
            <div>
                <div class="summary-num"><?= $total_mobil ?></div>
                <div class="summary-label">Mobil</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel -->
<div class="card-custom">
    <div class="card-custom-header">
        <div>
            <div class="card-custom-title">
                <i class="bi bi-car-front me-2 text-success"></i>Daftar Kendaraan
            </div>
            <div class="card-custom-sub"><?= count($kendaraan) ?> kendaraan terdaftar</div>
        </div>
    </div>

    <div class="table-wrap">
        <table id="kendaraanTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Kendaraan</th>
                    <th>Kode</th>
                    <th>Plat Nomor</th>
                    <th>Pemilik</th>
                    <th>Satuan</th>
                    <th>Status</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kendaraan as $i => $k): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php
                                $thumbSrc = null;
                                if (!empty($k['thumbnail'])) {
                                    $thumbSrc = base_url('uploads/kendaraan/' . $k['thumbnail']['nama_file']);
                                } elseif (!empty($k['foto'])) {
                                    $thumbSrc = base_url('uploads/kendaraan/' . $k['foto']);
                                }
                            ?>
                            <?php if ($thumbSrc): ?>
                            <img src="<?= $thumbSrc ?>" class="kendaraan-thumb" alt="">
                            <?php else: ?>
                            <div class="kendaraan-thumb-placeholder <?= $k['jenis'] ?>">
                                <i class="bi <?= $k['jenis'] === 'motor' ? 'bi-bicycle' : 'bi-car-front' ?>"></i>
                            </div>
                            <?php endif; ?>
                            <div>
                                <div class="fw-500">
                                    <?= esc($k['merk']) ?> <?= esc($k['model']) ?>
                                    <?php if ($k['tahun_kendaraan']): ?>
                                    <span class="text-muted" style="font-weight:400;font-size:.75rem;">
                                        (<?= $k['tahun_kendaraan'] ?>)
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <span class="badge-jenis <?= $k['jenis'] ?>">
                                    <?= ucfirst($k['jenis']) ?>
                                </span>
                                <?php if ($k['warna']): ?>
                                <span class="text-muted" style="font-size:.72rem;">
                                    · <?= esc($k['warna']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td><code class="code-tag"><?= esc($k['kendaraan_kode']) ?></code></td>
                    <td><span class="plat-nomor"><?= esc($k['plat_nomor']) ?></span></td>
                    <td>
                        <div class="fw-500" style="font-size:.83rem;"><?= esc($k['personel_nama']) ?></div>
                        <div style="font-size:.73rem;color:#9ca3af;"><?= esc($k['pangkat_singkat']) ?> ·
                            <?= esc($k['nrp']) ?>
                        </div>
                    </td>
                    <td style="font-size:.8rem;color:#6b7280;"><?= esc($k['satker_singkat']) ?: '-' ?></td>
                    <td>
                        <?php if ($k['status'] === 'aktif'): ?>
                        <span class="badge-status aktif"><span class="badge-dot"></span> Aktif</span>
                        <?php else: ?>
                        <span class="badge-status dibekukan"><span class="badge-dot"></span> Dibekukan</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-wrap">
                            <a href="<?= base_url('admin/kendaraan/edit/' . $k['uuid']) ?>" class="btn-action edit"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button"
                                class="btn-action <?= $k['status'] === 'aktif' ? 'deactivate' : 'activate' ?> btn-toggle-status"
                                data-uuid="<?= $k['uuid'] ?>" data-status="<?= $k['status'] ?>"
                                data-plat="<?= esc($k['plat_nomor']) ?>"
                                data-kendaraan="<?= esc($k['merk'] . ' ' . $k['model']) ?>"
                                title="<?= $k['status'] === 'aktif' ? 'Bekukan' : 'Aktifkan' ?>">
                                <i class="bi <?= $k['status'] === 'aktif' ? 'bi-lock' : 'bi-unlock' ?>"></i>
                            </button>
                            <button type="button" class="btn-action delete btn-delete-kendaraan"
                                data-uuid="<?= $k['uuid'] ?>" data-plat="<?= esc($k['plat_nomor']) ?>"
                                data-kendaraan="<?= esc($k['merk'] . ' ' . $k['model']) ?>" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Delete Modal -->
<div class="custom-modal-backdrop" id="deleteModal">
    <div class="custom-modal">
        <div class="custom-modal-icon">
            <i class="bi bi-trash"></i>
        </div>

        <h5>Hapus Kendaraan?</h5>

        <p id="deleteText">
            Apakah Anda yakin ingin menghapus kendaraan ini?
        </p>

        <form id="deleteForm" method="POST">
            <?= csrf_field() ?>

            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" id="closeDeleteModal">
                    Batal
                </button>

                <button type="submit" class="btn-modal-delete">
                    <i class="bi bi-trash me-1"></i>
                    Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toggle Status Modal -->
<div class="custom-modal-backdrop" id="toggleModal">
    <div class="custom-modal">

        <div class="custom-modal-icon" id="toggleIcon">
            <i class="bi bi-lock"></i>
        </div>

        <h5 id="toggleTitle">
            Ubah Status Kendaraan
        </h5>

        <p id="toggleText">
            Apakah Anda yakin?
        </p>

        <form id="toggleForm" method="POST">
            <?= csrf_field() ?>

            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" id="closeToggleModal">
                    Batal
                </button>

                <button type="submit" class="btn-modal-delete" id="toggleSubmitBtn">
                    Konfirmasi
                </button>
            </div>
        </form>

    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css">
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
.kendaraan-thumb {
    width: 52px;
    height: 40px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid #e5e7eb;
    flex-shrink: 0;
}

.kendaraan-thumb-placeholder {
    width: 52px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.kendaraan-thumb-placeholder.motor {
    background: #ede9fe;
    color: #5b21b6;
}

.kendaraan-thumb-placeholder.mobil {
    background: #dbeafe;
    color: #1e40af;
}

.fw-500 {
    font-weight: 500;
}

.badge-jenis {
    font-size: .68rem;
    font-weight: 500;
    padding: 1px 7px;
    border-radius: 99px;
}

.badge-jenis.motor {
    background: #ede9fe;
    color: #5b21b6;
}

.badge-jenis.mobil {
    background: #dbeafe;
    color: #1e40af;
}

.code-tag {
    background: #f3f4f6;
    color: #374151;
    padding: 2px 8px;
    border-radius: 5px;
    font-size: .78rem;
}

.plat-nomor {
    font-weight: 600;
    font-size: .85rem;
    letter-spacing: .05em;
    color: #111827;
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

.badge-status.dibekukan {
    background: #fff7ed;
    color: #c2410c;
}

.badge-status.dibekukan .badge-dot {
    background: #f97316;
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
   DATATABLES LAYOUT — TOP & BOTTOM BARS
   .dt-top-wrap   = length + search
   .dt-bottom-wrap = info + pagination
============================================= */

/* Top bar: search kiri, length kanan — sejajar, rapi */
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

/* Bottom bar: info kiri, pagination kanan */
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
    /* kiri */
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
    /* ikon kaca pembesar via unicode */
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
    /* kanan */
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
    padding: .38rem .65rem;
    font-size: .82rem;
    color: #374151;
    background: #fff;
    outline: none;
    cursor: pointer;
    transition: border-color .2s;
    /* hapus appearance bawaan browser supaya konsisten */
    -webkit-appearance: auto;
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

div.dataTables_wrapper div.dataTables_paginate .paginate_button {
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

div.dataTables_wrapper div.dataTables_paginate .paginate_button:hover {
    background: #f0fdf4 !important;
    color: #166534 !important;
    border-color: #bbf7d0 !important;
}

div.dataTables_wrapper div.dataTables_paginate .paginate_button.current,
div.dataTables_wrapper div.dataTables_paginate .paginate_button.current:hover {
    background: linear-gradient(135deg, #166534, #16a34a) !important;
    color: #fff !important;
    border-color: transparent !important;
    box-shadow: 0 2px 6px rgba(22, 101, 52, .3);
}

div.dataTables_wrapper div.dataTables_paginate .paginate_button.disabled,
div.dataTables_wrapper div.dataTables_paginate .paginate_button.disabled:hover {
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

/* Custom sort arrows yang lebih rapi */
table.dataTable thead .sorting::after,
table.dataTable thead .sorting_asc::after,
table.dataTable thead .sorting_desc::after {
    position: absolute;
    right: .5rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: .65rem;
    opacity: .4;
}

table.dataTable thead .sorting::after {
    content: '⇅';
    opacity: .3;
}

table.dataTable thead .sorting_asc::after {
    content: '↑';
    opacity: .7;
    color: #16a34a;
}

table.dataTable thead .sorting_desc::after {
    content: '↓';
    opacity: .7;
    color: #16a34a;
}

/* Hilangkan default DT processing overlay */
.dataTables_processing {
    border: none !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, .08) !important;
    border-radius: 10px !important;
    font-size: .82rem !important;
    color: #374151 !important;
}

/* Responsive — di mobile search full width */
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

/* =============================================
   DELETE MODAL
============================================= */

.custom-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(17, 24, 39, .45);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 1rem;
}

.custom-modal-backdrop.show {
    display: flex;
}

.custom-modal {
    width: 100%;
    max-width: 420px;
    background: #fff;
    border-radius: 16px;
    padding: 1.75rem;
    text-align: center;
    box-shadow: 0 20px 40px rgba(0, 0, 0, .15);
    animation: modalPop .18s ease;
}

@keyframes modalPop {
    from {
        opacity: 0;
        transform: scale(.96);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

.custom-modal-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: #fef2f2;
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.8rem;
}

.custom-modal h5 {
    margin: 0 0 .5rem;
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
}

.custom-modal p {
    margin: 0;
    font-size: .85rem;
    color: #6b7280;
    line-height: 1.6;
}

.modal-actions {
    display: flex;
    gap: .75rem;
    margin-top: 1.5rem;
}

.btn-modal-cancel,
.btn-modal-delete {
    flex: 1;
    border: none;
    border-radius: 10px;
    padding: .7rem 1rem;
    font-size: .82rem;
    font-weight: 500;
    cursor: pointer;
}

.btn-modal-cancel {
    background: #f3f4f6;
    color: #374151;
}

.btn-modal-cancel:hover {
    background: #e5e7eb;
}

.btn-modal-delete {
    background: #dc2626;
    color: #fff;
}

.btn-modal-delete:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(101, 22, 22, 0.35);
    background: #b91c1c;
}

#toggleIcon.activate {
    background: #dcfce7;
    color: #166534;
}

#toggleIcon.deactivate {
    background: #fff7ed;
    color: #c2410c;
}

.btn-modal-success {
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    border: none;
    border-radius: 9px;
    padding: .6rem 1rem;
    font-size: .82rem;
    font-weight: 500;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(22, 101, 52, .25);
}

.btn-modal-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(22, 101, 52, .35);
}
</style>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#kendaraanTable').DataTable({
        language: {
            search: "",
            searchPlaceholder: "Cari kendaraan...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_–_END_ dari _TOTAL_ kendaraan",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ data)",
            zeroRecords: "Tidak ada kendaraan yang cocok",
            emptyTable: "Belum ada data kendaraan",
            paginate: {
                first: "«",
                last: "»",
                next: "›",
                previous: "‹"
            }
        },

        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],

        columnDefs: [{
                orderable: false,
                searchable: false,
                targets: [0, 7]
            },
            {
                orderable: false,
                targets: [1]
            }
        ],

        order: [],

        responsive: true,

        // DOM: search (f) | length (l) — tabel (t) — info (i) | pagination (p)
        dom: '<"dt-top-wrap"fl>rt<"dt-bottom-wrap"ip>',

        drawCallback: function() {
            // Tidak perlu hover manual — sudah ditangani CSS
        }
    });
});

// =====================================
// DELETE MODAL
// =====================================

const deleteModal = document.getElementById('deleteModal');
const deleteForm = document.getElementById('deleteForm');
const deleteText = document.getElementById('deleteText');
const closeDeleteModal = document.getElementById('closeDeleteModal');

document.querySelectorAll('.btn-delete-kendaraan').forEach(btn => {

    btn.addEventListener('click', function() {

        const uuid = this.dataset.uuid;
        const plat = this.dataset.plat;
        const kendaraan = this.dataset.kendaraan;

        deleteText.innerHTML =
            `Kendaraan <strong>${kendaraan}</strong><br>
             dengan nomor polisi <strong>${plat}</strong><br>
             akan dihapus secara permanen.`;

        deleteForm.action =
            "<?= base_url('admin/kendaraan/delete') ?>/" + uuid;

        deleteModal.classList.add('show');
    });
});

closeDeleteModal.addEventListener('click', () => {
    deleteModal.classList.remove('show');
});

deleteModal.addEventListener('click', function(e) {
    if (e.target === deleteModal) {
        deleteModal.classList.remove('show');
    }
});

// =====================================
// TOGGLE STATUS MODAL
// =====================================

const toggleModal = document.getElementById('toggleModal');
const toggleForm = document.getElementById('toggleForm');

const toggleTitle = document.getElementById('toggleTitle');
const toggleText = document.getElementById('toggleText');

const toggleIcon = document.getElementById('toggleIcon');
const toggleSubmitBtn = document.getElementById('toggleSubmitBtn');

const closeToggleModal =
    document.getElementById('closeToggleModal');

document.querySelectorAll('.btn-toggle-status')
    .forEach(btn => {

        btn.addEventListener('click', function() {

            const uuid = this.dataset.uuid;
            const status = this.dataset.status;

            const kendaraan =
                this.dataset.kendaraan;

            const plat =
                this.dataset.plat;

            toggleForm.action =
                "<?= base_url('admin/kendaraan/toggle') ?>/" + uuid;

            if (status === 'aktif') {

                toggleTitle.innerHTML =
                    'Bekukan Kendaraan';

                toggleText.innerHTML =
                    `Kendaraan <strong>${kendaraan}</strong><br>
                     dengan nomor polisi <strong>${plat}</strong><br>
                     akan diubah menjadi <strong>Dibekukan</strong>.`;

                toggleIcon.className =
                    'custom-modal-icon deactivate';

                toggleIcon.innerHTML =
                    '<i class="bi bi-lock"></i>';

                toggleSubmitBtn.className =
                    'btn-modal-delete';

                toggleSubmitBtn.innerHTML =
                    '<i class="bi bi-lock me-1"></i> Bekukan';

            } else {

                toggleTitle.innerHTML =
                    'Aktifkan Kendaraan';

                toggleText.innerHTML =
                    `Kendaraan <strong>${kendaraan}</strong><br>
                     dengan nomor polisi <strong>${plat}</strong><br>
                     akan diaktifkan kembali.`;

                toggleIcon.className =
                    'custom-modal-icon activate';

                toggleIcon.innerHTML =
                    '<i class="bi bi-unlock"></i>';

                toggleSubmitBtn.className =
                    'btn-modal-success';

                toggleSubmitBtn.innerHTML =
                    '<i class="bi bi-unlock me-1"></i> Aktifkan';
            }

            toggleModal.classList.add('show');
        });
    });

closeToggleModal.addEventListener('click', () => {
    toggleModal.classList.remove('show');
});

toggleModal.addEventListener('click', function(e) {
    if (e.target === toggleModal) {
        toggleModal.classList.remove('show');
    }
});
</script>
<?= $this->endSection() ?>