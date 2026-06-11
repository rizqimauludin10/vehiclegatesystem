<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #166534, #16a34a);">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $log_hari_ini ?></div>
                <div class="stat-label">Scan Hari Ini</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #1d4ed8, #3b82f6);">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= session()->get('user_nama') ?></div>
                <div class="stat-label">Petugas Bertugas</div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <a href="<?= base_url('petugas/scan') ?>" class="stat-card scan-btn-card text-decoration-none">
            <div class="stat-icon" style="background: linear-gradient(135deg, #6b21a8, #9333ea);">
                <i class="bi bi-qr-code-scan"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:1.1rem;">Mulai Scan</div>
                <div class="stat-label">Tap untuk scan QR Code</div>
            </div>
            <i class="bi bi-arrow-right ms-auto text-muted"></i>
        </a>
    </div>
</div>

<!-- Log Scan Terbaru -->
<div class="card-custom">
    <div class="card-custom-header">
        <div>
            <div class="card-custom-title">
                <i class="bi bi-list-check me-2 text-success"></i>Log Scan Hari Ini
            </div>
            <div class="card-custom-sub"><?= date('l, d F Y') ?></div>
        </div>
    </div>

    <?php
    $logModel = new \App\Models\LogKeluarModel();
    $logs = $logModel->getLogTerbaruByPetugas(session()->get('user_id'), 20);
    ?>

    <?php if (empty($logs)): ?>
    <div class="empty-state">
        <i class="bi bi-qr-code"></i>
        <div>Belum ada scan hari ini</div>
        <a href="<?= base_url('petugas/scan') ?>" class="btn-sm-custom mt-2 d-inline-block">
            Mulai Scan Sekarang
        </a>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Personel</th>
                    <th>NRP</th>
                    <th>Pangkat</th>
                    <th>Shift</th>
                    <th>Waktu Keluar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
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
                    <td>
                        <span class="badge-shift <?= $log['shift'] ?>">
                            <?= ucfirst($log['shift']) ?>
                        </span>
                    </td>
                    <td class="text-muted" style="font-size:.82rem;">
                        <?= date('H:i:s', strtotime($log['timestamp_keluar'])) ?>
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

.avatar-sm {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    object-fit: cover;
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
</style>
<?= $this->endSection() ?>