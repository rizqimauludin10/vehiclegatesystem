<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Import Kendaraan via Excel</h5>
        <p class="page-header-sub">Tambahkan data kendaraan personel secara massal menggunakan file Excel</p>
    </div>
    <a href="<?= base_url('admin/kendaraan') ?>" class="btn-back">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert-box alert-error-box mb-3">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('import_errors')): ?>
        <div class="alert alert-warning alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert"
            style="border-radius:12px; background:#fffbeb; border:1px solid #fde68a;">
            <div class="d-flex align-items-center gap-2 mb-2 fw-bold" style="color:#92400e;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>Catatan Proses Import:</span>
            </div>
            <ul class="mb-0 ps-3" style="font-size: .83rem; color:#78350f;">
                <?php foreach (session()->getFlashdata('import_errors') as $err): ?>
                <li class="mb-1"><?= $err ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- STEP CARDS -->
        <div class="step-row mb-4">
            <div class="step-card">
                <div class="step-num">1</div>
                <div class="step-icon"><i class="bi bi-download"></i></div>
                <div class="step-title">Download Template</div>
                <div class="step-desc">Download file Excel template kendaraan yang sudah terformat</div>
            </div>
            <div class="step-arrow"><i class="bi bi-chevron-right"></i></div>
            <div class="step-card">
                <div class="step-num">2</div>
                <div class="step-icon"><i class="bi bi-pencil-square"></i></div>
                <div class="step-title">Isi Data</div>
                <div class="step-desc">Isi NRP Personel & data kendaraan sesuai kolom template</div>
            </div>
            <div class="step-arrow"><i class="bi bi-chevron-right"></i></div>
            <div class="step-card">
                <div class="step-num">3</div>
                <div class="step-icon"><i class="bi bi-upload"></i></div>
                <div class="step-title">Upload & Import</div>
                <div class="step-desc">Upload file dan klik tombol Proses Import</div>
            </div>
        </div>

        <div class="row g-3">

            <!-- Kolom Kiri: Panduan -->
            <div class="col-lg-5">

                <!-- Download Template -->
                <div class="info-card mb-3">
                    <div class="info-card-header">
                        <i class="bi bi-file-earmark-excel text-success me-2"></i>
                        Template Excel
                    </div>
                    <div class="info-card-body">
                        <p class="info-desc">
                            Gunakan template resmi agar format kolom sesuai dengan sistem.
                            Pastikan NRP pemilik sudah terdaftar sebelumnya di menu Personel.
                        </p>
                        <a href="<?= base_url('admin/kendaraan/templateExcel') ?>" class="btn-download">
                            <i class="bi bi-download me-2"></i>
                            Download Template
                        </a>
                    </div>
                </div>

                <!-- Ketentuan Kolom -->
                <div class="info-card mb-3">
                    <div class="info-card-header">
                        <i class="bi bi-table text-success me-2"></i>
                        Format Kolom Excel
                    </div>
                    <div class="info-card-body p-0">
                        <table class="col-table">
                            <thead>
                                <tr>
                                    <th>Kolom</th>
                                    <th>Isi</th>
                                    <th>Wajib</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="col-badge">A</span> NRP</td>
                                    <td>NRP Pemilik</td>
                                    <td><i class="bi bi-check-circle-fill text-success"></i></td>
                                </tr>
                                <tr>
                                    <td><span class="col-badge">B</span> Plat Nomor</td>
                                    <td>No Polisi (B 1234 ABC)</td>
                                    <td><i class="bi bi-check-circle-fill text-success"></i></td>
                                </tr>
                                <tr>
                                    <td><span class="col-badge">C</span> Jenis</td>
                                    <td>motor / mobil</td>
                                    <td><i class="bi bi-check-circle-fill text-success"></i></td>
                                </tr>
                                <tr>
                                    <td><span class="col-badge">D</span> Merk</td>
                                    <td>Honda / Toyota, dll</td>
                                    <td><span class="optional-tag">Opsional</span></td>
                                </tr>
                                <tr>
                                    <td><span class="col-badge">E</span> Model</td>
                                    <td>Vario 160 / Avanza</td>
                                    <td><span class="optional-tag">Opsional</span></td>
                                </tr>
                                <tr>
                                    <td><span class="col-badge">F</span> Warna</td>
                                    <td>Hitam / Putih, dll</td>
                                    <td><span class="optional-tag">Opsional</span></td>
                                </tr>
                                <tr>
                                    <td><span class="col-badge">G</span> Tahun</td>
                                    <td>Tahun pembuatan</td>
                                    <td><span class="optional-tag">Opsional</span></td>
                                </tr>
                                <tr>
                                    <td><span class="col-badge">H</span> No Rangka</td>
                                    <td>Nomor Rangka</td>
                                    <td><span class="optional-tag">Opsional</span></td>
                                </tr>
                                <tr>
                                    <td><span class="col-badge">I</span> No Mesin</td>
                                    <td>Nomor Mesin</td>
                                    <td><span class="optional-tag">Opsional</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Catatan Penting -->
                <div class="info-card notes-card">
                    <div class="info-card-header">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        Catatan Penting
                    </div>
                    <div class="info-card-body">
                        <ul class="notes-list">
                            <li>
                                <i class="bi bi-info-circle text-success"></i>
                                Kolom <strong>NRP</strong> wajib sudah terdaftar di data <strong>Personel</strong>.
                            </li>
                            <li>
                                <i class="bi bi-info-circle text-success"></i>
                                Kolom <strong>Jenis</strong> hanya boleh diisi: <code>motor</code> atau
                                <code>mobil</code>.
                            </li>
                            <li>
                                <i class="bi bi-skip-forward text-primary"></i>
                                Plat nomor yang sudah terdaftar di database akan <strong>dilewati otomatis</strong>.
                            </li>
                            <li>
                                <i class="bi bi-exclamation-triangle text-warning"></i>
                                Baris 1 adalah <strong>header</strong>, pengisian data dimulai dari baris ke-2.
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Upload -->
            <div class="col-lg-7">
                <div class="info-card h-100">
                    <div class="info-card-header">
                        <i class="bi bi-cloud-arrow-up text-success me-2"></i>
                        Upload File Excel
                    </div>
                    <div class="info-card-body">

                        <form action="<?= base_url('admin/kendaraan/prosesImport') ?>" method="POST"
                            enctype="multipart/form-data" autocomplete="off" id="formImport">
                            <?= csrf_field() ?>

                            <!-- Drop Zone -->
                            <div class="dropzone" id="dropzone" onclick="document.getElementById('file_excel').click()">
                                <div class="dropzone-icon" id="dzIcon">
                                    <i class="bi bi-file-earmark-excel"></i>
                                </div>
                                <div class="dropzone-title" id="dzTitle">
                                    Klik atau drag file ke sini
                                </div>
                                <div class="dropzone-sub" id="dzSub">
                                    Format: .xlsx atau .xls · Maksimal 5MB
                                </div>
                                <div class="dropzone-file-info d-none" id="dzFileInfo">
                                    <div class="file-info-name" id="dzFileName"></div>
                                    <div class="file-info-size" id="dzFileSize"></div>
                                </div>
                            </div>
                            <input type="file" id="file_excel" name="file_excel" accept=".xlsx,.xls" class="d-none">

                            <!-- Progress Bar (muncul saat upload) -->
                            <div class="upload-progress d-none" id="uploadProgress">
                                <div class="progress-label">
                                    <span>Mengupload file...</span>
                                    <span id="progressPct">0%</span>
                                </div>
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar-fill" id="progressBar"></div>
                                </div>
                            </div>

                            <!-- File Valid Info -->
                            <div class="file-ready d-none" id="fileReady">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span id="fileReadyText">File siap diimport</span>
                                <button type="button" class="btn-remove-file" onclick="removeFile()" title="Hapus file">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex gap-2 mt-4">
                                <a href="<?= base_url('admin/kendaraan') ?>" class="btn-cancel flex-shrink-0">
                                    Batal
                                </a>
                                <button type="submit" class="btn-import flex-grow-1" id="btnImport" disabled>
                                    <i class="bi bi-upload me-2"></i>
                                    Proses Import
                                </button>
                            </div>

                        </form>

                        <!-- Tips -->
                        <div class="tips-box mt-3">
                            <i class="bi bi-shield-check text-success me-2"></i>
                            Kode kendaraan (KND-xxxx) & UUID akan otomatis digenerate oleh sistem.
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* ── Step Row ── */
.step-row {
    display: flex;
    align-items: center;
    gap: 0;
}

.step-card {
    flex: 1;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px 14px;
    text-align: center;
    position: relative;
}

.step-num {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 22px;
    height: 22px;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: .7rem;
    font-weight: 700;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.step-icon {
    font-size: 1.6rem;
    color: #16a34a;
    margin-bottom: 6px;
    margin-top: 4px;
}

.step-title {
    font-size: .82rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 3px;
}

.step-desc {
    font-size: .72rem;
    color: #9ca3af;
    line-height: 1.4;
}

.step-arrow {
    color: #d1d5db;
    font-size: 1rem;
    padding: 0 6px;
    flex-shrink: 0;
    margin-top: 10px;
}

/* ── Info Card ── */
.info-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
}

.info-card-header {
    display: flex;
    align-items: center;
    padding: .75rem 1rem;
    font-size: .85rem;
    font-weight: 600;
    color: #111827;
    border-bottom: 1px solid #f3f4f6;
    background: #fafafa;
}

.info-card-body {
    padding: 1rem;
}

.info-desc {
    font-size: .82rem;
    color: #6b7280;
    line-height: 1.6;
    margin-bottom: 1rem;
}

/* ── Download Button ── */
.btn-download {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: .82rem;
    font-weight: 500;
    padding: .55rem 1.1rem;
    border-radius: 9px;
    text-decoration: none;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(22, 101, 52, .2);
    width: 100%;
    justify-content: center;
}

.btn-download:hover {
    transform: translateY(-1px);
    color: #fff;
    box-shadow: 0 4px 14px rgba(22, 101, 52, .3);
}

/* ── Kolom Table ── */
.col-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .78rem;
}

.col-table thead tr {
    background: #f9fafb;
}

.col-table th {
    padding: .5rem .75rem;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    font-size: .68rem;
    letter-spacing: .05em;
    border-bottom: 1px solid #f3f4f6;
}

.col-table td {
    padding: .55rem .75rem;
    color: #374151;
    border-bottom: 1px solid #f9fafb;
    vertical-align: middle;
}

.col-table tbody tr:last-child td {
    border-bottom: none;
}

.col-table tbody tr:hover {
    background: #fafafa;
}

.col-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    background: #166534;
    color: #fff;
    font-size: .68rem;
    font-weight: 700;
    border-radius: 5px;
    margin-right: 4px;
}

.optional-tag {
    font-size: .68rem;
    color: #9ca3af;
    background: #f3f4f6;
    padding: 1px 7px;
    border-radius: 99px;
}

/* ── Notes ── */
.notes-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.notes-list li {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: .8rem;
    color: #374151;
    line-height: 1.5;
}

.notes-list li i {
    font-size: .9rem;
    flex-shrink: 0;
    margin-top: 1px;
}

/* ── Dropzone ── */
.dropzone {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 36px 20px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    background: #fafafa;
    position: relative;
}

.dropzone:hover {
    border-color: #16a34a;
    background: #f0fff4;
}

.dropzone.drag-over {
    border-color: #16a34a;
    background: #f0fff4;
    transform: scale(1.01);
}

.dropzone.has-file {
    border-color: #16a34a;
    border-style: solid;
    background: #f0fff4;
}

.dropzone-icon {
    font-size: 2.8rem;
    color: #16a34a;
    margin-bottom: 10px;
    transition: transform .2s;
}

.dropzone:hover .dropzone-icon {
    transform: translateY(-3px);
}

.dropzone-title {
    font-size: .9rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}

.dropzone-sub {
    font-size: .78rem;
    color: #9ca3af;
}

.dropzone-file-info {
    margin-top: 10px;
}

.file-info-name {
    font-size: .85rem;
    font-weight: 600;
    color: #166534;
}

.file-info-size {
    font-size: .75rem;
    color: #6b7280;
    margin-top: 2px;
}

/* ── File Ready Bar ── */
.file-ready {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: .82rem;
    color: #166534;
    font-weight: 500;
}

.file-ready span {
    flex: 1;
}

.btn-remove-file {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    font-size: 1rem;
    padding: 0;
    line-height: 1;
    display: flex;
    align-items: center;
}

.btn-remove-file:hover {
    color: #dc2626;
}

/* ── Progress ── */
.upload-progress {
    margin-top: 12px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: .78rem;
    color: #6b7280;
    margin-bottom: 6px;
}

.progress-bar-wrap {
    height: 6px;
    background: #e5e7eb;
    border-radius: 99px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #166534, #16a34a);
    border-radius: 99px;
    width: 0%;
    transition: width .3s;
}

/* ── Import Button ── */
.btn-import {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: .88rem;
    font-weight: 600;
    padding: .65rem 1.25rem;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 2px 10px rgba(22, 101, 52, .25);
}

.btn-import:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(22, 101, 52, .35);
}

.btn-import:disabled {
    background: #d1d5db;
    color: #9ca3af;
    box-shadow: none;
    cursor: not-allowed;
}

/* ── Cancel Button ── */
.btn-cancel {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
    color: #6b7280;
    padding: .65rem 1.1rem;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    background: #fff;
    text-decoration: none;
    transition: all .15s;
}

.btn-cancel:hover {
    background: #f9fafb;
    color: #374151;
}

/* ── Alert Box ── */
.alert-box {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: .85rem 1rem;
    border-radius: 10px;
    font-size: .84rem;
}

.alert-error-box {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

/* ── Tips Box ── */
.tips-box {
    background: #f0fff4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: .78rem;
    color: #166534;
    display: flex;
    align-items: center;
}

/* ── Btn Back ── */
.btn-back {
    display: inline-flex;
    align-items: center;
    font-size: .82rem;
    padding: .45rem .9rem;
    border-radius: 8px;
    text-decoration: none;
    transition: all .15s;
    color: #6b7280;
    border: 1px solid #e5e7eb;
    background: #fff;
}

.btn-back:hover {
    background: #f9fafb;
    color: #374151;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const fileInput = document.getElementById('file_excel');
const btnImport = document.getElementById('btnImport');
const dzTitle = document.getElementById('dzTitle');
const dzSub = document.getElementById('dzSub');
const dzIcon = document.getElementById('dzIcon');
const dzFileInfo = document.getElementById('dzFileInfo');
const dzFileName = document.getElementById('dzFileName');
const dzFileSize = document.getElementById('dzFileSize');
const dropzone = document.getElementById('dropzone');
const fileReady = document.getElementById('fileReady');
const fileReadyText = document.getElementById('fileReadyText');

function setFile(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    if (!['xlsx', 'xls'].includes(ext)) {
        alert('Format file harus .xlsx atau .xls');
        return;
    }
    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran file maksimal 5MB.');
        return;
    }

    dropzone.classList.add('has-file');
    dzTitle.textContent = 'File terpilih';
    dzSub.classList.add('d-none');
    dzIcon.innerHTML = '<i class="bi bi-file-earmark-excel-fill" style="color:#166534;"></i>';
    dzFileInfo.classList.remove('d-none');
    dzFileName.textContent = file.name;
    dzFileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';

    fileReady.classList.remove('d-none');
    fileReadyText.textContent = `${file.name} siap diimport`;

    btnImport.disabled = false;
}

function removeFile() {
    fileInput.value = '';
    dropzone.classList.remove('has-file');
    dzTitle.textContent = 'Klik atau drag file ke sini';
    dzSub.classList.remove('d-none');
    dzIcon.innerHTML = '<i class="bi bi-file-earmark-excel"></i>';
    dzFileInfo.classList.add('d-none');
    fileReady.classList.add('d-none');
    btnImport.disabled = true;
}

fileInput.addEventListener('change', function() {
    if (this.files.length > 0) setFile(this.files[0]);
});

// Drag & drop
dropzone.addEventListener('dragover', e => {
    e.preventDefault();
    dropzone.classList.add('drag-over');
});
dropzone.addEventListener('dragleave', () => {
    dropzone.classList.remove('drag-over');
});
dropzone.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.classList.remove('drag-over');
    const f = e.dataTransfer.files[0];
    if (f) {
        const dt = new DataTransfer();
        dt.items.add(f);
        fileInput.files = dt.files;
        setFile(f);
    }
});

// Progress Bar saat submit
document.getElementById('formImport').addEventListener('submit', function() {
    if (btnImport.disabled) return;

    const progress = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const progressPct = document.getElementById('progressPct');

    progress.classList.remove('d-none');
    btnImport.disabled = true;
    btnImport.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Memproses...';

    let pct = 0;
    const interval = setInterval(() => {
        pct = Math.min(pct + Math.random() * 15, 90);
        progressBar.style.width = pct + '%';
        progressPct.textContent = Math.round(pct) + '%';
    }, 200);

    setTimeout(() => clearInterval(interval), 10000);
});
</script>
<?= $this->endSection() ?>