<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Edit Kendaraan</h5>
        <p class="page-header-sub">Perbarui data kendaraan</p>
    </div>
    <a href="<?= base_url('admin/kendaraan') ?>" class="btn-back">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert-error mb-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0 ps-3">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/kendaraan/update/' . $kendaraan['uuid']) ?>" method="POST"
            enctype="multipart/form-data" autocomplete="off">
            <?= csrf_field() ?>

            <!-- ── SECTION 1: Data Utama ── -->
            <div class="form-card mb-3 pb-4">
                <div class="form-card-header">
                    <i class="bi bi-car-front me-2 text-success"></i>
                    Data Utama
                    <span class="kode-preview ms-auto">
                        Kode: <strong><?= esc($kendaraan['kendaraan_kode']) ?></strong>
                    </span>
                </div>
                <div class="row g-0">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label-custom">Pemilik <span class="required">*</span></label>
                            <select name="personel_id" class="form-input" required>
                                <option value="">-- Pilih Personel --</option>
                                <?php foreach ($personelList as $p): ?>
                                <option value="<?= $p['id'] ?>"
                                    <?= old('personel_id', $kendaraan['personel_id']) == $p['id'] ? 'selected' : '' ?>>
                                    <?= esc($p['pangkat_singkat']) ?> <?= esc($p['nama']) ?> — <?= esc($p['nrp']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Plat Nomor <span class="required">*</span></label>
                            <input type="text" name="plat_nomor" class="form-input plat-input"
                                value="<?= old('plat_nomor', $kendaraan['plat_nomor']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Jenis <span class="required">*</span></label>
                            <div class="jenis-toggle-wrap">
                                <?php $sj = old('jenis', $kendaraan['jenis']); ?>
                                <label class="jenis-option <?= $sj === 'motor' ? 'selected' : '' ?>">
                                    <input type="radio" name="jenis" value="motor"
                                        <?= $sj === 'motor' ? 'checked' : '' ?>>
                                    <i class="bi bi-bicycle"></i> Motor
                                </label>
                                <label class="jenis-option <?= $sj === 'mobil' ? 'selected' : '' ?>">
                                    <input type="radio" name="jenis" value="mobil"
                                        <?= $sj === 'mobil' ? 'checked' : '' ?>>
                                    <i class="bi bi-car-front"></i> Mobil
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-custom">Merk</label>
                            <input type="text" name="merk" class="form-input"
                                value="<?= old('merk', $kendaraan['merk']) ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-custom">Model / Tipe</label>
                            <input type="text" name="model" class="form-input"
                                value="<?= old('model', $kendaraan['model']) ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label-custom">Warna</label>
                            <input type="text" name="warna" class="form-input"
                                value="<?= old('warna', $kendaraan['warna']) ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label-custom">Tahun</label>
                            <input type="number" name="tahun_kendaraan" class="form-input"
                                value="<?= old('tahun_kendaraan', $kendaraan['tahun_kendaraan']) ?>" min="1950"
                                max="<?= date('Y') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── SECTION 2: Data STNK ── -->
            <div class="form-card mb-3 pb-4">
                <div class="form-card-header">
                    <i class="bi bi-file-earmark-text me-2 text-success"></i>
                    Data STNK
                </div>
                <div class="row g-0">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Nomor Rangka</label>
                            <input type="text" name="nomor_rangka" class="form-input upper-input"
                                value="<?= old('nomor_rangka', $kendaraan['nomor_rangka']) ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Nomor Mesin</label>
                            <input type="text" name="nomor_mesin" class="form-input upper-input"
                                value="<?= old('nomor_mesin', $kendaraan['nomor_mesin']) ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Nomor STNK</label>
                            <input type="text" name="nomor_stnk" class="form-input"
                                value="<?= old('nomor_stnk', $kendaraan['nomor_stnk']) ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Nomor BPKB</label>
                            <input type="text" name="nomor_bpkb" class="form-input"
                                value="<?= old('nomor_bpkb', $kendaraan['nomor_bpkb']) ?>">
                        </div>
                    </div>
                    <!-- Foto STNK -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label-custom">
                                Foto STNK
                                <span class="hint-tag">Kosongkan jika tidak diubah</span>
                            </label>
                            <div class="foto-stnk-wrap" onclick="document.getElementById('foto_stnk').click()">
                                <?php if ($kendaraan['foto_stnk']): ?>
                                <img id="stnkPreview"
                                    src="<?= base_url('uploads/kendaraan/' . $kendaraan['foto_stnk']) ?>"
                                    class="stnk-preview" alt="">
                                <div id="stnkPlaceholder" class="foto-placeholder d-none">
                                    <i class="bi bi-file-image"></i>
                                    <div>Klik untuk ganti foto STNK</div>
                                </div>
                                <?php else: ?>
                                <img id="stnkPreview" src="" class="stnk-preview d-none" alt="">
                                <div id="stnkPlaceholder" class="foto-placeholder">
                                    <i class="bi bi-file-image"></i>
                                    <div>Klik untuk upload foto STNK</div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <input type="file" id="foto_stnk" name="foto_stnk" accept="image/*" class="d-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── SECTION 3: Galeri Foto ── -->
            <div class="form-card mb-4 pb-4">
                <div class="form-card-header">
                    <i class="bi bi-images me-2 text-success"></i>
                    Foto Kendaraan
                    <span class="hint-tag ms-2">
                        <?= count($galeri) ?>/<?= $maxFoto ?> foto tersimpan
                    </span>
                </div>
                <div class="form-group">

                    <!-- Existing Photos -->
                    <?php if (!empty($galeri)): ?>
                    <div class="existing-label">Foto Tersimpan</div>
                    <div class="galeri-existing-grid" id="existingGrid">
                        <?php foreach ($galeri as $idx => $foto): ?>
                        <div class="existing-item" id="existing-<?= $foto['uuid'] ?>">
                            <img src="<?= base_url('uploads/kendaraan/' . $foto['nama_file']) ?>" alt="">
                            <span class="preview-badge">Foto <?= $idx + 1 ?></span>
                            <button type="button" class="preview-remove"
                                onclick="deleteExistingFoto('<?= $foto['uuid'] ?>', this)">✕</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Tambah Foto Baru -->
                    <?php if ($fotoTersisa > 0): ?>
                    <div class="existing-label mt-3">
                        Tambah Foto Baru
                        <span class="hint-tag ms-1">Sisa slot: <?= $fotoTersisa ?></span>
                    </div>
                    <div class="galeri-dropzone" id="galeriDropzone"
                        onclick="document.getElementById('foto_galeri').click()">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <div class="dz-title">Klik atau drag foto ke sini</div>
                        <div class="dz-sub">Tambah hingga <?= $fotoTersisa ?> foto lagi</div>
                    </div>
                    <input type="file" id="foto_galeri" name="foto_galeri[]" accept="image/*" multiple class="d-none">
                    <div class="galeri-preview-grid" id="galeriPreviewGrid"></div>
                    <div class="foto-counter mt-2">
                        <span id="fotoCount">0</span> / <?= $fotoTersisa ?> foto baru dipilih
                    </div>
                    <?php else: ?>
                    <div class="foto-full-info">
                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                        Slot foto penuh (<?= $maxFoto ?>/<?= $maxFoto ?>).
                        Hapus foto yang ada untuk menambah yang baru.
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="<?= base_url('admin/kendaraan') ?>" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-submit">
                    <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
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
    background: #fff
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
    align-items: center;
    flex-wrap: wrap;
    gap: .5rem
}

.kode-preview {
    font-size: .75rem;
    font-weight: 400;
    color: #9ca3af
}

.kode-preview strong {
    color: #16a34a
}

.hint-tag {
    font-size: .7rem;
    font-weight: 400;
    color: #9ca3af;
    background: #f3f4f6;
    padding: 2px 7px;
    border-radius: 5px
}

.form-group {
    padding: 1rem 1.25rem 0
}

.form-label-custom {
    display: block;
    font-size: .8rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: .5rem
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

.plat-input,
.upper-input {
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 500
}

.jenis-toggle-wrap {
    display: flex;
    gap: .5rem
}

.jenis-option {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: .55rem;
    border: 1.5px solid #e5e7eb;
    border-radius: 9px;
    cursor: pointer;
    font-size: .82rem;
    font-weight: 500;
    color: #6b7280;
    transition: all .15s;
    user-select: none
}

.jenis-option input[type=radio] {
    display: none
}

.jenis-option:hover,
.jenis-option.selected {
    border-color: #16a34a;
    color: #16a34a;
    background: #f0fdf4
}

.jenis-option.selected {
    font-weight: 600
}

.foto-stnk-wrap {
    border: 2px dashed #e5e7eb;
    border-radius: 12px;
    padding: 1.25rem;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    min-height: 100px;
    display: flex;
    align-items: center;
    justify-content: center
}

.foto-stnk-wrap:hover {
    border-color: #16a34a;
    background: #f0fdf4
}

.stnk-preview {
    max-height: 200px;
    max-width: 100%;
    border-radius: 8px;
    object-fit: contain
}

.foto-placeholder i {
    font-size: 1.75rem;
    color: #d1d5db;
    display: block;
    margin-bottom: .5rem
}

.foto-placeholder div {
    font-size: .82rem;
    color: #6b7280
}

.existing-label {
    font-size: .75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: .5rem;
    display: flex;
    align-items: center;
    gap: .5rem
}

.galeri-existing-grid,
.galeri-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: .6rem;
    margin-top: .5rem
}

.existing-item,
.preview-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 9px;
    overflow: hidden;
    border: 2px solid #e5e7eb
}

.existing-item img,
.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover
}

.preview-badge {
    position: absolute;
    top: 4px;
    left: 4px;
    background: rgba(0, 0, 0, .5);
    color: #fff;
    font-size: .58rem;
    font-weight: 600;
    padding: 1px 5px;
    border-radius: 4px
}

.preview-remove {
    position: absolute;
    top: 4px;
    right: 4px;
    background: rgba(220, 38, 38, .85);
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    cursor: pointer;
    font-size: .65rem;
    display: flex;
    align-items: center;
    justify-content: center
}

.preview-remove:hover {
    background: #dc2626
}

.galeri-dropzone {
    border: 2px dashed #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    background: #fafafa
}

.galeri-dropzone:hover {
    border-color: #16a34a;
    background: #f0fdf4
}

.galeri-dropzone i {
    font-size: 1.75rem;
    color: #d1d5db;
    display: block;
    margin-bottom: .4rem
}

.dz-title {
    font-size: .82rem;
    font-weight: 500;
    color: #6b7280
}

.dz-sub {
    font-size: .72rem;
    color: #9ca3af
}

.foto-counter {
    font-size: .74rem;
    color: #9ca3af
}

.foto-full-info {
    font-size: .8rem;
    color: #166534;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 9px;
    padding: .65rem 1rem
}

.alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 9px;
    padding: .75rem 1rem;
    color: #991b1b;
    font-size: .82rem
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
    padding: .5rem 1.25rem;
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
const MAX_BARU = <?= $fotoTersisa ?>;
let selectedFiles = [];

// Jenis toggle
document.querySelectorAll('.jenis-option').forEach(label => {
    label.addEventListener('click', () => {
        document.querySelectorAll('.jenis-option').forEach(l => l.classList.remove('selected'));
        label.classList.add('selected');
    });
});

// Auto uppercase
document.querySelectorAll('.plat-input, .upper-input').forEach(el => {
    el.addEventListener('input', () => el.value = el.value.toUpperCase());
});

// Foto STNK preview
document.getElementById('foto_stnk').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('stnkPreview').src = e.target.result;
        document.getElementById('stnkPreview').classList.remove('d-none');
        document.getElementById('stnkPlaceholder').classList.add('d-none');
    };
    reader.readAsDataURL(file);
});

// Galeri baru (hanya jika ada slot tersisa)
<?php if ($fotoTersisa > 0): ?>
const galeriInput = document.getElementById('foto_galeri');
const previewGrid = document.getElementById('galeriPreviewGrid');
const dropzone = document.getElementById('galeriDropzone');
const counter = document.getElementById('fotoCount');

galeriInput.addEventListener('change', function() {
    const newFiles = Array.from(this.files);
    const remaining = MAX_BARU - selectedFiles.length;
    newFiles.slice(0, remaining).forEach(f => {
        if (f.type.startsWith('image/')) selectedFiles.push(f);
    });
    renderPreviews();
    syncFiles();
});

dropzone.addEventListener('dragover', e => {
    e.preventDefault();
    dropzone.classList.add('drag-over');
});
dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
dropzone.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.classList.remove('drag-over');
    const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
    const remaining = MAX_BARU - selectedFiles.length;
    files.slice(0, remaining).forEach(f => selectedFiles.push(f));
    renderPreviews();
    syncFiles();
});

function renderPreviews() {
    previewGrid.innerHTML = '';
    counter.textContent = selectedFiles.length;
    selectedFiles.forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = e => {
            const item = document.createElement('div');
            item.className = 'preview-item';
            item.innerHTML =
                `<img src="${e.target.result}"><span class="preview-badge">Baru ${idx+1}</span><button type="button" class="preview-remove" onclick="removeFile(${idx})">✕</button>`;
            previewGrid.appendChild(item);
        };
        reader.readAsDataURL(file);
    });
}

function removeFile(idx) {
    selectedFiles.splice(idx, 1);
    renderPreviews();
    syncFiles();
}

function syncFiles() {
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    galeriInput.files = dt.files;
}
<?php endif; ?>

// Delete foto existing via AJAX
function deleteExistingFoto(uuid, btn) {
    if (!confirm('Hapus foto ini?')) return;

    fetch('<?= base_url('admin/kendaraan/deletefoto/') ?>' + uuid, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: '<?= csrf_token() ?>=' + '<?= csrf_hash() ?>'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('existing-' + uuid).remove();
            } else {
                alert('Gagal menghapus foto.');
            }
        })
        .catch(() => alert('Terjadi kesalahan.'));
}
</script>
<?= $this->endSection() ?>