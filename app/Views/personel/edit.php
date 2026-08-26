<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Edit Personel</h5>
        <p class="page-header-sub">Perbarui data personel</p>
    </div>
    <a href="<?= base_url('admin/personel') ?>" class="btn-back">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="form-card">
            <div class="form-card-header">
                <i class="bi bi-pencil-square me-2 text-success"></i>
                Edit: <?= esc($personel['nama']) ?>
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

            <form action="<?= base_url('admin/personel/update/' . $personel['uuid']) ?>" method="POST"
                enctype="multipart/form-data" autocomplete="off">
                <?= csrf_field() ?>

                <div class="row g-0">
                    <!-- Foto -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label-custom">Foto Personel</label>
                            <div class="foto-upload-wrap" onclick="document.getElementById('foto').click()">
                                <?php if ($personel['foto']): ?>
                                <img id="fotoPreview" src="<?= base_url('uploads/personel/' . $personel['foto']) ?>"
                                    class="foto-preview" alt="">
                                <div id="fotoPlaceholder" class="foto-placeholder d-none">
                                    <i class="bi bi-camera"></i>
                                    <div>Klik untuk ganti foto</div>
                                </div>
                                <?php else: ?>
                                <img id="fotoPreview" src="" class="foto-preview d-none" alt="">
                                <div id="fotoPlaceholder" class="foto-placeholder">
                                    <i class="bi bi-camera"></i>
                                    <div>Klik untuk upload foto</div>
                                    <div class="foto-hint">JPG, PNG, WebP · Maks. 2MB</div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <input type="file" id="foto" name="foto" accept="image/*" class="d-none">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">NRP <span class="required">*</span></label>
                            <input type="text" name="nrp" class="form-input" value="<?= old('nrp', $personel['nrp']) ?>"
                                required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">
                                Pangkat <span class="required">*</span>
                            </label>

                            <select name="pangkat_id" id="pangkat_id" class="form-input" required>
                                <option value="">-- Pilih Pangkat --</option>

                                <?php foreach ($pangkat as $kelompok => $items): ?>
                                <optgroup label="<?= esc($kelompok) ?>">
                                    <?php foreach ($items as $row): ?>

                                    <option value="<?= $row['id'] ?>"
                                        <?= old('pangkat_id', $personel['pangkat_id']) == $row['id'] ? 'selected' : '' ?>>
                                        <?= esc($row['pangkat_singkat']) ?>
                                        — <?= esc($row['nama_pangkat']) ?>
                                    </option>

                                    <?php endforeach ?>
                                </optgroup>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label-custom">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" name="nama" class="form-input"
                                value="<?= old('nama', $personel['nama']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Jabatan</label>
                            <input type="text" name="jabatan" class="form-input"
                                value="<?= old('jabatan', $personel['jabatan']) ?>">
                        </div>
                    </div>

                    <!-- No HP -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">
                                No. HP / WhatsApp
                                <span class="hint-tag ms-1">Opsional</span>
                            </label>
                            <div class="input-wa-wrap">
                                <span class="input-wa-prefix">
                                    <i class="bi bi-whatsapp"></i> +62
                                </span>
                                <input type="text" name="no_hp" class="form-input input-wa" placeholder="81234567890"
                                    value="<?= old('no_hp', ltrim($personel['no_hp'] ?? '', '0+62')) ?>" maxlength="15">
                            </div>
                            <div class="form-hint">
                                Masukkan nomor tanpa awalan 0 atau +62
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">
                                Satuan Kerja <span class="required">*</span>
                            </label>

                            <select name="satker_id" id="satker_id" class="form-input" required>
                                <option value="">-- Pilih Satuan Kerja --</option>

                                <?php foreach ($satker as $s): ?>

                                <option value="<?= $s['id'] ?>"
                                    <?= old('satker_id', $personel['satker_id']) == $s['id'] ? 'selected' : '' ?>>
                                    <?= esc($s['kode_satker']) ?>
                                    — <?= esc($s['nama_singkat']) ?>
                                </option>

                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?= base_url('admin/personel') ?>" class="btn-cancel">Batal</a>
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
    gap: 1rem
}

.page-header-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin: 0
}

.page-header-sub {
    font-size: .78rem;
    color: #9ca3af;
    margin: 2px 0 0
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
    transition: all .15s
}

.btn-back:hover {
    background: #f9fafb
}

.form-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    overflow: hidden
}

.form-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
    font-size: .88rem;
    font-weight: 600;
    color: #111827
}

.form-group {
    padding: 1rem 1.25rem 0
}

.form-label-custom {
    display: block;
    font-size: .8rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px
}

.required {
    color: #ef4444
}

.form-input {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 9px;
    padding: .6rem .85rem;
    font-size: .85rem;
    color: #111827;
    outline: none;
    transition: all .15s
}

.form-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1)
}

.foto-upload-wrap {
    border: 2px dashed #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center
}

.foto-upload-wrap:hover {
    border-color: #16a34a;
    background: #f0fdf4
}

.foto-placeholder i {
    font-size: 2rem;
    color: #d1d5db;
    display: block;
    margin-bottom: .5rem
}

.foto-placeholder div {
    font-size: .82rem;
    color: #6b7280;
    font-weight: 500
}

.foto-hint {
    font-size: .72rem;
    color: #9ca3af;
    margin-top: 3px
}

.foto-preview {
    max-height: 160px;
    border-radius: 8px;
    object-fit: cover
}

.alert-error {
    margin: 1rem 1.25rem 0;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 9px;
    padding: .75rem 1rem;
    color: #991b1b;
    font-size: .82rem
}

.form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .75rem;
    padding: 1.25rem;
    border-top: 1px solid #f3f4f6;
    margin-top: 1rem
}

.input-wa-wrap {
    display: flex;
    align-items: center;
    border: 1px solid #e5e7eb;
    border-radius: 9px;
    overflow: hidden;
    transition: all .15s;
}

.input-wa-wrap:focus-within {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1);
}

.input-wa-prefix {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: .6rem .85rem;
    background: #f0fdf4;
    color: #16a34a;
    font-size: .82rem;
    font-weight: 600;
    border-right: 1px solid #e5e7eb;
    white-space: nowrap;
    flex-shrink: 0;
}

.input-wa {
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    flex: 1;
}

.input-wa:focus {
    border: none !important;
    box-shadow: none !important;
}

.form-hint {
    font-size: .72rem;
    color: #9ca3af;
    margin-top: 3px;
}

.btn-cancel {
    font-size: .82rem;
    color: #6b7280;
    text-decoration: none;
    padding: .5rem .9rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #fff
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
    box-shadow: 0 2px 8px rgba(22, 101, 52, .25)
}

.btn-submit:hover {
    transform: translateY(-1px)
}

select.form-input {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;

    background-image:
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M1.5 5.5L8 12l6.5-6.5' stroke='%236b7280' stroke-width='2' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");

    background-repeat: no-repeat;
    background-position: right .85rem center;
    background-size: 14px;

    padding-right: 2.5rem;
}

optgroup {
    font-weight: 600;
}
</style>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.getElementById('foto').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById('fotoPreview');
        const placeholder = document.getElementById('fotoPlaceholder');
        preview.src = e.target.result;
        preview.classList.remove('d-none');
        placeholder.classList.add('d-none');
    };
    reader.readAsDataURL(file);
});
</script>
<?= $this->endSection() ?>