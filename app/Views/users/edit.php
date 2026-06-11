<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Edit User Petugas</h5>
        <p class="page-header-sub">Perbarui data akun petugas</p>
    </div>
    <a href="<?= base_url('admin/users') ?>" class="btn-back">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="form-card">
            <div class="form-card-header">
                <i class="bi bi-pencil-square me-2 text-success"></i>
                Edit: <?= esc($user['nama']) ?>
            </div>

            <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert-error">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0 ps-3">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/users/update/' . $user['uuid']) ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label-custom">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-input" value="<?= old('nama', $user['nama']) ?>"
                        required>
                </div>

                <div class="form-group">
                    <label class="form-label-custom">Username <span class="required">*</span></label>
                    <input type="text" name="username" class="form-input"
                        value="<?= old('username', $user['username']) ?>" required>
                </div>

                <!-- Divider Password -->
                <div class="pass-divider">
                    <i class="bi bi-lock me-2"></i>
                    Ganti Password
                    <span class="pass-divider-hint">(kosongkan jika tidak ingin mengubah)</span>
                </div>

                <div class="form-group">
                    <label class="form-label-custom">Password Baru</label>
                    <div class="input-password-wrap">
                        <input type="password" name="password" id="password" class="form-input"
                            placeholder="Kosongkan jika tidak diubah">
                        <button type="button" class="toggle-pass" onclick="togglePassword('password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label-custom">Konfirmasi Password Baru</label>
                    <div class="input-password-wrap">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-input"
                            placeholder="Ulangi password baru">
                        <button type="button" class="toggle-pass" onclick="togglePassword('confirm_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?= base_url('admin/users') ?>" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<style>
.page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-header-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.page-header-sub {
    font-size: .78rem;
    color: #9ca3af;
    margin: 2px 0 0;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    font-size: .82rem;
    color: #6b7280;
    text-decoration: none;
    padding: .45rem .9rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #fff;
    transition: all .15s;
}

.btn-back:hover {
    background: #f9fafb;
    color: #374151;
}

.form-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.form-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
    font-size: .88rem;
    font-weight: 600;
    color: #111827;
}

.form-group {
    padding: 1rem 1.25rem 0;
}

.form-label-custom {
    display: block;
    font-size: .8rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
}

.required {
    color: #ef4444;
}

.form-input {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 9px;
    padding: .6rem .85rem;
    font-size: .85rem;
    color: #111827;
    outline: none;
    transition: all .15s;
}

.form-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1);
}

.form-input::placeholder {
    color: #d1d5db;
}

.input-password-wrap {
    position: relative;
}

.input-password-wrap .form-input {
    padding-right: 2.5rem;
}

.toggle-pass {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 0;
    font-size: .9rem;
}

.pass-divider {
    display: flex;
    align-items: center;
    font-size: .78rem;
    font-weight: 500;
    color: #6b7280;
    padding: 1.25rem 1.25rem 0;
    border-top: 1px dashed #f3f4f6;
    margin-top: 1.25rem;
}

.pass-divider-hint {
    color: #9ca3af;
    font-weight: 400;
    margin-left: 4px;
}

.alert-error {
    margin: 1rem 1.25rem 0;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 9px;
    padding: .75rem 1rem;
    color: #991b1b;
    font-size: .82rem;
}

.form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .75rem;
    padding: 1.25rem;
    border-top: 1px solid #f3f4f6;
    margin-top: 1rem;
}

.btn-cancel {
    font-size: .82rem;
    color: #6b7280;
    text-decoration: none;
    padding: .5rem .9rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #fff;
}

.btn-submit {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: .82rem;
    font-weight: 500;
    padding: .5rem 1.1rem;
    border-radius: 9px;
    border: none;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(22, 101, 52, .25);
}

.btn-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(22, 101, 52, .35);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
<?= $this->endSection() ?>