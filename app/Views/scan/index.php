<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="scan-wrap">

    <!-- Header Info -->
    <div class="scan-header">
        <div class="scan-header-left">
            <div class="scan-title">
                <i class="bi bi-qr-code-scan me-2"></i>Scan QR Code
            </div>
            <div class="scan-sub">Arahkan scanner ke kartu QR kendaraan</div>
        </div>
        <div class="scan-time-wrap">
            <div class="scan-time-label">Waktu Sekarang</div>
            <div class="scan-time" id="liveClock">--:--:--</div>
            <div class="scan-date" id="liveDate">--</div>
        </div>
        <div class="scan-header-right">
            <a href="<?= base_url('petugas/scan/monitor') ?>" target="_blank" class="btn-fullscreen">
                <i class="bi bi-arrows-fullscreen me-1"></i>Mode Full Screen
            </a>
        </div>
    </div>

    <div class="row g-3">

        <!-- Kolom Kiri: Input Scan -->
        <div class="col-lg-5">
            <div class="scanner-card">
                <div class="scanner-card-header">
                    <i class="bi bi-upc-scan me-2 text-success"></i>Input Scanner
                </div>
                <div class="scanner-body">
                    <div class="scanner-status" id="scannerStatus">
                        <div class="status-dot pulse"></div>
                        <span>Menunggu scan...</span>
                    </div>
                    <div class="input-scan-wrap">
                        <input type="text" id="scanInput" class="input-scan" placeholder="Scan QR Code di sini..."
                            autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                        <div class="input-scan-hint">
                            <i class="bi bi-info-circle me-1"></i>
                            Klik area ini lalu scan kartu QR
                        </div>
                    </div>
                    <button class="btn-scan-manual" onclick="processScan()">
                        <i class="bi bi-search me-1"></i> Proses Manual
                    </button>
                </div>
            </div>

            <!-- Log Mini -->
            <div class="log-mini-card mt-3">
                <div class="log-mini-header">
                    <i class="bi bi-clock-history me-2 text-success"></i>
                    Scan Hari Ini
                    <span class="log-mini-count" id="logCount">0</span>
                </div>
                <div id="logMiniList" class="log-mini-list">
                    <div class="log-mini-empty">Belum ada scan</div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Hasil Scan -->
        <div class="col-lg-7">
            <div class="result-card" id="resultCard">

                <!-- Idle -->
                <div id="stateIdle" class="result-state">
                    <div class="idle-icon"><i class="bi bi-qr-code"></i></div>
                    <div class="idle-title">Siap Scan</div>
                    <div class="idle-sub">Hasil scan akan tampil di sini</div>
                </div>

                <!-- Loading -->
                <div id="stateLoading" class="result-state d-none">
                    <div class="loading-spinner"></div>
                    <div class="idle-title mt-3">Memproses...</div>
                </div>

                <!-- Success -->
                <div id="stateSuccess" class="result-state d-none">

                    <!-- Personel -->
                    <div class="result-personel">
                        <div class="result-foto-wrap">
                            <img id="resFoto" src="" alt="" class="result-foto d-none">
                            <div id="resFotoPlaceholder" class="result-foto-placeholder">?</div>
                        </div>
                        <div class="result-personel-info">
                            <div class="result-nama" id="resNama">—</div>
                            <div class="result-pangkat" id="resPangkat">—</div>
                            <div class="result-nrp">
                                NRP: <span id="resNrp">—</span>
                            </div>
                            <div class="result-meta">
                                <span id="resJabatan"></span>
                                <span id="resSatuan"></span>
                            </div>
                        </div>
                        <div class="result-badge-ok">
                            <i class="bi bi-check-circle-fill"></i>
                            Teridentifikasi
                        </div>

                        <!-- Tambah di dalam #stateSuccess, setelah .result-badge-ok -->
                        <div id="resWaWrap" class="result-wa-wrap d-none">
                            <a id="resWaBtn" href="#" target="_blank" class="btn-wa">
                                <i class="bi bi-whatsapp"></i>
                                Hubungi via WhatsApp
                            </a>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="result-divider">
                        <span>Detail Kendaraan</span>
                    </div>

                    <!-- Kendaraan Detail -->
                    <div id="resKendaraan" class="result-kendaraan-wrap"></div>

                    <!-- Scan Meta -->
                    <div class="result-meta-bar">
                        <div class="meta-item">
                            <i class="bi bi-clock me-1"></i>
                            <span id="resScanTime">—</span>
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-person me-1"></i>
                            Petugas: <span id="resPetugas">—</span>
                        </div>
                    </div>

                </div>

                <!-- Error -->
                <div id="stateError" class="result-state d-none">
                    <div class="error-icon">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div class="error-title">QR Tidak Dikenali</div>
                    <div class="error-sub" id="errorSub">
                        QR Code tidak terdaftar atau sudah tidak aktif
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<style>
.scan-wrap {
    max-width: 100%
}

.scan-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
    gap: .75rem
}

.scan-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827
}

.scan-sub {
    font-size: .78rem;
    color: #9ca3af;
    margin-top: 2px
}

.scan-time-wrap {
    text-align: right
}

.scan-time-label {
    font-size: .65rem;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .06em
}

.scan-time {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    line-height: 1.1;
    font-variant-numeric: tabular-nums
}

.scan-date {
    font-size: .72rem;
    color: #6b7280;
    margin-top: 1px
}

.btn-fullscreen {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    background: #111827;
    color: #fff;
    padding: .55rem 1rem;
    border-radius: 10px;
    text-decoration: none;
    font-size: .8rem;
    font-weight: 500;
    transition: all .2s
}

.btn-fullscreen:hover {
    background: #1f2937;
    color: #fff
}

.result-wa-wrap {
    padding: .6rem 1.25rem;
    background: #f0fdf4;
    border-bottom: 1px solid #bbf7d0;
}

.btn-wa {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #22c55e;
    color: #fff;
    border-radius: 9px;
    padding: .5rem 1.1rem;
    font-size: .82rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(34, 197, 94, .3);
}

.btn-wa:hover {
    background: #16a34a;
    color: #fff;
    transform: translateY(-1px);
}

.btn-wa i {
    font-size: 1rem;
}

/* Scanner Card */
.scanner-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    overflow: hidden
}

.scanner-card-header {
    padding: .85rem 1.1rem;
    border-bottom: 1px solid #f3f4f6;
    font-size: .85rem;
    font-weight: 600;
    color: #111827
}

.scanner-body {
    padding: 1.1rem
}

.scanner-status {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .78rem;
    color: #6b7280;
    margin-bottom: .85rem
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #16a34a;
    flex-shrink: 0
}

.status-dot.pulse {
    animation: pulse 1.5s infinite
}

.status-dot.error-dot {
    background: #ef4444;
    animation: none
}

.status-dot.success-dot {
    background: #16a34a;
    animation: none
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
        transform: scale(1)
    }

    50% {
        opacity: .5;
        transform: scale(1.2)
    }
}

.input-scan-wrap {
    margin-bottom: .75rem
}

.input-scan {
    width: 100%;
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: .85rem 1rem;
    font-size: .9rem;
    color: #111827;
    outline: none;
    transition: all .2s;
    font-family: monospace;
    letter-spacing: .03em
}

.input-scan:focus {
    border-color: #16a34a;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1)
}

.input-scan.scanning {
    border-color: #3b82f6;
    background: #eff6ff
}

.input-scan.success {
    border-color: #16a34a;
    background: #f0fdf4
}

.input-scan.error {
    border-color: #ef4444;
    background: #fef2f2
}

.input-scan-hint {
    font-size: .7rem;
    color: #9ca3af;
    margin-top: 5px;
    text-align: center
}

.btn-scan-manual {
    width: 100%;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 9px;
    padding: .55rem;
    font-size: .82rem;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: all .15s;
    display: flex;
    align-items: center;
    justify-content: center
}

.btn-scan-manual:hover {
    background: #e5e7eb
}

/* Log Mini */
.log-mini-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    overflow: hidden
}

.log-mini-header {
    padding: .75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    font-size: .82rem;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center
}

.log-mini-count {
    margin-left: auto;
    background: #dcfce7;
    color: #166534;
    font-size: .68rem;
    font-weight: 600;
    padding: 1px 8px;
    border-radius: 99px
}

.log-mini-list {
    max-height: 220px;
    overflow-y: auto
}

.log-mini-item {
    display: flex;
    align-items: center;
    gap: .65rem;
    padding: .6rem 1rem;
    border-bottom: 1px solid #f9fafb;
    font-size: .78rem
}

.log-mini-item:last-child {
    border-bottom: none
}

.log-mini-avatar {
    width: 26px;
    height: 26px;
    border-radius: 7px;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: .68rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0
}

.log-mini-nama {
    font-weight: 500;
    color: #111827
}

.log-mini-plat {
    font-size: .7rem;
    color: #9ca3af;
    font-family: monospace
}

.log-mini-time {
    margin-left: auto;
    color: #9ca3af;
    font-size: .72rem;
    white-space: nowrap
}

.log-mini-empty {
    text-align: center;
    padding: 1.5rem;
    color: #9ca3af;
    font-size: .78rem
}

/* Result Card */
.result-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    min-height: 420px;
    display: flex;
    align-items: stretch;
    overflow: hidden
}

.result-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    width: 100%
}

.idle-icon {
    width: 72px;
    height: 72px;
    background: #f3f4f6;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #d1d5db;
    margin-bottom: 1rem
}

.idle-title {
    font-size: .95rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 4px
}

.idle-sub {
    font-size: .78rem;
    color: #9ca3af
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f4f6;
    border-top-color: #16a34a;
    border-radius: 50%;
    animation: spin .7s linear infinite
}

@keyframes spin {
    to {
        transform: rotate(360deg)
    }
}

/* Success */
#stateSuccess {
    align-items: stretch;
    justify-content: flex-start;
    padding: 0
}

.result-personel {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.1rem 1.25rem;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-bottom: 1px solid #bbf7d0;
    flex-wrap: wrap
}

.result-foto-wrap {
    flex-shrink: 0
}

.result-foto {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    object-fit: cover;
    border: 2px solid #bbf7d0
}

.result-foto-placeholder {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: 1.4rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center
}

.result-personel-info {
    flex: 1;
    min-width: 0
}

.result-nama {
    font-size: .95rem;
    font-weight: 700;
    color: #111827
}

.result-pangkat {
    font-size: .78rem;
    color: #6b7280;
    margin: 1px 0
}

.result-nrp {
    font-size: .75rem;
    color: #166534;
    font-weight: 600;
    font-family: monospace
}

.result-meta {
    font-size: .72rem;
    color: #9ca3af;
    margin-top: 2px
}

.result-meta span:not(:empty)::after {
    content: ' · '
}

.result-meta span:last-child::after {
    content: ''
}

.result-badge-ok {
    display: flex;
    align-items: center;
    gap: 5px;
    background: #16a34a;
    color: #fff;
    font-size: .72rem;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 99px;
    flex-shrink: 0;
    align-self: flex-start
}

/* Divider */
.result-divider {
    display: flex;
    align-items: center;
    padding: 0 1.25rem;
    height: 36px;
    background: #f9fafb;
    border-bottom: 1px solid #f3f4f6
}

.result-divider span {
    font-size: .7rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .07em
}

/* Kendaraan Detail */
.result-kendaraan-wrap {
    padding: .75rem 1.25rem;
    flex: 1
}

.kendaraan-detail-card {
    background: #f9fafb;
    border: 1px solid #f3f4f6;
    border-radius: 12px;
    overflow: hidden
}

.kendaraan-detail-header {
    display: flex;
    align-items: center;
    gap: .85rem;
    padding: .75rem 1rem;
    border-bottom: 1px solid #f3f4f6
}

.kendaraan-thumb {
    width: 80px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid #e5e7eb;
    flex-shrink: 0;
    cursor: pointer
}

.kendaraan-thumb:hover {
    opacity: .85
}

.kendaraan-thumb-placeholder {
    width: 80px;
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0
}

.kendaraan-thumb-placeholder.motor {
    background: #ede9fe;
    color: #5b21b6
}

.kendaraan-thumb-placeholder.mobil {
    background: #dbeafe;
    color: #1e40af
}

.kendaraan-plat {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: .04em
}

.kendaraan-sub {
    font-size: .75rem;
    color: #6b7280;
    margin-top: 2px
}

.kendaraan-kode {
    font-size: .7rem;
    color: #9ca3af;
    margin-top: 1px
}

/* STNK row */
.kendaraan-stnk {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border-top: 1px solid #f3f4f6
}

.stnk-cell {
    padding: .5rem 1rem;
    border-right: 1px solid #f3f4f6
}

.stnk-cell:last-child {
    border-right: none
}

.stnk-label {
    font-size: .62rem;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .06em
}

.stnk-val {
    font-size: .78rem;
    font-weight: 500;
    color: #374151;
    font-family: monospace;
    margin-top: 1px
}

/* Foto strip */
.kendaraan-foto-strip {
    display: flex;
    gap: 6px;
    padding: .6rem 1rem;
    border-top: 1px solid #f3f4f6;
    overflow-x: auto;
    scrollbar-width: thin;
    align-items: center
}

.strip-foto {
    width: 60px;
    height: 46px;
    border-radius: 7px;
    object-fit: cover;
    border: 1.5px solid #e5e7eb;
    cursor: pointer;
    flex-shrink: 0;
    transition: all .15s
}

.strip-foto:hover {
    border-color: #16a34a;
    transform: scale(1.05)
}

.strip-foto.stnk {
    border-color: rgba(251, 191, 36, .5);
    object-fit: contain;
    background: rgba(0, 0, 0, .05)
}

.strip-stnk-label {
    font-size: .62rem;
    color: #d97706;
    writing-mode: vertical-lr;
    transform: rotate(180deg);
    font-weight: 600;
    flex-shrink: 0;
    opacity: .7
}

/* Badges */
.badge-jenis-sm {
    font-size: .68rem;
    font-weight: 500;
    padding: 1px 7px;
    border-radius: 99px
}

.badge-jenis-sm.motor {
    background: #ede9fe;
    color: #5b21b6
}

.badge-jenis-sm.mobil {
    background: #dbeafe;
    color: #1e40af
}

.badge-status-sm {
    font-size: .68rem;
    font-weight: 500
}

.badge-status-sm.aktif {
    color: #16a34a
}

.badge-status-sm.dibekukan {
    color: #f97316
}

/* Meta Bar */
.result-meta-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .65rem 1.25rem;
    background: #f9fafb;
    border-top: 1px solid #f3f4f6;
    font-size: .72rem;
    color: #9ca3af;
    flex-wrap: wrap;
    gap: .5rem
}

.meta-item {
    display: flex;
    align-items: center
}

/* Error */
.error-icon {
    width: 72px;
    height: 72px;
    background: #fef2f2;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #ef4444;
    margin-bottom: 1rem
}

.error-title {
    font-size: .95rem;
    font-weight: 600;
    color: #dc2626;
    margin-bottom: 4px
}

.error-sub {
    font-size: .78rem;
    color: #9ca3af;
    text-align: center
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ── Live Clock ──────────────────────────────────────────
function updateClock() {
    const now = new Date();
    document.getElementById('liveClock').textContent =
        now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    document.getElementById('liveDate').textContent =
        now.toLocaleDateString('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
}
updateClock();
setInterval(updateClock, 1000);

// ── Auto Focus ──────────────────────────────────────────
const scanInput = document.getElementById('scanInput');
scanInput.focus();
setInterval(() => {
    if (document.activeElement !== scanInput) scanInput.focus();
}, 3000);
document.addEventListener('click', e => {
    if (!e.target.closest('button, a')) scanInput.focus();
});

// ── Scanner Input ───────────────────────────────────────
let scanBuffer = '';
let scanTimeout = null;

scanInput.addEventListener('input', function() {
    clearTimeout(scanTimeout);
    scanBuffer = this.value;
    this.classList.add('scanning');
    updateStatus('scanning', 'Membaca QR Code...');
    scanTimeout = setTimeout(() => {
        if (scanBuffer.trim().length > 0) processScan();
    }, 300);
});

scanInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        clearTimeout(scanTimeout);
        if (scanBuffer.trim().length > 0) processScan();
    }
});

// ── Process Scan ────────────────────────────────────────
let scanCount = 0;

function processScan() {
    const kode = scanInput.value.trim();
    if (!kode) return;

    showState('loading');
    updateStatus('scanning', 'Memproses...');

    fetch('<?= base_url('petugas/scan/process') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams({
                kode_unik: kode,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
            }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showResultSuccess(data);
                scanInput.classList.remove('scanning');
                scanInput.classList.add('success');
                updateStatus('success', `OK: ${data.kendaraan.plat_nomor}`);
                addLogMini(data.personel, data.kendaraan, data.scan_time);
                scanCount++;
                document.getElementById('logCount').textContent = scanCount;
            } else {
                showResultError(data.message);
                scanInput.classList.remove('scanning');
                scanInput.classList.add('error');
                updateStatus('error', data.message);
            }

            setTimeout(() => {
                scanInput.value = '';
                scanBuffer = '';
                scanInput.className = 'input-scan';
                scanInput.focus();
                updateStatus('idle', 'Menunggu scan...');
            }, 2500);
        })
        .catch(() => {
            showResultError('Gagal menghubungi server.');
            updateStatus('error', 'Koneksi error.');
            setTimeout(() => {
                scanInput.value = '';
                scanBuffer = '';
                scanInput.className = 'input-scan';
                scanInput.focus();
                updateStatus('idle', 'Menunggu scan...');
            }, 2500);
        });
}

// ── Show State ──────────────────────────────────────────
function showState(state) {
    ['stateIdle', 'stateLoading', 'stateSuccess', 'stateError'].forEach(id => {
        document.getElementById(id).classList.add('d-none');
    });
    document.getElementById('state' + state.charAt(0).toUpperCase() + state.slice(1))
        .classList.remove('d-none');
}

// ── Show Result Success ─────────────────────────────────
function showResultSuccess(data) {
    showState('success');

    const p = data.personel;
    const k = data.kendaraan;

    // Foto personel
    if (p.foto) {
        document.getElementById('resFoto').src = p.foto;
        document.getElementById('resFoto').classList.remove('d-none');
        document.getElementById('resFotoPlaceholder').classList.add('d-none');
    } else {
        document.getElementById('resFoto').classList.add('d-none');
        document.getElementById('resFotoPlaceholder').classList.remove('d-none');
        document.getElementById('resFotoPlaceholder').textContent =
            p.nama.charAt(0).toUpperCase();
    }

    // Data personel
    document.getElementById('resNama').textContent = p.nama;
    document.getElementById('resPangkat').textContent = p.pangkat_singkat || '-';
    document.getElementById('resNrp').textContent = p.nrp;
    document.getElementById('resJabatan').textContent = p.jabatan || '';
    document.getElementById('resSatuan').textContent = p.satker_singkat || '';

    // ── Tombol WhatsApp ──────────────────────────────────
    const waWrap = document.getElementById('resWaWrap');
    const waBtn = document.getElementById('resWaBtn');

    if (p.no_hp) {
        const pesan = encodeURIComponent(
            `Halo, ini petugas jaga.\n` +
            `Kendaraan Anda dengan plat *${data.kendaraan.plat_nomor}* ` +
            `baru saja melintas di penjagaan pada ${data.scan_time}.\n` +
            `Mohon konfirmasi. Terima kasih.`
        );
        waBtn.href = `https://wa.me/${p.no_hp}?text=${pesan}`;
        waWrap.classList.remove('d-none');
    } else {
        waWrap.classList.add('d-none');
    }
    // Meta
    document.getElementById('resScanTime').textContent = data.scan_time;
    document.getElementById('resPetugas').textContent = data.petugas;

    // Build kendaraan detail
    buildKendaraanDetail(k);
}

// ── Build Kendaraan Detail ──────────────────────────────
function buildKendaraanDetail(k) {
    const wrap = document.getElementById('resKendaraan');

    // Thumbnail
    const thumbHtml = k.thumbnail_url ?
        `<img src="${escAttr(k.thumbnail_url)}" class="kendaraan-thumb"
                onclick="openLightbox('${escAttr(k.thumbnail_url)}',
                'Foto Kendaraan — ${escAttr(k.plat_nomor)}')" alt="">` :
        `<div class="kendaraan-thumb-placeholder ${k.jenis}">
               <i class="bi ${k.jenis === 'motor' ? 'bi-bicycle' : 'bi-car-front'}"></i>
           </div>`;

    // Detail teks
    const detailParts = [k.merk, k.model, k.warna ? `· ${k.warna}` : '']
        .filter(Boolean).join(' ');

    // STNK
    const stnkHtml = k.nomor_stnk ? `
        <div class="kendaraan-stnk">
            <div class="stnk-cell">
                <div class="stnk-label">No. STNK</div>
                <div class="stnk-val">${escHtml(k.nomor_stnk)}</div>
            </div>
            <div class="stnk-cell">
                <div class="stnk-label">Tahun</div>
                <div class="stnk-val">${k.tahun || '-'}</div>
            </div>
        </div>` : '';

    // Foto strip
    const hasGaleri = k.galeri_urls && k.galeri_urls.length > 0;
    const hasFotoStnk = !!k.foto_stnk_url;
    let stripHtml = '';
    if (hasGaleri || hasFotoStnk) {
        const imgs = hasGaleri ?
            k.galeri_urls.map((url, i) => `
                <img src="${escAttr(url)}" class="strip-foto"
                     onclick="openLightbox('${escAttr(url)}',
                     'Foto ${i+1} — ${escAttr(k.plat_nomor)}')"
                     alt="Foto ${i+1}">`).join('') :
            '';
        const stnkImg = hasFotoStnk ?
            `<span class="strip-stnk-label">STNK</span>
               <img src="${escAttr(k.foto_stnk_url)}" class="strip-foto stnk"
                    onclick="openLightbox('${escAttr(k.foto_stnk_url)}',
                    'STNK — ${escAttr(k.plat_nomor)}')" alt="STNK">` :
            '';
        stripHtml = `<div class="kendaraan-foto-strip">${imgs}${stnkImg}</div>`;
    }

    const statusBadge = k.status === 'aktif' ?
        `<span class="badge-status-sm aktif">● Aktif</span>` :
        `<span class="badge-status-sm dibekukan">⚠ Dibekukan</span>`;

    wrap.innerHTML = `
        <div class="kendaraan-detail-card">
            <div class="kendaraan-detail-header">
                ${thumbHtml}
                <div style="flex:1;min-width:0;">
                    <div class="kendaraan-plat">${escHtml(k.plat_nomor)}</div>
                    <div class="kendaraan-sub">${escHtml(detailParts)}</div>
                    <div class="kendaraan-kode">
                        Kode: ${escHtml(k.kendaraan_kode)}
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">
                    <span class="badge-jenis-sm ${k.jenis}">
                        ${k.jenis.charAt(0).toUpperCase() + k.jenis.slice(1)}
                    </span>
                    ${statusBadge}
                </div>
            </div>
            ${stnkHtml}
            ${stripHtml}
        </div>`;
}

// ── Show Error ──────────────────────────────────────────
function showResultError(message) {
    showState('error');
    document.getElementById('errorSub').textContent = message;
}

// ── Update Status ───────────────────────────────────────
function updateStatus(type, text) {
    const dot = document.querySelector('.status-dot');
    const span = document.querySelector('.scanner-status span');
    dot.className = 'status-dot';
    span.textContent = text;
    if (type === 'scanning') {
        dot.classList.add('pulse');
        dot.style.background = '#3b82f6';
    } else if (type === 'success') {
        dot.classList.add('success-dot');
        dot.style.background = '#16a34a';
    } else if (type === 'error') {
        dot.classList.add('error-dot');
        dot.style.background = '#ef4444';
    } else {
        dot.classList.add('pulse');
        dot.style.background = '#16a34a';
    }
}

// ── Log Mini ────────────────────────────────────────────
function addLogMini(personel, kendaraan, time) {
    const list = document.getElementById('logMiniList');
    const empty = list.querySelector('.log-mini-empty');
    if (empty) empty.remove();

    const item = document.createElement('div');
    item.className = 'log-mini-item';
    item.innerHTML = `
        <div class="log-mini-avatar">
            ${personel.nama.charAt(0).toUpperCase()}
        </div>
        <div>
            <div class="log-mini-nama">${escHtml(personel.nama)}</div>
            <div class="log-mini-plat">${escHtml(kendaraan.plat_nomor)}</div>
        </div>
        <div class="log-mini-time">
            ${(time || '').split(',')[1]?.trim() || time}
        </div>`;

    list.insertBefore(item, list.firstChild);
    const items = list.querySelectorAll('.log-mini-item');
    if (items.length > 10) items[items.length - 1].remove();
}

// ── Lightbox ────────────────────────────────────────────
function openLightbox(url, caption) {
    const existing = document.getElementById('lightboxOverlay');
    if (existing) existing.remove();

    const overlay = document.createElement('div');
    overlay.id = 'lightboxOverlay';
    overlay.style.cssText = `
        position:fixed;inset:0;background:rgba(0,0,0,.9);
        z-index:9999;display:flex;align-items:center;
        justify-content:center;padding:20px;cursor:pointer;`;

    overlay.innerHTML = `
        <button onclick="document.getElementById('lightboxOverlay').remove()"
                style="position:absolute;top:18px;right:18px;background:rgba(255,255,255,.15);
                       border:none;color:#fff;width:38px;height:38px;border-radius:50%;
                       font-size:1rem;cursor:pointer;display:flex;align-items:center;
                       justify-content:center;">
            <i class="bi bi-x"></i>
        </button>
        <img src="${escAttr(url)}" onclick="event.stopPropagation()"
             style="max-width:92vw;max-height:88vh;border-radius:12px;object-fit:contain;" alt="">
        <div style="position:absolute;bottom:20px;left:50%;transform:translateX(-50%);
                    background:rgba(0,0,0,.55);color:#fff;font-size:.75rem;
                    padding:5px 16px;border-radius:99px;white-space:nowrap;">
            ${escHtml(caption)}
        </div>`;

    overlay.addEventListener('click', () => overlay.remove());
    document.body.appendChild(overlay);
    document.addEventListener('keydown', function escKey(e) {
        if (e.key === 'Escape') {
            overlay.remove();
            document.removeEventListener('keydown', escKey);
        }
    });
}

// ── XSS Helpers ─────────────────────────────────────────
function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function escAttr(str) {
    if (!str) return '';
    return String(str).replace(/'/g, '%27').replace(/"/g, '%22');
}
</script>
<?= $this->endSection() ?>