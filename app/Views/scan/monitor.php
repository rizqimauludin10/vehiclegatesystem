<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Monitor — Vehicle Gate System</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet"> -->
    <!-- <link
        href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=JetBrains+Mono:wght@400;700&family=Exo+2:wght@300;400;600;700;800&display=swap"
        rel="stylesheet"> -->

    <link rel="stylesheet" href="<?= base_url('vendor/bootstrap-icons/font/bootstrap-icons.min.css') ?>">

    <link rel="stylesheet" href="<?= base_url('vendor/fonts/rajdhani/rajdhani.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/fonts/jetbrains-mono/jetbrains-mono.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/fonts/exo-2/exo-2.css') ?>">

    <style>
    /* ============================================
       VARIABLES & RESET
    ============================================ */
    :root {
        --bg-base: #080d17;
        --bg-card: rgba(255, 255, 255, .06);
        --border: rgba(255, 255, 255, .08);
        --border-strong: rgba(255, 255, 255, .14);
        --text-primary: #f0f4ff;
        --text-muted: #64748b;
        --text-dim: #94a3b8;
        --accent: #38bdf8;
        --accent-glow: rgba(56, 189, 248, .22);
        --success: #22c55e;
        --success-bg: #052e16;
        --success-glow: rgba(34, 197, 94, .18);
        --error: #ef4444;
        --error-bg: #450a0a;
        --error-glow: rgba(239, 68, 68, .2);
        --gold: #fbbf24;

        --fs-xs: clamp(.62rem, .9vw, .82rem);
        --fs-sm: clamp(.74rem, 1.1vw, .95rem);
        --fs-md: clamp(.86rem, 1.4vw, 1.15rem);
        --fs-lg: clamp(1rem, 1.8vw, 1.5rem);
        --fs-xl: clamp(1.3rem, 2.6vw, 2.2rem);
        --fs-2xl: clamp(1.8rem, 4vw, 3.6rem);
        --fs-clock: clamp(2.2rem, 5vw, 4.6rem);

        --radius-sm: 8px;
        --radius-md: 14px;
        --radius-lg: 22px;

        /* Tinggi header & footer — dipakai untuk kalkulasi sisa ruang */
        --header-h: clamp(56px, 9vh, 88px);
        --footer-h: clamp(26px, 2.8vh, 38px);
    }

    *,
    *::before,
    *::after {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html,
    body {
        height: 100%;
        overflow: hidden;
    }

    body {
        background: var(--bg-base);
        color: var(--text-primary);
        font-family: 'Exo 2', sans-serif;
        transition: background .6s ease;
    }

    body.success-mode {
        background: #041a0c;
    }

    body.error-mode {
        background: var(--error-bg);
    }

    /* TV scanlines */
    body::after {
        content: '';
        position: fixed;
        inset: 0;
        background: repeating-linear-gradient(0deg, transparent, transparent 2px,
                rgba(0, 0, 0, .05) 2px, rgba(0, 0, 0, .05) 4px);
        pointer-events: none;
        z-index: 9998;
    }

    /* ============================================
       ROOT LAYOUT
       Header ukurannya auto, body flex-1, footer auto
       Kunci: tidak ada overflow di root — semua di dalam
    ============================================ */
    .root-layout {
        height: 100vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* ============================================
       HEADER
    ============================================ */
    .monitor-header {
        flex-shrink: 0;
        /* header tidak pernah menyusut */
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        padding: clamp(8px, 1.2vh, 16px) clamp(16px, 2.8vw, 48px);
        background: rgba(255, 255, 255, .025);
        border-bottom: 1px solid var(--border);
        gap: 16px;
        z-index: 10;
    }

    .header-brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-icon {
        width: clamp(32px, 3.4vw, 46px);
        height: clamp(32px, 3.4vw, 46px);
        background: linear-gradient(135deg, var(--accent), #0ea5e9);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(14px, 1.7vw, 22px);
        flex-shrink: 0;
        box-shadow: 0 0 18px var(--accent-glow);
    }

    .brand-text .title {
        font-family: 'Rajdhani', sans-serif;
        font-weight: 700;
        font-size: var(--fs-lg);
        letter-spacing: .04em;
        line-height: 1;
    }

    .brand-text .sub {
        font-size: var(--fs-xs);
        color: var(--text-muted);
        letter-spacing: .07em;
        text-transform: uppercase;
        margin-top: 2px;
    }

    .header-clock {
        text-align: center;
    }

    .clock-time {
        font-family: 'JetBrains Mono', monospace;
        font-size: var(--fs-clock);
        font-weight: 700;
        line-height: 1;
        letter-spacing: .03em;
        background: linear-gradient(135deg, #f0f4ff, var(--accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .clock-date {
        font-size: var(--fs-xs);
        color: var(--text-dim);
        margin-top: 3px;
        letter-spacing: .05em;
    }

    .header-stats {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: clamp(12px, 1.8vw, 28px);
    }

    .stat-block {
        text-align: right;
    }

    .stat-label {
        font-size: var(--fs-xs);
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--text-muted);
        margin-bottom: 1px;
    }

    .stat-value {
        font-family: 'Rajdhani', sans-serif;
        font-weight: 700;
        font-size: var(--fs-lg);
        line-height: 1;
    }

    .stat-value.scan-total {
        color: var(--accent);
    }

    .status-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(34, 197, 94, .1);
        border: 1px solid rgba(34, 197, 94, .22);
        border-radius: 999px;
        padding: clamp(4px, .7vh, 8px) clamp(10px, 1.2vw, 16px);
        font-size: var(--fs-sm);
        font-weight: 600;
        color: var(--success);
        transition: all .3s;
    }

    .status-badge.error-badge {
        background: rgba(239, 68, 68, .1);
        border-color: rgba(239, 68, 68, .25);
        color: var(--error);
    }

    .status-dot {
        width: clamp(7px, .9vw, 10px);
        height: clamp(7px, .9vw, 10px);
        border-radius: 50%;
        background: var(--success);
        flex-shrink: 0;
        animation: pulse 1.6s infinite;
        transition: background .3s;
    }

    .status-dot.error-dot {
        background: var(--error);
        animation: none;
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

    /* ============================================
       MAIN BODY
       flex: 1 + min-height: 0 = mengisi sisa ruang,
       tidak pernah overflow keluar root-layout
    ============================================ */
    .monitor-body {
        flex: 1;
        min-height: 0;
        /* KRITIS: tanpa ini flex child bisa overflow */
        display: flex;
        align-items: stretch;
        justify-content: center;
        padding: clamp(10px, 1.4vh, 20px) clamp(16px, 2.5vw, 48px);
        overflow: hidden;
        position: relative;
    }

    /* grid bg */
    .monitor-body::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(56, 189, 248, .03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(56, 189, 248, .03) 1px, transparent 1px);
        background-size: clamp(28px, 3.5vw, 52px) clamp(28px, 3.5vw, 52px);
        pointer-events: none;
    }

    /* result-wrapper mengisi lebar penuh, tinggi mengikuti monitor-body */
    .result-wrapper {
        width: 100%;
        max-width: 1700px;
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        /* tinggi PERSIS sama dengan monitor-body — tidak lebih */
        min-height: 0;
    }

    /* Semua state hidden by default */
    .result-state {
        display: none;
        flex: 1;
        min-height: 0;
    }

    /* State aktif: flex column, mengisi wrapper */
    .result-state.active {
        display: flex;
        flex-direction: column;
    }

    /* ============================================
       IDLE STATE
    ============================================ */
    .idle-screen {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        animation: fadeIn .5s ease;
    }

    .idle-ring {
        width: clamp(100px, 16vw, 190px);
        height: clamp(100px, 16vw, 190px);
        border-radius: 50%;
        border: 2px solid var(--border-strong);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .idle-ring::before {
        content: '';
        position: absolute;
        inset: -8px;
        border-radius: 50%;
        border: 1px dashed rgba(56, 189, 248, .3);
        animation: spin 12s linear infinite;
    }

    .idle-ring::after {
        content: '';
        position: absolute;
        inset: -16px;
        border-radius: 50%;
        border: 1px dashed rgba(56, 189, 248, .13);
        animation: spin 20s linear infinite reverse;
    }

    .idle-ring i {
        font-size: clamp(42px, 8vw, 86px);
        color: rgba(56, 189, 248, .35);
        animation: iconPulse 2.5s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }

    @keyframes iconPulse {

        0%,
        100% {
            opacity: .35
        }

        50% {
            opacity: .75
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0
        }

        to {
            opacity: 1
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(24px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    @keyframes shakeIn {

        0%,
        100% {
            transform: translateX(0);
            opacity: 1
        }

        20% {
            transform: translateX(-14px)
        }

        40% {
            transform: translateX(14px)
        }

        60% {
            transform: translateX(-8px)
        }

        80% {
            transform: translateX(8px)
        }
    }

    .idle-title {
        margin-top: clamp(14px, 2.2vh, 26px);
        font-family: 'Rajdhani', sans-serif;
        font-weight: 700;
        font-size: var(--fs-2xl);
        letter-spacing: .07em;
    }

    .idle-sub {
        color: var(--text-dim);
        font-size: var(--fs-md);
        margin-top: 6px;
    }

    /* ============================================
       SUCCESS STATE
       2 kolom: kiri = foto personel | kanan = kendaraan
       Tinggi = sisa ruang monitor-body
    ============================================ */
    .success-layout {
        flex: 1;
        min-height: 0;
        /* KRITIS: agar grid anak bisa scroll */
        display: grid;
        grid-template-columns: clamp(260px, 32vw, 440px) 1fr;
        gap: clamp(10px, 1.4vw, 20px);
        animation: slideUp .4s cubic-bezier(.22, .61, .36, 1);
    }

    /* ── LEFT: Personel Card ── */
    .personel-card {
        background: var(--bg-card);
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-lg);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        /* tinggi mengikuti grid row */
        min-height: 0;
    }

    /* Foto personel — flex:1 agar mengisi sisa ruang kartu */
    .personel-foto-wrap {
        position: relative;
        flex: 1;
        min-height: 0;
        background: rgba(0, 0, 0, .4);
        overflow: hidden;
    }

    /* object-fit: contain agar wajah tidak terpotong */
    .personel-foto {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center top;
        display: block;
        background: rgba(0, 0, 0, .3);
    }

    .personel-foto-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Rajdhani', sans-serif;
        font-weight: 800;
        font-size: clamp(4rem, 10vw, 10rem);
        color: rgba(255, 255, 255, .12);
        background: rgba(255, 255, 255, .03);
    }

    /* Gradient hanya di bawah untuk teks nama */
    .personel-foto-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top,
                rgba(0, 0, 0, .92) 0%, rgba(0, 0, 0, .25) 35%, transparent 60%);
        pointer-events: none;
    }

    .personel-foto-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: clamp(10px, 1.6vh, 20px) clamp(12px, 1.6vw, 20px);
    }

    .personel-nama {
        font-family: 'Rajdhani', sans-serif;
        font-weight: 800;
        font-size: var(--fs-xl);
        line-height: 1.1;
        letter-spacing: .02em;
        text-shadow: 0 2px 8px rgba(0, 0, 0, .8);
    }

    .personel-pangkat {
        font-size: var(--fs-sm);
        color: var(--accent);
        font-weight: 600;
        margin-top: 3px;
        letter-spacing: .03em;
    }

    /* Detail rows di bawah foto */
    .personel-detail {
        padding: clamp(7px, 1vh, 12px) clamp(12px, 1.5vw, 18px);
        display: flex;
        flex-direction: column;
        gap: clamp(3px, .6vh, 7px);
        flex-shrink: 0;
        border-top: 1px solid var(--border);
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }

    .detail-label {
        font-size: var(--fs-xs);
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .detail-value {
        font-family: 'JetBrains Mono', monospace;
        font-size: var(--fs-sm);
        font-weight: 700;
        text-align: right;
        letter-spacing: .04em;
    }

    .detail-value.nrp {
        color: var(--accent);
    }

    .access-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: rgba(34, 197, 94, .15);
        border: 1px solid rgba(34, 197, 94, .35);
        border-radius: 999px;
        margin: 0 clamp(10px, 1.5vw, 18px) clamp(7px, 1vh, 12px);
        padding: clamp(5px, .8vh, 9px);
        font-family: 'Rajdhani', sans-serif;
        font-weight: 700;
        font-size: var(--fs-md);
        letter-spacing: .08em;
        color: var(--success);
        box-shadow: 0 0 20px var(--success-glow);
        flex-shrink: 0;
    }

    /* ── RIGHT: Kendaraan Panel ── */
    .kendaraan-panel {
        background: var(--bg-card);
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-lg);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        /* tinggi mengikuti grid row, tidak overflow */
        min-height: 0;
    }

    .kendaraan-panel-header {
        padding: clamp(8px, 1.1vh, 13px) clamp(14px, 1.8vw, 22px);
        background: rgba(255, 255, 255, .03);
        border-bottom: 1px solid var(--border);
        font-size: var(--fs-xs);
        text-transform: uppercase;
        letter-spacing: .1em;
        color: var(--text-muted);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .kendaraan-count-badge {
        background: rgba(56, 189, 248, .15);
        color: var(--accent);
        font-size: var(--fs-xs);
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 99px;
        border: 1px solid rgba(56, 189, 248, .2);
    }

    /*
     * SCROLL KENDARAAN
     * flex:1 + min-height:0 + overflow-y:auto = scroll dalam batas panel
     * Panel tidak pernah melebihi tinggi grid (yang = tinggi monitor-body)
     */
    .kendaraan-scroll {
        flex: 1;
        min-height: 0;
        /* KRITIS */
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, .12) transparent;
        padding: clamp(8px, 1vw, 14px);
        display: flex;
        flex-direction: column;
        gap: clamp(8px, 1vw, 14px);
    }

    .kendaraan-scroll::-webkit-scrollbar {
        width: 5px;
    }

    .kendaraan-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .kendaraan-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, .14);
        border-radius: 99px;
    }

    .kendaraan-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, .25);
    }

    /* ── Kendaraan Item ── */
    .kendaraan-item {
        background: rgba(255, 255, 255, .05);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: border-color .2s;
        flex-shrink: 0;
        /* item tidak menyusut, tetap utuh */
    }

    .kendaraan-item:hover {
        border-color: var(--border-strong);
    }

    .kendaraan-item-header {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: clamp(10px, 1.2vw, 18px);
        padding: clamp(10px, 1.2vh, 16px) clamp(12px, 1.4vw, 20px);
        align-items: center;
    }

    /* Thumbnail kendaraan — diperbesar */
    .k-thumb {
        width: clamp(130px, 13vw, 200px);
        height: clamp(96px, 9.5vw, 148px);
        border-radius: var(--radius-sm);
        object-fit: cover;
        border: 1.5px solid var(--border-strong);
        flex-shrink: 0;
        cursor: pointer;
        transition: opacity .15s, transform .15s;
    }

    .k-thumb:hover {
        opacity: .85;
        transform: scale(1.03);
    }

    .k-thumb-placeholder {
        width: clamp(90px, 9vw, 136px);
        height: clamp(66px, 7vw, 100px);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(22px, 2.8vw, 40px);
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
        min-width: 0;
    }

    .k-plat {
        font-family: 'JetBrains Mono', monospace;
        font-size: var(--fs-2xl);
        font-weight: 700;
        letter-spacing: .06em;
    }

    .k-detail {
        font-size: var(--fs-xs);
        color: var(--text-dim);
        margin-top: 3px;
    }

    .k-kode {
        font-size: var(--fs-xs);
        color: var(--text-muted);
        margin-top: 2px;
        letter-spacing: .04em;
    }

    .k-badges {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 5px;
        flex-shrink: 0;
    }

    .badge-jenis {
        font-size: var(--fs-xs);
        font-weight: 600;
        letter-spacing: .05em;
        text-transform: uppercase;
        padding: 3px 10px;
        border-radius: 6px;
        white-space: nowrap;
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
        font-size: var(--fs-xs);
        font-weight: 600;
    }

    .badge-status-k.aktif {
        color: var(--success);
    }

    .badge-status-k.dibekukan {
        color: #fb923c;
    }

    /* STNK — hanya Nomor STNK & Tahun */
    .stnk-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border-top: 1px solid var(--border);
    }

    .stnk-cell {
        padding: clamp(5px, .8vh, 9px) clamp(12px, 1.4vw, 20px);
        border-right: 1px solid var(--border);
    }

    .stnk-cell:last-child {
        border-right: none;
    }

    .stnk-lbl {
        font-size: var(--fs-xs);
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--text-muted);
    }

    .stnk-val {
        font-family: 'JetBrains Mono', monospace;
        font-size: var(--fs-sm);
        font-weight: 600;
        color: var(--text-primary);
        margin-top: 2px;
        letter-spacing: .03em;
    }

    /* Foto strip — diperbesar */
    .foto-strip {
        display: flex;
        gap: 8px;
        padding: clamp(7px, .9vh, 11px) clamp(12px, 1.4vw, 20px);
        border-top: 1px solid var(--border);
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, .1) transparent;
        align-items: center;
    }

    .foto-strip::-webkit-scrollbar {
        height: 3px;
    }

    .foto-strip::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, .12);
        border-radius: 99px;
    }

    .strip-img {
        width: clamp(68px, 7.5vw, 106px);
        height: clamp(52px, 5.5vw, 78px);
        border-radius: 7px;
        object-fit: cover;
        flex-shrink: 0;
        border: 1.5px solid var(--border-strong);
        cursor: pointer;
        transition: all .15s;
    }

    .strip-img:hover {
        border-color: var(--accent);
        transform: scale(1.06);
    }

    .strip-img.stnk-foto {
        width: clamp(76px, 8.5vw, 116px);
        height: clamp(52px, 5.5vw, 78px);
        border-color: rgba(251, 191, 36, .45);
        object-fit: contain;
        background: rgba(0, 0, 0, .3);
    }

    .strip-img.stnk-foto:hover {
        border-color: var(--gold);
    }

    .strip-stnk-label {
        font-size: var(--fs-xs);
        color: var(--gold);
        writing-mode: vertical-lr;
        transform: rotate(180deg);
        letter-spacing: .06em;
        font-weight: 600;
        flex-shrink: 0;
        opacity: .7;
    }

    /* ============================================
       ERROR STATE
    ============================================ */
    .error-screen {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        animation: shakeIn .5s cubic-bezier(.22, .61, .36, 1);
    }

    .error-icon-wrap {
        font-size: clamp(70px, 12vw, 136px);
        color: var(--error);
        filter: drop-shadow(0 0 36px rgba(239, 68, 68, .4));
        line-height: 1;
    }

    .error-title {
        font-family: 'Rajdhani', sans-serif;
        font-weight: 700;
        font-size: var(--fs-2xl);
        letter-spacing: .07em;
        margin-top: clamp(10px, 1.8vh, 20px);
        color: var(--error);
    }

    .error-sub {
        color: var(--text-dim);
        font-size: var(--fs-lg);
        margin-top: 6px;
    }

    /* ============================================
       LIGHTBOX
    ============================================ */
    .lightbox-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .92);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        cursor: pointer;
        animation: fadeIn .2s ease;
    }

    .lightbox-img {
        max-width: 90vw;
        max-height: 88vh;
        border-radius: 14px;
        object-fit: contain;
        box-shadow: 0 24px 80px rgba(0, 0, 0, .7);
        cursor: default;
    }

    .lightbox-close {
        position: absolute;
        top: 18px;
        right: 18px;
        background: rgba(255, 255, 255, .12);
        border: none;
        color: #fff;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 1.1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s;
        z-index: 1;
    }

    .lightbox-close:hover {
        background: rgba(255, 255, 255, .25);
    }

    .lightbox-caption {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, .6);
        color: #fff;
        font-size: var(--fs-xs);
        letter-spacing: .05em;
        padding: 5px 16px;
        border-radius: 99px;
        white-space: nowrap;
    }

    /* ============================================
       FOOTER TICKER
    ============================================ */
    .monitor-footer {
        flex-shrink: 0;
        /* footer tidak pernah menyusut */
        height: clamp(26px, 2.8vh, 38px);
        background: rgba(255, 255, 255, .025);
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        padding: 0 clamp(14px, 2.5vw, 40px);
        gap: 24px;
        font-size: var(--fs-xs);
        color: var(--text-muted);
        overflow: hidden;
        z-index: 10;
    }

    .footer-ticker {
        flex: 1;
        overflow: hidden;
        white-space: nowrap;
    }

    .ticker-inner {
        display: inline-block;
        animation: ticker 30s linear infinite;
    }

    @keyframes ticker {
        from {
            transform: translateX(100vw)
        }

        to {
            transform: translateX(-100%)
        }
    }

    .footer-right {
        flex-shrink: 0;
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .footer-right span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* ============================================
       RESPONSIVE
    ============================================ */
    @media (max-width: 900px) {
        .success-layout {
            grid-template-columns: 1fr;
            overflow-y: auto;
        }

        .personel-foto-wrap {
            height: clamp(220px, 35vw, 320px);
            flex: none;
        }

        .monitor-header {
            grid-template-columns: 1fr 1fr;
        }

        .header-clock {
            grid-column: 1 / -1;
            order: -1;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--border);
        }
    }

    @media (min-width: 1920px) {
        :root {
            --fs-clock: 6.5rem;
        }

        .monitor-header {
            padding: 20px 72px;
        }

        .monitor-body {
            padding: 28px 80px;
        }
    }

    @media (min-width: 3840px) {
        :root {
            --fs-clock: 9.5rem;
            --fs-2xl: 5.5rem;
            --fs-xl: 3.2rem;
            --fs-lg: 2.3rem;
            --fs-md: 1.7rem;
            --fs-sm: 1.3rem;
            --fs-xs: 1rem;
        }

        .monitor-header {
            padding: 34px 120px;
        }

        .monitor-body {
            padding: 44px 120px;
        }
    }
    </style>
</head>

<body>

    <div class="root-layout">

        <!-- ═══ HEADER ═══ -->
        <header class="monitor-header">
            <div class="header-brand">
                <div class="brand-icon">
                    <i class="bi bi-shield-check" style="color:#fff"></i>
                </div>
                <div class="brand-text">
                    <div class="title">VEHICLE GATE SYSTEM</div>
                    <div class="sub">Monitoring Akses Kendaraan</div>
                </div>
            </div>

            <div class="header-clock">
                <div class="clock-time" id="liveClock">--:--:--</div>
                <div class="clock-date" id="liveDate">--</div>
            </div>

            <div class="header-stats">
                <div class="stat-block">
                    <div class="stat-label">Total Scan</div>
                    <div class="stat-value scan-total" id="scanCount">0</div>
                </div>
                <div class="stat-block">
                    <div class="stat-label">Petugas</div>
                    <div class="stat-value" style="font-size:var(--fs-sm);color:var(--text-dim);">
                        <?= esc($petugas) ?>
                    </div>
                </div>
                <div class="status-badge" id="statusBadge">
                    <div class="status-dot" id="statusDot"></div>
                    <span id="scannerText">Menunggu Scan</span>
                </div>
            </div>
        </header>

        <!-- ═══ MAIN BODY ═══ -->
        <main class="monitor-body">
            <div class="result-wrapper">

                <!-- IDLE -->
                <div id="stateIdle" class="result-state active">
                    <div class="idle-screen">
                        <div class="idle-ring">
                            <i class="bi bi-qr-code-scan"></i>
                        </div>
                        <div class="idle-title">MENUNGGU PEMINDAIAN QR CODE</div>
                        <div class="idle-sub">Arahkan scanner ke kartu QR personel</div>
                    </div>
                </div>

                <!-- SUCCESS -->
                <div id="stateSuccess" class="result-state">
                    <div class="success-layout">

                        <!-- Kiri: Personel -->
                        <div class="personel-card">
                            <div class="personel-foto-wrap">
                                <img id="resFoto" src="" class="personel-foto" alt="" style="display:none;">
                                <div id="resFotoPlaceholder" class="personel-foto-placeholder">?</div>
                                <div class="personel-foto-overlay"></div>
                                <div class="personel-foto-info">
                                    <div class="personel-nama" id="resNama">-</div>
                                    <div class="personel-pangkat" id="resPangkat">-</div>
                                </div>
                            </div>
                            <div class="personel-detail">
                                <div class="detail-row">
                                    <span class="detail-label">NRP</span>
                                    <span class="detail-value nrp" id="resNrp">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Jabatan</span>
                                    <span class="detail-value" id="resJabatan">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Satuan</span>
                                    <span class="detail-value" id="resSatuan">-</span>
                                </div>
                                <!-- Setelah detail-row satuan -->
                                <a id="resWaMonitor" href="#" target="_blank" style="display:none;align-items:center;justify-content:center;
          gap:6px;background:#22c55e;color:#fff;border-radius:999px;
          padding:clamp(5px,.8vh,9px) clamp(10px,1.3vw,18px);
          font-size:var(--fs-sm);font-weight:700;text-decoration:none;
          margin:0 clamp(10px,1.5vw,18px);letter-spacing:.03em;">
                                    <i class="bi bi-whatsapp"></i> Hubungi via WA
                                </a>
                            </div>
                            <div class="access-badge">
                                <i class="bi bi-check-circle-fill"></i>
                                AKSES DIIZINKAN
                            </div>
                        </div>

                        <!-- Kanan: Kendaraan -->
                        <div class="kendaraan-panel">
                            <div class="kendaraan-panel-header">
                                <span>
                                    <i class="bi bi-car-front me-2"></i> Kendaraan Terdaftar
                                </span>
                                <span class="kendaraan-count-badge" id="kendaraanCount">0 unit</span>
                            </div>
                            <div id="resKendaraanList" class="kendaraan-scroll"></div>
                        </div>

                    </div>
                </div>

                <!-- ERROR -->
                <div id="stateError" class="result-state">
                    <div class="error-screen">
                        <div class="error-icon-wrap">
                            <i class="bi bi-x-octagon-fill"></i>
                        </div>
                        <div class="error-title">QR TIDAK DIKENALI</div>
                        <div class="error-sub" id="errorSub"></div>
                    </div>
                </div>

            </div>
        </main>

        <!-- ═══ FOOTER ═══ -->
        <footer class="monitor-footer">
            <div class="footer-ticker">
                <span class="ticker-inner">
                    &nbsp;&nbsp;&nbsp;SISTEM AKTIF &bull;
                    Seluruh akses kendaraan tercatat dan dipantau secara real-time &bull;
                    Laporkan aktivitas mencurigakan kepada petugas jaga &bull;
                    Kartu QR bersifat personal dan tidak dapat dipindahtangankan &bull;
                    &nbsp;&nbsp;&nbsp;SISTEM AKTIF &bull;
                    Seluruh akses kendaraan tercatat dan dipantau secara real-time &bull;
                    Laporkan aktivitas mencurigakan kepada petugas jaga &bull;
                    Kartu QR bersifat personal dan tidak dapat dipindahtangankan
                </span>
            </div>
            <div class="footer-right">
                <span><i class="bi bi-hdd-network-fill"></i> ONLINE</span>
                <span><i class="bi bi-person-badge-fill"></i> <?= esc($petugas) ?></span>
                <span><i class="bi bi-geo-alt-fill"></i> GATE UTAMA</span>
            </div>
        </footer>

    </div>

    <!-- Hidden scanner input -->
    <input type="text" id="scanInput" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
        style="position:fixed;opacity:0;pointer-events:none;left:-9999px;top:-9999px;">

    <script>
    /* ═══════════════════════════════════════════════════
   CONFIG
═══════════════════════════════════════════════════ */
    const CONFIG = {
        processUrl: '<?= $process_url ?>',
        csrfToken: '<?= $csrf_token ?>',
        csrfHash: '<?= $csrf_hash ?>',
        // null = tidak auto reset, data stay sampai scan berikutnya
        autoResetMs: null,
    };

    /* ═══════════════════════════════════════════════════
       CLOCK
    ═══════════════════════════════════════════════════ */
    (function initClock() {
        function tick() {
            const now = new Date();
            document.getElementById('liveClock').textContent =
                now.toLocaleTimeString('id-ID');
            document.getElementById('liveDate').textContent =
                now.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
        }
        tick();
        setInterval(tick, 1000);
    })();

    /* ═══════════════════════════════════════════════════
       CSRF — refresh hash setelah setiap request
    ═══════════════════════════════════════════════════ */
    function refreshCsrf(newHash) {
        if (newHash) CONFIG.csrfHash = newHash;
    }

    /* ═══════════════════════════════════════════════════
       FOCUS SCANNER
    ═══════════════════════════════════════════════════ */
    const scanInput = document.getElementById('scanInput');

    function focusScanner() {
        try {
            scanInput.focus();
        } catch (e) {}
    }

    window.addEventListener('load', focusScanner);

    setInterval(() => {
        if (!document.getElementById('lightboxEl')) focusScanner();
    }, 1000);

    document.addEventListener('click', e => {
        if (!e.target.closest('button, a, .lightbox-overlay')) focusScanner();
    });

    /* ═══════════════════════════════════════════════════
       AUDIO BEEP
    ═══════════════════════════════════════════════════ */
    function playBeep(freq, type, dur) {
        try {
            const ctx = new(window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = type;
            osc.frequency.value = freq;
            gain.gain.setValueAtTime(.6, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(.001, ctx.currentTime + dur / 1000);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + dur / 1000);
            setTimeout(() => ctx.close(), dur + 100);
        } catch (e) {}
    }
    const playSuccess = () => playBeep(880, 'sine', 150);
    const playError = () => playBeep(220, 'square', 280);

    /* ═══════════════════════════════════════════════════
       STATE MANAGEMENT
    ═══════════════════════════════════════════════════ */
    function showState(state) {
        document.querySelectorAll('.result-state')
            .forEach(el => el.classList.remove('active'));
        document.body.classList.remove('success-mode', 'error-mode');
        if (state === 'success') document.body.classList.add('success-mode');
        if (state === 'error') document.body.classList.add('error-mode');
        const map = {
            idle: 'stateIdle',
            success: 'stateSuccess',
            error: 'stateError'
        };
        document.getElementById(map[state]).classList.add('active');
    }

    function setStatus(type, text) {
        const badge = document.getElementById('statusBadge');
        const dot = document.getElementById('statusDot');
        const span = document.getElementById('scannerText');
        span.textContent = text;
        dot.className = 'status-dot';
        badge.className = 'status-badge';
        if (type === 'error') {
            dot.classList.add('error-dot');
            badge.classList.add('error-badge');
        }
    }

    /* ═══════════════════════════════════════════════════
       SCANNER INPUT
    ═══════════════════════════════════════════════════ */
    let scanBuffer = '';
    let scanTimeout = null;
    let isProcessing = false;
    let totalScan = 0;
    let resetTimer = null;

    scanInput.addEventListener('input', function() {
        if (isProcessing) {
            this.value = '';
            return;
        }
        clearTimeout(scanTimeout);
        scanBuffer = this.value;
        setStatus('idle', 'Membaca QR Code...');
        scanTimeout = setTimeout(() => {
            if (scanBuffer.trim().length > 0) processScan();
        }, 250);
    });

    scanInput.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        if (isProcessing) return;
        clearTimeout(scanTimeout);
        if (scanBuffer.trim().length > 0) processScan();
    });

    /* ═══════════════════════════════════════════════════
       PROCESS SCAN
    ═══════════════════════════════════════════════════ */
    function processScan() {
        const kode = scanInput.value.trim();
        if (!kode) return;

        isProcessing = true;
        clearTimeout(resetTimer);
        setStatus('idle', 'Memproses...');

        // Bersihkan input segera — scanner siap scan berikutnya
        scanInput.value = '';
        scanBuffer = '';

        const body = new URLSearchParams();
        body.append('kode_unik', kode);
        body.append(CONFIG.csrfToken, CONFIG.csrfHash);

        fetch(CONFIG.processUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: body.toString(),
            })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (data.csrf_hash) refreshCsrf(data.csrf_hash);
                if (data.status === 'success') showSuccess(data);
                else showError(data.message || 'QR Code tidak valid.');
            })
            .catch(err => {
                console.error('Scan error:', err);
                showError('Gagal terhubung ke server. Coba scan ulang.');
            })
            .finally(() => {
                isProcessing = false;
                focusScanner();
            });
    }

    /* ═══════════════════════════════════════════════════
       SHOW SUCCESS
       Data TIDAK auto hilang — hanya diganti saat scan baru
    ═══════════════════════════════════════════════════ */
    function showSuccess(data) {
        playSuccess();
        showState('success');

        const p = data.personel;
        const k = data.kendaraan;

        // Foto personel
        const foto = document.getElementById('resFoto');
        const placeholder = document.getElementById('resFotoPlaceholder');
        if (p.foto) {
            foto.src = p.foto;
            foto.style.display = 'block';
            placeholder.style.display = 'none';
        } else {
            foto.style.display = 'none';
            placeholder.style.display = 'flex';
            placeholder.textContent = p.nama.charAt(0).toUpperCase();
        }

        // Identitas personel
        document.getElementById('resNama').textContent = p.nama;
        document.getElementById('resPangkat').textContent = p.pangkat_singkat || '-';
        document.getElementById('resNrp').textContent = p.nrp;
        document.getElementById('resJabatan').textContent = p.jabatan || '-';
        document.getElementById('resSatuan').textContent = p.satker_singkat || '-';

        // Tambah di buildKendaraanDetail atau showSuccess di monitor.php
        // Setelah set resJabatan & resSatuan

        // WA Button di monitor
        const waMonitor = document.getElementById('resWaMonitor');
        if (waMonitor) {
            if (p.no_hp) {
                const pesan = encodeURIComponent(
                    `Halo, petugas pos jaga.\nKendaraan ${data.kendaraan.plat_nomor} melintas pada ${data.scan_time}.`
                );
                waMonitor.href = `https://wa.me/${p.no_hp}?text=${pesan}`;
                waMonitor.style.display = 'flex';
            } else {
                waMonitor.style.display = 'none';
            }
        }

        // Counter
        totalScan++;
        document.getElementById('scanCount').textContent = totalScan;
        setStatus('idle', `OK: ${k.plat_nomor} ✓`);

        // Build tampilan kendaraan
        buildKendaraanDetail(k);

        if (CONFIG.autoResetMs) {
            resetTimer = setTimeout(() => {
                showState('idle');
                setStatus('idle', 'Menunggu Scan');
            }, CONFIG.autoResetMs);
        }
    }

    /* ═══════════════════════════════════════════════════
   BUILD KENDARAAN DETAIL — 1 kendaraan spesifik
═══════════════════════════════════════════════════ */
    function buildKendaraanDetail(k) {
        const container = document.getElementById('resKendaraanList');
        const countBadge = document.getElementById('kendaraanCount');

        countBadge.textContent = k.plat_nomor;

        // Thumbnail
        const thumbHtml = k.thumbnail_url ?
            `<img src="${escAttr(k.thumbnail_url)}" class="k-thumb" alt=""
                onclick="openLightbox('${escAttr(k.thumbnail_url)}',
                'Foto — ${escAttr(k.plat_nomor)}')">` :
            `<div class="k-thumb-placeholder ${k.jenis}">
               <i class="bi ${k.jenis === 'motor' ? 'bi-bicycle' : 'bi-car-front'}"></i>
           </div>`;

        // STNK — Nomor STNK & Tahun
        const stnkHtml = k.nomor_stnk ? `
        <div class="stnk-row">
            <div class="stnk-cell">
                <div class="stnk-lbl">No. STNK</div>
                <div class="stnk-val">${escHtml(k.nomor_stnk)}</div>
            </div>
            <div class="stnk-cell">
                <div class="stnk-lbl">Tahun</div>
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
                <img src="${escAttr(url)}" class="strip-img" alt="Foto ${i+1}"
                     onclick="openLightbox('${escAttr(url)}',
                     'Foto ${i+1} — ${escAttr(k.plat_nomor)}')">`).join('') :
                '';
            const stnkImg = hasFotoStnk ?
                `<span class="strip-stnk-label">STNK</span>
               <img src="${escAttr(k.foto_stnk_url)}" class="strip-img stnk-foto"
                    alt="STNK" onclick="openLightbox('${escAttr(k.foto_stnk_url)}',
                    'STNK — ${escAttr(k.plat_nomor)}')">` :
                '';
            stripHtml = `<div class="foto-strip">${imgs}${stnkImg}</div>`;
        }

        const detailParts = [k.merk, k.model, k.warna ? `· ${k.warna}` : '']
            .filter(Boolean).join(' ');

        const statusBadge = k.status === 'aktif' ?
            `<span class="badge-status-k aktif">● Aktif</span>` :
            `<span class="badge-status-k dibekukan">⚠ Dibekukan</span>`;

        container.innerHTML = `
        <div class="kendaraan-item">
            <div class="kendaraan-item-header">
                ${thumbHtml}
                <div class="k-info">
                    <div class="k-plat">${escHtml(k.plat_nomor)}</div>
                    <div class="k-detail">${escHtml(detailParts)}</div>
                    <div class="k-kode">Kode: ${escHtml(k.kendaraan_kode)}</div>
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
    }

    /* ═══════════════════════════════════════════════════
       BUILD KENDARAAN LIST
       Tampil: Thumbnail, Plat Nomor, No. STNK saja
    ═══════════════════════════════════════════════════ */
    function buildKendaraanList(list) {
        const container = document.getElementById('resKendaraanList');
        const countBadge = document.getElementById('kendaraanCount');

        if (!list || list.length === 0) {
            countBadge.textContent = '0 unit';
            container.innerHTML = `
            <div style="text-align:center;padding:2.5rem 1rem;
                        color:var(--text-muted);font-size:var(--fs-sm);">
                <i class="bi bi-car-front"
                   style="font-size:2.5rem;display:block;
                          margin-bottom:.6rem;opacity:.25;"></i>
                Belum ada kendaraan terdaftar
            </div>`;
            return;
        }

        countBadge.textContent = `${list.length} unit`;

        container.innerHTML = list.map((k) => {

            // Thumbnail
            const thumbHtml = k.thumbnail_url ?
                `<img src="${escAttr(k.thumbnail_url)}" class="k-thumb" alt=""
                    onclick="openLightbox('${escAttr(k.thumbnail_url)}',
                    'Foto Kendaraan — ${escAttr(k.plat_nomor)}')">` :
                `<div class="k-thumb-placeholder ${k.jenis}">
                   <i class="bi ${k.jenis === 'motor' ? 'bi-bicycle' : 'bi-car-front'}"></i>
               </div>`;

            // Nomor STNK & Tahun saja
            const stnkHtml = k.nomor_stnk ? `
            <div class="stnk-row">
                <div class="stnk-cell">
                    <div class="stnk-lbl">No. STNK</div>
                    <div class="stnk-val">${escHtml(k.nomor_stnk)}</div>
                </div>
                <div class="stnk-cell">
                    <div class="stnk-lbl">Tahun</div>
                    <div class="stnk-val">${k.tahun || '-'}</div>
                </div>
            </div>` : '';

            // Foto strip (galeri + STNK foto)
            const hasGaleri = k.galeri_urls && k.galeri_urls.length > 0;
            const hasFotoStnk = !!k.foto_stnk_url;
            let stripHtml = '';
            if (hasGaleri || hasFotoStnk) {
                const galeriImgs = hasGaleri ?
                    k.galeri_urls.map((url, i) => `
                    <img src="${escAttr(url)}" class="strip-img"
                         alt="Foto ${i+1}"
                         onclick="openLightbox('${escAttr(url)}',
                         'Foto ${i+1} — ${escAttr(k.plat_nomor)}')">`).join('') :
                    '';
                const stnkImg = hasFotoStnk ? `
                <span class="strip-stnk-label">STNK</span>
                <img src="${escAttr(k.foto_stnk_url)}"
                    class="strip-img stnk-foto" alt="STNK"
                    onclick="openLightbox('${escAttr(k.foto_stnk_url)}',
                    'Foto STNK — ${escAttr(k.plat_nomor)}')">` : '';
                stripHtml = `<div class="foto-strip">${galeriImgs}${stnkImg}</div>`;
            }

            const statusBadge = k.status === 'aktif' ?
                `<span class="badge-status-k aktif">● Aktif</span>` :
                `<span class="badge-status-k dibekukan">⚠ Dibekukan</span>`;

            const detailParts = [k.merk, k.model, k.warna ? `· ${k.warna}` : '']
                .filter(Boolean).join(' ');

            return `
            <div class="kendaraan-item">
                <div class="kendaraan-item-header">
                    ${thumbHtml}
                    <div class="k-info">
                        <div class="k-plat">${escHtml(k.plat_nomor)}</div>
                        <div class="k-detail">${escHtml(detailParts)}</div>
                        <div class="k-kode">Kode: ${escHtml(k.kendaraan_kode)}</div>
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

    /* ═══════════════════════════════════════════════════
       SHOW ERROR
       Auto reset ke idle setelah 8 detik
    ═══════════════════════════════════════════════════ */
    function showError(msg) {
        playError();
        showState('error');
        document.getElementById('errorSub').textContent = msg;
        setStatus('error', 'Akses Ditolak');

        clearTimeout(resetTimer);
        resetTimer = setTimeout(() => {
            showState('idle');
            setStatus('idle', 'Menunggu Scan');
        }, 8000);
    }

    /* ═══════════════════════════════════════════════════
    LIGHTBOX
    ═══════════════════════════════════════════════════ */
    function openLightbox(url, caption) {
        closeLightbox();
        const el = document.createElement('div');
        el.className = 'lightbox-overlay';
        el.id = 'lightboxEl';
        el.innerHTML = `
        <button class="lightbox-close" onclick="closeLightbox()">
            <i class="bi bi-x"></i>
        </button>
        <img src="${escAttr(url)}" class="lightbox-img"
            alt="${escAttr(caption)}" onclick="event.stopPropagation()">
        <div class="lightbox-caption">${escHtml(caption)}</div>`;
        el.addEventListener('click', closeLightbox);
        document.body.appendChild(el);
        document.addEventListener('keydown', lbKey);
    }

    function closeLightbox() {
        const el = document.getElementById('lightboxEl');
        if (el) el.remove();
        document.removeEventListener('keydown', lbKey);
        focusScanner();
    }

    function lbKey(e) {
        if (e.key === 'Escape') closeLightbox();
    }

    /* ═══════════════════════════════════════════════════
    SECURITY HELPERS — Prevent XSS
    ═══════════════════════════════════════════════════ */
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

    /* ═══════════════════════════════════════════════════
    INIT
    ═══════════════════════════════════════════════════ */
    showState('idle');
    focusScanner();
    </script>

</body>

</html>