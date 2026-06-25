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
    <title><?= $title ?? 'Vehicle Gate System' ?></title>

    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"> -->

    <link rel="stylesheet" href="<?= base_url('vendor/fonts/inter/inter.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/bootstrap-icons/font/bootstrap-icons.min.css') ?>">
    <style>
    * {
        font-family: 'Inter', sans-serif;
    }

    body {
        background: #0a1628;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    /* Background pattern militer */
    body::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255, 255, 255, .03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, .03) 1px, transparent 1px);
        background-size: 40px 40px;
    }

    body::after {
        content: '';
        position: absolute;
        top: -200px;
        right: -200px;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(34, 197, 94, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.04);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 2.5rem;
        width: 100%;
        max-width: 420px;
        position: relative;
        z-index: 1;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
    }

    .logo-wrap {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #166534, #15803d);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        box-shadow: 0 8px 24px rgba(22, 101, 52, 0.4);
    }

    .login-title {
        color: #fff;
        font-size: 1.25rem;
        font-weight: 600;
        text-align: center;
        margin-bottom: 4px;
    }

    .login-subtitle {
        color: rgba(255, 255, 255, 0.45);
        font-size: 0.8rem;
        text-align: center;
        margin-bottom: 2rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .form-label {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.8rem;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        color: #fff;
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
        transition: all .2s;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.09);
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
        color: #fff;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.25);
    }

    .input-group-text {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-right: none;
        color: rgba(255, 255, 255, 0.4);
        border-radius: 10px 0 0 10px;
    }

    .input-group .form-control {
        border-left: none;
        border-radius: 0 10px 10px 0;
    }

    .btn-login {
        background: linear-gradient(135deg, #166534, #15803d);
        border: none;
        border-radius: 10px;
        color: #fff;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.7rem;
        width: 100%;
        transition: all .2s;
        box-shadow: 0 4px 15px rgba(22, 101, 52, 0.35);
    }

    .btn-login:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(22, 101, 52, 0.45);
        color: #fff;
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .alert-custom {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.25);
        border-radius: 10px;
        color: #fca5a5;
        font-size: 0.85rem;
        padding: 0.65rem 1rem;
    }

    .footer-text {
        color: rgba(255, 255, 255, 0.2);
        font-size: 0.72rem;
        text-align: center;
        margin-top: 1.75rem;
    }
    </style>
</head>

<body>
    <?= $this->renderSection('content') ?>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script> -->
    <script src="<?= base_url('vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

    <!-- <script>
    /* ── Register Service Worker ── */
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', async () => {
            try {
                const reg = await navigator.serviceWorker.register(
                    '<?= base_url('sw.js') ?>', {
                        scope: '/'
                    }
                );
                console.log('[PWA] SW registered:', reg.scope);
            } catch (err) {
                console.warn('[PWA] SW registration failed:', err);
            }
        });
    }
    </script> -->
</body>

</html>