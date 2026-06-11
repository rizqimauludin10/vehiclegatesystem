<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="scan-wrap">

    <!-- Header Info -->

    <div class="scan-header">
        <div class="scan-header-left">
            <div class="scan-title">
                <i class="bi bi-qr-code-scan me-2"></i>Scan QR Code
            </div>
            <div class="scan-sub">
                Arahkan scanner ke kartu QR personel
            </div>
        </div>
        <div class="scan-time-wrap">
            <div class="scan-time-label">Waktu Sekarang</div>
            <div class="scan-time" id="liveClock">--:--:--</div>
            <div class="scan-date" id="liveDate">--</div>
        </div>
        <div class="scan-header-right">
            <a href="<?= base_url('petugas/scan/monitor') ?>" target="_blank" class="btn-fullscreen">
                <i class="bi bi-arrows-fullscreen me-1"></i>
                Mode Full Screen
            </a>
        </div>
    </div>

    <div class="row g-3">

        <!-- Kolom Kiri: Input Scan -->
        <div class="col-lg-5">
            <!-- Scanner Input Card -->
            <div class="scanner-card">
                <div class="scanner-card-header">
                    <i class="bi bi-upc-scan me-2 text-success"></i>
                    Input Scanner
                </div>
                <div class="scanner-body">
                    <!-- Status Indicator -->
                    <div class="scanner-status" id="scannerStatus">
                        <div class="status-dot pulse"></div>
                        <span>Menunggu scan...</span>
                    </div>

                    <!-- Input field — autofocus, hardware scanner langsung input ke sini -->
                    <div class="input-scan-wrap">
                        <input type="text" id="scanInput" class="input-scan" placeholder="Scan QR Code di sini..."
                            autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                        <div class="input-scan-hint">
                            <i class="bi bi-info-circle me-1"></i>
                            Klik area ini lalu scan kartu QR
                        </div>
                    </div>

                    <!-- Manual Submit (backup jika perlu) -->
                    <button class="btn-scan-manual" id="btnScanManual" onclick="processScan()">
                        <i class="bi bi-search me-1"></i> Proses Manual
                    </button>
                </div>
            </div>

            <!-- Log Mini Hari Ini -->
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

                <!-- State: Idle -->
                <div id="stateIdle" class="result-state">
                    <div class="idle-icon">
                        <i class="bi bi-qr-code"></i>
                    </div>
                    <div class="idle-title">Siap Scan</div>
                    <div class="idle-sub">
                        Hasil scan akan tampil di sini
                    </div>
                </div>

                <!-- State: Loading -->
                <div id="stateLoading" class="result-state d-none">
                    <div class="loading-spinner"></div>
                    <div class="idle-title mt-3">Memproses...</div>
                </div>

                <!-- State: Success -->
                <div id="stateSuccess" class="result-state d-none">

                    <!-- Personel Info -->
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
                    </div>

                    <!-- Divider -->
                    <div class="result-divider">
                        <span>Kendaraan Terdaftar</span>
                    </div>

                    <!-- Kendaraan List -->
                    <div id="resKendaraanList" class="result-kendaraan-list"></div>

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

                <!-- State: Not Found / Error -->
                <div id="stateError" class="result-state d-none">
                    <div class="error-icon">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div class="error-title" id="errorTitle">QR Tidak Dikenali</div>
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
/* ── Layout ── */
.scan-wrap {
    max-width: 100%;
}

.scan-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
    gap: .75rem;
}

.scan-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
}

.scan-sub {
    font-size: .78rem;
    color: #9ca3af;
    margin-top: 2px;
}

.scan-time-wrap {
    text-align: right;
}

.scan-time-label {
    font-size: .65rem;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.scan-time {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    line-height: 1.1;
    font-variant-numeric: tabular-nums;
}

.scan-date {
    font-size: .72rem;
    color: #6b7280;
    margin-top: 1px;
}

/* ── Scanner Card ── */
.scanner-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.scanner-card-header {
    padding: .85rem 1.1rem;
    border-bottom: 1px solid #f3f4f6;
    font-size: .85rem;
    font-weight: 600;
    color: #111827;
}

.scanner-body {
    padding: 1.1rem;
}

/* Status */
.scanner-status {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .78rem;
    color: #6b7280;
    margin-bottom: .85rem;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #16a34a;
    flex-shrink: 0;
}

.status-dot.pulse {
    animation: pulse 1.5s infinite;
}

.status-dot.error-dot {
    background: #ef4444;
    animation: none;
}

.status-dot.success-dot {
    background: #16a34a;
    animation: none;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }

    50% {
        opacity: .5;
        transform: scale(1.2);
    }
}

/* Input Scan */
.input-scan-wrap {
    margin-bottom: .75rem;
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
    letter-spacing: .03em;
}

.input-scan:focus {
    border-color: #16a34a;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1);
}

.input-scan.scanning {
    border-color: #3b82f6;
    background: #eff6ff;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
}

.input-scan.success {
    border-color: #16a34a;
    background: #f0fdf4;
}

.input-scan.error {
    border-color: #ef4444;
    background: #fef2f2;
}

.input-scan-hint {
    font-size: .7rem;
    color: #9ca3af;
    margin-top: 5px;
    text-align: center;
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
    justify-content: center;
}

.btn-scan-manual:hover {
    background: #e5e7eb;
}

/* ── Log Mini ── */
.log-mini-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.log-mini-header {
    padding: .75rem 1rem;
    border-bottom: 1px solid #f3f4f6;
    font-size: .82rem;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
}

.log-mini-count {
    margin-left: auto;
    background: #dcfce7;
    color: #166534;
    font-size: .68rem;
    font-weight: 600;
    padding: 1px 8px;
    border-radius: 99px;
}

.log-mini-list {
    max-height: 220px;
    overflow-y: auto;
}

.log-mini-item {
    display: flex;
    align-items: center;
    gap: .65rem;
    padding: .6rem 1rem;
    border-bottom: 1px solid #f9fafb;
    font-size: .78rem;
}

.log-mini-item:last-child {
    border-bottom: none;
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
    flex-shrink: 0;
}

.log-mini-nama {
    font-weight: 500;
    color: #111827;
}

.log-mini-time {
    margin-left: auto;
    color: #9ca3af;
    font-size: .72rem;
    white-space: nowrap;
}

.log-mini-empty {
    text-align: center;
    padding: 1.5rem;
    color: #9ca3af;
    font-size: .78rem;
}

/* ── Result Card ── */
.result-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    min-height: 420px;
    display: flex;
    align-items: stretch;
    overflow: hidden;
}

.result-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    width: 100%;
}

/* Idle */
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
    margin-bottom: 1rem;
}

.idle-title {
    font-size: .95rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 4px;
}

.idle-sub {
    font-size: .78rem;
    color: #9ca3af;
}

/* Loading */
.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f4f6;
    border-top-color: #16a34a;
    border-radius: 50%;
    animation: spin .7s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Success State */
#stateSuccess {
    align-items: stretch;
    justify-content: flex-start;
    padding: 0;
}

.result-personel {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.1rem 1.25rem;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-bottom: 1px solid #bbf7d0;
    flex-wrap: wrap;
}

.result-foto-wrap {
    flex-shrink: 0;
}

.result-foto {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    object-fit: cover;
    border: 2px solid #bbf7d0;
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
    justify-content: center;
}

.result-personel-info {
    flex: 1;
    min-width: 0;
}

.result-nama {
    font-size: .95rem;
    font-weight: 700;
    color: #111827;
}

.result-pangkat {
    font-size: .78rem;
    color: #6b7280;
    margin: 1px 0;
}

.result-nrp {
    font-size: .75rem;
    color: #166534;
    font-weight: 600;
    font-family: monospace;
}

.result-meta {
    font-size: .72rem;
    color: #9ca3af;
    margin-top: 2px;
}

.result-meta span:not(:empty)::after {
    content: ' · ';
}

.result-meta span:last-child::after {
    content: '';
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
    align-self: flex-start;
}

/* Divider */
.result-divider {
    display: flex;
    align-items: center;
    padding: 0 1.25rem;
    margin: 0;
    height: 36px;
    background: #f9fafb;
    border-bottom: 1px solid #f3f4f6;
}

.result-divider span {
    font-size: .7rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .07em;
}

/* Kendaraan List */
.result-kendaraan-list {
    padding: .75rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: .5rem;
    flex: 1;
}

.kendaraan-result-item {
    display: flex;
    align-items: center;
    gap: .85rem;
    background: #f9fafb;
    border: 1px solid #f3f4f6;
    border-radius: 10px;
    padding: .65rem .85rem;
    transition: background .15s;
}

/* ── Kendaraan Result Item (Updated) ── */
.kendaraan-result-item {
    background: #f9fafb;
    border: 1px solid #f3f4f6;
    border-radius: 12px;
    overflow: hidden;
    transition: border-color .15s;
}

.kendaraan-result-item:hover {
    border-color: #e5e7eb;
}

/* Header kendaraan */
.kendaraan-result-header {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .65rem .85rem;
}

.kendaraan-result-foto {
    width: 54px;
    height: 42px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid #e5e7eb;
    flex-shrink: 0;
    cursor: pointer;
    transition: opacity .15s;
}

.kendaraan-result-foto:hover {
    opacity: .85;
}

.kendaraan-result-placeholder {
    width: 54px;
    height: 42px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.kendaraan-result-placeholder.motor {
    background: #ede9fe;
    color: #5b21b6;
}

.kendaraan-result-placeholder.mobil {
    background: #dbeafe;
    color: #1e40af;
}

.kendaraan-result-info {
    flex: 1;
    min-width: 0;
}

.kendaraan-result-plat {
    font-size: .88rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: .04em;
}

.kendaraan-result-detail {
    font-size: .72rem;
    color: #6b7280;
    margin-top: 1px;
}

.kendaraan-result-right {
    text-align: right;
    flex-shrink: 0;
}

.badge-jenis {
    display: inline-block;
    font-size: .65rem;
    font-weight: 600;
    padding: 2px 8px;
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

.badge-kendaraan-status {
    display: block;
    font-size: .62rem;
    margin-top: 3px;
}

.badge-kendaraan-status.aktif {
    color: #16a34a;
}

.badge-kendaraan-status.nonaktif {
    color: #ef4444;
}

/* STNK Info Row */
.kendaraan-stnk-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    border-top: 1px solid #f3f4f6;
}

.stnk-field {
    padding: .4rem .85rem;
    border-right: 1px solid #f3f4f6;
}

.stnk-field:nth-child(even) {
    border-right: none;
}

.stnk-field:nth-child(n+3) {
    border-top: 1px solid #f3f4f6;
}

.stnk-label {
    font-size: .6rem;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.stnk-val {
    font-size: .75rem;
    font-weight: 500;
    color: #374151;
    font-family: monospace;
    letter-spacing: .03em;
    margin-top: 1px;
}

/* Foto Galeri Strip */
.kendaraan-foto-strip {
    display: flex;
    gap: 5px;
    padding: .5rem .85rem;
    border-top: 1px solid #f3f4f6;
    overflow-x: auto;
    scrollbar-width: thin;
}

.kendaraan-foto-strip::-webkit-scrollbar {
    height: 3px;
}

.kendaraan-foto-strip::-webkit-scrollbar-track {
    background: #f3f4f6;
}

.kendaraan-foto-strip::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 99px;
}

.strip-foto {
    width: 52px;
    height: 40px;
    border-radius: 6px;
    object-fit: cover;
    border: 1.5px solid #e5e7eb;
    cursor: pointer;
    flex-shrink: 0;
    transition: all .15s;
}

.strip-foto:hover {
    border-color: #16a34a;
    transform: scale(1.05);
}

.strip-foto-stnk {
    border-color: #fbbf24;
    position: relative;
}

.strip-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 40px;
    border-radius: 6px;
    background: #fef9c3;
    border: 1.5px solid #fbbf24;
    flex-shrink: 0;
    cursor: pointer;
}

.strip-label span {
    font-size: .52rem;
    font-weight: 600;
    color: #854d0e;
    text-transform: uppercase;
    letter-spacing: .04em;
    line-height: 1.2;
    text-align: center;
}

/* Lightbox */
.lightbox-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .88);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    cursor: pointer;
}

.lightbox-img {
    max-width: 92vw;
    max-height: 88vh;
    border-radius: 12px;
    object-fit: contain;
    box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
    cursor: default;
}

.lightbox-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(255, 255, 255, .15);
    border: none;
    color: #fff;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s;
}

.lightbox-close:hover {
    background: rgba(255, 255, 255, .25);
}

.lightbox-caption {
    position: absolute;
    bottom: 1.25rem;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, .5);
    color: #fff;
    font-size: .75rem;
    padding: .35rem .9rem;
    border-radius: 99px;
    white-space: nowrap;
}

.no-kendaraan-result {
    text-align: center;
    padding: 1.5rem;
    color: #9ca3af;
    font-size: .82rem;
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
    gap: .5rem;
}

.meta-item {
    display: flex;
    align-items: center;
}

/* Error State */
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
    margin-bottom: 1rem;
}

.error-title {
    font-size: .95rem;
    font-weight: 600;
    color: #dc2626;
    margin-bottom: 4px;
}

.error-sub {
    font-size: .78rem;
    color: #9ca3af;
    text-align: center;
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
    transition: all .2s;
}

.btn-fullscreen:hover {
    background: #1f2937;
    color: #fff;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ── Live Clock ──────────────────────────────────────────
function updateClock() {
    const now = new Date();
    const time = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const date = now.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
    document.getElementById('liveClock').textContent = time;
    document.getElementById('liveDate').textContent = date;
}
updateClock();
setInterval(updateClock, 1000);

// ── Auto Focus & Refocus Input ──────────────────────────
const scanInput = document.getElementById('scanInput');

// Focus saat halaman load
scanInput.focus();

// Refocus setiap 3 detik agar scanner selalu bisa input
setInterval(() => {
    if (document.activeElement !== scanInput) {
        scanInput.focus();
    }
}, 3000);

// Klik di mana saja → fokus ke input
document.addEventListener('click', (e) => {
    if (!e.target.closest('button') && !e.target.closest('a')) {
        scanInput.focus();
    }
});

// ── Deteksi Input dari Hardware Scanner ────────────────
// Hardware scanner kirim karakter cepat lalu Enter
let scanBuffer = '';
let scanTimeout = null;

scanInput.addEventListener('input', function() {
    clearTimeout(scanTimeout);
    scanBuffer = this.value;

    // Tandai sedang scanning
    this.classList.add('scanning');
    updateStatus('scanning', 'Membaca QR Code...');

    // Auto proses setelah 300ms tidak ada input baru
    // (hardware scanner biasanya selesai dalam < 100ms)
    scanTimeout = setTimeout(() => {
        if (scanBuffer.trim().length > 0) {
            processScan();
        }
    }, 300);
});

// Enter key juga trigger proses
scanInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        clearTimeout(scanTimeout);
        if (scanBuffer.trim().length > 0) {
            processScan();
        }
    }
});

// ── Proses Scan ke Server ───────────────────────────────
let scanCount = 0;

function processScan() {
    const kode = scanInput.value.trim();
    if (!kode) return;

    // Tampilkan loading
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
                updateStatus('success', `Teridentifikasi: ${data.personel.nama}`);
                addLogMini(data.personel, data.scan_time);
                scanCount++;
                document.getElementById('logCount').textContent = scanCount;
            } else {
                showResultError(data.message);
                scanInput.classList.remove('scanning');
                scanInput.classList.add('error');
                updateStatus('error', data.message);
            }

            // Reset input & kelas setelah 2.5 detik
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

// ── Tampilkan State ─────────────────────────────────────
function showState(state) {
    ['stateIdle', 'stateLoading', 'stateSuccess', 'stateError'].forEach(id => {
        document.getElementById(id).classList.add('d-none');
    });
    document.getElementById('state' + state.charAt(0).toUpperCase() + state.slice(1))
        .classList.remove('d-none');
}

// ── Tampilkan Hasil Sukses (Updated) ───────────────────
function showResultSuccess(data) {
    showState('success');

    const p = data.personel;

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

    // Identitas personel
    document.getElementById('resNama').textContent = p.nama;
    document.getElementById('resPangkat').textContent = p.pangkat_singkat;
    document.getElementById('resNrp').textContent = p.nrp;
    document.getElementById('resJabatan').textContent = p.jabatan || '';
    document.getElementById('resSatuan').textContent = p.satker_singkat || '';

    // Meta scan
    document.getElementById('resScanTime').textContent = data.scan_time;
    document.getElementById('resPetugas').textContent = data.petugas;

    // ── Build Kendaraan List ──────────────────────────
    const list = document.getElementById('resKendaraanList');

    if (!data.kendaraan || data.kendaraan.length === 0) {
        list.innerHTML = `
            <div class="no-kendaraan-result">
                <i class="bi bi-car-front me-2"></i>Belum ada kendaraan terdaftar
            </div>`;
        return;
    }

    list.innerHTML = data.kendaraan.map((k, idx) => {

        // ── Thumbnail ──
        const thumbHtml = k.thumbnail_url ?
            `<img src="${k.thumbnail_url}" class="kendaraan-result-foto"
                    alt="" onclick="openLightbox('${k.thumbnail_url}', 'Foto Kendaraan ${k.plat_nomor}')">` :
            `<div class="kendaraan-result-placeholder ${k.jenis}">
                   <i class="bi ${k.jenis === 'motor' ? 'bi-bicycle' : 'bi-car-front'}"></i>
               </div>`;

        // ── Field STNK ──
        const stnkFields = [{
                label: 'No. Rangka',
                val: k.nomor_rangka || '-'
            },
            {
                label: 'No. Mesin',
                val: k.nomor_mesin || '-'
            },
            {
                label: 'No. STNK',
                val: k.nomor_stnk || '-'
            },
            {
                label: 'No. BPKB',
                val: k.nomor_bpkb || '-'
            },
        ];

        const hasStnkData = k.nomor_rangka || k.nomor_mesin ||
            k.nomor_stnk || k.nomor_bpkb;

        const stnkRowHtml = hasStnkData ? `
            <div class="kendaraan-stnk-row">
                ${stnkFields.map(f => `
                    <div class="stnk-field">
                        <div class="stnk-label">${f.label}</div>
                        <div class="stnk-val">${f.val}</div>
                    </div>`).join('')}
            </div>` : '';

        // ── Foto Strip (galeri + STNK) ──
        const hasGaleri = k.galeri_urls && k.galeri_urls.length > 0;
        const hasFotoStnk = k.foto_stnk_url;

        let stripHtml = '';
        if (hasGaleri || hasFotoStnk) {
            const galeriItems = hasGaleri ?
                k.galeri_urls.map((url, i) => `
                    <img src="${url}" class="strip-foto"
                         alt="Foto ${i + 1}"
                         onclick="openLightbox('${url}', 'Foto ${i + 1} — ${k.plat_nomor}')"
                         title="Foto kendaraan ${i + 1}">`).join('') :
                '';

            const stnkItem = hasFotoStnk ? `
                <div class="strip-label"
                     onclick="openLightbox('${k.foto_stnk_url}', 'STNK — ${k.plat_nomor}')"
                     title="Lihat Foto STNK">
                    <img src="${k.foto_stnk_url}"
                         style="width:100%;height:100%;object-fit:cover;border-radius:4px;"
                         alt="STNK">
                </div>` : '';

            stripHtml = `
                <div class="kendaraan-foto-strip">
                    ${galeriItems}
                    ${stnkItem}
                </div>`;
        }

        return `
            <div class="kendaraan-result-item">

                <!-- Header: Thumbnail + Info + Badge -->
                <div class="kendaraan-result-header">
                    ${thumbHtml}
                    <div class="kendaraan-result-info">
                        <div class="kendaraan-result-plat">${k.plat_nomor}</div>
                        <div class="kendaraan-result-detail">
                            ${[k.merk, k.model, k.tahun ? `(${k.tahun})` : ''].filter(Boolean).join(' ')}
                            ${k.warna ? `· ${k.warna}` : ''}
                        </div>
                        <div style="font-size:.68rem;color:#9ca3af;margin-top:2px;">
                            Kode: ${k.kendaraan_kode}
                        </div>
                    </div>
                    <div class="kendaraan-result-right">
                        <span class="badge-jenis ${k.jenis}">
                            ${k.jenis.charAt(0).toUpperCase() + k.jenis.slice(1)}
                        </span>
                        <span class="badge-kendaraan-status ${k.status}">
                            ${k.status === 'aktif' ? '● Aktif' : '⚠ Nonaktif'}
                        </span>
                    </div>
                </div>

                <!-- STNK Data -->
                ${stnkRowHtml}

                <!-- Foto Strip -->
                ${stripHtml}

            </div>`;

    }).join('');
}

// ── Lightbox ────────────────────────────────────────────
function openLightbox(url, caption) {
    // Hapus lightbox lama jika ada
    const existing = document.getElementById('lightboxOverlay');
    if (existing) existing.remove();

    const overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';
    overlay.id = 'lightboxOverlay';

    overlay.innerHTML = `
        <button class="lightbox-close" onclick="closeLightbox()">
            <i class="bi bi-x"></i>
        </button>
        <img src="${url}" class="lightbox-img" alt="${caption}"
             onclick="event.stopPropagation()">
        <div class="lightbox-caption">${caption}</div>
    `;

    // Klik overlay untuk tutup
    overlay.addEventListener('click', closeLightbox);

    document.body.appendChild(overlay);

    // Keyboard ESC untuk tutup
    document.addEventListener('keydown', handleLightboxKey);
}

function closeLightbox() {
    const overlay = document.getElementById('lightboxOverlay');
    if (overlay) overlay.remove();
    document.removeEventListener('keydown', handleLightboxKey);

    // Refocus ke input scan setelah lightbox ditutup
    document.getElementById('scanInput').focus();
}

function handleLightboxKey(e) {
    if (e.key === 'Escape') closeLightbox();
}

// ── Tampilkan Hasil Error ───────────────────────────────
function showResultError(message) {
    showState('error');
    document.getElementById('errorSub').textContent = message;
}

// ── Update Status Indicator ─────────────────────────────
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

// ── Tambah Item ke Log Mini ─────────────────────────────
function addLogMini(personel, time) {
    const list = document.getElementById('logMiniList');
    const empty = list.querySelector('.log-mini-empty');
    if (empty) empty.remove();

    const item = document.createElement('div');
    item.className = 'log-mini-item';
    item.innerHTML = `
            <div class="log-mini-avatar">${personel.nama.charAt(0).toUpperCase()}</div>
            <div>
                <div class="log-mini-nama">${personel.nama}</div>
                <div style="font-size:.7rem; color:#9ca3af;">${personel.pangkat_singkat}</div>
            </div>
            <div class="log-mini-time">${time.split(',')[1]?.trim() || time}</div>
        `;

    // Tambah di atas
    list.insertBefore(item, list.firstChild);

    // Max 10 item di log mini
    const items = list.querySelectorAll('.log-mini-item');
    if (items.length > 10) items[items.length - 1].remove();
}
</script>
<?= $this->endSection() ?>