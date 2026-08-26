<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Tambah Kendaraan</h5>
        <p class="page-header-sub">Daftarkan kendaraan baru milik personel</p>
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

        <form action="<?= base_url('admin/kendaraan/store') ?>" method="POST" enctype="multipart/form-data"
            autocomplete="off">
            <?= csrf_field() ?>

            <!-- ── SECTION 1: Data Utama ── -->
            <div class="form-card mb-3 pb-4">
                <div class="form-card-header">
                    <i class="bi bi-car-front me-2 text-success"></i>
                    Data Utama Kendaraan
                    <span class="kode-preview ms-auto">
                        Kode: <strong><?= $nextKode ?></strong>
                    </span>
                </div>
                <div class="row g-0">
                    <!-- Pemilik -->
                    <!-- Pemilik — Search by NRP -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label-custom">
                                Pemilik <span class="required">*</span>
                            </label>

                            <!-- Input NRP -->
                            <div class="nrp-search-wrap">
                                <input type="text" id="inputNRP" class="form-input"
                                    placeholder="Ketik NRP lalu tekan Enter..." autocomplete="off" maxlength="20">
                                <span id="nrpSpinner" class="nrp-spinner d-none">
                                    <i class="bi bi-arrow-repeat spin"></i>
                                </span>
                            </div>

                            <!-- Hidden field untuk dikirim ke server -->
                            <input type="hidden" name="personel_id" id="personelId" value="<?= old('personel_id') ?>">

                            <!-- Card hasil pencarian -->
                            <div id="personelCard" class="personel-card d-none">
                                <div class="personel-card-inner">
                                    <img id="pcFoto" src="" alt="" class="pc-foto">
                                    <div class="pc-info">
                                        <div class="pc-nama">
                                            <span id="pcPangkat"></span>
                                            <strong id="pcNama"></strong>
                                        </div>
                                        <div class="pc-meta">
                                            <span><i class="bi bi-person-badge me-1"></i><span id="pcNRP"></span></span>
                                            <span><i class="bi bi-building me-1"></i><span id="pcSatker"></span></span>
                                            <span><i class="bi bi-briefcase me-1"></i><span
                                                    id="pcJabatan"></span></span>
                                        </div>
                                    </div>
                                    <button type="button" class="pc-clear" onclick="clearPersonel()"
                                        title="Ganti personel">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Pesan error pencarian -->
                            <div id="nrpError" class="nrp-error d-none">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                <span id="nrpErrorMsg"></span>
                            </div>
                        </div>
                    </div>
                    <!-- Plat Nomor + Jenis -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Plat Nomor <span class="required">*</span></label>
                            <input type="text" name="plat_nomor" class="form-input plat-input"
                                placeholder="Contoh: B 1234 ABC" value="<?= old('plat_nomor') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Jenis <span class="required">*</span></label>
                            <div class="jenis-toggle-wrap">
                                <label class="jenis-option <?= old('jenis') === 'motor' ? 'selected' : '' ?>">
                                    <input type="radio" name="jenis" value="motor"
                                        <?= old('jenis') === 'motor' ? 'checked' : '' ?>>
                                    <i class="bi bi-bicycle"></i> Motor
                                </label>
                                <label class="jenis-option <?= old('jenis') === 'mobil' ? 'selected' : '' ?>">
                                    <input type="radio" name="jenis" value="mobil"
                                        <?= old('jenis') === 'mobil' ? 'checked' : '' ?>>
                                    <i class="bi bi-car-front"></i> Mobil
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- Merk, Model, Warna, Tahun -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-custom">Merk</label>
                            <input type="text" name="merk" class="form-input" placeholder="Honda"
                                value="<?= old('merk') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label-custom">Model / Tipe</label>
                            <input type="text" name="model" class="form-input" placeholder="Vario 160"
                                value="<?= old('model') ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label-custom">Warna</label>
                            <input type="text" name="warna" class="form-input" placeholder="Hitam"
                                value="<?= old('warna') ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-label-custom">Tahun</label>
                            <input type="number" name="tahun_kendaraan" class="form-input"
                                placeholder="<?= date('Y') ?>" min="1950" max="<?= date('Y') ?>"
                                value="<?= old('tahun_kendaraan') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── SECTION 2: Data STNK ── -->
            <div class="form-card mb-3 pb-4">
                <div class="form-card-header">
                    <i class="bi bi-file-earmark-text me-2 text-success"></i>
                    Data STNK / Identitas Kendaraan
                </div>
                <div class="row g-0">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Nomor Rangka</label>
                            <input type="text" name="nomor_rangka" class="form-input upper-input"
                                placeholder="Contoh: MH1JB9113MK123456" value="<?= old('nomor_rangka') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Nomor Mesin</label>
                            <input type="text" name="nomor_mesin" class="form-input upper-input"
                                placeholder="Contoh: JB91E1234567" value="<?= old('nomor_mesin') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Nomor STNK</label>
                            <input type="text" name="nomor_stnk" class="form-input" placeholder="Nomor STNK"
                                value="<?= old('nomor_stnk') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Nomor BPKB</label>
                            <input type="text" name="nomor_bpkb" class="form-input" placeholder="Nomor BPKB"
                                value="<?= old('nomor_bpkb') ?>">
                        </div>
                    </div>
                    <!-- Foto STNK -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label-custom">
                                Foto STNK
                                <span class="hint-tag">JPG / PNG / WebP · Maks. 3MB</span>
                            </label>
                            <div class="foto-stnk-wrap" onclick="document.getElementById('foto_stnk').click()">
                                <img id="stnkPreview" src="" class="stnk-preview d-none" alt="">
                                <div id="stnkPlaceholder" class="foto-placeholder">
                                    <i class="bi bi-file-image"></i>
                                    <div>Klik untuk upload foto STNK</div>
                                    <div class="foto-hint">Tampilkan seluruh halaman STNK dengan jelas</div>
                                </div>
                            </div>
                            <input type="file" id="foto_stnk" name="foto_stnk" accept="image/*" class="d-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── SECTION 3: Galeri Foto Kendaraan ── -->
            <div class="form-card mb-4 pb-4">
                <div class="form-card-header">
                    <i class="bi bi-images me-2 text-success"></i>
                    Foto Kendaraan
                    <span class="hint-tag ms-2">Maks. <?= $maxFoto ?> foto · JPG/PNG/WebP · 2MB/foto</span>
                </div>
                <div class="form-group">
                    <!-- Drop Zone -->
                    <div class="galeri-dropzone" id="galeriDropzone"
                        onclick="document.getElementById('foto_galeri').click()">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <div class="dz-title">Klik atau drag foto kendaraan ke sini</div>
                        <div class="dz-sub">
                            Tambahkan hingga <?= $maxFoto ?> foto dari berbagai sudut
                        </div>
                    </div>
                    <input type="file" id="foto_galeri" name="foto_galeri[]" accept="image/*" multiple class="d-none">

                    <!-- Preview Grid -->
                    <div class="galeri-preview-grid" id="galeriPreviewGrid"></div>

                    <div class="foto-counter mt-2">
                        <span id="fotoCount">0</span> / <?= $maxFoto ?> foto dipilih
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="<?= base_url('admin/kendaraan') ?>" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-submit">
                    <i class="bi bi-check-lg me-1"></i> Simpan Kendaraan
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

.form-input::placeholder {
    color: #d1d5db
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

/* STNK upload */
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
    color: #6b7280;
    font-weight: 500
}

.foto-hint {
    font-size: .72rem;
    color: #9ca3af;
    margin-top: 3px
}

/* Galeri dropzone */
.galeri-dropzone {
    border: 2px dashed #e5e7eb;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    background: #fafafa
}

.galeri-dropzone:hover,
.galeri-dropzone.drag-over {
    border-color: #16a34a;
    background: #f0fdf4
}

.galeri-dropzone i {
    font-size: 2rem;
    color: #d1d5db;
    display: block;
    margin-bottom: .5rem
}

.dz-title {
    font-size: .85rem;
    font-weight: 500;
    color: #6b7280
}

.dz-sub {
    font-size: .75rem;
    color: #9ca3af;
    margin-top: 3px
}

/* Preview grid */
.galeri-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: .75rem;
    margin-top: 1rem
}

.preview-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 10px;
    overflow: hidden;
    border: 2px solid #e5e7eb
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover
}

.preview-item .preview-badge {
    position: absolute;
    top: 4px;
    left: 4px;
    background: rgba(0, 0, 0, .5);
    color: #fff;
    font-size: .6rem;
    font-weight: 600;
    padding: 1px 6px;
    border-radius: 4px
}

.preview-item .preview-remove {
    position: absolute;
    top: 4px;
    right: 4px;
    background: rgba(220, 38, 38, .85);
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    cursor: pointer;
    font-size: .7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1
}

.preview-item .preview-remove:hover {
    background: #dc2626
}

.foto-counter {
    font-size: .75rem;
    color: #9ca3af
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

/* ── NRP Search ── */
.nrp-search-wrap {
    position: relative;
}

.nrp-spinner {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--bs-secondary);
}

.spin {
    animation: spin .7s linear infinite;
    display: inline-block;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.nrp-error {
    margin-top: 6px;
    font-size: .82rem;
    color: #dc3545;
}

/* ── Personel Card ── */
.personel-card {
    margin-top: 10px;
    border: 1.5px solid #198754;
    border-radius: 10px;
    background: #f0fff4;
    padding: 12px 14px;
}

.personel-card-inner {
    display: flex;
    align-items: center;
    gap: 14px;
}

.pc-foto {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #198754;
    flex-shrink: 0;
}

.pc-info {
    flex: 1;
    min-width: 0;
}

.pc-nama {
    font-size: .95rem;
    margin-bottom: 4px;
}

.pc-nama span {
    color: #555;
    margin-right: 4px;
}

.pc-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    font-size: .78rem;
    color: #555;
}

.pc-clear {
    background: none;
    border: none;
    color: #aaa;
    font-size: 1rem;
    cursor: pointer;
    padding: 4px;
    flex-shrink: 0;
}

.pc-clear:hover {
    color: #dc3545;
}
</style>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
const MAX_FOTO = <?= $maxFoto ?>;
let selectedFiles = [];

// ── Jenis Toggle ─────────────────────────────────────────
document.querySelectorAll('.jenis-option').forEach(label => {
    label.addEventListener('click', () => {
        document.querySelectorAll('.jenis-option').forEach(l => l.classList.remove('selected'));
        label.classList.add('selected');
    });
});

// ── Plat + Nomor Rangka/Mesin Auto Uppercase ─────────────
document.querySelectorAll('.plat-input, .upper-input').forEach(el => {
    el.addEventListener('input', () => el.value = el.value.toUpperCase());
});

// ── Foto STNK Preview ────────────────────────────────────
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

// ── Galeri Multi Foto ────────────────────────────────────
const galeriInput = document.getElementById('foto_galeri');
const previewGrid = document.getElementById('galeriPreviewGrid');
const dropzone = document.getElementById('galeriDropzone');
const counter = document.getElementById('fotoCount');

galeriInput.addEventListener('change', function() {
    const newFiles = Array.from(this.files);
    const remaining = MAX_FOTO - selectedFiles.length;

    newFiles.slice(0, remaining).forEach(file => {
        if (!file.type.startsWith('image/')) return;
        selectedFiles.push(file);
    });

    renderPreviews();
    syncFilesToInput();
});

// Drag & drop
dropzone.addEventListener('dragover', e => {
    e.preventDefault();
    dropzone.classList.add('drag-over');
});
dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
dropzone.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.classList.remove('drag-over');
    const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
    const remaining = MAX_FOTO - selectedFiles.length;
    files.slice(0, remaining).forEach(f => selectedFiles.push(f));
    renderPreviews();
    syncFilesToInput();
});

function renderPreviews() {
    previewGrid.innerHTML = '';
    counter.textContent = selectedFiles.length;

    selectedFiles.forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = e => {
            const item = document.createElement('div');
            item.className = 'preview-item';
            item.innerHTML = `
                <img src="${e.target.result}" alt="">
                <span class="preview-badge">Foto ${idx + 1}</span>
                <button type="button" class="preview-remove" onclick="removeFile(${idx})">✕</button>
            `;
            previewGrid.appendChild(item);
        };
        reader.readAsDataURL(file);
    });
}

function removeFile(idx) {
    selectedFiles.splice(idx, 1);
    renderPreviews();
    syncFilesToInput();
}

function syncFilesToInput() {
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    galeriInput.files = dt.files;
}

const CARI_URL = '<?= base_url('admin/kendaraan/cariPersonel') ?>';

const inputNRP = document.getElementById('inputNRP');
const personelId = document.getElementById('personelId');
const personelCard = document.getElementById('personelCard');
const nrpError = document.getElementById('nrpError');
const nrpErrorMsg = document.getElementById('nrpErrorMsg');
const nrpSpinner = document.getElementById('nrpSpinner');

// Jika ada old('personel_id'), restore card tidak perlu karena data hilang — cukup biarkan kosong
// User harus ketik ulang NRP jika ada validasi error

inputNRP.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        cariPersonel();
    }
});

async function cariPersonel() {
    const nrp = inputNRP.value.trim();
    if (!nrp) return;

    // Reset state
    hideError();
    hideCard();
    personelId.value = '';
    nrpSpinner.classList.remove('d-none');
    inputNRP.disabled = true;

    try {
        const res = await fetch(`${CARI_URL}?nrp=${encodeURIComponent(nrp)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();

        if (data.found) {
            tampilkanCard(data.personel);
        } else {
            showError(data.message);
        }
    } catch (err) {
        showError('Terjadi kesalahan, coba lagi.');
    } finally {
        nrpSpinner.classList.add('d-none');
        inputNRP.disabled = false;
    }
}

function tampilkanCard(p) {
    document.getElementById('pcFoto').src = p.foto;
    document.getElementById('pcPangkat').textContent = p.pangkat;
    document.getElementById('pcNama').textContent = p.nama;
    document.getElementById('pcNRP').textContent = p.nrp;
    document.getElementById('pcSatker').textContent = p.satker;
    document.getElementById('pcJabatan').textContent = p.jabatan;
    personelId.value = p.id;
    personelCard.classList.remove('d-none');
    inputNRP.classList.add('d-none'); // sembunyikan input setelah ketemu
}

function clearPersonel() {
    personelId.value = '';
    inputNRP.value = '';
    inputNRP.classList.remove('d-none');
    hideCard();
    hideError();
    inputNRP.focus();
}

function showError(msg) {
    nrpErrorMsg.textContent = msg;
    nrpError.classList.remove('d-none');
}

function hideError() {
    nrpError.classList.add('d-none');
}

function hideCard() {
    personelCard.classList.add('d-none');
}
</script>
<?= $this->endSection() ?>