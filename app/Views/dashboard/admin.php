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

<!-- Row 3: Info Cards -->
<div class="row g-3">
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
            data: totalShift > 0 ?
                [shiftPagi, shiftSiang, shiftMalam] :
                [1, 1, 1],
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
   CHART 3 — Horizontal Bar: Top Satker
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
   CHART 4 — Doughnut: Personel by Kelompok Pangkat
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
                pangkatLabels.map(l => pangkatColors[l] || '#6b7280') :
                ['#e5e7eb'],
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