<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Vehicle Gate System' ?> — VGS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <style>
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
        width: 240px;
        height: 100vh;
        background: #0a1628;
        display: flex;
        flex-direction: column;
        z-index: 100;
        border-right: 1px solid rgba(255, 255, 255, 0.05);
    }

    .sidebar-brand {
        padding: 1.25rem 1.25rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
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
        font-size: 0.85rem;
        font-weight: 600;
        line-height: 1.2;
    }

    .brand-sub {
        color: rgba(255, 255, 255, 0.35);
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .sidebar-nav {
        padding: 1rem 0.75rem;
        flex: 1;
        overflow-y: auto;
    }

    .nav-section-label {
        color: rgba(255, 255, 255, 0.25);
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 0.5rem 0.5rem 0.35rem;
        margin-top: 0.5rem;
    }

    .nav-link-custom {
        display: flex;
        align-items: center;
        gap: 10px;
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.82rem;
        font-weight: 500;
        padding: 0.55rem 0.75rem;
        border-radius: 8px;
        text-decoration: none;
        transition: all .15s;
        margin-bottom: 2px;
    }

    .nav-link-custom:hover {
        background: rgba(255, 255, 255, 0.06);
        color: rgba(255, 255, 255, 0.85);
    }

    .nav-link-custom.active {
        background: rgba(22, 101, 52, 0.25);
        color: #4ade80;
        border: 1px solid rgba(22, 101, 52, 0.3);
    }

    .nav-link-custom i {
        font-size: 1rem;
        width: 18px;
        text-align: center;
    }

    .sidebar-footer {
        padding: 0.75rem;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .user-card {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 10px;
        padding: 0.65rem 0.75rem;
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
        font-size: 0.8rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    .user-nama {
        color: #fff;
        font-size: 0.78rem;
        font-weight: 500;
        line-height: 1.2;
    }

    .user-role {
        color: rgba(255, 255, 255, 0.35);
        font-size: 0.68rem;
        text-transform: capitalize;
    }

    /* ── Main Content ── */
    .main-content {
        margin-left: 240px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .topbar {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        padding: 0.85rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 50;
    }

    .page-title {
        font-size: 1rem;
        font-weight: 600;
        color: #111827;
    }

    .page-breadcrumb {
        font-size: 0.75rem;
        color: #9ca3af;
    }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-logout {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 500;
        padding: 0.4rem 0.85rem;
        display: flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        transition: all .15s;
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
    </style>
    <?= $this->renderSection('styles') ?>
</head>

<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand d-flex align-items-center gap-2">
            <div class="brand-icon">
                <i class="bi bi-shield-lock-fill text-white" style="font-size:.9rem;"></i>
            </div>
            <div>
                <div class="brand-title">VGS</div>
                <div class="brand-sub">Gate Monitoring</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <?php if (session()->get('role') === 'admin'): ?>

            <div class="nav-section-label">Main</div>
            <a href="<?= base_url('admin/dashboard') ?>"
                class="nav-link-custom <?= uri_string() === 'admin/dashboard' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="nav-section-label">Data Master</div>
            <a href="<?= base_url('admin/personel') ?>"
                class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/personel') ? 'active' : '' ?>">
                <i class="bi bi-person-badge"></i> Personel
            </a>
            <a href="<?= base_url('admin/pangkat') ?>"
                class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/pangkat') ? 'active' : '' ?>">
                <i class="bi bi-award"></i> Pangkat
            </a>
            <a href="<?= base_url('admin/satker') ?>"
                class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/satker') ? 'active' : '' ?>">
                <i class="bi bi-building"></i> Satuan Kerja
            </a>
            <a href="<?= base_url('admin/kendaraan') ?>"
                class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/kendaraan') ? 'active' : '' ?>">
                <i class="bi bi-car-front"></i> Kendaraan
            </a>

            <div class="nav-section-label">QR & Akses</div>
            <a href="<?= base_url('admin/qrcode') ?>"
                class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/qrcode') ? 'active' : '' ?>">
                <i class="bi bi-qr-code"></i> QR Code
            </a>
            <a href="<?= base_url('admin/users') ?>"
                class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/users') ? 'active' : '' ?>">
                <i class="bi bi-people"></i> User Petugas
            </a>

            <div class="nav-section-label">Monitoring</div>
            <a href="<?= base_url('admin/laporan') ?>"
                class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/laporan') ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-line"></i> Laporan
            </a>

            <?php else: ?>

            <div class="nav-section-label">Menu</div>
            <a href="<?= base_url('petugas/dashboard') ?>"
                class="nav-link-custom <?= uri_string() === 'petugas/dashboard' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="<?= base_url('petugas/scan') ?>"
                class="nav-link-custom <?= str_starts_with(uri_string(), 'petugas/scan') ? 'active' : '' ?>">
                <i class="bi bi-qr-code-scan"></i> Scan QR Code
            </a>
            <a href="<?= base_url('petugas/laporan') ?>"
                class="nav-link-custom <?= str_starts_with(uri_string(), 'petugas/laporan') ? 'active' : '' ?>">
                <i class="bi bi-clock-history"></i> Log Scan
            </a>

            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar"><?= strtoupper(substr(session()->get('user_nama'), 0, 1)) ?></div>
                <div style="flex:1; min-width:0;">
                    <div class="user-nama text-truncate"><?= session()->get('user_nama') ?></div>
                    <div class="user-role"><?= session()->get('role') ?></div>
                </div>
                <a href="<?= base_url('auth/logout') ?>" title="Logout">
                    <i class="bi bi-box-arrow-right text-danger" style="font-size:1rem;"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div>
                <div class="page-title"><?= $title ?? 'Dashboard' ?></div>
                <div class="page-breadcrumb">Vehicle Gate System &rsaquo; <?= $title ?? 'Dashboard' ?></div>
            </div>
            <div class="topbar-right">
                <span style="font-size:.78rem; color:#6b7280;">
                    <i class="bi bi-clock me-1"></i>
                    <span id="clock"></span>
                </span>
                <a href="<?= base_url('auth/logout') ?>" class="btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
    // Live clock
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent =
            now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }) +
            ' ' + now.toLocaleTimeString('id-ID');
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Auto hide toast
    setTimeout(() => {
        document.querySelectorAll('.toast').forEach(t => {
            bootstrap.Toast.getOrCreateInstance(t).hide();
        });
    }, 4000);
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>