<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Log Scan Saya</h5>
        <p class="page-header-sub">Riwayat scan QR yang kamu lakukan</p>
    </div>
</div>

<!-- Filter -->
<div class="filter-card mb-4">
    <form method="GET" action="<?= base_url('petugas/laporan') ?>" id="filterForm">
        <div class="filter-grid">
            <div class="filter-group">
                <label class="filter-label">Tanggal</label>
                <input type="date" name="tanggal" class="filter-input" value="<?= $tanggal ?>"
                    max="<?= date('Y-m-d') ?>">
            </div>
            <div class="filter-group">
                <label class="filter-label">Shift</label>
                <select name="shift" class="filter-input">
                    <option value="">Semua Shift</option>
                    <option value="pagi" <?= $filter['shift'] === 'pagi'  ? 'selected' : '' ?>>Pagi</option>
                    <option value="siang" <?= $filter['shift'] === 'siang' ? 'selected' : '' ?>>Siang</option>
                    <option value="malam" <?= $filter['shift'] === 'malam' ? 'selected' : '' ?>>Malam</option>
                </select>
            </div>
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
            <div class="filter-group">
                <label class="filter-label">Pangkat</label>
                <select name="pangkat_id" class="filter-input">
                    <option value="">Semua Pangkat</option>
                    <?php foreach ($pangkat as $kelompok => $items): ?>
                    <optgroup label="── <?= $kelompok ?> ──">
                        <?php foreach ($items as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $filter['pangkat_id'] == $p['id'] ? 'selected' : '' ?>>
                            <?= esc($p['pangkat_singkat']) ?> — <?= esc($p['nama_pangkat']) ?>
                        </option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
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
            <div class="filter-group filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="<?= base_url('petugas/laporan') ?>" class="btn-reset">
                    Hari Ini
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#166534,#16a34a);">
                <i class="bi bi-qr-code-scan"></i>
            </div>
            <div>
                <div class="stat-value"><?= $stats['total'] ?></div>
                <div class="stat-label">Total Scan</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
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
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#92400e,#d97706);">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div>
                <div class="stat-value"><?= date('d M', strtotime($tanggal)) ?></div>
                <div class="stat-label"><?= date('Y', strtotime($tanggal)) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel -->
<div class="card-custom">
    <div class="card-custom-header">
        <div>
            <div class="card-custom-title">
                <i class="bi bi-list-check me-2 text-success"></i>Riwayat Scan
            </div>
            <div class="card-custom-sub">
                <?= date('l, d F Y', strtotime($tanggal)) ?> · <?= $stats['total'] ?> scan
            </div>
        </div>
    </div>

    <div class="table-wrap">
        <?php if (empty($logs)): ?>
        <div class="empty-state">
            <i class="bi bi-qr-code"></i>
            <div>Belum ada scan pada filter ini</div>
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
                    <th>Waktu</th>
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
                    <td>
                        <div style="font-size:.85rem;font-weight:600;color:#111827;">
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
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css">
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

.filter-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    padding: 1.1rem 1.25rem
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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
    cursor: pointer
}

.btn-reset {
    display: inline-flex;
    align-items: center;
    background: #fff;
    color: #6b7280;
    font-size: .82rem;
    padding: .5rem .75rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    text-decoration: none
}

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

.badge-kelompok {
    font-size: .68rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 99px
}

.badge-perwira {
    background: #dbeafe;
    color: #1e40af
}

.badge-bintara {
    background: #dcfce7;
    color: #166534
}

.badge-tamtama {
    background: #fef9c3;
    color: #854d0e
}

.badge-pns {
    background: #f3e8ff;
    color: #5b21b6
}

.badge-default {
    background: #f3f4f6;
    color: #6b7280
}

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

div.dataTables_wrapper div.dataTables_paginate .paginate_button {
    border-radius: 7px !important;
    font-size: .78rem !important;
    padding: .25rem .6rem !important
}

div.dataTables_wrapper div.dataTables_paginate .paginate_button.current,
div.dataTables_wrapper div.dataTables_paginate .paginate_button.current:hover {
    background: linear-gradient(135deg, #166534, #16a34a) !important;
    color: #fff !important;
    border-color: transparent !important
}

div.dataTables_wrapper div.dataTables_paginate .paginate_button:hover {
    background: #f0fdf4 !important;
    color: #166534 !important;
    border-color: #dcfce7 !important
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap5.min.js"></script>
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
            emptyTable: "Belum ada log scan",
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
            [7, 'desc']
        ],
        responsive: true,
        dom: '<"dt-top-wrap"lf>rt<"dt-bottom-wrap"ip>',
    });
    <?php endif; ?>

    // Auto submit saat tanggal berubah
    document.querySelector('input[type="date"]').addEventListener('change', () => {
        document.getElementById('filterForm').submit();
    });
});
</script>
<?= $this->endSection() ?>