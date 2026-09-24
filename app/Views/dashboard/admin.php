<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Greeting -->
<div class="dash-greeting mb-4">
    <div>
        <h5 class="dash-title">Dashboard</h5>
        <p class="dash-sub">
            <?= date('l, d F Y') ?> &nbsp;·&nbsp;
            Selamat datang, <strong><?= esc(session()->get('user_nama')) ?></strong>
        </p>
    </div>
    <div class="dash-date-badge">
        <i class="bi bi-calendar3 me-2"></i>
        <?= date('d M Y') ?>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#1d4ed8,#3b82f6);">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $total_personel ?></div>
                <div class="stat-label">Total Personel</div>
            </div>
            <div class="stat-trend">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#166534,#16a34a);">
                <i class="bi bi-car-front-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $total_kendaraan ?></div>
                <div class="stat-label">Total Kendaraan</div>
            </div>
            <div class="stat-trend">
                <i class="bi bi-car-front"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:linear-gradient(135deg,#92400e,#d97706);">
                <i class="bi bi-shield-fill-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $total_petugas ?></div>
                <div class="stat-label">Total Petugas</div>
            </div>
            <div class="stat-trend">
                <i class="bi bi-person-check"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card highlight">
            <div class="stat-icon" style="background:linear-gradient(135deg,#6b21a8,#9333ea);">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $log_hari_ini ?></div>
                <div class="stat-label">Keluar Hari Ini</div>
            </div>
            <div class="stat-trend up">
                <i class="bi bi-arrow-up-right"></i>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Info Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="info-card">
            <div class="info-card-icon" style="background:rgba(22,163,74,.1);color:#16a34a;">
                <i class="bi bi-qr-code"></i>
            </div>
            <div class="info-card-val"><?= $total_qr_aktif ?></div>
            <div class="info-card-label">QR Aktif</div>
            <a href="<?= base_url('admin/qrcode') ?>" class="info-card-link">
                Kelola <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="info-card <?= $kendaraan_tanpa_qr > 0 ? 'warning' : '' ?>">
            <div class="info-card-icon" style="background:rgba(245,158,11,.1);color:#d97706;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="info-card-val"><?= $kendaraan_tanpa_qr ?></div>
            <div class="info-card-label">Kendaraan Tanpa QR</div>
            <a href="<?= base_url('admin/qrcode') ?>" class="info-card-link">
                Generate <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="info-card">
            <div class="info-card-icon" style="background:rgba(99,102,241,.1);color:#6366f1;">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="info-card-val"><?= $log_bulan_ini ?></div>
            <div class="info-card-label">Scan Bulan Ini</div>
            <a href="<?= base_url('admin/laporan') ?>" class="info-card-link">
                Laporan <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="info-card">
            <div class="info-card-icon" style="background:rgba(59,130,246,.1);color:#3b82f6;">
                <i class="bi bi-award"></i>
            </div>
            <div class="info-card-val"><?= $total_pangkat ?></div>
            <div class="info-card-label">Data Pangkat</div>
            <a href="<?= base_url('admin/pangkat') ?>" class="info-card-link">
                Kelola <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Row 1: Chart Harian + Chart Shift -->
<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="chart-card-header">
                <div>
                    <div class="chart-card-title">
                        <i class="bi bi-bar-chart-line me-2 text-success"></i>
                        Aktivitas Kendaraan Keluar
                    </div>
                    <div class="chart-card-sub">7 hari terakhir</div>
                </div>
                <span class="chart-badge">
                    <span class="legend-dot" style="background:#16a34a;"></span> Scan
                </span>
            </div>
            <div class="chart-body">
                <canvas id="chartHarian" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="chart-card-header">
                <div>
                    <div class="chart-card-title">
                        <i class="bi bi-pie-chart me-2 text-success"></i>
                        Distribusi Shift
                    </div>
                    <div class="chart-card-sub">Bulan ini</div>
                </div>
            </div>
            <div class="chart-body d-flex flex-column align-items-center justify-content-center">
                <canvas id="chartShift" width="180" height="180" style="max-width:180px;max-height:180px;"></canvas>
                <div class="shift-legend mt-3">
                    <div class="shift-legend-item">
                        <span class="legend-dot" style="background:#f59e0b;"></span>
                        <span>Pagi</span>
                        <strong id="countPagi">0</strong>
                    </div>
                    <div class="shift-legend-item">
                        <span class="legend-dot" style="background:#3b82f6;"></span>
                        <span>Siang</span>
                        <strong id="countSiang">0</strong>
                    </div>
                    <div class="shift-legend-item">
                        <span class="legend-dot" style="background:#6366f1;"></span>
                        <span>Malam</span>
                        <strong id="countMalam">0</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Chart Satker + Chart Pangkat -->
<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="chart-card h-100">
            <div class="chart-card-header">
                <div>
                    <div class="chart-card-title">
                        <i class="bi bi-building me-2 text-success"></i>
                        Top Satuan Kerja
                    </div>
                    <div class="chart-card-sub">Paling aktif bulan ini</div>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="chartSatker" height="160"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="chart-card h-100">
            <div class="chart-card-header">
                <div>
                    <div class="chart-card-title">
                        <i class="bi bi-people me-2 text-success"></i>
                        Personel Keluar Bulan Ini
                    </div>
                    <div class="chart-card-sub">Berdasarkan kelompok pangkat</div>
                </div>
            </div>
            <div class="chart-body d-flex flex-column align-items-center justify-content-center">
                <canvas id="chartPangkat" width="180" height="180" style="max-width:180px;max-height:180px;"></canvas>
                <div id="pangkatLegend" class="shift-legend mt-3 w-100"></div>
            </div>
        </div>
    </div>
</div>

<!-- Row: Tren Input Data -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="chart-card">
            <div class="chart-card-header flex-wrap gap-2">
                <div>
                    <div class="chart-card-title">
                        <i class="bi bi-graph-up-arrow me-2 text-primary"></i>
                        Tren Aktivitas Input Data Harian
                    </div>
                    <div class="chart-card-sub">Jumlah personel dan kendaraan baru yang diinput dalam 7 hari terakhir
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="chart-badge">
                        <span class="legend-dot" style="background:#3b82f6;"></span> Personel Baru
                    </span>
                    <span class="chart-badge">
                        <span class="legend-dot" style="background:#16a34a;"></span> Kendaraan Baru
                    </span>
                </div>
            </div>
            <div class="chart-body">
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="chartTrenInput"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section: Data per Satuan Kerja & Monitoring Input -->
<div class="row g-3 mt-1 mb-4">

    <!-- 1. Card Mini Monitoring Progres Input Satker -->
    <?php if (isset($monitoring_input)): ?>
    <div class="col-12">
        <div class="progress-summary-card">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="progress-radial-icon">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-6">Progres Kesiapan Input Data Satker</div>
                        <div class="text-muted small">
                            <strong><?= $monitoring_input['satker_sudah_input'] ?></strong> dari
                            <strong><?= $monitoring_input['total_satker'] ?></strong> satuan kerja telah menginputkan
                            data.
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="stat-pill success">
                        <span class="dot"></span>
                        <span>Sudah Input: <strong><?= $monitoring_input['satker_sudah_input'] ?> Satker</strong></span>
                    </div>
                    <div class="stat-pill danger">
                        <span class="dot"></span>
                        <span>Belum Ada Data: <strong><?= $monitoring_input['satker_belum_input'] ?>
                                Satker</strong></span>
                    </div>
                    <div class="badge-pct">
                        <?= $monitoring_input['persentase_aktif'] ?>%
                    </div>
                </div>
            </div>
            <!-- Progress Bar -->
            <div class="progress mt-3" style="height: 7px; background:#e2e8f0; border-radius: 99px;">
                <div class="progress-bar bg-success" role="progressbar"
                    style="width: <?= $monitoring_input['persentase_aktif'] ?>%; border-radius: 99px;"></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- 2. Tabel Rekap Satker + Filter + Tombol Export -->
    <div class="col-12">
        <div class="chart-card">
            <div class="chart-card-header flex-wrap gap-2">
                <div>
                    <div class="chart-card-title">
                        <i class="bi bi-building me-2 text-success"></i>
                        Rekap Data per Satuan Kerja
                    </div>
                    <div class="chart-card-sub">
                        Visualisasi Top Satker (Jumlah personel & kendaraan terdaftar)
                    </div>
                </div>

                <!-- Filter Status & Search & Export -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Tab Filter: Semua / Sudah / Belum -->
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-filter-satker active" data-filter="all">Semua</button>
                        <button type="button" class="btn btn-filter-satker" data-filter="filled">Sudah Input
                            (🟢)</button>
                        <button type="button" class="btn btn-filter-satker" data-filter="empty">Belum Input
                            (🔴)</button>
                    </div>

                    <!-- Search Input -->
                    <div class="search-wrap">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="searchSatker" class="search-input" placeholder="Cari satker...">
                    </div>

                    <!-- Tombol Export Excel -->
                    <a href="<?= base_url('admin/dashboard/export-satker') ?>" class="btn-export-excel"
                        title="Download Format Excel untuk Laporan Pimpinan">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                    </a>
                </div>
            </div>

            <?php if (empty($data_satker)): ?>
            <div class="empty-state">
                <i class="bi bi-building"></i>
                <div>Belum ada data satuan kerja</div>
            </div>
            <?php else: ?>

            <!-- Chart Horizontal Bar (Top Satker) -->
            <div class="chart-body pb-3">
                <div id="chartSatkerContainer" style="position: relative; min-height: 320px; width: 100%;">
                    <canvas id="chartSatkerPersonel"></canvas>
                </div>
            </div>

            <!-- Tabel Detail -->
            <div class="table-responsive">
                <table class="table-custom" id="satkerTable">
                    <thead>
                        <tr>
                            <th width="40" class="text-center">#</th>
                            <th>Satuan Kerja</th>
                            <th>Kode</th>
                            <th class="text-center" width="110">Personel</th>
                            <th class="text-center" width="110">Kendaraan</th>
                            <th class="text-center" width="110">QR Aktif</th>
                            <th class="text-center" width="140">Coverage QR</th>
                            <th class="text-center" width="120">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_satker as $i => $s): ?>
                        <?php
                                $personel   = (int) $s['total_personel'];
                                $kendaraan  = (int) $s['total_kendaraan'];
                                $qrAktif    = (int) $s['total_qr_aktif'];
                                $hasData    = ($personel > 0 || $kendaraan > 0);
                                
                                $coverage = $kendaraan > 0 ? round(($qrAktif / $kendaraan) * 100) : 0;
                                $coverageClass = $coverage >= 80 ? 'success' : ($coverage >= 50 ? 'warning' : 'danger');
                            ?>
                        <tr data-has-data="<?= $hasData ? '1' : '0' ?>">
                            <td class="text-center text-muted fw-500 row-idx"><?= $i + 1 ?></td>
                            <td>
                                <div class="satker-name"><?= esc($s['nama_singkat']) ?></div>
                                <div class="satker-sub"><?= esc($s['nama_panjang']) ?></div>
                            </td>
                            <td>
                                <span class="code-badge"><?= esc($s['kode_satker']) ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($personel > 0): ?>
                                <span class="badge-count blue"><?= $personel ?></span>
                                <?php else: ?>
                                <span class="text-muted-zero">0</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($kendaraan > 0): ?>
                                <span class="badge-count green"><?= $kendaraan ?></span>
                                <?php else: ?>
                                <span class="text-muted-zero">0</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($qrAktif > 0): ?>
                                <span class="badge-count purple"><?= $qrAktif ?></span>
                                <?php else: ?>
                                <span class="text-muted-zero">0</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($kendaraan > 0): ?>
                                <div class="coverage-wrap">
                                    <div class="coverage-bar">
                                        <div class="coverage-fill <?= $coverageClass ?>"
                                            style="width:<?= $coverage ?>%"></div>
                                    </div>
                                    <span class="coverage-pct <?= $coverageClass ?>"><?= $coverage ?>%</span>
                                </div>
                                <?php else: ?>
                                <span class="text-muted-zero" style="font-size: .75rem;">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($hasData): ?>
                                <span class="badge-status aktif" style="font-size: .68rem;">
                                    <span class="badge-dot"></span> Terisi
                                </span>
                                <?php else: ?>
                                <span class="badge-status nonaktif"
                                    style="font-size: .68rem; background: #fee2e2; color:#b91c1c;">
                                    <span class="badge-dot" style="background:#ef4444;"></span> Kosong
                                </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <!-- Footer total -->
                    <tfoot>
                        <tr class="tfoot-row">
                            <td colspan="3" class="fw-600">Total Keseluruhan</td>
                            <td class="text-center fw-600 text-primary">
                                <?= array_sum(array_column($data_satker, 'total_personel')) ?>
                            </td>
                            <td class="text-center fw-600 text-success">
                                <?= array_sum(array_column($data_satker, 'total_kendaraan')) ?>
                            </td>
                            <td class="text-center fw-600 text-purple" style="color: #8b5cf6;">
                                <?= array_sum(array_column($data_satker, 'total_qr_aktif')) ?>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* ── Greeting ── */
.dash-greeting {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .75rem;
}

.dash-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.dash-sub {
    font-size: .78rem;
    color: #9ca3af;
    margin: 2px 0 0;
}

.dash-date-badge {
    display: inline-flex;
    align-items: center;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .4rem .85rem;
    font-size: .78rem;
    font-weight: 500;
    color: #374151;
}

/* ── Stat Cards ── */
.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 1.1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: .85rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
    transition: transform .2s, box-shadow .2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, .07);
}

.stat-card.highlight {
    border-color: #e9d5ff;
    background: linear-gradient(135deg, #faf5ff, #fff);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.stat-info {
    flex: 1;
}

.stat-value {
    font-size: 1.65rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

.stat-label {
    font-size: .72rem;
    color: #6b7280;
    margin-top: 3px;
}

.stat-trend {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
    color: #9ca3af;
    flex-shrink: 0;
}

.stat-trend.up {
    background: #dcfce7;
    color: #16a34a;
}

/* ── Chart Cards ── */
.chart-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
    overflow: hidden;
}

.chart-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
}

.chart-card-title {
    font-size: .88rem;
    font-weight: 600;
    color: #111827;
}

.chart-card-sub {
    font-size: .72rem;
    color: #9ca3af;
    margin-top: 2px;
}

.chart-body {
    padding: 1rem 1.25rem;
}

.chart-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .72rem;
    color: #6b7280;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    padding: .2rem .6rem;
}

.legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    display: inline-block;
}

/* ── Shift Legend ── */
.shift-legend {
    display: flex;
    flex-direction: column;
    gap: 6px;
    width: 100%;
    padding: 0 .5rem;
}

.shift-legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .78rem;
    color: #6b7280;
}

.shift-legend-item strong {
    margin-left: auto;
    color: #111827;
    font-size: .85rem;
}

/* ── Info Cards ── */
.info-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
    padding: 1rem 1.1rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: transform .2s, box-shadow .2s;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, .07);
}

.info-card.warning {
    border-color: #fde68a;
    background: #fffbeb;
}

.info-card-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    margin-bottom: .65rem;
    flex-shrink: 0;
}

.info-card-val {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

.info-card-label {
    font-size: .72rem;
    color: #6b7280;
    margin-top: 3px;
    flex: 1;
}

.info-card-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: .72rem;
    font-weight: 500;
    color: #16a34a;
    text-decoration: none;
    margin-top: .65rem;
}

.info-card-link:hover {
    color: #15803d;
}

/* ── Progress Summary Card ── */
.progress-summary-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    padding: 1.2rem 1.4rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
}

.progress-radial-icon {
    width: 44px;
    height: 44px;
    background: #f0fdf4;
    color: #16a34a;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}

.stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .75rem;
    padding: 4px 10px;
    border-radius: 99px;
    font-weight: 500;
}

.stat-pill.success {
    background: #dcfce7;
    color: #166534;
}

.stat-pill.success .dot {
    width: 6px;
    height: 6px;
    background: #16a34a;
    border-radius: 50%;
}

.stat-pill.danger {
    background: #fee2e2;
    color: #991b1b;
}

.stat-pill.danger .dot {
    width: 6px;
    height: 6px;
    background: #ef4444;
    border-radius: 50%;
}

.badge-pct {
    background: #16a34a;
    color: #fff;
    font-size: .82rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
}

/* ── Filter Tab Satker ── */
.btn-filter-satker {
    font-size: .75rem;
    padding: .35rem .75rem;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #475569;
    font-weight: 500;
    transition: all .15s;
}

.btn-filter-satker:hover {
    background: #f8fafc;
    color: #0f172a;
}

.btn-filter-satker.active {
    background: #16a34a;
    color: #fff;
    border-color: #16a34a;
}

/* ── Button Export Excel ── */
.btn-export-excel {
    display: inline-flex;
    align-items: center;
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #86efac;
    padding: .35rem .85rem;
    border-radius: 8px;
    font-size: .78rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
    white-space: nowrap;
}

.btn-export-excel:hover {
    background: #16a34a;
    color: #fff;
    transform: translateY(-1px);
}

/* ── Satker Table Custom ── */
.table-custom {
    width: 100%;
    border-collapse: collapse;
    font-size: .83rem;
}

.table-custom thead th {
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    padding: .75rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    border-top: 1px solid #f1f5f9;
}

.table-custom tbody td {
    padding: .75rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
}

.table-custom tbody tr:hover {
    background-color: #f8fafc;
}

.table-custom tfoot .tfoot-row td {
    padding: .85rem 1rem;
    background: #f8fafc;
    border-top: 2px solid #cbd5e1;
    font-size: .85rem;
}

.satker-name {
    font-weight: 600;
    color: #0f172a;
    font-size: .85rem;
}

.satker-sub {
    font-size: .72rem;
    color: #64748b;
    margin-top: 1px;
}

.code-badge {
    display: inline-block;
    padding: 2px 8px;
    font-size: .72rem;
    font-weight: 600;
    font-family: monospace;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
}

.text-muted-zero {
    color: #cbd5e1;
    font-weight: 500;
}

.fw-600 {
    font-weight: 600;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 6px;
    font-weight: 600;
}

.badge-status.aktif {
    background: #dcfce7;
    color: #166534;
}

.badge-status.aktif .badge-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #16a34a;
}

.badge-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    padding: 3px 10px;
    border-radius: 99px;
    font-size: .75rem;
    font-weight: 600;
}

.badge-count.blue {
    background: #dbeafe;
    color: #1e40af;
}

.badge-count.green {
    background: #dcfce7;
    color: #166534;
}

.badge-count.purple {
    background: #ede9fe;
    color: #5b21b6;
}

.coverage-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
}

.coverage-bar {
    width: 80px;
    height: 6px;
    background: #f3f4f6;
    border-radius: 99px;
    overflow: hidden;
    flex-shrink: 0;
}

.coverage-fill {
    height: 100%;
    border-radius: 99px;
    transition: width .5s ease;
}

.coverage-fill.success {
    background: #16a34a;
}

.coverage-fill.warning {
    background: #f59e0b;
}

.coverage-fill.danger {
    background: #ef4444;
}

.coverage-pct {
    font-size: .72rem;
    font-weight: 600;
    white-space: nowrap;
}

.coverage-pct.success {
    color: #16a34a;
}

.coverage-pct.warning {
    color: #d97706;
}

.coverage-pct.danger {
    color: #ef4444;
}

.search-wrap {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: .85rem;
}

.search-input {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .4rem .75rem .4rem 2rem;
    font-size: .82rem;
    width: 200px;
    outline: none;
}

.search-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1);
}

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
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('vendor/chartjs/chart.umd.min.js') ?>"></script>
<script>
// ── Data dari PHP ───────────────────────────────────────
const dataHarian = <?= json_encode($chart_harian) ?>;
const dataShift = <?= json_encode($chart_shift) ?>;
const dataSatker = <?= json_encode($chart_satker) ?>;
const dataPangkat = <?= json_encode($chart_pangkat) ?>;

// ── Global defaults ─────────────────────────────────────
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#9ca3af';

/* ════════════════════════════════════════════════
   CHART 1 — Bar: Scan 7 Hari Terakhir
════════════════════════════════════════════════ */
new Chart(document.getElementById('chartHarian'), {
    type: 'bar',
    data: {
        labels: dataHarian.map(d => d.label),
        datasets: [{
            label: 'Kendaraan Keluar',
            data: dataHarian.map(d => d.total),
            backgroundColor: dataHarian.map((d, i) =>
                i === dataHarian.length - 1 ?
                'rgba(22,163,74,.85)' :
                'rgba(22,163,74,.25)'
            ),
            borderColor: dataHarian.map((d, i) =>
                i === dataHarian.length - 1 ?
                '#16a34a' :
                'rgba(22,163,74,.4)'
            ),
            borderWidth: 1.5,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.parsed.y} kendaraan keluar`
                }
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                border: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,.05)'
                },
                border: {
                    display: false,
                    dash: [4, 4]
                },
                ticks: {
                    font: {
                        size: 11
                    },
                    stepSize: 1,
                    precision: 0
                }
            }
        }
    }
});

/* ════════════════════════════════════════════════
   CHART 2 — Doughnut: Distribusi Shift
════════════════════════════════════════════════ */
const shiftPagi = dataShift.pagi || 0;
const shiftSiang = dataShift.siang || 0;
const shiftMalam = dataShift.malam || 0;
const totalShift = shiftPagi + shiftSiang + shiftMalam;

document.getElementById('countPagi').textContent = shiftPagi;
document.getElementById('countSiang').textContent = shiftSiang;
document.getElementById('countMalam').textContent = shiftMalam;

new Chart(document.getElementById('chartShift'), {
    type: 'doughnut',
    data: {
        labels: ['Pagi', 'Siang', 'Malam'],
        datasets: [{
            data: totalShift > 0 ? [shiftPagi, shiftSiang, shiftMalam] : [1, 1, 1],
            backgroundColor: ['#f59e0b', '#3b82f6', '#6366f1'],
            borderColor: '#fff',
            borderWidth: 3,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: false,
        cutout: '68%',
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                enabled: totalShift > 0,
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.parsed} scan`
                }
            }
        }
    }
});

/* ════════════════════════════════════════════════
   CHART 3 — Horizontal Bar: Top Satker (Scan)
════════════════════════════════════════════════ */
new Chart(document.getElementById('chartSatker'), {
    type: 'bar',
    data: {
        labels: dataSatker.map(d => d.label),
        datasets: [{
            label: 'Scan',
            data: dataSatker.map(d => d.total),
            backgroundColor: [
                'rgba(22,163,74,.75)',
                'rgba(59,130,246,.75)',
                'rgba(245,158,11,.75)',
                'rgba(99,102,241,.75)',
                'rgba(239,68,68,.75)',
            ],
            borderRadius: 5,
            borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.parsed.x} scan`
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,.05)'
                },
                border: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 11
                    },
                    precision: 0
                }
            },
            y: {
                grid: {
                    display: false
                },
                border: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});

/* ════════════════════════════════════════════════
   CHART — Area/Line: Tren Input Data Harian
════════════════════════════════════════════════ */
const dataTrenInput = <?= json_encode($chart_tren_input ?? []) ?>;

const canvasTren = document.getElementById('chartTrenInput');
if (canvasTren && dataTrenInput.length > 0) {
    const ctxTren = canvasTren.getContext('2d');

    // Buat efek gradasi lembut (gradient area fill)
    const gradientPersonel = ctxTren.createLinearGradient(0, 0, 0, 240);
    gradientPersonel.addColorStop(0, 'rgba(59, 130, 246, 0.25)');
    gradientPersonel.addColorStop(1, 'rgba(59, 130, 246, 0.00)');

    const gradientKendaraan = ctxTren.createLinearGradient(0, 0, 0, 240);
    gradientKendaraan.addColorStop(0, 'rgba(22, 163, 74, 0.25)');
    gradientKendaraan.addColorStop(1, 'rgba(22, 163, 74, 0.00)');

    new Chart(canvasTren, {
        type: 'line',
        data: {
            labels: dataTrenInput.map(d => d.label),
            datasets: [{
                    label: 'Personel Diinput',
                    data: dataTrenInput.map(d => d.personel),
                    borderColor: '#3b82f6',
                    backgroundColor: gradientPersonel,
                    fill: true,
                    tension: 0.38,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Kendaraan Diinput',
                    data: dataTrenInput.map(d => d.kendaraan),
                    borderColor: '#16a34a',
                    backgroundColor: gradientKendaraan,
                    fill: true,
                    tension: 0.38,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#16a34a',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false // Sudah dibuatkan badge custom di header
                },
                tooltip: {
                    padding: 12,
                    boxPadding: 6,
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y} data`
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    border: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,.04)',
                        drawBorder: false
                    },
                    ticks: {
                        precision: 0,
                        stepSize: 5,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}

/* ════════════════════════════════════════════════════════════
   CHART 4 — Horizontal Bar: Personel, Kendaraan & QR per Satker
════════════════════════════════════════════════════════════ */
<?php if (!empty($data_satker)): ?>
const rawDataSatker = <?= json_encode($data_satker) ?>;

// Filter satker yang memiliki data dan urutkan dari yang terbesar
const activeSatker = rawDataSatker
    .filter(d => parseInt(d.total_personel) > 0 || parseInt(d.total_kendaraan) > 0)
    .sort((a, b) => {
        const totalA = parseInt(a.total_personel) + parseInt(a.total_kendaraan);
        const totalB = parseInt(b.total_personel) + parseInt(b.total_kendaraan);
        return totalB - totalA;
    });

// Tampilkan Top 8 Satker teratas agar chart lega dan rapi
const topLimit = 10;
const displayData = activeSatker.length > 0 ? activeSatker.slice(0, topLimit) : rawDataSatker.slice(0, 10);

const satkerNames = displayData.map(d => d.nama_singkat);
const satkerPersonel = displayData.map(d => parseInt(d.total_personel));
const satkerKendaraan = displayData.map(d => parseInt(d.total_kendaraan));
const satkerQR = displayData.map(d => parseInt(d.total_qr_aktif));

// Auto height container
const chartContainer = document.getElementById('chartSatkerContainer');
if (chartContainer) {
    chartContainer.style.height = `${displayData.length * 40 + 60}px`; // ~460px untuk 10 bar
}

new Chart(document.getElementById('chartSatkerPersonel'), {
    type: 'bar',
    data: {
        labels: satkerNames,
        datasets: [{
                label: 'Personel',
                data: satkerPersonel,
                backgroundColor: 'rgba(59,130,246,.85)',
                hoverBackgroundColor: '#3b82f6',
                borderRadius: 4,
                barPercentage: 0.85,
                categoryPercentage: 0.75,
            },
            {
                label: 'Kendaraan',
                data: satkerKendaraan,
                backgroundColor: 'rgba(22,163,74,.85)',
                hoverBackgroundColor: '#16a34a',
                borderRadius: 4,
                barPercentage: 0.85,
                categoryPercentage: 0.75,
            },
            {
                label: 'QR Aktif',
                data: satkerQR,
                backgroundColor: 'rgba(139,92,246,.85)',
                hoverBackgroundColor: '#8b5cf6',
                borderRadius: 4,
                barPercentage: 0.85,
                categoryPercentage: 0.75,
            },
        ]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                align: 'end',
                labels: {
                    font: {
                        size: 11,
                        weight: '600'
                    },
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 15,
                }
            },
            tooltip: {
                padding: 10,
                callbacks: {
                    label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.x}`
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,.04)',
                    drawBorder: false,
                },
                ticks: {
                    precision: 0,
                    font: {
                        size: 11
                    }
                }
            },
            y: {
                grid: {
                    display: false,
                    drawBorder: false,
                },
                ticks: {
                    font: {
                        size: 11,
                        weight: '600'
                    },
                    color: '#334155',
                    autoSkip: false
                }
            }
        }
    }
});

// ── Filter Satker (Live Search + Tab Status) ─────────
let currentFilter = 'all';

document.querySelectorAll('.btn-filter-satker').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.btn-filter-satker').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.filter;
        applySatkerFilter();
    });
});

document.getElementById('searchSatker').addEventListener('input', applySatkerFilter);

function applySatkerFilter() {
    const keyword = document.getElementById('searchSatker').value.toLowerCase();

    document.querySelectorAll('#satkerTable tbody tr').forEach(row => {
        const hasData = row.getAttribute('data-has-data');
        const textMatch = row.textContent.toLowerCase().includes(keyword);

        let statusMatch = true;
        if (currentFilter === 'filled') statusMatch = (hasData === '1');
        if (currentFilter === 'empty') statusMatch = (hasData === '0');

        if (textMatch && statusMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
<?php endif; ?>

/* ════════════════════════════════════════════════
   CHART 5 — Doughnut: Personel by Kelompok Pangkat
════════════════════════════════════════════════ */
const pangkatColors = {
    'Perwira': '#3b82f6',
    'Bintara': '#10b981',
    'Tamtama': '#f59e0b',
    'PNS': '#8b5cf6',
    'PPPK': '#ec4899',
};

const pangkatLabels = dataPangkat.map(d => d.label);
const pangkatTotals = dataPangkat.map(d => parseInt(d.total));
const totalPangkat = pangkatTotals.reduce((a, b) => a + b, 0);

// Build legend dinamis
const legendEl = document.getElementById('pangkatLegend');
pangkatLabels.forEach((label, i) => {
    const pct = totalPangkat > 0 ?
        ((pangkatTotals[i] / totalPangkat) * 100).toFixed(1) :
        0;
    const item = document.createElement('div');
    item.className = 'shift-legend-item';
    item.innerHTML = `
        <span class="legend-dot" style="background:${pangkatColors[label] || '#6b7280'};"></span>
        <span>${label}</span>
        <strong>${pangkatTotals[i]} <span style="font-weight:400;color:#9ca3af;font-size:.7rem;">(${pct}%)</span></strong>
    `;
    legendEl.appendChild(item);
});

new Chart(document.getElementById('chartPangkat'), {
    type: 'doughnut',
    data: {
        labels: pangkatLabels,
        datasets: [{
            data: totalPangkat > 0 ? pangkatTotals : [1],
            backgroundColor: totalPangkat > 0 ?
                pangkatLabels.map(l => pangkatColors[l] || '#6b7280') : ['#e5e7eb'],
            borderColor: '#fff',
            borderWidth: 3,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: false,
        cutout: '68%',
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                enabled: totalPangkat > 0,
                callbacks: {
                    label: function(ctx) {
                        const pct = ((ctx.parsed / totalPangkat) * 100).toFixed(1);
                        return ` ${ctx.label}: ${ctx.parsed} personel (${pct}%)`;
                    }
                }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>