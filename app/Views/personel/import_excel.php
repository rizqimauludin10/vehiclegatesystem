<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Import Personel via Excel</h5>
        <p class="page-header-sub">Upload file Excel untuk menambahkan data personel secara massal</p>
    </div>
    <a href="<?= base_url('admin/personel') ?>" class="btn-back">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">

        <!-- Panduan -->
        <div class="form-card mb-3 pb-3">
            <div class="form-card-header">
                <i class="bi bi-info-circle me-2 text-success"></i>
                Panduan Import
            </div>
            <div class="px-3 pt-2" style="font-size:.88rem; color:#444;">
                <ol class="mb-2 ps-3" style="line-height:2">
                    <li>Download template Excel terlebih dahulu</li>
                    <li>Isi data personel sesuai kolom yang tersedia</li>
                    <li>Kolom <strong>Pangkat</strong> diisi dengan <strong>Pangkat Singkat</strong> (lihat sheet
                        <em>Ref Pangkat</em>)</li>
                    <li>Kolom <strong>Satker</strong> diisi dengan <strong>Kode Satker</strong> (lihat sheet <em>Ref
                            Satker</em>)</li>
                    <li>NRP yang sudah terdaftar akan <strong>dilewati</strong> secara otomatis</li>
                    <li>Upload file lalu klik <strong>Proses Import</strong></li>
                </ol>
                <a href="<?= base_url('admin/personel/templateExcel') ?>"
                    class="btn-submit d-inline-flex align-items-center gap-2">
                    <i class="bi bi-download"></i> Download Template Excel
                </a>
            </div>
        </div>

        <!-- Form Upload -->
        <div class="form-card mb-4 pb-4">
            <div class="form-card-header">
                <i class="bi bi-file-earmark-excel me-2 text-success"></i>
                Upload File Excel
            </div>

            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-error mx-3 mt-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/personel/prosesImport') ?>" method="POST" enctype="multipart/form-data"
                autocomplete="off">
                <?= csrf_field() ?>

                <div class="form-group px-3 pt-3">
                    <label class="form-label-custom">
                        File Excel <span class="required">*</span>
                        <span class="hint-tag">.xlsx / .xls · Maks. 5MB</span>
                    </label>

                    <!-- Drop Zone -->
                    <div class="galeri-dropzone" id="excelDropzone"
                        onclick="document.getElementById('file_excel').click()">
                        <i class="bi bi-file-earmark-excel" style="font-size:2rem; color:#198754;"></i>
                        <div class="dz-title" id="dzTitle">Klik atau drag file Excel ke sini</div>
                        <div class="dz-sub" id="dzSub">.xlsx atau .xls · Maksimal 5MB</div>
                    </div>
                    <input type="file" id="file_excel" name="file_excel" accept=".xlsx,.xls" class="d-none">
                </div>

                <div class="d-flex justify-content-end gap-2 px-3 mt-3">
                    <a href="<?= base_url('admin/personel') ?>" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-submit" id="btnImport" disabled>
                        <i class="bi bi-upload me-1"></i> Proses Import
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.galeri-dropzone {
    cursor: pointer;
    border: 2px dashed #198754;
    border-radius: 10px;
    padding: 32px;
    text-align: center;
    background: #f8fff9;
    transition: background .2s;
}

.galeri-dropzone:hover {
    background: #edfbf1;
}

.dz-title {
    font-weight: 600;
    margin-top: 8px;
}

.dz-sub {
    font-size: .82rem;
    color: #888;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const fileInput = document.getElementById('file_excel');
const btnImport = document.getElementById('btnImport');
const dzTitle = document.getElementById('dzTitle');
const dzSub = document.getElementById('dzSub');
const dropzone = document.getElementById('excelDropzone');

fileInput.addEventListener('change', function() {
    if (this.files.length > 0) {
        const f = this.files[0];
        dzTitle.textContent = f.name;
        dzSub.textContent = (f.size / 1024).toFixed(1) + ' KB';
        btnImport.disabled = false;
        dropzone.style.borderColor = '#0d6efd';
        dropzone.style.background = '#f0f4ff';
    }
});

// Drag & drop support
dropzone.addEventListener('dragover', e => {
    e.preventDefault();
    dropzone.style.background = '#edfbf1';
});
dropzone.addEventListener('dragleave', e => {
    dropzone.style.background = '#f8fff9';
});
dropzone.addEventListener('drop', e => {
    e.preventDefault();
    const f = e.dataTransfer.files[0];
    if (f) {
        fileInput.files = e.dataTransfer.files;
        dzTitle.textContent = f.name;
        dzSub.textContent = (f.size / 1024).toFixed(1) + ' KB';
        btnImport.disabled = false;
    }
});
</script>
<?= $this->endSection() ?>