<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu QR — <?= esc($personel['nama']) ?></title>
    <!-- <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Mono:wght@400;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet"> -->

    <link rel="stylesheet" href="<?= base_url('vendor/fonts/bebas-neue/bebas-neue.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/fonts/ibm-plex-mono/ibm-plex-mono.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/fonts/ibm-plex-sans/ibm-plex-sans.css') ?>">

    <style>
    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    :root {
        /* CR80 landscape: 85.6mm × 54mm, scaled 3.5× */
        --card-w: 300px;
        --card-h: 189px;
        --stripe: #16a34a;
        --stripe-dim: #0f6b30;
        --accent: #38bdf8;
    }

    body {
        font-family: 'IBM Plex Sans', sans-serif;
        background: #1a2235;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        padding: 2rem 1rem;
        gap: 1.25rem;
    }

    /* ── Controls ── */
    .controls {
        display: flex;
        gap: .6rem;
        align-items: center;
    }

    .btn-print {
        background: linear-gradient(135deg, #166534, #16a34a);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: .5rem 1rem;
        font-family: 'IBM Plex Sans', sans-serif;
        font-size: .78rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 10px rgba(22, 101, 52, .3);
    }

    .btn-print:hover {
        filter: brightness(1.1);
    }

    .btn-back {
        background: rgba(255, 255, 255, .07);
        color: rgba(255, 255, 255, .65);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 8px;
        padding: .5rem 1rem;
        font-family: 'IBM Plex Sans', sans-serif;
        font-size: .78rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, .13);
        color: #fff;
    }

    .size-hint {
        font-size: .68rem;
        color: rgba(255, 255, 255, .3);
        letter-spacing: .05em;
    }

    /* ══════════════════════════════════
       KARTU — 300 × 189 px (CR80 3.5×)
    ══════════════════════════════════ */
    .ktp-card {
        width: var(--card-w);
        height: var(--card-h);
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        font-family: 'IBM Plex Sans', sans-serif;
        box-shadow:
            0 0 0 1px rgba(255, 255, 255, .07),
            0 8px 32px rgba(0, 0, 0, .55);
    }

    /* latar */
    .card-bg {
        position: absolute;
        inset: 0;
        background: #09141f;
    }

    .card-bg::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: repeating-linear-gradient(-45deg,
                transparent 0, transparent 3px,
                rgba(255, 255, 255, .012) 3px, rgba(255, 255, 255, .012) 4px);
    }

    .card-bg::after {
        content: '';
        position: absolute;
        right: 50px;
        top: -40px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(56, 189, 248, .04) 0%, transparent 65%);
    }

    /* stripe atas */
    .top-stripe {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #0f6b30, #16a34a, #22c55e, #16a34a, #0f6b30);
        z-index: 5;
    }

    /* stripe bawah */
    .bottom-stripe {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(22, 163, 74, .55), #16a34a, rgba(22, 163, 74, .55), transparent);
        z-index: 5;
    }

    /* ── LAYOUT BODY ── */
    .ktp-body {
        position: relative;
        z-index: 2;
        display: flex;
        height: 100%;
        padding: 8px 0 6px 0;
    }

    /* ── KOLOM KIRI: foto + nrp + status ── */
    .col-left {
        width: 64px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 4px 0 4px 8px;
        gap: 4px;
    }

    .photo-wrap {
        position: relative;
        width: 48px;
        height: 60px;
        flex-shrink: 0;
    }

    .profile-photo {
        width: 48px;
        height: 60px;
        border-radius: 5px;
        object-fit: cover;
        object-position: top center;
        border: 1px solid rgba(255, 255, 255, .1);
        display: block;
    }

    .profile-placeholder {
        width: 48px;
        height: 60px;
        border-radius: 5px;
        background: linear-gradient(135deg, #0f2d4d, #1a4d6e);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Bebas Neue', sans-serif;
        font-size: 22px;
        color: rgba(255, 255, 255, .25);
        border: 1px solid rgba(255, 255, 255, .08);
    }

    /* corner accent foto */
    .photo-wrap::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        width: 9px;
        height: 9px;
        border-top: 1.5px solid var(--stripe);
        border-left: 1.5px solid var(--stripe);
        border-radius: 2px 0 0 0;
        z-index: 2;
    }

    .photo-wrap::after {
        content: '';
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 9px;
        height: 9px;
        border-bottom: 1.5px solid var(--stripe);
        border-right: 1.5px solid var(--stripe);
        border-radius: 0 0 2px 0;
        z-index: 2;
    }

    .nrp-box {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 4px;
        font-weight: 700;
        color: rgba(255, 255, 255, .55);
        background: rgba(255, 255, 255, .05);
        border: .5px solid rgba(255, 255, 255, .1);
        border-radius: 2px;
        padding: 2px 3px;
        letter-spacing: .04em;
        text-align: center;
        width: 100%;
    }

    .status-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
        background: rgba(34, 197, 94, .12);
        border: .5px solid rgba(34, 197, 94, .3);
        border-radius: 2px;
        padding: 2px 0;
        width: 100%;
        font-size: 3.5px;
        font-weight: 700;
        color: #4ade80;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .status-dot {
        width: 3px;
        height: 3px;
        border-radius: 50%;
        background: #4ade80;
        animation: activePulse 2s infinite;
    }

    @keyframes activePulse {

        0%,
        100% {
            opacity: 1
        }

        50% {
            opacity: .3
        }
    }

    /* ── KOLOM TENGAH: info personel ── */
    .col-mid {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        padding: 4px 6px 4px 8px;
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 4px;
        padding-bottom: 4px;
        border-bottom: .5px solid rgba(255, 255, 255, .07);
    }

    .brand-shield {
        width: 14px;
        height: 14px;
        background: linear-gradient(135deg, var(--stripe), var(--stripe-dim));
        border-radius: 3px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 7px;
        flex-shrink: 0;
    }

    .brand-title {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 7.5px;
        letter-spacing: .1em;
        color: #fff;
        line-height: 1;
    }

    .brand-subtitle {
        font-size: 3.5px;
        color: rgba(255, 255, 255, .32);
        letter-spacing: .07em;
        text-transform: uppercase;
        margin-top: 1px;
    }

    .profile-nama {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 14px;
        letter-spacing: .04em;
        color: #fff;
        line-height: 1;
        margin-bottom: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-pangkat {
        font-size: 5px;
        color: var(--accent);
        font-weight: 600;
        letter-spacing: .07em;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px 8px;
        flex: 1;
    }

    .meta-label {
        font-size: 3.5px;
        font-weight: 700;
        color: rgba(255, 255, 255, .24);
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 1px;
    }

    .meta-val {
        font-size: 6px;
        font-weight: 600;
        color: rgba(255, 255, 255, .82);
        letter-spacing: .02em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .doc-row {
        margin-top: auto;
        padding-top: 4px;
        border-top: .5px solid rgba(255, 255, 255, .06);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .doc-id {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 3.5px;
        color: rgba(255, 255, 255, .17);
        letter-spacing: .07em;
    }

    .doc-secure {
        font-size: 3px;
        color: rgba(255, 255, 255, .15);
        letter-spacing: .06em;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 2px;
    }

    /* ── KOLOM KANAN: QR panel ── */
    .col-right {
        width: 78px;
        flex-shrink: 0;
        background: rgba(255, 255, 255, .025);
        border-left: .5px solid rgba(255, 255, 255, .06);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 6px 6px 5px;
        gap: 3px;
    }

    .qr-top-label {
        font-size: 3.5px;
        font-weight: 700;
        color: rgba(255, 255, 255, .33);
        text-transform: uppercase;
        letter-spacing: .08em;
        align-self: flex-start;
    }

    .qr-img-wrap {
        position: relative;
        width: 62px;
        height: 62px;
        flex-shrink: 0;
    }

    .qr-image {
        width: 62px;
        height: 62px;
        border-radius: 4px;
        background: #fff;
        padding: 3px;
        border: 1.5px solid rgba(255, 255, 255, .08);
        display: block;
    }

    /* corner dekorasi QR */
    .qr-corner {
        position: absolute;
        width: 7px;
        height: 7px;
    }

    .qr-corner.tl {
        top: -2px;
        left: -2px;
        border-top: 1px solid var(--stripe);
        border-left: 1px solid var(--stripe);
        border-radius: 1.5px 0 0 0;
    }

    .qr-corner.tr {
        top: -2px;
        right: -2px;
        border-top: 1px solid var(--stripe);
        border-right: 1px solid var(--stripe);
        border-radius: 0 1.5px 0 0;
    }

    .qr-corner.bl {
        bottom: -2px;
        left: -2px;
        border-bottom: 1px solid var(--stripe);
        border-left: 1px solid var(--stripe);
        border-radius: 0 0 0 1.5px;
    }

    .qr-corner.br {
        bottom: -2px;
        right: -2px;
        border-bottom: 1px solid var(--stripe);
        border-right: 1px solid var(--stripe);
        border-radius: 0 0 1.5px 0;
    }

    .qr-scan-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 3.5px;
        font-weight: 700;
        color: var(--stripe);
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .kend-label {
        font-size: 3.5px;
        font-weight: 700;
        color: rgba(255, 255, 255, .3);
        text-transform: uppercase;
        letter-spacing: .08em;
        align-self: flex-start;
    }

    .kend-list {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 2px;
        flex: 1;
        overflow: hidden;
    }

    .kend-item {
        display: flex;
        align-items: center;
        gap: 3px;
        background: rgba(255, 255, 255, .04);
        border: .5px solid rgba(255, 255, 255, .07);
        border-radius: 2px;
        padding: 2px 4px;
    }

    .k-dot {
        width: 3px;
        height: 3px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .k-dot.motor {
        background: #a78bfa;
    }

    .k-dot.mobil {
        background: #60a5fa;
    }

    .k-plat {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 4.5px;
        font-weight: 700;
        color: rgba(255, 255, 255, .82);
        letter-spacing: .03em;
        flex: 1;
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .k-merk {
        font-size: 3.5px;
        color: rgba(255, 255, 255, .25);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 30px;
    }

    .qr-code-id {
        margin-top: auto;
        width: 100%;
        padding-top: 3px;
        border-top: .5px solid rgba(255, 255, 255, .06);
        font-family: 'IBM Plex Mono', monospace;
        font-size: 3px;
        color: rgba(255, 255, 255, .13);
        letter-spacing: .04em;
        text-align: center;
    }

    /* ══════════════════
       PRINT STYLES
    ══════════════════ */
    @media print {
        body {
            background: #fff;
            padding: 0;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .controls,
        .size-hint {
            display: none !important;
        }

        .ktp-card {
            box-shadow: none;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }
    }
    </style>
</head>

<body>

    <!-- Controls -->
    <div class="controls">
        <button class="btn-print" onclick="window.print()">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 6 2 18 2 18 9" />
                <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" />
                <rect x="6" y="14" width="12" height="8" />
            </svg>
            Cetak Kartu
        </button>
        <a href="<?= base_url('admin/qrcode') ?>" class="btn-back">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6" />
            </svg>
            Kembali
        </a>
        <span class="size-hint">300 × 189 px &nbsp;·&nbsp; CR80 KTP (3.5× scale)</span>
    </div>

    <!-- Kartu -->
    <div class="ktp-card">

        <div class="card-bg"></div>
        <div class="top-stripe"></div>
        <div class="bottom-stripe"></div>

        <div class="ktp-body">

            <!-- Kolom Kiri: Foto -->
            <div class="col-left">
                <div class="photo-wrap">
                    <?php if (!empty($personel['foto'])): ?>
                    <img src="<?= base_url('uploads/personel/' . $personel['foto']) ?>" class="profile-photo"
                        alt="Foto <?= esc($personel['nama']) ?>">
                    <?php else: ?>
                    <div class="profile-placeholder">
                        <?= strtoupper(substr($personel['nama'], 0, 1)) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="nrp-box"><?= esc($personel['nrp']) ?></div>

                <div class="status-badge">
                    <span class="status-dot"></span>
                    AKTIF
                </div>
            </div>

            <!-- Kolom Tengah: Info Personel -->
            <div class="col-mid">

                <div class="card-header">
                    <div class="brand-shield">🛡</div>
                    <div>
                        <div class="brand-title">VEHICLE GATE SYSTEM</div>
                        <div class="brand-subtitle">Kartu Akses Kendaraan Personel</div>
                    </div>
                </div>

                <div class="profile-nama"><?= esc($personel['nama']) ?></div>
                <div class="profile-pangkat"><?= esc($personel['pangkat_singkat']) ?></div>

                <div class="info-grid">
                    <div>
                        <div class="meta-label">Jabatan</div>
                        <div class="meta-val"><?= esc($personel['jabatan']) ?: '—' ?></div>
                    </div>
                    <div>
                        <div class="meta-label">Satuan</div>
                        <div class="meta-val"><?= esc($personel['satker_singkat']) ?: '—' ?></div>
                    </div>
                    <div>
                        <div class="meta-label">Kendaraan</div>
                        <div class="meta-val"><?= count($personel['kendaraan']) ?> Unit</div>
                    </div>
                    <div>
                        <div class="meta-label">Terbit</div>
                        <div class="meta-val"><?= date('d M Y', strtotime($qr['generated_at'])) ?></div>
                    </div>
                </div>

                <div class="doc-row">
                    <div class="doc-id">DOC-<?= strtoupper(substr(md5($personel['nrp']), 0, 8)) ?></div>
                    <div class="doc-secure">
                        <svg width="6" height="6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0110 0v4" />
                        </svg>
                        INTRANET ONLY
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: QR -->
            <div class="col-right">
                <div class="qr-top-label">Scan Access</div>

                <div class="qr-img-wrap">
                    <img src="<?= $qrImage ?>" class="qr-image" alt="QR Code">
                    <div class="qr-corner tl"></div>
                    <div class="qr-corner tr"></div>
                    <div class="qr-corner bl"></div>
                    <div class="qr-corner br"></div>
                </div>

                <div class="qr-scan-label">▶ SCAN TO VERIFY</div>

                <div class="kend-label">Kendaraan</div>
                <div class="kend-list">
                    <?php if (empty($personel['kendaraan'])): ?>
                    <div style="font-size:4px; color:rgba(255,255,255,.28); font-style:italic;">
                        Belum ada kendaraan
                    </div>
                    <?php else: ?>
                    <?php foreach ($personel['kendaraan'] as $k): ?>
                    <div class="kend-item">
                        <span class="k-dot <?= esc($k['jenis']) ?>"></span>
                        <span class="k-plat"><?= esc($k['plat_nomor']) ?></span>
                        <span class="k-merk"><?= esc($k['merk']) ?></span>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="qr-code-id">
                    QR-<?= strtoupper(substr(md5($personel['nrp']), 0, 10)) ?>
                </div>
            </div>

        </div>
    </div>

</body>

</html>