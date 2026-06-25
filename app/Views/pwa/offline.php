<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline — VGS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #0a0f1e;
        color: #f1f5f9;
        font-family: system-ui, sans-serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        text-align: center;
    }

    .offline-icon {
        width: 80px;
        height: 80px;
        background: rgba(239, 68, 68, .15);
        border: 1px solid rgba(239, 68, 68, .3);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        color: #ef4444;
        margin: 0 auto 1.5rem;
    }

    .offline-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: .5rem;
    }

    .offline-sub {
        font-size: .85rem;
        color: #64748b;
        line-height: 1.6;
        max-width: 280px;
        margin: 0 auto 2rem;
    }

    .btn-retry {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #166534, #16a34a);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: .75rem 1.5rem;
        font-size: .9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
    }

    .btn-retry:hover {
        opacity: .9;
    }

    .offline-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, .05);
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: 99px;
        padding: .35rem .85rem;
        font-size: .72rem;
        color: #64748b;
        margin-top: 1.5rem;
    }
    </style>
</head>

<body>
    <div class="offline-icon">
        <i class="bi bi-wifi-off"></i>
    </div>
    <div class="offline-title">Tidak Ada Koneksi</div>
    <div class="offline-sub">
        Perangkat sedang offline. Pastikan terhubung ke jaringan intranet untuk menggunakan aplikasi.
    </div>
    <button class="btn-retry" onclick="window.location.reload()">
        <i class="bi bi-arrow-clockwise"></i>
        Coba Lagi
    </button>
    <div class="offline-badge">
        <i class="bi bi-shield-check"></i>
        Vehicle Gate System
    </div>

    <script>
    // Auto retry setiap 5 detik
    setInterval(() => {
        fetch('/').then(() => window.location.reload()).catch(() => {});
    }, 5000);
    </script>
</body>

</html>