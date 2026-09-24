<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Stat 1: Scan Hari Ini -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #166534, #16a34a);">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="stat-value"><?= esc($log_hari_ini ?? 0) ?></div>
                <div class="stat-label">Scan Hari Ini</div>
            </div>
        </div>
    </div>

    <!-- Stat 2: Petugas Bertugas -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #1d4ed8, #3b82f6);">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div>
                <div class="stat-value" style="font-size:1.25rem;"><?= esc(session()->get('user_nama')) ?></div>
                <div class="stat-label">Petugas Bertugas</div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Mulai Scan QR -->
    <div class="col-xl-4">
        <a href="<?= base_url('petugas/scan') ?>" class="stat-card scan-btn-card text-decoration-none">
            <div class="stat-icon" style="background: linear-gradient(135deg, #6b21a8, #9333ea);">
                <i class="bi bi-qr-code-scan"></i>
            </div>
            <div>
                <div class="stat-value" style="font-size:1.1rem;">Mulai Scan</div>
                <div class="stat-label">Tap untuk scan QR Code</div>
            </div>
            <i class="bi bi-arrow-right ms-auto text-muted fs-5"></i>
        </a>
    </div>
</div>

<!-- Log Scan Hari Ini -->
<div class="card-custom">
    <div class="card-custom-header">
        <div>
            <div class="card-custom-title">
                <i class="bi bi-list-check me-2 text-success"></i>Log Scan Hari Ini
            </div>
            <div class="card-custom-sub"><?= date('l, d F Y') ?></div>
        </div>
    </div>

    <div class="table-wrap">
        <?php if (empty($logs)): ?>
        <div class="empty-state">
            <i class="bi bi-qr-code"></i>
            <div>Belum ada scan hari ini</div>
            <a href="<?= base_url('petugas/scan') ?>" class="btn-sm-custom mt-2 d-inline-block">
                Mulai Scan Sekarang
            </a>
        </div>
        <?php else: ?>
        <table id="logTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th width="40">#</th>
                    <th>Personel</th>
                    <th>NRP</th>
                    <th>Pangkat & Satker</th>
                    <th>Kendaraan & Plat</th>
                    <th>Shift</th>
                    <th>Waktu Scan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $i => $log): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if (!empty($log['personel_foto'])): ?>
                            <img src="<?= base_url('uploads/personel/' . $log['personel_foto']) ?>" class="avatar-photo"
                                alt="">
                            <?php else: ?>
                            <div class="avatar-initial">
                                <?= strtoupper(substr($log['personel_nama'] ?? 'P', 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                            <span class="fw-500"><?= esc($log['personel_nama'] ?? '-') ?></span>
                        </div>
                    </td>
                    <td><code class="code-tag"><?= esc($log['nrp'] ?? '-') ?></code></td>
                    <td>
                        <div style="font-size:.82rem;font-weight:500;color:#111827;">
                            <?= esc($log['pangkat_singkat'] ?? '-') ?>
                        </div>
                        <div style="font-size:.75rem;color:#6b7280;">
                            <?= esc($log['satker_singkat'] ?? '-') ?>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="plate-number-badge">
                                <?= esc($log['plat_nomor'] ?? '-') ?>
                            </span>
                            <span class="badge bg-light text-secondary border fw-normal fs-xs text-capitalize">
                                <?php if (in_array(strtolower($log['jenis'] ?? ''), ['motor', 'sepeda motor', 'r2'])): ?>
                                <i class="bi bi-scooter me-1 text-primary"></i>
                                <?php else: ?>
                                <i class="bi bi-car-front me-1 text-success"></i>
                                <?php endif; ?>
                                <?= esc($log['jenis'] ?? 'Kendaraan') ?>
                            </span>
                        </div>
                    </td>
                    <td>
                        <?php if (!empty($log['shift'])): ?>
                        <span class="badge-shift <?= strtolower($log['shift']) ?>">
                            <?= ucfirst($log['shift']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-size:.82rem;font-weight:500;color:#111827;">
                            <?= date('H:i:s', strtotime($log['timestamp_keluar'])) ?>
                        </div>
                        <div style="font-size:.75rem;color:#9ca3af;">
                            <?= \CodeIgniter\I18n\Time::parse($log['timestamp_keluar'])->humanize() ?>
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
<style>
/* Stat Cards */
.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
    transition: transform .2s, box-shadow .2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, .07);
}

.scan-btn-card {
    cursor: pointer;
    border: 2px dashed #e9d5ff !important;
    background: #faf5ff !important;
}

.scan-btn-card:hover {
    border-color: #9333ea !important;
    background: #f3e8ff !important;
}

.stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

.stat-label {
    font-size: 0.78rem;
    color: #6b7280;
    margin-top: 3px;
}

/* Card & Table Wrap */
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
    padding: 1.1rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
}

.card-custom-title {
    font-size: .9rem;
    font-weight: 600;
    color: #111827;
}

.card-custom-sub {
    font-size: .75rem;
    color: #9ca3af;
    margin-top: 1px;
}

.table-wrap {
    padding: 1rem 1.25rem;
}

.btn-sm-custom {
    font-size: .78rem;
    font-weight: 500;
    color: #16a34a;
    text-decoration: none;
    padding: .35rem .75rem;
    border-radius: 7px;
    border: 1px solid #dcfce7;
    background: #f0fdf4;
}

/* Table Custom */
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
    padding: .72rem 1rem;
    color: #374151;
    border-bottom: 1px solid #f9fafb;
    vertical-align: middle;
}

.table-custom tbody tr:hover {
    background: #fafafa;
}

.fw-500 {
    font-weight: 500;
}

.avatar-photo {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid #e5e7eb;
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
    justify-content: center;
}

.code-tag {
    background: #f3f4f6;
    color: #374151;
    padding: 2px 8px;
    border-radius: 5px;
    font-size: .78rem;
}

.plate-number-badge {
    background: #0f172a;
    color: #ffffff;
    font-family: 'Courier New', Courier, monospace;
    font-weight: 800;
    font-size: 0.78rem;
    padding: 2px 7px;
    border-radius: 4px;
    letter-spacing: 0.5px;
    display: inline-block;
}

.badge-shift {
    font-size: .7rem;
    font-weight: 500;
    padding: 2px 9px;
    border-radius: 99px;
}

.badge-shift.pagi {
    background: #fef9c3;
    color: #854d0e;
}

.badge-shift.siang {
    background: #dbeafe;
    color: #1e40af;
}

.badge-shift.malam {
    background: #ede9fe;
    color: #5b21b6;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #9ca3af;
    font-size: .85rem;
}

.empty-state i {
    font-size: 2.5rem;
    display: block;
    margin-bottom: .75rem;
    opacity: .4;
}

/* =============================================
   DATATABLES WRAPPERS & PAGINATION FIX (SUPER CLEAN)
============================================= */
div.dataTables_wrapper div.dt-top-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .75rem;
    padding: 0 0 .85rem;
    border-bottom: 1px solid #f3f4f6;
    margin-bottom: .5rem;
}

div.dataTables_wrapper div.dt-bottom-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .75rem;
    padding: .85rem 0 0;
    border-top: 1px solid #f3f4f6;
    margin-top: .5rem;
}

div.dataTables_wrapper div.dataTables_filter label,
div.dataTables_wrapper div.dataTables_length label {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .8rem;
    color: #6b7280;
    margin: 0;
    font-weight: 500;
}

div.dataTables_wrapper div.dataTables_filter input {
    width: 200px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .35rem .75rem;
    font-size: .82rem;
    color: #374151;
    outline: none;
}

div.dataTables_wrapper div.dataTables_filter input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1);
}

div.dataTables_wrapper div.dataTables_length select {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .35rem .65rem;
    font-size: .82rem;
    color: #374151;
    outline: none;
}

div.dataTables_wrapper div.dataTables_info {
    font-size: .78rem;
    color: #6b7280;
}

/* PAGINATION STYLING SPESIFIK & BULLETPROOF */
div.dataTables_wrapper div.dataTables_paginate {
    display: flex;
    align-items: center;
    gap: 4px;
}

div.dataTables_wrapper div.dataTables_paginate .paginate_button,
div.dataTables_wrapper div.dataTables_paginate .page-item .page-link {
    border: 1px solid #e5e7eb !important;
    border-radius: 7px !important;
    font-size: .78rem !important;
    padding: .28rem .62rem !important;
    min-width: 32px;
    text-align: center;
    line-height: 1.4;
    cursor: pointer !important;
    color: #374151 !important;
    background: #ffffff !important;
    transition: all .15s ease;
    text-decoration: none !important;
    display: inline-block;
    box-shadow: none !important;
}

div.dataTables_wrapper div.dataTables_paginate .paginate_button:hover:not(.current):not(.disabled),
div.dataTables_wrapper div.dataTables_paginate .page-item .page-link:hover {
    background: #f0fdf4 !important;
    color: #166534 !important;
    border-color: #bbf7d0 !important;
}

div.dataTables_wrapper div.dataTables_paginate .paginate_button.current,
div.dataTables_wrapper div.dataTables_paginate .page-item.active .page-link {
    background: linear-gradient(135deg, #166534, #16a34a) !important;
    color: #ffffff !important;
    border-color: transparent !important;
    box-shadow: 0 2px 6px rgba(22, 101, 52, .3) !important;
    font-weight: 600;
}

div.dataTables_wrapper div.dataTables_paginate .paginate_button.disabled,
div.dataTables_wrapper div.dataTables_paginate .page-item.disabled .page-link {
    background: #f9fafb !important;
    color: #d1d5db !important;
    border-color: #f3f4f6 !important;
    cursor: not-allowed !important;
    box-shadow: none !important;
    opacity: 0.6;
}

/* Styling Khusus Titik-Titik (Ellipsis) DataTables */
div.dataTables_wrapper div.dataTables_paginate .paginate_button.ellipsis {
    border: none !important;
    background: transparent !important;
    color: #94a3b8 !important;
    cursor: default !important;
    padding: .28rem .35rem !important;
    min-width: auto !important;
    box-shadow: none !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
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
            emptyTable: "Belum ada log hari ini",
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
                targets: [1, 3, 4]
            }
        ],
        order: [
            [6, 'desc']
        ],
        responsive: true,
        dom: '<"dt-top-wrap"lf>rt<"dt-bottom-wrap"ip>',
    });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>