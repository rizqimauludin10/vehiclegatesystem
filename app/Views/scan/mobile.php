<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- Theme color (warna status bar browser) -->
    <meta name="theme-color" content="#38bdf8">

    <!-- iOS Safari PWA support -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="VGS Scan">
    <link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <!-- Splash screen iOS (opsional tapi bagus) -->
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Mobile Scanner — VGS</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet"> -->
    <!-- <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap"
    rel="stylesheet"> -->
    <link rel="stylesheet" href="<?= base_url('vendor/bootstrap-icons/font/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/fonts/inter/inter.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/fonts/jetbrains-mono/jetbrains-mono.css') ?>">

    <style>
    /* ════════════════════════════════════════
    RESET & VARIABLES
    ════════════════════════════════════════ */
    :root {
        --bg: #0a0f1e;
        --bg-card: #111827;
        --bg-card2: #1a2235;
        --border: rgba(255, 255, 255, .08);
        --border-md: rgba(255, 255, 255, .13);
        --text: #f1f5f9;
        --text-muted: #64748b;
        --text-dim: #94a3b8;
        --accent: #38bdf8;
        --success: #22c55e;
        --success-bg: rgba(34, 197, 94, .12);
        --success-bdr: rgba(34, 197, 94, .3);
        --error: #ef4444;
        --error-bg: rgba(239, 68, 68, .12);
        --error-bdr: rgba(239, 68, 68, .3);
        --gold: #fbbf24;
        --radius: 16px;
        --radius-sm: 10px;
        --safe-top: env(safe-area-inset-top, 0px);
        --safe-bottom: env(safe-area-inset-bottom, 0px);
    }

    *,
    *::before,
    *::after {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        -webkit-tap-highlight-color: transparent;
    }

    html {
        height: 100%;
    }

    body {
        background: var(--bg);
        color: var(--text);
        font-family: 'Inter', system-ui, sans-serif;
        min-height: 100%;
        overflow-x: hidden;
        padding-top: var(--safe-top);
        padding-bottom: calc(var(--safe-bottom) + 80px);
    }

    /* ════════════════════════════════════════
       HEADER
    ════════════════════════════════════════ */
    .app-header {
        position: sticky;
        top: 0;
        z-index: 100;
        background: rgba(10, 15, 30, .92);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-bottom: 1px solid var(--border);
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .header-brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .brand-icon {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #38bdf8, #0ea5e9);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        box-shadow: 0 0 14px rgba(56, 189, 248, .3);
    }

    .brand-name {
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .02em;
        line-height: 1.1;
    }

    .brand-sub {
        font-size: 10px;
        color: var(--text-muted);
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .header-petugas {
        display: flex;
        align-items: center;
        gap: 7px;
        background: rgba(255, 255, 255, .05);
        border: 1px solid var(--border-md);
        border-radius: 99px;
        padding: 5px 12px 5px 7px;
        font-size: 12px;
        color: var(--text-dim);
        max-width: 160px;
    }

    .petugas-avatar {
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, #166534, #16a34a);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        flex-shrink: 0;
        color: #fff;
    }

    .petugas-name {
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ════════════════════════════════════════
       MAIN CONTENT
    ════════════════════════════════════════ */
    .content {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        max-width: 520px;
        margin: 0 auto;
    }

    /* ════════════════════════════════════════
       STATUS BAR
    ════════════════════════════════════════ */
    .status-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius-sm);
        padding: 12px 14px;
        transition: all .3s;
    }

    .status-bar.success {
        background: var(--success-bg);
        border-color: var(--success-bdr);
    }

    .status-bar.error {
        background: var(--error-bg);
        border-color: var(--error-bdr);
    }

    .status-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: var(--text-muted);
        flex-shrink: 0;
        transition: background .3s;
    }

    .status-dot.idle {
        background: var(--accent);
        animation: pulse 1.8s infinite;
    }

    .status-dot.success {
        background: var(--success);
        animation: none;
    }

    .status-dot.error {
        background: var(--error);
        animation: none;
    }

    .status-dot.loading {
        background: var(--gold);
        animation: pulse .8s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
            transform: scale(1)
        }

        50% {
            opacity: .4;
            transform: scale(1.4)
        }
    }

    .status-text {
        font-size: 13px;
        font-weight: 500;
        flex: 1;
    }

    .status-count {
        background: rgba(56, 189, 248, .15);
        color: var(--accent);
        font-size: 11px;
        font-weight: 600;
        padding: 2px 9px;
        border-radius: 99px;
        border: 1px solid rgba(56, 189, 248, .2);
        white-space: nowrap;
    }

    /* ════════════════════════════════════════
       SCANNER SECTION
    ════════════════════════════════════════ */
    .scanner-section {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .scanner-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
    }

    .scanner-header-title {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Viewport kamera */
    .camera-viewport {
        position: relative;
        width: 100%;
        aspect-ratio: 1;
        background: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    /* Override html5-qrcode default styles */
    #reader {
        width: 100% !important;
        border: none !important;
    }

    #reader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }

    #reader img {
        display: none !important;
    }

    #reader__scan_region {
        position: relative !important;
    }

    #reader__scan_region>img {
        display: none !important;
    }

    #reader__dashboard {
        display: none !important;
    }

    /* QR Frame overlay — di atas video */
    .qr-frame {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        z-index: 10;
    }

    .qr-frame-box {
        width: clamp(160px, 55%, 240px);
        height: clamp(160px, 55%, 240px);
        position: relative;
    }

    /* Corner brackets */
    .qr-frame-box::before,
    .qr-frame-box::after,
    .qr-frame-inner::before,
    .qr-frame-inner::after {
        content: '';
        position: absolute;
        width: 24px;
        height: 24px;
        border-color: var(--accent);
        border-style: solid;
    }

    .qr-frame-box::before {
        top: 0;
        left: 0;
        border-width: 3px 0 0 3px;
        border-radius: 4px 0 0 0;
    }

    .qr-frame-box::after {
        top: 0;
        right: 0;
        border-width: 3px 3px 0 0;
        border-radius: 0 4px 0 0;
    }

    .qr-frame-inner::before {
        bottom: 0;
        left: 0;
        border-width: 0 0 3px 3px;
        border-radius: 0 0 0 4px;
    }

    .qr-frame-inner::after {
        bottom: 0;
        right: 0;
        border-width: 0 3px 3px 0;
        border-radius: 0 0 4px 0;
    }

    /* Scan line animasi */
    .scan-line {
        position: absolute;
        left: 4px;
        right: 4px;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--accent), transparent);
        animation: scanLine 2s ease-in-out infinite;
        box-shadow: 0 0 8px var(--accent);
    }

    @keyframes scanLine {
        0% {
            top: 4px;
            opacity: 1
        }

        50% {
            top: calc(100% - 6px);
            opacity: 1
        }

        100% {
            top: 4px;
            opacity: 1
        }
    }

    /* Camera idle placeholder */
    .camera-idle {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: #000;
        z-index: 5;
    }

    .camera-idle i {
        font-size: 48px;
        color: rgba(255, 255, 255, .15);
    }

    .camera-idle p {
        font-size: 13px;
        color: var(--text-muted);
        text-align: center;
        padding: 0 20px;
        line-height: 1.5;
    }

    /* Scanner controls */
    .scanner-controls {
        display: flex;
        gap: 8px;
        padding: 12px 16px;
        border-top: 1px solid var(--border);
    }

    .btn-start {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, #16a34a, #22c55e);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
        box-shadow: 0 4px 14px rgba(22, 163, 74, .3);
        letter-spacing: .01em;
    }

    .btn-start:active {
        transform: scale(.97);
    }

    .btn-start:disabled {
        background: rgba(255, 255, 255, .08);
        color: var(--text-muted);
        box-shadow: none;
        cursor: not-allowed;
    }

    .btn-stop {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: rgba(239, 68, 68, .12);
        color: var(--error);
        border: 1px solid rgba(239, 68, 68, .25);
        border-radius: 12px;
        padding: 14px;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
    }

    .btn-stop:active {
        transform: scale(.97);
    }

    .btn-icon-only {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .06);
        border: 1px solid var(--border-md);
        border-radius: 12px;
        color: var(--text-dim);
        cursor: pointer;
        font-size: 18px;
        flex-shrink: 0;
        transition: all .15s;
    }

    .btn-icon-only:active {
        transform: scale(.95);
        background: rgba(255, 255, 255, .1);
    }

    /* ════════════════════════════════════════
       RESULT CARD — Success
    ════════════════════════════════════════ */
    .result-card {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        overflow: hidden;
        display: none;
        animation: slideUp .35s cubic-bezier(.22, .61, .36, 1);
    }

    .result-card.show {
        display: block;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(16px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    /* Personel header */
    .result-personel {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 16px;
        background: var(--success-bg);
        border-bottom: 1px solid var(--success-bdr);
    }

    .personel-foto {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid var(--success-bdr);
        flex-shrink: 0;
    }

    .personel-foto-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        background: linear-gradient(135deg, #166534, #16a34a);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }

    .personel-info {
        flex: 1;
        min-width: 0;
    }

    .personel-nama {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .personel-pangkat {
        font-size: 14px;
        color: var(--accent);
        font-weight: 600;
        margin-top: 2px;
    }

    .personel-nrp {
        font-family: 'JetBrains Mono', monospace;
        font-size: 18px;
        font-weight: 600;
        color: var(--text-muted);
        margin-top: 3px;
    }

    .personel-meta {
        font-family: 'JetBrains Mono', monospace;
        font-size: 18px;
        color: var(--text-muted);
        margin-top: 2px;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .badge-ok {
        display: flex;
        align-items: center;
        gap: 4px;
        background: var(--success);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 99px;
        white-space: nowrap;
        align-self: flex-start;
        letter-spacing: .04em;
    }

    /* Kendaraan section */
    .kendaraan-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        background: rgba(255, 255, 255, .02);
        border-bottom: 1px solid var(--border);
    }

    .kendaraan-section-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--text-muted);
    }

    .kendaraan-count {
        background: rgba(56, 189, 248, .12);
        color: var(--accent);
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 99px;
        border: 1px solid rgba(56, 189, 248, .2);
    }

    /* Kendaraan item */
    .kendaraan-item {
        border-bottom: 1px solid var(--border);
    }

    .kendaraan-item:last-child {
        border-bottom: none;
    }

    .kendaraan-item-main {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
    }

    /* Thumbnail kendaraan */
    .k-thumb {
        width: 72px;
        height: 54px;
        border-radius: 10px;
        object-fit: cover;
        border: 1.5px solid var(--border-md);
        flex-shrink: 0;
        cursor: pointer;
        transition: opacity .15s;
    }

    .k-thumb:active {
        opacity: .75;
    }

    .k-thumb-placeholder {
        width: 72px;
        height: 54px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .k-thumb-placeholder.motor {
        background: rgba(139, 92, 246, .15);
        color: #a78bfa;
    }

    .k-thumb-placeholder.mobil {
        background: rgba(59, 130, 246, .15);
        color: #60a5fa;
    }

    .k-info {
        flex: 1;
        min-width: 0;
    }

    .k-plat {
        font-family: 'JetBrains Mono', monospace;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: .05em;
        color: var(--text);
    }

    .k-detail {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .k-badges {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
        flex-shrink: 0;
    }

    .badge-jenis {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 2px 8px;
        border-radius: 5px;
    }

    .badge-jenis.motor {
        background: rgba(139, 92, 246, .2);
        color: #c4b5fd;
        border: 1px solid rgba(139, 92, 246, .25);
    }

    .badge-jenis.mobil {
        background: rgba(59, 130, 246, .2);
        color: #93c5fd;
        border: 1px solid rgba(59, 130, 246, .25);
    }

    .badge-status-k {
        font-size: 10px;
        font-weight: 600;
    }

    .badge-status-k.aktif {
        color: var(--success);
    }

    .badge-status-k.dibekukan {
        color: #fb923c;
    }

    /* STNK row */
    .k-stnk {
        display: flex;
        border-top: 1px solid var(--border);
        font-size: 11px;
    }

    .k-stnk-cell {
        flex: 1;
        padding: 7px 16px;
        border-right: 1px solid var(--border);
    }

    .k-stnk-cell:last-child {
        border-right: none;
    }

    .k-stnk-label {
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .06em;
        font-size: 10px;
        margin-bottom: 2px;
    }

    .k-stnk-val {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 600;
        color: var(--text);
        font-size: 11px;
    }

    /* Foto strip */
    .k-foto-strip {
        display: flex;
        gap: 6px;
        padding: 8px 16px;
        border-top: 1px solid var(--border);
        overflow-x: auto;
        scrollbar-width: none;
        align-items: center;
    }

    .k-foto-strip::-webkit-scrollbar {
        display: none;
    }

    .strip-img {
        width: 62px;
        height: 46px;
        border-radius: 7px;
        object-fit: cover;
        border: 1.5px solid var(--border-md);
        flex-shrink: 0;
        cursor: pointer;
        transition: opacity .15s;
    }

    .strip-img:active {
        opacity: .75;
    }

    .strip-img.stnk {
        border-color: rgba(251, 191, 36, .4);
        object-fit: contain;
        background: rgba(0, 0, 0, .3);
    }

    .strip-stnk-label {
        font-size: 9px;
        font-weight: 700;
        color: var(--gold);
        text-transform: uppercase;
        letter-spacing: .06em;
        flex-shrink: 0;
        writing-mode: vertical-lr;
        transform: rotate(180deg);
        opacity: .7;
    }

    /* ════════════════════════════════════════
       ERROR CARD
    ════════════════════════════════════════ */
    .error-card {
        background: var(--error-bg);
        border: 1px solid var(--error-bdr);
        border-radius: var(--radius);
        padding: 20px 16px;
        text-align: center;
        display: none;
        animation: shakeIn .4s ease;
    }

    .error-card.show {
        display: block;
    }

    @keyframes shakeIn {

        0%,
        100% {
            transform: translateX(0)
        }

        20% {
            transform: translateX(-8px)
        }

        40% {
            transform: translateX(8px)
        }

        60% {
            transform: translateX(-5px)
        }

        80% {
            transform: translateX(5px)
        }
    }

    .error-icon {
        font-size: 36px;
        color: var(--error);
        margin-bottom: 8px;
    }

    .error-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--error);
        margin-bottom: 4px;
    }

    .error-msg {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    /* ════════════════════════════════════════
       SCAN LAGI BUTTON
    ════════════════════════════════════════ */
    .btn-scan-lagi {
        display: none;
        width: 100%;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: var(--bg-card2);
        color: var(--text);
        border: 1px solid var(--border-md);
        border-radius: var(--radius-sm);
        padding: 14px;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
    }

    .btn-scan-lagi.show {
        display: flex;
    }

    .btn-scan-lagi:active {
        transform: scale(.98);
    }

    /* ════════════════════════════════════════
   DEVELOPER MODE — Red Theme
════════════════════════════════════════ */
    :root {
        --dev-red: #f43f5e;
        --dev-red-bg: rgba(244, 63, 94, .1);
        --dev-red-bdr: rgba(244, 63, 94, .3);
    }

    .result-card.dev-mode {
        border-color: var(--dev-red-bdr);
        box-shadow: 0 0 24px rgba(244, 63, 94, .15);
    }

    .result-card.dev-mode .result-personel {
        background: var(--dev-red-bg);
        border-color: var(--dev-red-bdr);
    }

    .result-card.dev-mode .result-personel-foto {
        border-color: var(--dev-red-bdr) !important;
    }

    .result-card.dev-mode .result-personel-foto-overlay {
        background: linear-gradient(to top,
                rgba(120, 10, 30, .92) 0%,
                rgba(120, 10, 30, .3) 40%,
                transparent 65%);
    }

    .result-card.dev-mode .personel-pangkat {
        color: var(--dev-red);
    }

    .result-card.dev-mode .kendaraan-section-header {
        background: var(--dev-red-bg);
        border-color: var(--dev-red-bdr);
    }

    .result-card.dev-mode .kendaraan-count {
        background: rgba(244, 63, 94, .15);
        color: var(--dev-red);
        border-color: var(--dev-red-bdr);
    }

    .badge-developer {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: linear-gradient(135deg, #be123c, #f43f5e);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 99px;
        letter-spacing: .05em;
        white-space: nowrap;
        align-self: flex-start;
        box-shadow: 0 2px 12px rgba(244, 63, 94, .4);
    }

    .dev-signature {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 16px;
        background: var(--dev-red-bg);
        border-top: 1px solid var(--dev-red-bdr);
        font-size: 11px;
        color: var(--dev-red);
        font-weight: 600;
        letter-spacing: .04em;
    }

    .dev-signature i {
        font-size: 13px;
        opacity: .8;
    }

    .status-bar.dev-mode {
        background: var(--dev-red-bg);
        border-color: var(--dev-red-bdr);
    }

    .status-dot.dev {
        background: var(--dev-red);
        animation: pulse 1.8s infinite;
    }

    /* ════════════════════════════════════════
       RECENT LOG MINI
    ════════════════════════════════════════ */
    .log-section {
        background: var(--bg-card);
        border: 1px solid var(--border-md);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .log-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
    }

    .log-header-title {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .log-count-badge {
        background: var(--success-bg);
        color: var(--success);
        font-size: 11px;
        font-weight: 600;
        padding: 2px 9px;
        border-radius: 99px;
        border: 1px solid var(--success-bdr);
    }

    .log-list {
        max-height: 220px;
        overflow-y: auto;
    }

    .log-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        border-bottom: 1px solid var(--border);
        transition: background .15s;
    }

    .log-item:last-child {
        border-bottom: none;
    }

    .log-item:active {
        background: rgba(255, 255, 255, .03);
    }

    .log-avatar {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: linear-gradient(135deg, #166534, #16a34a);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .log-info {
        flex: 1;
        min-width: 0;
    }

    .log-nama {
        font-size: 13px;
        font-weight: 500;
        color: var(--text);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .log-pangkat {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 1px;
    }

    .log-time {
        font-size: 11px;
        color: var(--text-muted);
        font-family: 'JetBrains Mono', monospace;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .log-empty {
        text-align: center;
        padding: 24px 16px;
        color: var(--text-muted);
        font-size: 13px;
    }

    /* ════════════════════════════════════════
       LIGHTBOX
    ════════════════════════════════════════ */
    .lightbox {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .94);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fadeIn .2s ease;
    }

    .lightbox.show {
        display: flex;
    }

    @keyframes fadeIn {
        from {
            opacity: 0
        }

        to {
            opacity: 1
        }
    }

    .lightbox img {
        max-width: 100%;
        max-height: 85vh;
        border-radius: 12px;
        object-fit: contain;
    }

    .lightbox-close {
        position: absolute;
        top: 16px;
        right: 16px;
        background: rgba(255, 255, 255, .12);
        border: none;
        color: #fff;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lightbox-caption {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, .6);
        color: #fff;
        font-size: 12px;
        padding: 4px 14px;
        border-radius: 99px;
        white-space: nowrap;
        max-width: 90vw;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ════════════════════════════════════════
       BOTTOM SAFE AREA
    ════════════════════════════════════════ */
    .bottom-spacer {
        height: calc(var(--safe-bottom) + 16px);
    }


    /* ════════════════════════════════════════
   SCANNER WRAPPER — hide/show
════════════════════════════════════════ */
    .scanner-section {
        transition: all .3s ease;
    }

    .scanner-section.hidden {
        display: none;
    }

    /* ════════════════════════════════════════
   FOTO PERSONEL BESAR (saat hasil scan)
════════════════════════════════════════ */
    .personel-foto-besar {
        width: 100%;
        height: auto;
        /* tinggi mengikuti proporsi foto */
        max-height: 350px;
        /* batas maksimal biar tidak terlalu panjang */
        object-fit: contain;
        /* foto tampil full, tidak terpotong */
        background: #000;
        /* background hitam di sisi kiri/kanan jika foto portrait */
        display: block;
    }

    .personel-foto-besar-placeholder {
        width: 100%;
        height: 220px;
        /* placeholder tetap fixed height */
        background: linear-gradient(135deg, #166534, #16a34a);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 72px;
        font-weight: 700;
        color: rgba(255, 255, 255, .3);
    }

    .result-personel-foto {
        position: relative;
        overflow: hidden;
    }

    .result-personel-foto-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top,
                rgba(0, 0, 0, .9) 0%,
                rgba(0, 0, 0, .3) 40%,
                transparent 65%);
        pointer-events: none;
    }

    .result-personel-foto-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 14px 16px;
    }

    .result-personel-foto-info .personel-nama {
        font-size: 18px;
        font-weight: 800;
        text-shadow: 0 2px 8px rgba(0, 0, 0, .8);
    }

    .result-personel-foto-info .personel-pangkat {
        font-size: 14px;
        margin-top: 3px;
    }

    /* ════════════════════════════════════════
    KENDARAAN DETAIL — tampilan lebih besar
    ════════════════════════════════════════ */
    .k-thumb-besar {
        width: 100%;
        height: 160px;
        object-fit: cover;
        display: block;
        cursor: pointer;
        transition: opacity .15s;
    }

    .k-thumb-besar:active {
        opacity: .8;
    }

    .k-thumb-besar-placeholder {
        width: 100%;
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 52px;
    }

    .k-thumb-besar-placeholder.motor {
        background: rgba(139, 92, 246, .15);
        color: #a78bfa;
    }

    .k-thumb-besar-placeholder.mobil {
        background: rgba(59, 130, 246, .15);
        color: #60a5fa;
    }

    .k-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }

    .k-detail-cell {
        padding: 9px 16px;
        border-right: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
    }

    .k-detail-cell:nth-child(even) {
        border-right: none;
    }

    .k-detail-cell:nth-last-child(-n+2) {
        border-bottom: none;
    }

    .k-detail-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text-muted);
        margin-bottom: 2px;
    }

    .k-detail-val {
        font-family: 'JetBrains Mono', monospace;
        font-size: 16px;
        font-weight: 800;
        color: var(--text);
    }

    .k-plat-besar {
        font-family: 'JetBrains Mono', monospace;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: .06em;
        padding: 10px 16px 4px;
        color: var(--text);
    }

    .k-badges-row {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 0 16px 10px;
    }

    /* Portrait default — 1 kolom */
    .result-card-inner {
        display: flex;
        flex-direction: column;
    }

    /* ════════════════════════════════════════
   GALERI FOTO KENDARAAN
════════════════════════════════════════ */
    .k-galeri-section {
        border-top: 1px solid var(--border);
    }

    .k-galeri-header {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px 8px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--text-muted);
    }

    .k-galeri-count {
        margin-left: auto;
        background: rgba(56, 189, 248, .12);
        color: var(--accent);
        font-size: 10px;
        font-weight: 600;
        padding: 1px 8px;
        border-radius: 99px;
        border: 1px solid rgba(56, 189, 248, .2);
        text-transform: none;
        letter-spacing: 0;
    }

    .k-galeri-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
        padding: 0 16px 12px;
    }

    .galeri-item {
        position: relative;
        aspect-ratio: 4/3;
        border-radius: 9px;
        overflow: hidden;
        border: 1.5px solid var(--border-md);
        cursor: pointer;
        transition: all .15s;
        background: rgba(0, 0, 0, .3);
    }

    .galeri-item:active {
        transform: scale(.96);
        opacity: .85;
    }

    .galeri-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .galeri-item-label {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0, 0, 0, .75));
        color: #fff;
        font-size: 9px;
        font-weight: 600;
        padding: 10px 6px 5px;
        text-align: center;
        letter-spacing: .03em;
        line-height: 1.2;
    }

    /* STNK item — gold border */
    .galeri-item-stnk {
        border-color: rgba(251, 191, 36, .4);
    }

    .galeri-item-stnk img {
        object-fit: contain;
        background: rgba(0, 0, 0, .2);
    }

    .stnk-label {
        background: linear-gradient(transparent, rgba(180, 120, 0, .8));
        color: #fef08a;
    }

    /* Landscape: 4 kolom */
    @media (orientation: landscape) {
        .k-galeri-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* ════════════════════════════════════════
    LANDSCAPE MODE — 2 kolom
    Kiri: foto personel + identitas
    Kanan: detail kendaraan
    ════════════════════════════════════════ */
    @media (orientation: landscape) {

        body {
            padding-bottom: calc(var(--safe-bottom) + 8px);
        }

        .content {
            max-width: 100%;
            padding: 8px 14px;
            gap: 8px;
        }

        /* Status bar lebih compact */
        .status-bar {
            padding: 8px 14px;
        }

        /* Scanner lebih pendek di landscape */
        .camera-viewport {
            aspect-ratio: 16/7;
        }

        /* ── Result Card: 2 kolom ── */
        .result-card-inner {
            display: grid;
            grid-template-columns: 300px 1fr;
            grid-template-rows: auto auto 1fr;
            min-height: 0;
        }

        /* Kolom kiri: foto personel — span 3 row */
        .result-personel-foto {
            grid-column: 1;
            grid-row: 1 / 4;
            border-right: 1px solid var(--success-bdr);
            border-bottom: none !important;
            display: flex;
            flex-direction: column;
        }

        /* Foto personel fill tinggi penuh kolom kiri */
        .personel-foto-besar {
            flex: 1;
            width: 100%;
            height: 0;
            /* trick: flex:1 akan expand */
            min-height: 180px;
            max-height: none;
            object-fit: contain;
            object-position: center top;
            background: #000;
        }

        .personel-foto-besar-placeholder {
            flex: 1;
            min-height: 180px;
            height: 0;
        }

        .result-personel-foto-info .personel-pangkat {
            font-size: 14px;
        }

        /* Info nama overlay tetap di bawah foto */
        .result-personel-foto-overlay {
            /* tetap absolute, tidak berubah */
        }

        .result-personel-foto-info {
            /* tetap absolute, tidak berubah */
        }

        /* Kolom kanan row 1: info personel (NRP, meta) */
        .result-personel {
            grid-column: 2;
            grid-row: 1;
            border-top: none !important;
            border-bottom: 1px solid var(--success-bdr);
            padding: 10px 14px;
        }

        /* Kolom kanan row 2: header kendaraan */
        .kendaraan-section-header {
            grid-column: 2;
            grid-row: 2;
        }

        /* Kolom kanan row 3: list kendaraan */
        #kendaraanList {
            grid-column: 2;
            grid-row: 3;
            /* overflow-y: auto;
            max-height: calc(100vh - 200px); */
        }

        /* Foto kendaraan lebih kecil di landscape */
        .k-thumb-besar {
            height: 240px;
        }

        .k-thumb-besar-placeholder {
            height: 140px;
            font-size: 36px;
        }

        /* Detail grid font lebih kecil */
        .k-detail-val {
            font-size: 18px;
        }

        .k-plat-besar {
            font-size: 30px;
            font-weight: 800;
            padding: 8px 14px 2px;
            letter-spacing: .06em;
        }

        /* Log disembunyikan di landscape */
        .log-section {
            display: none;
        }

        .personel-nrp {
            font-size: 18px;
            font-weight: 700;
        }

        .personel-meta {
            font-size: 18px;
        }

        /* Scanner section lebih compact */
        .scanner-section {
            margin-bottom: 0;
        }
    }
    </style>
</head>

<body>

    <!-- ═══ HEADER ═══ -->
    <header class="app-header">
        <div class="header-brand">
            <div class="brand-icon">
                <i class="bi bi-shield-check" style="color:#fff;"></i>
            </div>
            <div>
                <div class="brand-name">VGS Mobile</div>
                <div class="brand-sub">Gate Scanner</div>
            </div>
        </div>
        <div class="header-petugas">
            <div class="petugas-avatar">
                <?= strtoupper(substr($petugas, 0, 1)) ?>
            </div>
            <span class="petugas-name"><?= esc($petugas) ?></span>
        </div>
    </header>

    <button id="pwaInstallBtn" style="
    display: none;
    align-items: center;
    gap: 8px;
    position: fixed;
    bottom: 56px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9000;
    white-space: nowrap;
    padding: 11px 24px;
    border-radius: 99px;
    border: 1px solid rgba(56,189,248,.35);
    background: rgba(8,13,23,.92);
    color: #38bdf8;
    font-family: 'Exo 2', system-ui, sans-serif;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: .05em;
    cursor: pointer;
    backdrop-filter: blur(8px);
    box-shadow: 0 4px 24px rgba(0,0,0,.5);
    ">
        <i class="bi bi-download"></i>
        Install Aplikasi VGS
    </button>


    <!-- ═══ CONTENT ═══ -->
    <div class="content">

        <!-- Status Bar -->
        <div class="status-bar" id="statusBar">
            <div class="status-dot idle" id="statusDot"></div>
            <span class="status-text" id="statusText">Siap memindai QR Code</span>
            <span class="status-count" id="scanCount">0 scan</span>
        </div>

        <!-- Scan Lagi Button -->
        <button class="btn-scan-lagi" id="btnScanLagi" onclick="resetAndScan()">
            <i class="bi bi-arrow-clockwise"></i>
            Scan Lagi
        </button>

        <!-- Scanner Section -->
        <div class="scanner-section" id="scannerSection">
            <div class=" scanner-header">
                <div class="scanner-header-title">
                    <i class="bi bi-camera"></i>
                    Kamera Scanner
                </div>
            </div>

            <!-- Viewport Kamera -->
            <div class="camera-viewport">
                <!-- Idle placeholder — hilang saat kamera aktif -->
                <div class="camera-idle" id="cameraIdle">
                    <i class="bi bi-camera-video-off"></i>
                    <p>Tekan tombol di bawah untuk mengaktifkan kamera dan mulai scan</p>
                </div>

                <!-- html5-qrcode reader -->
                <div id="reader"></div>

                <!-- QR Frame overlay -->
                <div class="qr-frame" id="qrFrame" style="display:none;">
                    <div class="qr-frame-box">
                        <div class="qr-frame-inner"></div>
                        <div class="scan-line"></div>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <div class="scanner-controls">
                <button class="btn-start" id="btnStart" onclick="startScanner()">
                    <i class="bi bi-camera-fill"></i>
                    Buka Kamera
                </button>
                <button class="btn-icon-only" id="btnTorch" onclick="toggleTorch()" title="Flash" style="display:none;">
                    <i class="bi bi-lightning-fill" id="torchIcon"></i>
                </button>
            </div>
        </div>

        <!-- Result Card (Success) -->
        <div class="result-card" id="resultCard">
            <div class="result-card-inner" id="resultCardInner">

                <!-- KOLOM KIRI: Foto Personel Besar -->
                <div class="result-personel-foto" id="resFotoSection">
                    <!-- diisi JS -->
                </div>

                <!-- KOLOM KANAN ROW 1: Info Personel -->
                <div class="result-personel" style="border-top:1px solid var(--success-bdr);">
                    <div class="personel-info">
                        <div class="personel-nrp" id="resNrp">-</div>
                        <div class="personel-meta" id="resMeta">-</div>
                    </div>
                    <div id="resBadge" class="badge-ok">
                        <i class="bi bi-check-circle-fill"></i> AKSES OK
                    </div>
                </div>
                <!-- WA Button Mobile -->
                <div id="resWaMobile" style="display:none;padding:10px 16px;
     background:rgba(34,197,94,.08);border-bottom:1px solid rgba(34,197,94,.2);">
                    <a id="resWaMobileBtn" href="#" target="_blank" style="display:flex;align-items:center;justify-content:center;
              gap:8px;background:#22c55e;color:#fff;border-radius:12px;
              padding:12px;font-size:14px;font-weight:600;
              text-decoration:none;letter-spacing:.02em;">
                        <i class="bi bi-whatsapp" style="font-size:16px;"></i>
                        Hubungi via WhatsApp
                    </a>
                </div>

                <!-- KOLOM KANAN ROW 2: Header Kendaraan -->
                <div class="kendaraan-section-header">
                    <span class="kendaraan-section-label">
                        <i class="bi bi-car-front me-1"></i> Kendaraan
                    </span>
                    <span class="kendaraan-count" id="kendaraanCount">-</span>
                </div>

                <!-- KOLOM KANAN ROW 3: Detail Kendaraan -->
                <div id="kendaraanList"></div>
            </div>
        </div>

        <!-- Error Card -->
        <div class="error-card" id="errorCard">
            <div class="error-icon"><i class="bi bi-x-octagon-fill"></i></div>
            <div class="error-title">QR Tidak Dikenali</div>
            <div class="error-msg" id="errorMsg">-</div>
        </div>

        <!-- Recent Log Mini -->
        <div class="log-section">
            <div class="log-header">
                <div class="log-header-title">
                    <i class="bi bi-clock-history"></i>
                    Log Scan Terbaru
                </div>
                <span class="log-count-badge" id="logCountBadge">0</span>
            </div>
            <div class="log-list" id="logList">
                <div class="log-empty">Belum ada scan</div>
            </div>
        </div>

        <div class="bottom-spacer"></div>
    </div>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox" onclick="closeLightbox()">
        <button class="lightbox-close" onclick="closeLightbox()">
            <i class="bi bi-x"></i>
        </button>
        <img id="lightboxImg" src="" alt="">
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>

    <!-- html5-qrcode library -->
    <script src="<?= base_url('vendor/qrcode/html5-qrcode.min.js') ?>"></script>

    <script>
    /* ════════════════════════════════════════════════
    CONFIG
    ════════════════════════════════════════════════ */
    const CONFIG = {
        processUrl: '<?= $process_url ?>',
        csrfToken: '<?= $csrf_token ?>',
        csrfHash: '<?= $csrf_hash ?>',
        devNrp: ['137745', '199707152022031002'],

    };

    /* ════════════════════════════════════════════════
    STATE
    ════════════════════════════════════════════════ */
    let html5QrCode = null;
    let isScanning = false;
    let isProcessing = false;
    let torchOn = false;
    let scanTotal = 0;
    let logItems = [];

    /* ════════════════════════════════════════════════
    CSRF REFRESH
    ════════════════════════════════════════════════ */
    function refreshCsrf(hash) {
        if (hash) CONFIG.csrfHash = hash;
    }

    /* ════════════════════════════════════════════════
    STATUS HELPERS
    ════════════════════════════════════════════════ */
    function setStatus(type, text) {
        const bar = document.getElementById('statusBar');
        const dot = document.getElementById('statusDot');
        const span = document.getElementById('statusText');

        span.textContent = text;
        dot.className = 'status-dot ' + type;
        bar.className = 'status-bar ' + (type === 'success' || type === 'error' ? type : '');
    }

    function updateScanCount() {
        document.getElementById('scanCount').textContent = `${scanTotal} scan`;
        document.getElementById('logCountBadge').textContent = logItems.length;
    }

    /* ════════════════════════════════════════════════
    AUDIO BEEP
    ════════════════════════════════════════════════ */
    function playBeep(freq, type, dur) {
        try {
            const ctx = new(window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = type;
            osc.frequency.value = freq;
            gain.gain.setValueAtTime(.5, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(.001, ctx.currentTime + dur / 1000);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + dur / 1000);
            setTimeout(() => ctx.close(), dur + 100);
        } catch (e) {}
    }

    const beepSuccess = () => playBeep(880, 'sine', 150);
    const beepError = () => playBeep(220, 'square', 280);

    /* ════════════════════════════════════════════════
    SCANNER — START
    ════════════════════════════════════════════════ */
    async function startScanner() {
        if (isScanning) return;

        const btnStart = document.getElementById('btnStart');
        const cameraIdle = document.getElementById('cameraIdle');
        const qrFrame = document.getElementById('qrFrame');
        const btnTorch = document.getElementById('btnTorch');

        btnStart.disabled = true;
        btnStart.innerHTML = '<i class="bi bi-hourglass-split"></i> Membuka Kamera...';
        setStatus('loading', 'Mengaktifkan kamera...');

        try {
            // Reset reader div
            document.getElementById('reader').innerHTML = '';

            html5QrCode = new Html5Qrcode('reader');

            // Cari kamera belakang
            const cameras = await Html5Qrcode.getCameras();
            if (!cameras || !cameras.length) throw new Error('Kamera tidak ditemukan.');

            const rearCam =
                // 1. Prioritas kamera belakang
                cameras.find(c =>
                    /back|rear|environment/i.test(c.label)
                )

                // 2. Kalau tidak ada, cari kamera bawaan Mac
                ||
                cameras.find(c =>
                    /facetime|built.?in|macbook/i.test(c.label)
                )

                // 3. Kalau tetap tidak ada, gunakan kamera pertama
                ||
                cameras[0];

            await html5QrCode.start({
                    deviceId: {
                        exact: rearCam.id
                    }
                }, {
                    fps: 12,
                    qrbox: {
                        width: 240,
                        height: 240
                    },
                    aspectRatio: 1.0,
                    disableFlip: false,
                },
                onQRSuccess,
                () => {} // onQRError — silent
            );

            isScanning = true;
            cameraIdle.style.display = 'none';
            qrFrame.style.display = 'flex';

            // Ganti tombol jadi Stop
            btnStart.innerHTML = '<i class="bi bi-camera-video-off-fill"></i> Tutup Kamera';
            btnStart.className = 'btn-stop';
            btnStart.disabled = false;
            btnStart.onclick = stopScanner;

            // Tampilkan torch button jika supported
            btnTorch.style.display = 'flex';

            setStatus('idle', 'Arahkan ke QR Code...');

        } catch (err) {
            console.error(err);
            setStatus('error', err.message || 'Gagal membuka kamera.');
            btnStart.disabled = false;
            btnStart.innerHTML = '<i class="bi bi-camera-fill"></i> Buka Kamera';
            btnStart.className = 'btn-start';
            btnStart.onclick = startScanner;
        }
    }

    /* ════════════════════════════════════════════════
    SCANNER — STOP
    ════════════════════════════════════════════════ */
    async function stopScanner() {
        if (!isScanning || !html5QrCode) return;

        try {
            await html5QrCode.stop();
            html5QrCode = null;
        } catch (e) {}

        isScanning = false;
        torchOn = false;

        document.getElementById('cameraIdle').style.display = 'flex';
        document.getElementById('qrFrame').style.display = 'none';
        document.getElementById('btnTorch').style.display = 'none';
        document.getElementById('torchIcon').className = 'bi bi-lightning-fill';

        const btnStart = document.getElementById('btnStart');
        btnStart.innerHTML = '<i class="bi bi-camera-fill"></i> Buka Kamera';
        btnStart.className = 'btn-start';
        btnStart.disabled = false;
        btnStart.onclick = startScanner;

        setStatus('idle', 'Siap memindai QR Code');
    }

    /* ════════════════════════════════════════════════
    TORCH TOGGLE
    ════════════════════════════════════════════════ */
    async function toggleTorch() {
        if (!html5QrCode || !isScanning) return;
        try {
            torchOn = !torchOn;
            await html5QrCode.applyVideoConstraints({
                advanced: [{
                    torch: torchOn
                }]
            });
            document.getElementById('torchIcon').className =
                torchOn ? 'bi bi-lightning-charge-fill' : 'bi bi-lightning-fill';
            document.getElementById('btnTorch').style.color =
                torchOn ? 'var(--gold)' : 'var(--text-dim)';
        } catch (e) {
            console.warn('Torch tidak didukung:', e);
        }
    }

    /* ════════════════════════════════════════════════
    ON QR SUCCESS — callback dari html5-qrcode
    ════════════════════════════════════════════════ */
    async function onQRSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;

        // Haptic feedback
        navigator.vibrate?.(120);

        // Pause scanner sementara
        try {
            await html5QrCode.pause();
        } catch (e) {}

        setStatus('loading', 'Memproses...');

        const body = new URLSearchParams();
        body.append('kode_unik', decodedText);
        body.append(CONFIG.csrfToken, CONFIG.csrfHash);

        try {
            const res = await fetch(CONFIG.processUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: body.toString(),
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            if (data.csrf_hash) refreshCsrf(data.csrf_hash);

            if (data.status === 'success') {
                beepSuccess();
                showSuccess(data);
            } else {
                beepError();
                showError(data.message || 'QR Code tidak valid.');
            }

        } catch (err) {
            console.error(err);
            beepError();
            showError('Gagal terhubung ke server.');
        }
    }

    /* ════════════════════════════════════════════════
    SHOW SUCCESS
    ════════════════════════════════════════════════ */
    function showSuccess(data) {
        const p = data.personel;
        const k = data.kendaraan;

        // ── Developer mode detection ──
        const isDevMode = CONFIG.devNrp.includes(String(p.nrp).trim());
        const resultCard = document.getElementById('resultCard');

        if (isDevMode) {
            resultCard.classList.add('dev-mode');
        } else {
            resultCard.classList.remove('dev-mode');
        }

        const resBadge = document.getElementById('resBadge');
        if (isDevMode) {
            resBadge.className = 'badge-developer';
            resBadge.innerHTML = '<i class="bi bi-code-slash"></i> DEVELOPER';
        } else {
            resBadge.className = 'badge-ok';
            resBadge.innerHTML = '<i class="bi bi-check-circle-fill"></i> AKSES OK';
        }

        // Sembunyikan scanner
        document.getElementById('scannerSection').classList.add('hidden');
        document.getElementById('qrFrame').style.display = 'none';

        isDevMode
            ?
            setStatus('dev', `DEV MODE · ${k.plat_nomor}`) :
            setStatus('success', `OK: ${k.plat_nomor}`);

        if (isDevMode) {
            document.getElementById('statusBar').classList.add('dev-mode');
        } else {
            document.getElementById('statusBar').classList.remove('dev-mode');
        }


        // ── Foto personel besar ──
        const fotoSection = document.getElementById('resFotoSection');
        if (p.foto) {
            fotoSection.innerHTML = `
            <img src="${escAttr(p.foto)}" class="personel-foto-besar" alt="">
            <div class="result-personel-foto-overlay"></div>
            <div class="result-personel-foto-info">
                <div class="personel-nama">${escHtml(p.nama)}</div>
                <div class="personel-pangkat">${escHtml(p.pangkat_singkat || '-')}</div>
            </div>`;
        } else {
            fotoSection.innerHTML = `
            <div class="personel-foto-besar-placeholder">
                ${escHtml(p.nama.charAt(0).toUpperCase())}
            </div>
            <div class="result-personel-foto-overlay"></div>
            <div class="result-personel-foto-info">
                <div class="personel-nama">${escHtml(p.nama)}</div>
                <div class="personel-pangkat">${escHtml(p.pangkat_singkat || '-')}</div>
            </div>`;
        }

        // WA Button Mobile
        const waMobile = document.getElementById('resWaMobile');
        const waMobileBtn = document.getElementById('resWaMobileBtn');

        if (p.no_hp) {
            const pesan = encodeURIComponent(
                `Halo, ini petugas pos jaga.\n` +
                `Kendaraan Anda plat *${k.plat_nomor}* melintas pada ${data.scan_time}.\n` +
                `Mohon konfirmasi.`
            );
            waMobileBtn.href = `https://wa.me/${p.no_hp}?text=${pesan}`;
            waMobile.style.display = 'block';
        } else {
            waMobile.style.display = 'none';
        }

        // ── Info bawah foto ──
        document.getElementById('resNrp').textContent = 'NRP: ' + (p.nrp || '-');
        document.getElementById('resMeta').textContent = [p.jabatan, p.satker_singkat].filter(Boolean).join(' · ') ||
            '-';

        // ── Build kendaraan ──
        buildKendaraanDetail(k, isDevMode);

        // ── Counter & log ──
        scanTotal++;
        updateScanCount();
        addLogItem(p, k, data.scan_time);

        // ── Tampilkan result ──
        document.getElementById('resultCard').classList.add('show');
        document.getElementById('errorCard').classList.remove('show');
        document.getElementById('btnScanLagi').classList.add('show');
        document.getElementById('resultCard').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

        isProcessing = false;
    }

    function buildKendaraanDetail(k, isDevMode = false) {
        const container = document.getElementById('kendaraanList');
        const countBadge = document.getElementById('kendaraanCount');

        countBadge.textContent = k.plat_nomor;

        // ── Foto utama besar ──
        const thumbHtml = k.thumbnail_url ?
            `<img src="${escAttr(k.thumbnail_url)}" class="k-thumb-besar" alt=""
                onclick="openLightbox('${escAttr(k.thumbnail_url)}',
                'Foto Utama — ${escAttr(k.plat_nomor)}')">` :
            `<div class="k-thumb-besar-placeholder ${k.jenis}">
                <i class="bi ${k.jenis === 'motor' ? 'bi-bicycle' : 'bi-car-front'}"></i>
            </div>`;

        // ── Badge status ──
        const statusBadge = k.status === 'aktif' ?
            `<span class="badge-status-k aktif">● Aktif</span>` :
            `<span class="badge-status-k dibekukan">⚠ Dibekukan</span>`;

        // ── Galeri foto kendaraan ──
        const hasGaleri = k.galeri_urls && k.galeri_urls.length > 0;
        const hasFotoStnk = !!k.foto_stnk_url;

        // ── Dev signature di bawah kendaraan ──
        if (isDevMode) {
            const sig = document.createElement('div');
            sig.className = 'dev-signature';
            sig.innerHTML = `
            <i class="bi bi-code-slash"></i>
            Developed by Rizqi &nbsp;·&nbsp; VGS v1.0
            &nbsp;·&nbsp;
            <i class="bi bi-shield-check"></i>
        `;
            container.appendChild(sig);
        }

        let galeriHtml = '';
        if (hasGaleri || hasFotoStnk) {
            const fotoLabels = [
                'Tampak Depan', 'Tampak Samping Kiri',
                'Tampak Samping Kanan', 'Tampak Belakang',
                'Tampak Atas', 'Lainnya'
            ];

            const galeriItems = hasGaleri ?
                k.galeri_urls.map((url, i) => `
                <div class="galeri-item" onclick="openLightbox('${escAttr(url)}',
                    '${escHtml(fotoLabels[i] || 'Foto ' + (i+1))} — ${escAttr(k.plat_nomor)}')">
                    <img src="${escAttr(url)}" alt="Foto ${i+1}">
                    <div class="galeri-item-label">
                        ${escHtml(fotoLabels[i] || 'Foto ' + (i+1))}
                    </div>
                </div>`).join('') :
                '';


            const stnkItem = hasFotoStnk ? `
            <div class="galeri-item galeri-item-stnk"
                onclick="openLightbox('${escAttr(k.foto_stnk_url)}',
                'Foto STNK — ${escAttr(k.plat_nomor)}')">
                <img src="${escAttr(k.foto_stnk_url)}" alt="STNK">
                <div class="galeri-item-label stnk-label">
                    <i class="bi bi-file-earmark-text"></i> STNK
                </div>
            </div>` : '';

            galeriHtml = `
            <div class="k-galeri-section">
                <div class="k-galeri-header">
                    <i class="bi bi-images me-1"></i>
                    Foto Kendaraan
                    <span class="k-galeri-count">
                        ${k.galeri_urls ? k.galeri_urls.length : 0} foto
                        ${hasFotoStnk ? '+ STNK' : ''}
                    </span>
                </div>
                <div class="k-galeri-grid">
                    ${galeriItems}
                    ${stnkItem}
                </div>
            </div>`;
        }

        // ── STNK data ──
        const stnkHtml = k.nomor_stnk ? `
        <div class="k-stnk">
            <div class="k-stnk-cell">
                <div class="k-stnk-label">No. STNK</div>
                <div class="k-stnk-val">${escHtml(k.nomor_stnk)}</div>
            </div>
            <div class="k-stnk-cell">
                <div class="k-stnk-label">Tahun</div>
                <div class="k-stnk-val">${k.tahun || '-'}</div>
            </div>
        </div>` : '';

        container.innerHTML = `
        <div class="kendaraan-item">

            <!-- Foto Utama -->
            ${thumbHtml}

            <!-- Plat & Badge -->
            <div class="k-plat-besar">${escHtml(k.plat_nomor)}</div>
            <div class="k-badges-row">
                <span class="badge-jenis ${k.jenis}">
                    ${k.jenis.charAt(0).toUpperCase() + k.jenis.slice(1)}
                </span>
                ${statusBadge}
            </div>

            <!-- Detail Grid -->
            <div class="k-detail-grid">
                <div class="k-detail-cell">
                    <div class="k-detail-label">Merk / Model</div>
                    <div class="k-detail-val" style="font-size:13px;">
                        ${escHtml((k.merk || '-') + ' ' + (k.model || ''))}
                    </div>
                </div>
                <div class="k-detail-cell">
                    <div class="k-detail-label">Warna</div>
                    <div class="k-detail-val" style="font-size:13px;">
                        ${escHtml(k.warna || '-') + (k.tahun ? ' · ' + escHtml(k.tahun) : '')}
                    </div>
                </div>
            </div>

            <!-- STNK -->
            ${stnkHtml}

            <!-- Galeri Foto -->
            ${galeriHtml}

        </div>`;
    }

    /* ════════════════════════════════════════════════
    BUILD KENDARAAN LIST
    ════════════════════════════════════════════════ */
    function buildKendaraanList(list) {
        const container = document.getElementById('kendaraanList');
        const countBadge = document.getElementById('kendaraanCount');

        if (!list || !list.length) {
            countBadge.textContent = '0 unit';
            container.innerHTML = `
            <div style="text-align:center;padding:20px 16px;color:var(--text-muted);font-size:13px;">
                <i class="bi bi-car-front" style="font-size:24px;display:block;margin-bottom:6px;opacity:.3;"></i>
                Belum ada kendaraan terdaftar
            </div>`;
            return;
        }

        countBadge.textContent = `${list.length} unit`;

        container.innerHTML = list.map(k => {

            // Thumbnail
            const thumbHtml = k.thumbnail_url ?
                `<img src="${escAttr(k.thumbnail_url)}" class="k-thumb" alt=""
                    onclick="openLightbox('${escAttr(k.thumbnail_url)}','Foto — ${escAttr(k.plat_nomor)}')">` :
                `<div class="k-thumb-placeholder ${k.jenis}">
                <i class="bi ${k.jenis === 'motor' ? 'bi-bicycle' : 'bi-car-front'}"></i>
                </div>`;

            // STNK
            const stnkHtml = k.nomor_stnk ? `
            <div class="k-stnk">
                <div class="k-stnk-cell">
                    <div class="k-stnk-label">No. STNK</div>
                    <div class="k-stnk-val">${escHtml(k.nomor_stnk)}</div>
                </div>
                <div class="k-stnk-cell">
                    <div class="k-stnk-label">Tahun</div>
                    <div class="k-stnk-val">${k.tahun || '-'}</div>
                </div>
            </div>` : '';

            // Foto strip
            const hasGaleri = k.galeri_urls && k.galeri_urls.length > 0;
            const hasFotoStnk = !!k.foto_stnk_url;
            let stripHtml = '';
            if (hasGaleri || hasFotoStnk) {
                const imgs = hasGaleri ?
                    k.galeri_urls.map((url, i) =>
                        `<img src="${escAttr(url)}" class="strip-img" alt="Foto ${i+1}"
                            onclick="openLightbox('${escAttr(url)}','Foto ${i+1} — ${escAttr(k.plat_nomor)}')">`
                    ).join('') :
                    '';
                const stnkImg = hasFotoStnk ?
                    `<span class="strip-stnk-label">STNK</span>
                    <img src="${escAttr(k.foto_stnk_url)}" class="strip-img stnk" alt="STNK"
                        onclick="openLightbox('${escAttr(k.foto_stnk_url)}','STNK — ${escAttr(k.plat_nomor)}')">` :
                    '';
                stripHtml = `<div class="k-foto-strip">${imgs}${stnkImg}</div>`;
            }

            const detailParts = [k.merk, k.model, k.warna ? `· ${k.warna}` : ''].filter(Boolean).join(' ');
            const statusBadge = k.status === 'aktif' ?
                `<span class="badge-status-k aktif">● Aktif</span>` :
                `<span class="badge-status-k dibekukan">⚠ Dibekukan</span>`;

            return `
            <div class="kendaraan-item">
                <div class="kendaraan-item-main">
                    ${thumbHtml}
                    <div class="k-info">
                        <div class="k-plat">${escHtml(k.plat_nomor)}</div>
                        <div class="k-detail">${escHtml(detailParts)}</div>
                    </div>
                    <div class="k-badges">
                        <span class="badge-jenis ${k.jenis}">
                            ${k.jenis.charAt(0).toUpperCase() + k.jenis.slice(1)}
                        </span>
                        ${statusBadge}
                    </div>
                </div>
                ${stnkHtml}
                ${stripHtml}
            </div>`;

        }).join('');
    }

    /* ════════════════════════════════════════════════
    SHOW ERROR
    ════════════════════════════════════════════════ */
    function showError(msg) {
        setStatus('error', 'QR Tidak Dikenali');
        document.getElementById('errorMsg').textContent = msg;
        document.getElementById('errorCard').classList.add('show');
        document.getElementById('resultCard').classList.remove('show');
        document.getElementById('btnScanLagi').classList.add('show');

        document.getElementById('errorCard').scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });

        isProcessing = false;
    }

    /* ════════════════════════════════════════════════
    RESET & SCAN LAGI
    ════════════════════════════════════════════════ */
    async function resetAndScan() {
        // Sembunyikan hasil, tampilkan scanner kembali
        document.getElementById('resultCard').classList.remove('show', 'dev-mode');
        document.getElementById('statusBar').classList.remove('dev-mode');
        document.getElementById('resultCard').classList.remove('show');
        document.getElementById('errorCard').classList.remove('show');
        document.getElementById('btnScanLagi').classList.remove('show');
        document.getElementById('scannerSection').classList.remove('hidden');

        // Resume scanner
        if (html5QrCode && isScanning) {
            try {
                await html5QrCode.resume();
                document.getElementById('qrFrame').style.display = 'flex';
                setStatus('idle', 'Arahkan ke QR Code...');
            } catch (e) {
                await stopScanner();
                await startScanner();
            }
        } else {
            setStatus('idle', 'Siap memindai QR Code');
        }

        // Scroll ke atas
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        isProcessing = false;
    }

    /* ════════════════════════════════════════════════
    LOG MINI
    ════════════════════════════════════════════════ */
    function addLogItem(personel, kendaraan, scanTime) {
        logItems.unshift({
            personel,
            kendaraan,
            scanTime
        });
        if (logItems.length > 20) logItems.pop();

        const list = document.getElementById('logList');
        if (logItems.length === 1) list.innerHTML = '';

        const item = document.createElement('div');
        item.className = 'log-item';
        item.innerHTML = `
        <div class="log-avatar">
            ${escHtml(personel.nama.charAt(0).toUpperCase())}
        </div>
        <div class="log-info">
            <div class="log-nama">${escHtml(personel.nama)}</div>
            <div class="log-pangkat">
                ${escHtml(kendaraan.plat_nomor)}
                · ${escHtml(personel.pangkat_singkat || '-')}
            </div>
        </div>
        <div class="log-time">
            ${escHtml((scanTime || '').split(',')[1]?.trim() || scanTime || '')}
        </div>`;

        list.insertBefore(item, list.firstChild);
        document.getElementById('logCountBadge').textContent = logItems.length;
    }

    /* ════════════════════════════════════════════════
    LIGHTBOX
    ════════════════════════════════════════════════ */
    function openLightbox(url, caption) {
        document.getElementById('lightboxImg').src = url;
        document.getElementById('lightboxCaption').textContent = caption;
        document.getElementById('lightbox').classList.add('show');
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('show');
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeLightbox();
    });

    /* ════════════════════════════════════════════════
    XSS HELPERS
    ════════════════════════════════════════════════ */
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

    <!-- ═══════════════════════════════════════════════════════
    PWA SERVICE WORKER REGISTER
    Paste tepat sebelum </body>
    di file view: app/Views/Scan/mobile.php
    ════════════════════════════════════════════════════════ -->
    <script>
    (function() {
        if (!('serviceWorker' in navigator)) {
            console.warn('[PWA] Service Worker tidak didukung browser ini.');
            return;
        }

        window.addEventListener('load', function() {
            navigator.serviceWorker
                .register('/sw.js', {
                    scope: '/petugas/scan/mobile'
                })
                .then(function(reg) {
                    console.log('[PWA] SW registered ✓ scope:', reg.scope);

                    // Simpan halaman scanner ke cache supaya bisa dibuka offline,
                    // termasuk sejak kunjungan pertama
                    navigator.serviceWorker.ready.then(function(readyReg) {
                        if (readyReg.active) {
                            readyReg.active.postMessage({
                                type: 'CACHE_PAGE',
                                url: location.pathname
                            });
                        }
                    });

                    // Deteksi update SW — kalau ada versi baru, reload otomatis
                    reg.addEventListener('updatefound', function() {
                        var newSW = reg.installing;
                        if (!newSW) return;
                        newSW.addEventListener('statechange', function() {
                            if (newSW.state === 'activated' && navigator.serviceWorker
                                .controller) {
                                console.log('[PWA] SW updated → reload');
                                window.location.reload();
                            }
                        });
                    });
                })
                .catch(function(err) {
                    console.error('[PWA] SW registration failed:', err);
                });
        });

        // ── Install prompt — simpan event, tampilkan tombol install custom ──
        var deferredPrompt = null;

        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            console.log('[PWA] Install prompt siap.');

            // Tampilkan tombol install jika ada di halaman
            var btn = document.getElementById('pwaInstallBtn');
            if (btn) btn.style.display = 'flex';
        });

        // Tombol install (opsional — lihat pwa-install-btn.html)
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#pwaInstallBtn')) return;
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function(result) {
                console.log('[PWA] User choice:', result.outcome);
                deferredPrompt = null;
                var btn = document.getElementById('pwaInstallBtn');
                if (btn) btn.style.display = 'none';
            });
        });

        window.addEventListener('appinstalled', function() {
            console.log('[PWA] App berhasil di-install ✓');
            deferredPrompt = null;
        });
    })();
    </script>

</body>

</html>