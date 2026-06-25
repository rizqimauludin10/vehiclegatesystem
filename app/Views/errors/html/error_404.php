<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>404 - Halaman Tidak Ditemukan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <!-- <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"> -->

    <link rel="stylesheet" href="<?= base_url('vendor/fonts/jetbrains-mono/jetbrains-mono.css') ?>">
    <link rel="stylesheet" href="<?= base_url('vendor/fontawesome/css/all.min.css') ?>">

    <style>
    :root {

        --bg: #0b1220;
        --bg-card: rgba(17, 24, 39, .85);

        --text: #f8fafc;
        --text-muted: #94a3b8;

        --accent: #38bdf8;

        --border-md: rgba(255, 255, 255, .08);

        --success: #22c55e;
        --danger: #ef4444;

        --shadow:
            0 20px 40px rgba(0, 0, 0, .35);

        --radius: 24px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html,
    body {
        min-height: 100%;
    }

    body {

        font-family: 'Inter', sans-serif;

        background:
            radial-gradient(circle at top left,
                rgba(56, 189, 248, .08),
                transparent 35%),

            radial-gradient(circle at bottom right,
                rgba(34, 197, 94, .06),
                transparent 35%),

            var(--bg);

        color: var(--text);

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 20px;
    }

    .error404-container {
        width: 100%;
        max-width: 620px;
    }

    .error404-card {

        background: var(--bg-card);

        backdrop-filter: blur(16px);

        border: 1px solid var(--border-md);

        border-radius: var(--radius);

        padding: clamp(24px, 4vw, 42px);

        text-align: center;

        box-shadow: var(--shadow);
    }

    .system-tag {

        display: inline-flex;
        align-items: center;
        gap: 8px;

        padding: 8px 14px;

        border-radius: 999px;

        border: 1px solid rgba(56, 189, 248, .2);

        background: rgba(56, 189, 248, .08);

        color: var(--accent);

        font-size: 11px;

        font-weight: 700;

        letter-spacing: .18em;

        text-transform: uppercase;
    }

    .error404-icon {

        width: clamp(72px, 15vw, 96px);
        height: clamp(72px, 15vw, 96px);

        margin: 26px auto 0;

        border-radius: 50%;

        display: flex;
        align-items: center;
        justify-content: center;

        background: rgba(239, 68, 68, .12);

        border: 1px solid rgba(239, 68, 68, .25);

        color: var(--danger);

        font-size: clamp(30px, 5vw, 40px);
    }

    .error404-code {

        margin-top: 20px;

        font-family: 'JetBrains Mono', monospace;

        font-size: clamp(52px, 16vw, 96px);

        font-weight: 900;

        line-height: 1;

        letter-spacing: 4px;

        color: var(--accent);
    }

    .error404-title {

        margin-top: 12px;

        font-size: clamp(20px, 3vw, 28px);

        font-weight: 800;
    }

    .error404-desc {

        margin-top: 14px;

        max-width: 460px;

        margin-left: auto;
        margin-right: auto;

        color: var(--text-muted);

        line-height: 1.8;

        font-size: 14px;
    }

    .status-box {

        margin-top: 24px;

        padding: 12px 16px;

        border-radius: 14px;

        background: rgba(239, 68, 68, .08);

        border: 1px solid rgba(239, 68, 68, .15);

        color: #fca5a5;

        font-size: 13px;

        font-family: 'JetBrains Mono', monospace;
    }

    .error404-actions {

        margin-top: 28px;

        display: flex;
        justify-content: center;
        gap: 12px;

        flex-wrap: wrap;
    }

    .btn-404 {

        min-width: 170px;

        height: 48px;

        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;

        border-radius: 14px;

        text-decoration: none;

        font-size: 14px;
        font-weight: 700;

        transition: .25s ease;
    }

    .btn-404-primary {

        background: linear-gradient(135deg,
                #0ea5e9,
                #38bdf8);

        color: white;
    }

    .btn-404-primary:hover {

        transform: translateY(-2px);
    }

    .btn-404-secondary {

        background: rgba(255, 255, 255, .04);

        border: 1px solid var(--border-md);

        color: var(--text);
    }

    .btn-404-secondary:hover {

        background: rgba(255, 255, 255, .08);
    }

    .footer-note {

        margin-top: 22px;

        color: #64748b;

        font-size: 12px;
    }

    @media (max-width: 480px) {

        .error404-actions {

            flex-direction: column;
        }

        .btn-404 {

            width: 100%;
        }
    }

    @media (orientation: landscape) and (max-height: 500px) {

        body {
            padding: 12px;
        }

        .error404-card {
            padding: 24px;
        }

        .error404-code {
            font-size: 64px;
        }
    }
    </style>
</head>

<body>

    <div class="error404-container">

        <div class="error404-card">

            <div class="system-tag">
                <i class="fa-solid fa-shield-halved"></i>
                SECURITY ACCESS SYSTEM
            </div>

            <div class="error404-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div class="error404-code">
                404
            </div>

            <div class="error404-title">
                HALAMAN TIDAK DITEMUKAN
            </div>

            <div class="error404-desc">
                Resource yang Anda akses tidak tersedia,
                telah dipindahkan, atau URL yang dimasukkan tidak valid.
            </div>

            <div class="status-box">
                ERROR_CODE : RESOURCE_NOT_FOUND
            </div>

            <div class="error404-actions">

                <a href="javascript:history.back()" class="btn-404 btn-404-secondary">

                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali

                </a>

                <a href="<?= base_url() ?>" class="btn-404 btn-404-primary">

                    <i class="fa-solid fa-house"></i>
                    Dashboard

                </a>

            </div>

            <div class="footer-note">
                Security Vehicle Verification System
            </div>

        </div>

    </div>

</body>

</html>