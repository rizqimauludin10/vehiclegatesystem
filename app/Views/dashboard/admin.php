<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-3 mb-4">
    <!-- Stat Card: Personel -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #1d4ed8, #3b82f6);">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $total_personel ?></div>
                <div class="stat-label">Total Personel</div>
            </div>
        </div>
    </div>

    <!-- Stat Card: Kendaraan -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #166534, #16a34a);">
                <i class="bi bi-car-front-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $total_kendaraan ?></div>
                <div class="stat-label">Total Kendaraan</div>
            </div>
        </div>
    </div>

    <!-- Stat Card: Petugas -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #92400e, #d97706);">
                <i class="bi bi-shield-fill-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $total_petugas ?></div>
                <div class="stat-label">Total Petugas</div>
            </div>
        </div>
    </div>

    <!-- Stat Card: Log Hari Ini -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #6b21a8, #9333ea);">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $log_hari_ini ?></div>
                <div class="stat-label">Keluar Hari Ini</div>
            </div>
        </div>
    </div>
</div>

<!-- Log Terbaru -->
<div class="card-custom">
    <div class="card-custom-header">
        <div>
            <div class="card-custom-title">
                <i class="bi bi-clock-history me-2 text-success"></i>Aktivitas Terbaru
            </div>
            <div class="card-custom-sub">Log kendaraan keluar terbaru</div>
        </div>
        <a href="<?= base_url('admin/laporan') ?>" class="btn-sm-custom">
            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <?php if (empty($log_terbaru)): ?>
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <div>Belum ada aktivitas hari ini</div>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Personel</th>
                    <th>NRP</th>
                    <th>Pangkat</th>
                    <th>Petugas</th>
                    <th>Shift</th>
                    <th>Waktu Keluar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($log_terbaru as $log): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if ($log['personel_foto']): ?>
                            <img src="<?= base_url('uploads/personel/' . $log['personel_foto']) ?>" class="avatar-sm"
                                alt="">
                            <?php else: ?>
                            <div class="avatar-sm-placeholder">
                                <?= strtoupper(substr($log['personel_nama'], 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                            <span class="fw-500"><?= esc($log['personel_nama']) ?></span>
                        </div>
                    </td>
                    <td><code><?= esc($log['nrp']) ?></code></td>
                    <td><?= esc($log['pangkat_singkat']) ?></td>
                    <td><?= esc($log['petugas_nama']) ?></td>
                    <td>
                        <span class="badge-shift <?= $log['shift'] ?>">
                            <?= ucfirst($log['shift']) ?>
                        </span>
                    </td>
                    <td class="text-muted" style="font-size:.82rem;">
                        <?= date('d M Y, H:i', strtotime($log['timestamp_keluar'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* ── Stat Cards ── */
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

/* ── Card ── */
.card-custom {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
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

.btn-sm-custom {
    font-size: .78rem;
    font-weight: 500;
    color: #16a34a;
    text-decoration: none;
    padding: .35rem .75rem;
    border-radius: 7px;
    border: 1px solid #dcfce7;
    background: #f0fdf4;
    white-space: nowrap;
    transition: all .15s;
}

.btn-sm-custom:hover {
    background: #dcfce7;
    color: #15803d;
}

/* ── Table ── */
.table-custom {
    width: 100%;
    border-collapse: collapse;
    font-size: .83rem;
}

.table-custom thead tr {
    background: #f9fafb;
    border-bottom: 1px solid #f3f4f6;
}

.table-custom th {
    padding: .7rem 1.25rem;
    font-size: .72rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
}

.table-custom td {
    padding: .75rem 1.25rem;
    color: #374151;
    border-bottom: 1px solid #f9fafb;
    vertical-align: middle;
}

.table-custom tbody tr:hover {
    background: #fafafa;
}

.table-custom tbody tr:last-child td {
    border-bottom: none;
}

.fw-500 {
    font-weight: 500;
}

/* ── Avatar ── */
.avatar-sm {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}

.avatar-sm-placeholder {
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
    flex-shrink: 0;
}

/* ── Badge Shift ── */
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

/* ── Empty State ── */
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
</style>
<?= $this->endSection() ?>