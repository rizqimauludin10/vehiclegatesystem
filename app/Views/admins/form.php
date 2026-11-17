<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
    $isEdit = !empty($admin);
    $action = $isEdit
        ? base_url('admin/admins/update/' . $admin['uuid'])
        : base_url('admin/admins/store');
    $errors = session()->getFlashdata('errors') ?? [];
?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title"><?= $isEdit ? 'Edit Akun Admin' : 'Tambah Akun Admin' ?></h5>
        <p class="page-header-sub">
            <?= $isEdit ? 'Perbarui data atau reset password admin' : 'Buat akun baru untuk admin yang menginput data' ?>
        </p>
    </div>
    <a href="<?= base_url('admin/admins') ?>" class="btn-back">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="form-card">
            <div class="form-card-header">
                <i class="bi <?= $isEdit ? 'bi-pencil-square' : 'bi-person-plus' ?> me-2 text-success"></i>
                <?= $isEdit ? 'Edit: ' . esc($admin['nama']) : 'Data Akun Admin' ?>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert-error">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $err): ?>
                    <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if ($isEdit && $admin['status'] !== 'aktif'): ?>
            <div class="alert-warning">
                <i class="bi bi-slash-circle me-2"></i>
                Akun ini sedang nonaktif. Perubahan tetap disimpan, tapi admin belum bisa login sampai akunnya
                diaktifkan.
            </div>
            <?php endif; ?>

            <form action="<?= $action ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label-custom" for="nama">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="nama" name="nama"
                        class="form-input <?= isset($errors['nama']) ? 'is-error' : '' ?>"
                        placeholder="Contoh: Budi Santoso" value="<?= esc(old('nama', $admin['nama'] ?? ''), 'attr') ?>"
                        required>
                </div>

                <div class="form-group">
                    <label class="form-label-custom" for="username">Username <span class="required">*</span></label>
                    <input type="text" id="username" name="username"
                        class="form-input <?= isset($errors['username']) ? 'is-error' : '' ?>"
                        placeholder="Contoh: budi.admin"
                        value="<?= esc(old('username', $admin['username'] ?? ''), 'attr') ?>" required>
                    <div class="form-hint">Huruf, angka, titik, dan underscore. Minimal 4 karakter.</div>
                </div>

                <?php if ($isEdit): ?>
                <div class="pass-divider">
                    <i class="bi bi-key me-2"></i>
                    Reset Password
                    <span class="pass-divider-hint">(kosongkan jika tidak ingin mengubah)</span>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label-custom" for="password">
                        <?= $isEdit ? 'Password Baru' : 'Password' ?>
                        <?php if (!$isEdit): ?><span class="required">*</span><?php endif; ?>
                    </label>
                    <div class="input-password-wrap">
                        <input type="password" id="password" name="password"
                            class="form-input <?= isset($errors['password']) ? 'is-error' : '' ?>"
                            placeholder="<?= $isEdit ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' ?>"
                            autocomplete="new-password" <?= $isEdit ? '' : 'required' ?>>
                        <button type="button" class="toggle-pass" data-target="password"
                            aria-label="Tampilkan password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="form-hint">Minimal 8 karakter. Gunakan kombinasi huruf dan angka.</div>
                </div>

                <div class="form-group">
                    <label class="form-label-custom" for="confirm_password">
                        Konfirmasi Password
                        <?php if (!$isEdit): ?><span class="required">*</span><?php endif; ?>
                    </label>
                    <div class="input-password-wrap">
                        <input type="password" id="confirm_password" name="confirm_password"
                            class="form-input <?= isset($errors['confirm_password']) ? 'is-error' : '' ?>"
                            placeholder="Ulangi password" autocomplete="new-password" <?= $isEdit ? '' : 'required' ?>>
                        <button type="button" class="toggle-pass" data-target="confirm_password"
                            aria-label="Tampilkan password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?= base_url('admin/admins') ?>" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-lg me-1"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Admin' ?>
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

.form-hint {
    font-size: .72rem;
    color: #9ca3af;
    margin-top: 4px;
}

.form-input {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 9px;
    padding: .6rem .85rem;
    font-size: .85rem;
    color: #111827;
    outline: none;
}

.form-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1);
}

.form-input.is-error {
    border-color: #ef4444;
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

.toggle-pass:hover {
    color: #374151;
}

.pass-divider {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    font-size: .78rem;
    font-weight: 500;
    color: #6b7280;
    padding: 1.25rem 1.25rem 0;
    border-top: 1px dashed #e5e7eb;
    margin-top: 1.25rem;
}

.pass-divider-hint {
    color: #9ca3af;
    font-weight: 400;
    margin-left: 4px;
}

.alert-error,
.alert-warning {
    margin: 1rem 1.25rem 0;
    border-radius: 9px;
    padding: .75rem 1rem;
    font-size: .82rem;
}

.alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.alert-warning {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #92400e;
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

.btn-cancel:hover {
    background: #f9fafb;
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
    box-shadow: 0 2px 8px rgba(22, 101, 52, .25);
    transition: transform .2s, box-shadow .2s;
}

.btn-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(22, 101, 52, .35);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.toggle-pass').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const icon = btn.querySelector('i');
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
});
</script>
<?= $this->endSection() ?>