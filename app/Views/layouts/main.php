<?php
    $uri      = uri_string();
    $isActive = static fn(string $prefix): bool => $uri === $prefix || str_starts_with($uri, $prefix . '/');

    // Role pengguna
    $role       = (string) session()->get('role');
    $isAdminish = in_array($role, ['admin', 'superadmin'], true); // admin & Super Admin memakai menu admin
    $isSuper    = $role === 'superadmin';
    $roleLabel  = ['superadmin' => 'Super Admin', 'admin' => 'Admin', 'petugas' => 'Petugas'][$role] ?? $role;

    // Status maintenance (hanya dicek untuk admin & Super Admin)
    $maintenanceOn = $isAdminish && \App\Libraries\MaintenanceMode::isActive();

    // Grup yang bisa dilipat: otomatis terbuka kalau halaman aktif ada di dalamnya
    $refOpen      = $isActive('admin/pangkat') || $isActive('admin/satker');
    $settingsOpen = $isActive('admin/users') || $isActive('admin/backup') || $isActive('admin/maintenance')
                  || $isActive('admin/admins');

    // Petugas: "Scan QR Code" jangan ikut aktif saat buka Mobile Scanner
    $scanMobileActive = $isActive('petugas/scan/mobile');
    $scanActive       = $isActive('petugas/scan') && !$scanMobileActive;

    $userNama = (string) session()->get('user_nama');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- PWA -->
    <meta name="theme-color" content="#16a34a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="VGS">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/icons/icon-192.png') ?>">

    <title><?= esc($title ?? 'Vehicle Gate System') ?> — VGS</title>

    <link rel="stylesheet" href="<?= base_url('vendor/fonts/inter/inter.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/bootstrap-icons/font/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/datatables/css/dataTables.bootstrap5.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/choices/css/choices.min.css') ?>">

    <style>
    :root {
        --sb-bg: #0a1628;
        --sb-bg-raised: #111f36;
        --sb-line: rgba(255, 255, 255, .06);
        --sb-text: rgba(255, 255, 255, .55);
        --sb-text-strong: #f1f5f9;
        --sb-muted: rgba(255, 255, 255, .3);
        --sb-accent: #4ade80;
        --sb-accent-deep: #16a34a;
        --sb-width: 248px;
    }

    * {
        font-family: 'Inter', sans-serif;
    }

    body {
        background: #f0f2f5;
    }

    /* ── Sidebar ── */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: var(--sb-width);
        height: 100vh;
        height: 100dvh;
        background: var(--sb-bg);
        display: flex;
        flex-direction: column;
        z-index: 1040;
        border-right: 1px solid var(--sb-line);
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: 1.15rem 1.25rem 1rem;
        border-bottom: 1px solid var(--sb-line);
    }

    .brand-icon {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #166534, #15803d);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .brand-title {
        color: #fff;
        font-size: .88rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .brand-sub {
        color: var(--sb-muted);
        font-size: .7rem;
    }

    .sidebar-close {
        display: none;
        margin-left: auto;
        background: none;
        border: none;
        color: var(--sb-text);
        font-size: 1.2rem;
        padding: .25rem;
        border-radius: 6px;
    }

    .sidebar-nav {
        padding: .9rem .75rem 1rem;
        flex: 1;
        overflow-y: auto;
    }

    .nav-label {
        color: var(--sb-muted);
        font-size: .7rem;
        font-weight: 500;
        padding: .35rem .5rem .4rem;
        margin-top: .9rem;
    }

    .nav-label:first-child {
        margin-top: 0;
    }

    /* Menu biasa (ringkas) */
    .nav-item-link {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--sb-text);
        font-size: .82rem;
        font-weight: 500;
        padding: .5rem .75rem;
        border-radius: 8px;
        text-decoration: none;
        margin-bottom: 2px;
        transition: background-color .15s, color .15s;
    }

    .nav-item-link i {
        font-size: .95rem;
        width: 18px;
        text-align: center;
    }

    .nav-item-link:hover {
        background: rgba(255, 255, 255, .05);
        color: rgba(255, 255, 255, .88);
    }

    .nav-item-link.active {
        background: rgba(22, 101, 52, .25);
        color: var(--sb-accent);
    }

    /* Menu utama (Personel, Kendaraan, QR Code) — lebih menonjol */
    .nav-core {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .nav-core-link {
        position: relative;
        display: flex;
        align-items: center;
        gap: .7rem;
        padding: .6rem .7rem;
        border-radius: 10px;
        text-decoration: none;
        color: var(--sb-text-strong);
        transition: background-color .15s;
    }

    .nav-core-link:hover {
        background: rgba(255, 255, 255, .05);
    }

    .nav-core-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        background: var(--sb-bg-raised);
        border: 1px solid var(--sb-line);
        color: rgba(255, 255, 255, .7);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
        transition: background-color .15s, color .15s, border-color .15s;
    }

    .nav-core-title {
        font-size: .85rem;
        font-weight: 600;
        line-height: 1.2;
    }

    .nav-core-desc {
        font-size: .7rem;
        color: var(--sb-muted);
        line-height: 1.3;
        margin-top: 1px;
    }

    .nav-core-link.active {
        background: rgba(22, 101, 52, .22);
    }

    .nav-core-link.active::before {
        content: '';
        position: absolute;
        left: -.75rem;
        top: 10px;
        bottom: 10px;
        width: 3px;
        border-radius: 0 3px 3px 0;
        background: var(--sb-accent);
    }

    .nav-core-link.active .nav-core-icon {
        background: var(--sb-accent-deep);
        border-color: var(--sb-accent-deep);
        color: #fff;
    }

    .nav-core-link.active .nav-core-title {
        color: var(--sb-accent);
    }

    /* Grup yang bisa dilipat */
    .nav-group-toggle {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        background: none;
        border: none;
        color: var(--sb-text);
        font-size: .82rem;
        font-weight: 500;
        padding: .5rem .75rem;
        border-radius: 8px;
        text-align: left;
        transition: background-color .15s, color .15s;
    }

    .nav-group-toggle i:first-child {
        font-size: .95rem;
        width: 18px;
        text-align: center;
    }

    .nav-group-toggle:hover {
        background: rgba(255, 255, 255, .05);
        color: rgba(255, 255, 255, .88);
    }

    .nav-group-toggle .chevron {
        margin-left: auto;
        font-size: .72rem;
        transition: transform .2s;
    }

    .nav-group-toggle[aria-expanded="true"] .chevron {
        transform: rotate(90deg);
    }

    .nav-group-toggle.has-active {
        color: rgba(255, 255, 255, .88);
    }

    .nav-group-items {
        margin: 2px 0 4px 1.05rem;
        padding-left: .6rem;
        border-left: 1px solid var(--sb-line);
    }

    .nav-group-items[hidden] {
        display: none;
    }

    .nav-group-items .nav-item-link {
        font-size: .8rem;
        padding: .42rem .65rem;
    }

    /* Penanda status di ujung kanan menu (misal maintenance aktif) */
    .nav-pill {
        margin-left: auto;
        font-size: .62rem;
        font-weight: 600;
        padding: 1px 7px;
        border-radius: 99px;
        background: rgba(245, 158, 11, .18);
        color: #fbbf24;
    }

    /* Titik penanda di grup Pengaturan saat maintenance aktif */
    .nav-dot-warn {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #f59e0b;
        flex-shrink: 0;
    }

    /* Banner pengingat maintenance aktif */
    .maintenance-banner {
        display: flex;
        align-items: center;
        gap: .5rem;
        background: #fef3c7;
        border-bottom: 1px solid #fde68a;
        color: #92400e;
        font-size: .8rem;
        padding: .6rem 1.5rem;
    }

    .maintenance-banner a {
        margin-left: auto;
        color: #92400e;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Keyboard focus */
    .sidebar a:focus-visible,
    .sidebar button:focus-visible,
    .topbar a:focus-visible,
    .topbar button:focus-visible {
        outline: 2px solid var(--sb-accent);
        outline-offset: 2px;
    }

    /* Footer */
    .sidebar-footer {
        padding: .75rem;
        border-top: 1px solid var(--sb-line);
    }

    .sidebar-install {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        margin-bottom: .6rem;
        padding: .55rem .75rem;
        background: rgba(22, 163, 74, .1);
        border: 1px solid rgba(22, 163, 74, .2);
        border-radius: 8px;
        font-size: .78rem;
        font-weight: 500;
        color: var(--sb-accent);
        cursor: pointer;
        transition: background-color .15s;
    }

    .sidebar-install:hover {
        background: rgba(22, 163, 74, .2);
    }

    .user-card {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, .04);
        border-radius: 10px;
        padding: .65rem .75rem;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #166534, #15803d);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: .8rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    .user-nama {
        color: #fff;
        font-size: .78rem;
        font-weight: 500;
        line-height: 1.2;
    }

    .user-role {
        color: var(--sb-muted);
        font-size: .68rem;
        text-transform: capitalize;
    }

    /* ── Main Content ── */
    .main-content {
        margin-left: var(--sb-width);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .topbar {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        padding: .85rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        position: sticky;
        top: 0;
        z-index: 50;
    }

    .topbar-left {
        display: flex;
        align-items: center;
        gap: .75rem;
        min-width: 0;
    }

    .btn-sidebar-toggle {
        display: none;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        font-size: 1.15rem;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .page-title {
        font-size: 1rem;
        font-weight: 600;
        color: #111827;
    }

    .page-breadcrumb {
        font-size: .75rem;
        color: #9ca3af;
    }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .topbar-clock {
        font-size: .78rem;
        color: #6b7280;
        white-space: nowrap;
    }

    .btn-logout {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        border-radius: 8px;
        font-size: .78rem;
        font-weight: 500;
        padding: .4rem .85rem;
        display: flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        transition: background-color .15s, color .15s;
    }

    .btn-logout:hover {
        background: #fecaca;
        color: #b91c1c;
    }

    .content-area {
        padding: 1.5rem;
        flex: 1;
    }

    /* ── Flash Messages ── */
    .toast-container-custom {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 9999;
    }

    /* ── Backdrop sidebar (mobile) ── */
    .sidebar-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10, 22, 40, .5);
        z-index: 1030;
    }

    /* ── Responsive ── */
    @media (max-width: 991.98px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform .22s ease;
        }

        .sidebar.open {
            transform: translateX(0);
            box-shadow: 8px 0 30px rgba(0, 0, 0, .25);
        }

        .sidebar-close {
            display: inline-flex;
        }

        .sidebar-backdrop.show {
            display: block;
        }

        .main-content {
            margin-left: 0;
        }

        .btn-sidebar-toggle {
            display: inline-flex;
        }

        .topbar {
            padding: .75rem 1rem;
        }

        .content-area {
            padding: 1rem;
        }
    }

    @media (max-width: 767.98px) {

        .topbar-clock,
        .page-breadcrumb {
            display: none;
        }

        .btn-logout span {
            display: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .sidebar,
        .nav-group-toggle .chevron {
            transition: none;
        }
    }

    /* ── Popup konfirmasi (pengganti confirm() bawaan browser) ── */
    .vc-backdrop {
        position: fixed;
        inset: 0;
        z-index: 1080;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(15, 23, 42, .45);
        backdrop-filter: blur(3px);
    }

    .vc-backdrop.show {
        display: flex;
    }

    .vc-dialog {
        width: 100%;
        max-width: 400px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 24px 48px rgba(15, 23, 42, .18);
        overflow: hidden;
        animation: vcPop .18s ease-out;
    }

    @keyframes vcPop {
        from {
            opacity: 0;
            transform: translateY(8px) scale(.97);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    .vc-body {
        padding: 1.6rem 1.5rem 1.25rem;
        text-align: center;
    }

    .vc-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto .9rem;
    }

    .vc-dialog[data-variant="danger"] .vc-icon {
        background: #fee2e2;
        color: #dc2626;
    }

    .vc-dialog[data-variant="warning"] .vc-icon {
        background: #fef3c7;
        color: #d97706;
    }

    .vc-dialog[data-variant="success"] .vc-icon {
        background: #dcfce7;
        color: #16a34a;
    }

    .vc-dialog[data-variant="info"] .vc-icon {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .vc-title {
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        margin: 0 0 .4rem;
    }

    .vc-message {
        font-size: .84rem;
        color: #6b7280;
        line-height: 1.6;
        margin: 0;
        white-space: pre-line;
    }

    .vc-actions {
        display: flex;
        gap: .6rem;
        padding: 1rem 1.25rem;
        background: #f9fafb;
        border-top: 1px solid #f3f4f6;
    }

    .vc-btn {
        flex: 1;
        border-radius: 10px;
        padding: .65rem 1rem;
        font-size: .84rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color .15s;
    }

    .vc-btn:focus-visible {
        outline: 2px solid #16a34a;
        outline-offset: 2px;
    }

    .vc-cancel {
        background: #fff;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .vc-cancel:hover {
        background: #f3f4f6;
    }

    .vc-ok {
        color: #fff;
        border: none;
    }

    .vc-dialog[data-variant="danger"] .vc-ok {
        background: #dc2626;
    }

    .vc-dialog[data-variant="danger"] .vc-ok:hover {
        background: #b91c1c;
    }

    .vc-dialog[data-variant="warning"] .vc-ok {
        background: #d97706;
    }

    .vc-dialog[data-variant="warning"] .vc-ok:hover {
        background: #b45309;
    }

    .vc-dialog[data-variant="success"] .vc-ok {
        background: #16a34a;
    }

    .vc-dialog[data-variant="success"] .vc-ok:hover {
        background: #15803d;
    }

    .vc-dialog[data-variant="info"] .vc-ok {
        background: #1d4ed8;
    }

    .vc-dialog[data-variant="info"] .vc-ok:hover {
        background: #1e40af;
    }

    @media (prefers-reduced-motion: reduce) {
        .vc-dialog {
            animation: none;
        }
    }
    </style>
    <?= $this->renderSection('styles') ?>
</head>

<body>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar" aria-label="Menu utama">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="bi bi-shield-lock-fill text-white" style="font-size:.9rem;"></i>
            </div>
            <div>
                <div class="brand-title">VGS</div>
                <div class="brand-sub">Vehicle Gate System</div>
            </div>
            <button type="button" class="sidebar-close" id="sidebarClose" aria-label="Tutup menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <?php if ($isAdminish): ?>

            <a href="<?= base_url('admin/dashboard') ?>"
                class="nav-item-link <?= $isActive('admin/dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <!-- Menu utama: yang paling sering dipakai -->
            <div class="nav-label">Kelola akses</div>
            <div class="nav-core">
                <a href="<?= base_url('admin/personel') ?>"
                    class="nav-core-link <?= $isActive('admin/personel') ? 'active' : '' ?>">
                    <span class="nav-core-icon"><i class="bi bi-person-badge"></i></span>
                    <span>
                        <span class="nav-core-title d-block">Personel</span>
                        <span class="nav-core-desc d-block">Data anggota pemilik kendaraan</span>
                    </span>
                </a>
                <a href="<?= base_url('admin/kendaraan') ?>"
                    class="nav-core-link <?= $isActive('admin/kendaraan') ? 'active' : '' ?>">
                    <span class="nav-core-icon"><i class="bi bi-car-front"></i></span>
                    <span>
                        <span class="nav-core-title d-block">Kendaraan</span>
                        <span class="nav-core-desc d-block">Plat nomor, jenis, dan foto</span>
                    </span>
                </a>
                <a href="<?= base_url('admin/qrcode') ?>"
                    class="nav-core-link <?= $isActive('admin/qrcode') ? 'active' : '' ?>">
                    <span class="nav-core-icon"><i class="bi bi-qr-code"></i></span>
                    <span>
                        <span class="nav-core-title d-block">QR Code</span>
                        <span class="nav-core-desc d-block">Kartu akses gerbang</span>
                    </span>
                </a>
            </div>

            <div class="nav-label">Monitoring</div>
            <a href="<?= base_url('admin/laporan') ?>"
                class="nav-item-link <?= $isActive('admin/laporan') ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-line"></i> Laporan
            </a>

            <div class="nav-label">Lainnya</div>

            <!-- Data referensi: jarang dipakai, dilipat -->
            <button type="button" class="nav-group-toggle <?= $refOpen ? 'has-active' : '' ?>"
                aria-expanded="<?= $refOpen ? 'true' : 'false' ?>" aria-controls="groupReferensi">
                <i class="bi bi-journal-bookmark"></i>
                Data referensi
                <i class="bi bi-chevron-right chevron"></i>
            </button>
            <div class="nav-group-items" id="groupReferensi" <?= $refOpen ? '' : 'hidden' ?>>
                <a href="<?= base_url('admin/pangkat') ?>"
                    class="nav-item-link <?= $isActive('admin/pangkat') ? 'active' : '' ?>">
                    <i class="bi bi-award"></i> Pangkat
                </a>
                <a href="<?= base_url('admin/satker') ?>"
                    class="nav-item-link <?= $isActive('admin/satker') ? 'active' : '' ?>">
                    <i class="bi bi-building"></i> Satuan Kerja
                </a>
            </div>

            <!-- Pengaturan: jarang dipakai, dilipat -->
            <button type="button" class="nav-group-toggle <?= $settingsOpen ? 'has-active' : '' ?>"
                aria-expanded="<?= $settingsOpen ? 'true' : 'false' ?>" aria-controls="groupPengaturan">
                <i class="bi bi-gear"></i>
                Pengaturan
                <?php if ($isSuper && $maintenanceOn): ?>
                <span class="nav-dot-warn" title="Mode maintenance aktif"></span>
                <?php endif; ?>
                <i class="bi bi-chevron-right chevron"></i>
            </button>
            <div class="nav-group-items" id="groupPengaturan" <?= $settingsOpen ? '' : 'hidden' ?>>
                <a href="<?= base_url('admin/users') ?>"
                    class="nav-item-link <?= $isActive('admin/users') ? 'active' : '' ?>">
                    <i class="bi bi-people"></i> User Petugas
                </a>
                <a href="<?= base_url('admin/backup') ?>"
                    class="nav-item-link <?= $isActive('admin/backup') ? 'active' : '' ?>">
                    <i class="bi bi-database"></i> Backup Database
                </a>

                <?php if ($isSuper): ?>
                <a href="<?= base_url('admin/admins') ?>"
                    class="nav-item-link <?= $isActive('admin/admins') ? 'active' : '' ?>">
                    <i class="bi bi-person-gear"></i> Akun Admin
                </a>
                <!-- Khusus Super Admin -->
                <a href="<?= base_url('admin/maintenance') ?>"
                    class="nav-item-link <?= $isActive('admin/maintenance') ? 'active' : '' ?>">
                    <i class="bi bi-cone-striped"></i> Mode Maintenance
                    <?php if ($maintenanceOn): ?><span class="nav-pill">Aktif</span><?php endif; ?>
                </a>
                <?php endif; ?>
            </div>

            <?php else: ?>

            <a href="<?= base_url('petugas/dashboard') ?>"
                class="nav-item-link <?= $isActive('petugas/dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="nav-label">Pemeriksaan gerbang</div>
            <div class="nav-core">
                <a href="<?= base_url('petugas/scan') ?>" class="nav-core-link <?= $scanActive ? 'active' : '' ?>">
                    <span class="nav-core-icon"><i class="bi bi-qr-code-scan"></i></span>
                    <span>
                        <span class="nav-core-title d-block">Scan QR Code</span>
                        <span class="nav-core-desc d-block">Pakai scanner di pos</span>
                    </span>
                </a>
                <a href="<?= base_url('petugas/scan/mobile') ?>"
                    class="nav-core-link <?= $scanMobileActive ? 'active' : '' ?>">
                    <span class="nav-core-icon"><i class="bi bi-phone"></i></span>
                    <span>
                        <span class="nav-core-title d-block">Mobile Scanner</span>
                        <span class="nav-core-desc d-block">Pakai kamera HP</span>
                    </span>
                </a>
            </div>

            <div class="nav-label">Riwayat</div>
            <a href="<?= base_url('petugas/laporan') ?>"
                class="nav-item-link <?= $isActive('petugas/laporan') ? 'active' : '' ?>">
                <i class="bi bi-clock-history"></i> Log Scan
            </a>

            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <!-- Install PWA — hanya tampil jika belum ter-install -->
            <button type="button" class="sidebar-install" id="btnInstallPwa" style="display:none;"
                onclick="installPwa()">
                <i class="bi bi-download"></i>
                <span>Install aplikasi</span>
            </button>

            <div class="user-card">
                <div class="user-avatar"><?= esc(strtoupper(substr($userNama, 0, 1))) ?></div>
                <div style="flex:1; min-width:0;">
                    <div class="user-nama text-truncate"><?= esc($userNama) ?></div>
                    <div class="user-role"><?= esc($roleLabel) ?></div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-left">
                <button type="button" class="btn-sidebar-toggle" id="sidebarToggle" aria-label="Buka menu"
                    aria-controls="sidebar" aria-expanded="false">
                    <i class="bi bi-list"></i>
                </button>
                <div style="min-width:0;">
                    <div class="page-title text-truncate"><?= esc($title ?? 'Dashboard') ?></div>
                    <div class="page-breadcrumb">Vehicle Gate System &rsaquo; <?= esc($title ?? 'Dashboard') ?></div>
                </div>
            </div>
            <div class="topbar-right">
                <span class="topbar-clock">
                    <i class="bi bi-clock me-1"></i>
                    <span id="clock"></span>
                </span>
                <a href="<?= base_url('auth/logout') ?>" class="btn-logout" title="Logout">
                    <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Pengingat: maintenance sedang aktif -->
        <?php if ($maintenanceOn): ?>
        <div class="maintenance-banner" role="status">
            <i class="bi bi-cone-striped"></i>
            <span>Mode maintenance aktif. Pengguna lain melihat halaman maintenance.</span>
            <?php if ($isSuper): ?>
            <a href="<?= base_url('admin/maintenance') ?>">Kelola</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success') || session()->getFlashdata('error')): ?>
        <div class="toast-container-custom">
            <div class="toast show align-items-center border-0 <?= session()->getFlashdata('success') ? 'bg-success' : 'bg-danger' ?> text-white"
                role="alert">
                <div class="d-flex">
                    <div class="toast-body" style="font-size:.85rem;">
                        <i
                            class="bi <?= session()->getFlashdata('success') ? 'bi-check-circle' : 'bi-exclamation-circle' ?> me-2"></i>
                        <?= session()->getFlashdata('success') ?? session()->getFlashdata('error') ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Content -->
        <div class="content-area">
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <!-- Popup konfirmasi global -->
    <div class="vc-backdrop" id="vcBackdrop">
        <div class="vc-dialog" id="vcDialog" role="alertdialog" aria-modal="true" aria-labelledby="vcTitle"
            aria-describedby="vcMessage" data-variant="danger">
            <div class="vc-body">
                <div class="vc-icon"><i class="bi" id="vcIcon"></i></div>
                <h2 class="vc-title" id="vcTitle"></h2>
                <p class="vc-message" id="vcMessage"></p>
            </div>
            <div class="vc-actions">
                <button type="button" class="vc-btn vc-cancel" id="vcCancel">Batal</button>
                <button type="button" class="vc-btn vc-ok" id="vcOk">Ya, lanjutkan</button>
            </div>
        </div>
    </div>

    <script src="<?= base_url('vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('vendor/datatables/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('vendor/datatables/js/dataTables.bootstrap5.min.js') ?>"></script>
    <script src="<?= base_url('vendor/choices/js/choices.min.js') ?>"></script>

    <script>
    // ── Live clock ──
    function updateClock() {
        const el = document.getElementById('clock');
        if (!el) return;
        const now = new Date();
        el.textContent =
            now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }) + ' ' + now.toLocaleTimeString('id-ID');
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── Auto hide toast ──
    setTimeout(() => {
        document.querySelectorAll('.toast').forEach(t => {
            bootstrap.Toast.getOrCreateInstance(t).hide();
        });
    }, 4000);

    // ── Popup konfirmasi ──
    // Pemakaian 1 (tanpa JavaScript): tambahkan atribut di <form>
    //   <form data-confirm="Pesan..." data-confirm-title="Judul" data-confirm-ok="Ya, hapus"
    //         data-confirm-variant="danger|warning|success|info">
    // Pemakaian 2 (JavaScript): const ok = await vgsConfirm({ title, message, okText, variant });
    (function() {
        const ICONS = {
            danger: 'bi-exclamation-triangle-fill',
            warning: 'bi-exclamation-circle-fill',
            success: 'bi-check-circle-fill',
            info: 'bi-question-circle-fill',
        };

        const backdrop = document.getElementById('vcBackdrop');
        const dialog = document.getElementById('vcDialog');
        const btnOk = document.getElementById('vcOk');
        const btnCancel = document.getElementById('vcCancel');
        let resolver = null;
        let lastFocus = null;

        function close(result) {
            backdrop.classList.remove('show');
            document.removeEventListener('keydown', onKey);
            if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
            if (resolver) {
                resolver(result);
                resolver = null;
            }
        }

        function onKey(e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                close(false);
            } else if (e.key === 'Tab') {
                // Jaga fokus tetap di dalam popup
                if (e.shiftKey && document.activeElement === btnCancel) {
                    e.preventDefault();
                    btnOk.focus();
                } else if (!e.shiftKey && document.activeElement === btnOk) {
                    e.preventDefault();
                    btnCancel.focus();
                }
            }
        }

        window.vgsConfirm = function(opts = {}) {
            const variant = ICONS[opts.variant] ? opts.variant : 'danger';

            dialog.dataset.variant = variant;
            document.getElementById('vcIcon').className = 'bi ' + ICONS[variant];
            document.getElementById('vcTitle').textContent = opts.title || 'Konfirmasi';
            document.getElementById('vcMessage').textContent = opts.message || 'Lanjutkan tindakan ini?';
            btnOk.textContent = opts.okText || 'Ya, lanjutkan';
            btnCancel.textContent = opts.cancelText || 'Batal';

            lastFocus = document.activeElement;
            backdrop.classList.add('show');
            document.addEventListener('keydown', onKey);
            // Fokus awal di "Batal" supaya Enter tidak langsung menjalankan tindakan berbahaya
            btnCancel.focus();

            return new Promise(resolve => {
                resolver = resolve;
            });
        };

        btnOk.addEventListener('click', () => close(true));
        btnCancel.addEventListener('click', () => close(false));
        backdrop.addEventListener('click', e => {
            if (e.target === backdrop) close(false);
        });

        // Form dengan atribut data-confirm otomatis memakai popup ini
        document.addEventListener('submit', async function(e) {
            const form = e.target;
            if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm')) return;

            e.preventDefault();

            const ok = await window.vgsConfirm({
                title: form.dataset.confirmTitle,
                message: form.dataset.confirm,
                okText: form.dataset.confirmOk,
                variant: form.dataset.confirmVariant,
            });

            if (ok) {
                const btn = form.querySelector('[type="submit"]');
                if (btn) btn.disabled = true; // cegah klik ganda
                HTMLFormElement.prototype.submit.call(form);
            }
        });
    })();

    // ── Sidebar: grup yang bisa dilipat ──
    document.querySelectorAll('.nav-group-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.getAttribute('aria-controls'));
            const expanded = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            target.hidden = expanded;
        });
    });

    // ── Sidebar: buka/tutup di mobile ──
    (function() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggle = document.getElementById('sidebarToggle');
        const close = document.getElementById('sidebarClose');

        function openSidebar() {
            sidebar.classList.add('open');
            backdrop.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            backdrop.classList.remove('show');
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', openSidebar);
        close.addEventListener('click', closeSidebar);
        backdrop.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) closeSidebar();
        });
    })();

    // ── Choices.js: hanya dijalankan kalau elemennya ada di halaman ──
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Choices === 'undefined') return;

        const pangkatEl = document.getElementById('pangkat_id');
        if (pangkatEl) {
            new Choices(pangkatEl, {
                searchEnabled: true,
                searchPlaceholderValue: 'Cari pangkat...',
                itemSelectText: '',
                noResultsText: 'Pangkat tidak ditemukan',
                noChoicesText: 'Tidak ada pilihan',
                shouldSort: false,
            });
        }

        const satkerEl = document.getElementById('satker_id');
        if (satkerEl) {
            new Choices(satkerEl, {
                searchEnabled: true,
                searchPlaceholderValue: 'Cari satker...',
                itemSelectText: '',
                noResultsText: 'Satker tidak ditemukan',
                noChoicesText: 'Tidak ada pilihan',
                shouldSort: false,
            });
        }
    });
    </script>

    <script>
    /* ── Register Service Worker ── */
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', async () => {
            try {
                const reg = await navigator.serviceWorker.register(
                    '<?= base_url('sw.js') ?>', {
                        scope: '/'
                    }
                );

                reg.addEventListener('updatefound', () => {
                    const newSW = reg.installing;
                    newSW.addEventListener('statechange', () => {
                        if (newSW.state === 'installed' && navigator.serviceWorker
                            .controller) {
                            showUpdateBanner();
                        }
                    });
                });

                console.log('[PWA] SW registered:', reg.scope);
            } catch (err) {
                console.warn('[PWA] SW registration failed:', err);
            }
        });
    }

    /* ── Install Prompt ── */
    let deferredPrompt = null;

    window.addEventListener('beforeinstallprompt', e => {
        e.preventDefault();
        deferredPrompt = e;
        const btn = document.getElementById('btnInstallPwa');
        if (btn) btn.style.display = 'flex';
    });

    window.addEventListener('appinstalled', () => {
        deferredPrompt = null;
        const btn = document.getElementById('btnInstallPwa');
        if (btn) btn.style.display = 'none';
        console.log('[PWA] App installed!');
    });

    async function installPwa() {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        const result = await deferredPrompt.userChoice;
        console.log('[PWA] Install choice:', result.outcome);
        deferredPrompt = null;
    }

    /* ── Update Banner ── */
    function showUpdateBanner() {
        const banner = document.createElement('div');
        banner.id = 'swUpdateBanner';
        banner.style.cssText = `
        position:fixed;bottom:1rem;left:50%;transform:translateX(-50%);
        background:#111827;color:#fff;border:1px solid rgba(255,255,255,.1);
        border-radius:12px;padding:.75rem 1.25rem;font-size:.82rem;
        display:flex;align-items:center;gap:.75rem;z-index:9999;
        box-shadow:0 8px 24px rgba(0,0,0,.3);white-space:nowrap;
    `;
        banner.innerHTML = `
        <i class="bi bi-arrow-clockwise" style="color:#16a34a;"></i>
        Versi baru tersedia
        <button onclick="location.reload()" style="
            background:#16a34a;color:#fff;border:none;border-radius:7px;
            padding:.3rem .75rem;font-size:.78rem;cursor:pointer;font-weight:600;">
            Update
        </button>
        <button onclick="this.parentElement.remove()" style="
            background:none;border:none;color:#9ca3af;cursor:pointer;font-size:1rem;">
            ✕
        </button>
    `;
        document.body.appendChild(banner);
    }
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>