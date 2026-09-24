<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Tambah Pangkat</h5>
        <p class="page-header-sub">Tambah data pangkat baru</p>
    </div>
    <a href="<?= base_url('admin/pangkat') ?>" class="btn-back">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="form-card">
            <div class="form-card-header">
                <i class="bi bi-award me-2 text-success"></i> Data Pangkat
            </div>

            <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert-error">
                <ul class="mb-0 ps-3">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/pangkat/store') ?>" method="POST" autocomplete="off">
                <?= csrf_field() ?>

                <div class="row g-0">
                    <!-- Nama Pangkat -->
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-label-custom">
                                Nama Pangkat <span class="required">*</span>
                            </label>
                            <input type="text" name="nama_pangkat" class="form-input" placeholder="Contoh: Letnan Dua"
                                value="<?= old('nama_pangkat') ?>" required>
                        </div>
                    </div>
                    <!-- Singkatan -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-custom">
                                Singkatan <span class="required">*</span>
                            </label>
                            <input type="text" name="pangkat_singkat" class="form-input" placeholder="Contoh: Letda"
                                value="<?= old('pangkat_singkat') ?>" required>
                        </div>
                    </div>
                    <!-- Kategori -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">
                                Kategori <span class="required">*</span>
                            </label>
                            <select name="kategori" id="kategori" class="form-input" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Militer" <?= old('kategori') === 'Militer' ? 'selected' : '' ?>>
                                    Militer
                                </option>
                                <option value="PNS" <?= old('kategori') === 'PNS' ? 'selected' : '' ?>>
                                    PNS
                                </option>
                                <option value="PPPK" <?= old('kategori') === 'PPPK' ? 'selected' : '' ?>>
                                    PPPK
                                </option>
                                <option value="PHL" <?= old('kategori') === 'PHL' ? 'selected' : '' ?>>
                                    PHL
                                </option>
                            </select>
                        </div>
                    </div>
                    <!-- Kelompok -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">
                                Kelompok <span class="required">*</span>
                            </label>
                            <select name="kelompok" id="kelompok" class="form-input" required>
                                <option value="">-- Pilih Kelompok --</option>
                                <option value="Perwira" <?= old('kelompok') === 'Perwira' ? 'selected' : '' ?>>
                                    Perwira
                                </option>
                                <option value="Bintara" <?= old('kelompok') === 'Bintara' ? 'selected' : '' ?>>
                                    Bintara
                                </option>
                                <option value="Tamtama" <?= old('kelompok') === 'Tamtama' ? 'selected' : '' ?>>
                                    Tamtama
                                </option>
                                <option value="PNS" <?= old('kelompok') === 'PNS' ? 'selected' : '' ?>>
                                    PNS
                                </option>
                                <option value="PPPK" <?= old('kelompok') === 'PPPK' ? 'selected' : '' ?>>
                                    PPPK
                                </option>
                                <option value="PHL" <?= old('kelompok') === 'PHL' ? 'selected' : '' ?>>
                                    PHL
                                </option>

                            </select>
                        </div>
                    </div>
                    <!-- Golongan Militer -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">
                                Golongan
                                <span class="hint-tag ms-1">Opsional</span>
                            </label>
                            <input type="text" name="golongan_militer" class="form-input"
                                placeholder="Contoh: IV/a atau Golongan IV" value="<?= old('golongan_militer') ?>">
                        </div>
                    </div>
                    <!-- Urutan -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">
                                Urutan <span class="required">*</span>
                                <span class="hint-tag ms-1">Angka terkecil = tertinggi</span>
                            </label>
                            <input type="number" name="urutan" class="form-input"
                                value="<?= old('urutan', $nextUrutan) ?>" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?= base_url('admin/pangkat') ?>" class="btn-cancel">
                        Batal
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-lg me-1"></i> Simpan Pangkat
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
    color: #111827;
    display: flex;
    align-items: center
}

.form-group {
    padding: 1rem 1.25rem 0
}

.form-label-custom {
    display: flex;
    align-items: center;
    font-size: .8rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px
}

.required {
    color: #ef4444;
    margin-left: 2px
}

.hint-tag {
    font-size: .68rem;
    font-weight: 400;
    color: #9ca3af;
    background: #f3f4f6;
    padding: 1px 6px;
    border-radius: 4px
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

.form-input::placeholder {
    color: #d1d5db
}

.upper-input {
    text-transform: uppercase
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
</style>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
// Auto uppercase singkatan
document.querySelector('.upper-input').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Sinkronisasi kategori → kelompok
document.getElementById('kategori').addEventListener('change', function() {
    const kelompok = document.getElementById('kelompok');
    const val = this.value;

    // Reset semua option
    [...kelompok.options].forEach(o => o.disabled = false);

    if (val === 'PNS') {
        // Kalau PNS, paksa kelompok PNS
        kelompok.value = 'PNS';
        ['Perwira', 'Bintara', 'Tamtama'].forEach(k => {
            const opt = [...kelompok.options].find(o => o.value === k);
            if (opt) opt.disabled = true;
        });
    } else if (val === 'Militer') {
        // Kalau Militer, disable PNS
        const pnsOpt = [...kelompok.options].find(o => o.value === 'PNS');
        if (pnsOpt) pnsOpt.disabled = true;
        if (kelompok.value === 'PNS') kelompok.value = '';
    } else if (val === 'PPPK') {
        // Kalau PPPK, paksa kelompok PPPK
        kelompok.value = 'PPPK';
        ['Perwira', 'Bintara', 'Tamtama', 'PNS'].forEach(k => {
            const opt = [...kelompok.options].find(o => o.value === k);
            if (opt) opt.disabled = true;
        });
    } else {
        // Kalau kosong, reset kelompok
        kelompok.value = '';
    }
});
</script>
<?= $this->endSection() ?>