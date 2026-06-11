<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<div class="login-card">
    <div class="logo-wrap">
        <i class="bi bi-shield-lock-fill text-white fs-4"></i>
    </div>
    <div class="login-title">Vehicle Gate System</div>
    <div class="login-subtitle">Sistem Monitoring Kendaraan</div>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert-custom mb-3">
        <i class="bi bi-exclamation-circle me-1"></i>
        <?= session()->getFlashdata('error') ?>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success rounded-3 py-2 px-3 mb-3" style="font-size:.85rem;">
        <?= session()->getFlashdata('success') ?>
    </div>
    <?php endif; ?>

    <form action="<?= base_url('auth/login') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username"
                    value="<?= old('username') ?>" autocomplete="username" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="passwordInput" class="form-control"
                    placeholder="Masukkan password" autocomplete="current-password" required>
            </div>
        </div>

        <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>
    </form>

    <div class="footer-text">
        &copy; <?= date('Y') ?> Vehicle Gate Monitoring System &mdash; Intranet Only
    </div>
</div>

<?= $this->endSection() ?>