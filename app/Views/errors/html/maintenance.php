<?php
    $state    = $state ?? [];
    $estimasi = $state['estimasi'] ?? '± 15 - 30 Menit';
    $pesan    = trim((string) ($state['pesan'] ?? ''));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Dalam Pemeliharaan — Vehicle Gate System</title>

    <!-- Aset lokal, tetap tampil rapi walau tanpa internet -->
    <link rel="stylesheet" href="<?= base_url('vendor/fonts/inter/inter.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/bootstrap-icons/font/bootstrap-icons.min.css') ?>">

    <style>
    :root {
        --primary-green: #166534;
        --secondary-green: #16a34a;
        --bg-canvas: #f8fafc;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', system-ui, sans-serif;
        background-color: var(--bg-canvas);
        background-image:
            radial-gradient(at 0% 0%, rgba(22, 163, 74, 0.05) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(37, 99, 235, 0.05) 0px, transparent 50%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding: 1.5rem;
        color: #0f172a;
    }

    .maintenance-wrapper {
        max-width: 540px;
        width: 100%;
    }

    .maintenance-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 3rem 2.5rem;
        text-align: center;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.06);
        position: relative;
        overflow: hidden;
    }

    .maintenance-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #166534, #16a34a, #eab308);
    }

    .icon-illustration {
        width: 90px;
        height: 90px;
        background: #fef9c3;
        border: 2px solid #fef08a;
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.75rem auto;
        box-shadow: 0 10px 25px -5px rgba(234, 179, 8, 0.25);
    }

    .icon-illustration i {
        font-size: 2.75rem;
        color: #ca8a04;
        animation: pulse-gear 3s infinite ease-in-out;
    }

    @keyframes pulse-gear {

        0%,
        100% {
            transform: scale(1) rotate(0deg);
        }

        50% {
            transform: scale(1.08) rotate(15deg);
        }
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
        font-size: 0.76rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 99px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 1.25rem;
    }

    .dot-ping {
        width: 8px;
        height: 8px;
        background-color: #f59e0b;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
        animation: ping 1.5s infinite cubic-bezier(0, 0, 0.2, 1);
    }

    @keyframes ping {

        70%,
        100% {
            box-shadow: 0 0 0 8px rgba(245, 158, 11, 0);
        }
    }

    .main-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
        margin: 0 0 0.75rem;
    }

    .description {
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.6;
        margin: 0 0 2rem;
    }

    .info-box {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-around;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .info-item {
        text-align: center;
    }

    .info-label {
        font-size: 0.72rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }

    .info-val {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
    }

    .info-val i {
        margin-right: 4px;
    }

    .info-val.warn {
        color: #d97706;
    }

    .info-val.ok {
        color: #16a34a;
    }

    .info-divider {
        width: 1px;
        height: 30px;
        background: #e2e8f0;
        flex-shrink: 0;
    }

    .btn-action-group {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    @media (min-width: 480px) {
        .btn-action-group {
            flex-direction: row;
        }
    }

    .btn-primary-custom,
    .btn-secondary-custom {
        flex: 1;
        font-family: inherit;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        color: #ffffff;
        border: none;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);
    }

    .btn-primary-custom:hover {
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(22, 163, 74, 0.35);
    }

    .btn-primary-custom:disabled {
        opacity: .8;
        cursor: default;
        transform: none;
    }

    .btn-secondary-custom {
        background: #ffffff;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .btn-secondary-custom:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #94a3b8;
    }

    .btn-secondary-custom .bi-whatsapp {
        color: #16a34a;
    }

    .app-footer-brand {
        margin-top: 2rem;
        font-size: 0.78rem;
        color: #94a3b8;
        font-weight: 500;
        line-height: 1.7;
    }

    .app-footer-brand .bi-shield-check {
        color: #16a34a;
        margin-right: 4px;
    }

    .admin-login {
        display: inline-block;
        margin-top: .75rem;
        font-size: .74rem;
        color: #94a3b8;
        text-decoration: none;
    }

    .admin-login:hover {
        color: #475569;
        text-decoration: underline;
    }

    .spin-icon {
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 479.98px) {
        .maintenance-card {
            padding: 2.25rem 1.5rem;
        }

        .info-box {
            flex-direction: column;
        }

        .info-divider {
            width: 60px;
            height: 1px;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .icon-illustration i,
        .dot-ping,
        .spin-icon {
            animation: none;
        }
    }
    </style>
</head>

<body>

    <div class="maintenance-wrapper">
        <div class="maintenance-card">

            <div class="icon-illustration">
                <i class="bi bi-shield-lock-fill"></i>
            </div>

            <div class="badge-status">
                <span class="dot-ping"></span>
                <span>System under maintenance</span>
            </div>

            <h1 class="main-title">Aplikasi Gate Sedang Diperbarui</h1>
            <p class="description">
                <?php if ($pesan !== ''): ?>
                <?= nl2br(esc($pesan)) ?>
                <?php else: ?>
                Sistem <strong>Vehicle Gate System</strong> sedang dalam proses pemeliharaan server dan pembaruan
                sistem keamanan rutin. Portal gate akan segera beroperasi kembali secara normal.
                <?php endif; ?>
            </p>

            <div class="info-box">
                <div class="info-item">
                    <div class="info-label">Status Palang Gate</div>
                    <div class="info-val warn">
                        <i class="bi bi-pause-circle"></i>Standby (Manual)
                    </div>
                </div>
                <div class="info-divider"></div>
                <div class="info-item">
                    <div class="info-label">Estimasi Selesai</div>
                    <div class="info-val ok">
                        <i class="bi bi-clock-history"></i><?= esc($estimasi) ?>
                    </div>
                </div>
            </div>

            <div class="btn-action-group">
                <button type="button" class="btn-primary-custom" onclick="reloadPage(this)">
                    <i class="bi bi-arrow-clockwise" id="reloadIcon"></i>
                    <span>Cek Status Sistem</span>
                </button>
                <a href="https://wa.me/6289685191803?text=Halo%20Admin%20Pos%20Gate" target="_blank" rel="noopener"
                    class="btn-secondary-custom">
                    <i class="bi bi-whatsapp"></i>
                    <span>Hubungi IT Support</span>
                </a>
            </div>

            <div class="app-footer-brand">
                <i class="bi bi-shield-check"></i> Vehicle Gate System &bull; Versi 2.4.0 <br>
                &copy; PNS RIZQI MAULUDIN &bull; 2026
            </div>

            <a href="<?= base_url('auth/login') ?>" class="admin-login">Masuk sebagai admin</a>

        </div>
    </div>

    <script>
    function reloadPage(btn) {
        document.getElementById('reloadIcon').classList.add('spin-icon');
        btn.disabled = true;
        btn.querySelector('span').innerText = 'Memeriksa...';

        setTimeout(() => window.location.reload(), 800);
    }
    </script>
</body>

</html>