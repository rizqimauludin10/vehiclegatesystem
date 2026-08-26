<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Laporan & Rekap</h5>
        <p class="page-header-sub">
            Periode <?= date('d M Y', strtotime($filter['tanggal_dari'])) ?>
            — <?= date('d M Y', strtotime($filter['tanggal_sampai'])) ?>
        </p>
    </div>
    <a href="<?= base_url('admin/laporan/export?' . http_build_query($filter)) ?>" class="btn-export">
        <i class="bi bi-download me-1"></i> Export CSV
    </a>
</div>

<!-- Filter Form -->
<div class="filter-card mb-4">
    <form method="GET" action="<?= base_url('admin/laporan') ?>" id="filterForm">
        <div class="filter-grid">

            <!-- Tanggal Dari -->
            <div class="filter-group">
                <label class="filter-label">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" class="filter-input" value="<?= $filter['tanggal_dari'] ?>">
            </div>

            <!-- Tanggal Sampai -->
            <div class="filter-group">
                <label class="filter-label">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" class="filter-input" value="<?= $filter['tanggal_sampai'] ?>">
            </div>

            <!-- Shift -->
            <div class="filter-group">
                <label class="filter-label">Shift</label>
                <select name="shift" class="filter-input">
                    <option value="">Semua Shift</option>
                    <option value="pagi" <?= $filter['shift'] === 'pagi'  ? 'selected' : '' ?>>Pagi</option>
                    <option value="siang" <?= $filter['shift'] === 'siang' ? 'selected' : '' ?>>Siang</option>
                    <option value="malam" <?= $filter['shift'] === 'malam' ? 'selected' : '' ?>>Malam</option>
                </select>
            </div>

            <!-- Kelompok Pangkat -->
            <div class="filter-group">
                <label class="filter-label">Kelompok</label>
                <select name="kelompok" class="filter-input">
                    <option value="">Semua Kelompok</option>
                    <?php foreach (['Perwira','Bintara','Tamtama','PNS'] as $k): ?>
                    <option value="<?= $k ?>" <?= $filter['kelompok'] === $k ? 'selected' : '' ?>>
                        <?= $k ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Pangkat -->
            <div class="filter-group">
                <label class="filter-label">Pangkat</label>
                <select name="pangkat_id" class="filter-input">
                    <option value="">Semua Pangkat</option>
                    <?php foreach ($pangkat as $kelompok => $items): ?>
                    <optgroup label="- <?= $kelompok ?> ─">
                        <?php foreach ($items as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $filter['pangkat_id'] == $p['id'] ? 'selected' : '' ?>>
                            <?= esc($p['pangkat_singkat']) ?> — <?= esc($p['nama_pangkat']) ?>
                        </option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Satker -->
            <div class="filter-group">
                <label class="filter-label">Satuan Kerja</label>
                <select name="satker_id" class="filter-input">
                    <option value="">Semua Satker</option>
                    <?php foreach ($satker as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $filter['satker_id'] == $s['id'] ? 'selected' : '' ?>>
                        <?= esc($s['kode_satker']) ?> — <?= esc($s['nama_singkat']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Petugas -->
            <div class="filter-group">
                <label class="filter-label">Petugas</label>
                <select name="petugas_id" class="filter-input">
                    <option value="">Semua Petugas</option>
                    <?php foreach ($petugas as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $filter['petugas_id'] == $p['id'] ? 'selected' : '' ?>>
                        <?= esc($p['nama']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Action -->
            <div class="filter-group filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="<?= base_url('admin/laporan') ?>" class="btn-reset">
                    <i class="bi bi-x-circle me-1"></i> Reset
                </a>
            </div>

        </div>
    </form>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#166534,#16a34a);">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="stat-value"><?= $stats['total'] ?></div>
                <div class="stat-label">Total Scan</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#1d4ed8,#3b82f6);">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <div class="stat-value"><?= $stats['unique_personel'] ?></div>
                <div class="stat-label">Personel Unik</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#92400e,#d97706);">
                <i class="bi bi-sun"></i>
            </div>
            <div>
                <div class="stat-value"><?= $stats['by_shift']['pagi'] ?></div>
                <div class="stat-label">Shift Pagi</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#4c1d95,#7c3aed);">
                <i class="bi bi-moon-stars"></i>
            </div>
            <div>
                <div class="stat-value"><?= $stats['by_shift']['malam'] ?></div>
                <div class="stat-label">Shift Malam</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Log — DataTables -->
<div class="card-custom">
    <div class="card-custom-header">
        <div>
            <div class="card-custom-title">
                <i class="bi bi-list-check me-2 text-success"></i>Detail Log Keluar
            </div>
            <div class="card-custom-sub"><?= $stats['total'] ?> record ditemukan</div>
        </div>
    </div>

    <div class="table-wrap">
        <?php if (empty($logs)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <div>Tidak ada data untuk filter ini</div>
            <a href="<?= base_url('admin/laporan') ?>" class="btn-reset mt-3 d-inline-flex">
                Reset Filter
            </a>
        </div>
        <?php else: ?>
        <table id="logTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Personel</th>
                    <th>NRP</th>
                    <th>Pangkat</th>
                    <th>Kelompok</th>
                    <th>Satuan Kerja</th>
                    <th>Shift</th>
                    <th>Petugas</th>
                    <th>Tanggal & Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $i => $log): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if ($log['personel_foto']): ?>
                            <img src="<?= base_url('uploads/personel/' . $log['personel_foto']) ?>" class="avatar-photo"
                                alt="">
                            <?php else: ?>
                            <div class="avatar-initial">
                                <?= strtoupper(substr($log['personel_nama'], 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                            <span class="fw-500"><?= esc($log['personel_nama']) ?></span>
                        </div>
                    </td>
                    <td><code class="code-tag"><?= esc($log['nrp']) ?></code></td>
                    <td style="font-size:.82rem;">
                        <?= esc($log['pangkat_singkat'] ?? '-') ?>
                    </td>
                    <td>
                        <?php
                                $kelompok = $log['pangkat_kelompok'] ?? '';
                                $kelompokClass = [
                                    'Perwira' => 'badge-perwira',
                                    'Bintara' => 'badge-bintara',
                                    'Tamtama' => 'badge-tamtama',
                                    'PNS'     => 'badge-pns',
                                    'PPPK'     => 'badge-pppk',
                                ][$kelompok] ?? 'badge-default';
                            ?>
                        <span class="badge-kelompok <?= $kelompokClass ?>">
                            <?= esc($kelompok ?: '-') ?>
                        </span>
                    </td>
                    <td style="font-size:.8rem;color:#6b7280;">
                        <?= esc($log['satker_singkat'] ?? '-') ?>
                    </td>
                    <td>
                        <?php if ($log['shift']): ?>
                        <span class="badge-shift <?= $log['shift'] ?>">
                            <?= ucfirst($log['shift']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:.82rem;"><?= esc($log['petugas_nama']) ?></td>
                    <td>
                        <div style="font-size:.82rem;font-weight:500;color:#111827;">
                            <?= date('d M Y', strtotime($log['timestamp_keluar'])) ?>
                        </div>
                        <div style="font-size:.75rem;color:#9ca3af;">
                            <?= date('H:i:s', strtotime($log['timestamp_keluar'])) ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<!-- DataTables CSS -->
<!-- <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css"> -->
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

.btn-export {
    display: inline-flex;
    align-items: center;
    background: #fff;
    color: #166534;
    font-size: .82rem;
    font-weight: 500;
    padding: .5rem 1rem;
    border-radius: 9px;
    text-decoration: none;
    border: 1px solid #bbf7d0;
    transition: all .2s
}

.btn-export:hover {
    background: #f0fdf4;
    color: #15803d
}

/* Filter */
.filter-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    padding: 1.1rem 1.25rem
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: .85rem;
    align-items: end
}

.filter-label {
    display: block;
    font-size: .75rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 5px
}

.filter-input {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .5rem .75rem;
    font-size: .82rem;
    color: #111827;
    outline: none;
    transition: all .15s
}

.filter-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1)
}

.filter-actions {
    display: flex;
    gap: .5rem
}

.btn-filter {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: .82rem;
    font-weight: 500;
    padding: .5rem .75rem;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all .2s
}

.btn-filter:hover {
    transform: translateY(-1px)
}

.btn-reset {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    color: #6b7280;
    font-size: .82rem;
    padding: .5rem .75rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    text-decoration: none;
    transition: all .15s
}

.btn-reset:hover {
    background: #f9fafb
}

/* Stats */
.stat-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: .85rem
}

.stat-icon {
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

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    line-height: 1
}

.stat-label {
    font-size: .72rem;
    color: #9ca3af;
    margin-top: 2px
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

.table-wrap {
    padding: 1rem 1.25rem
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
    padding: .65rem 1rem;
    font-size: .7rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
    border-bottom: 1px solid #f3f4f6
}

.table-custom td {
    padding: .72rem 1rem;
    color: #374151;
    border-bottom: 1px solid #f9fafb;
    vertical-align: middle
}

.table-custom tbody tr:hover {
    background: #fafafa
}

.fw-500 {
    font-weight: 500
}

.avatar-photo {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid #e5e7eb
}

.avatar-initial {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: .75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center
}

.code-tag {
    background: #f3f4f6;
    color: #374151;
    padding: 2px 8px;
    border-radius: 5px;
    font-size: .78rem
}

/* Badge kelompok */
.badge-kelompok {
    font-size: .68rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 99px
}

.badge-perwira {
    background: #f4f09f;
    color: #554e0a
}

.badge-bintara {
    background: #dbeafe;
    color: #1e40af
}

.badge-tamtama {
    background: #fec3c3;
    color: #850e0e
}

.badge-pns {
    background: #f3e8ff;
    color: #5b21b6
}

.badge-pppk {
    background: #e8f8ff;
    color: #21b1b6
}

.badge-default {
    background: #f3f4f6;
    color: #6b7280
}

/* Badge shift */
.badge-shift {
    font-size: .7rem;
    font-weight: 500;
    padding: 2px 9px;
    border-radius: 99px
}

.badge-shift.pagi {
    background: #fef9c3;
    color: #854d0e
}

.badge-shift.siang {
    background: #dbeafe;
    color: #1e40af
}

.badge-shift.malam {
    background: #ede9fe;
    color: #5b21b6
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

/* DataTables override */
div.dataTables_wrapper div.dataTables_length select,
div.dataTables_wrapper div.dataTables_filter input {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .35rem .65rem;
    font-size: .82rem;
    outline: none
}

div.dataTables_wrapper div.dataTables_filter input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1)
}

div.dataTables_wrapper div.dataTables_length label,
div.dataTables_wrapper div.dataTables_filter label,
div.dataTables_wrapper div.dataTables_info {
    font-size: .78rem;
    color: #6b7280
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
    border-bottom: 1px solid #f3f4f6 !important
}

table.dataTable.no-footer {
    border-bottom: none !important
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap5.min.js"></script> -->
<script>
$(document).ready(function() {
    <?php if (!empty($logs)): ?>
    $('#logTable').DataTable({
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ log",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang cocok",
            emptyTable: "Belum ada log",
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
                targets: [0]
            },
            {
                orderable: false,
                targets: [1]
            },
        ],
        order: [
            [8, 'desc']
        ], // Sort by tanggal DESC
        responsive: true,
        dom: '<"dt-top-wrap"lf>rt<"dt-bottom-wrap"ip>',
    });
    <?php endif; ?>

    // Auto submit filter saat tanggal berubah
    document.querySelectorAll('input[type="date"]').forEach(input => {
        input.addEventListener('change', () => {
            document.getElementById('filterForm').submit();
        });
    });
});
</script>
<?= $this->endSection() ?>