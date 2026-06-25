<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Detail Personel</h5>
        <p class="page-header-sub">Informasi lengkap personel dan kendaraan terdaftar</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/personel/edit/' . $personel['uuid']) ?>" class="btn-edit">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="<?= base_url('admin/personel') ?>" class="btn-back">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <!-- Kartu Identitas Personel -->
    <div class="col-lg-4">
        <div class="profile-card">
            <div class="profile-photo-wrap">
                <?php if ($personel['foto']): ?>
                <img src="<?= base_url('uploads/personel/' . $personel['foto']) ?>" class="profile-photo" alt="">
                <?php else: ?>
                <div class="profile-photo-placeholder">
                    <?= strtoupper(substr($personel['nama'], 0, 1)) ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="profile-name"><?= esc($personel['nama']) ?></div>
            <div class="profile-pangkat">
                <?= esc($personel['pangkat_singkat']) ?>
            </div>
            <div class="profile-nrp">NRP: <?= esc($personel['nrp']) ?></div>

            <!-- Status Badge -->
            <div class="mt-3">
                <?php if ($personel['status'] === 'aktif'): ?>
                <span class="badge-status aktif"><span class="badge-dot"></span> Aktif</span>
                <?php else: ?>
                <span class="badge-status nonaktif"><span class="badge-dot"></span> Nonaktif</span>
                <?php endif; ?>
            </div>

            <!-- Status QR — per kendaraan -->
            <div class="qr-status-wrap mt-3">
                <?php 
                    $totalQrAktif = count($qrPerKendaraan);
                    $totalKendaraan = count($personel['kendaraan']);
                    ?>
                <?php if ($totalQrAktif > 0): ?>
                <div class="qr-status-badge has-qr">
                    <i class="bi bi-qr-code me-1"></i>
                    <?= $totalQrAktif ?>/<?= $totalKendaraan ?> QR Aktif
                </div>
                <?php else: ?>
                <div class="qr-status-badge no-qr">
                    <i class="bi bi-qr-code me-1"></i> Belum Ada QR
                </div>
                <?php endif; ?>
                <a href="<?= base_url('admin/qrcode') ?>" class="qr-link">
                    Kelola QR Code →
                </a>
            </div>

            <!-- Info Detail -->
            <div class="profile-info-list">
                <?php if ($personel['pangkat_singkat']): ?>
                <div class="profile-info-item">
                    <span class="info-label"><i class="bi bi-award me-1"></i>Pangkat</span>
                    <span class="info-val"><?= esc($personel['pangkat_singkat']) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($personel['jabatan']): ?>
                <div class="profile-info-item">
                    <span class="info-label"><i class="bi bi-briefcase me-1"></i>Jabatan</span>
                    <span class="info-val"><?= esc($personel['jabatan']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($personel['satker_singkat'])): ?>
                <div class="profile-info-item">
                    <span class="info-label">
                        <i class="bi bi-building me-1"></i>Satker
                    </span>
                    <span class="info-val">
                        <?= esc($personel['satker_singkat']) ?>
                    </span>
                </div>
                <?php endif; ?>
                <div class="profile-info-item">
                    <span class="info-label"><i class="bi bi-calendar me-1"></i>Terdaftar</span>
                    <span class="info-val"><?= date('d M Y', strtotime($personel['created_at'])) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Kendaraan -->
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-custom-header">
                <div>
                    <div class="card-custom-title">
                        <i class="bi bi-car-front me-2 text-success"></i>
                        Kendaraan Terdaftar
                    </div>
                    <div class="card-custom-sub">
                        <?= count($personel['kendaraan']) ?> kendaraan milik personel ini
                    </div>
                </div>
                <a href="<?= base_url('admin/kendaraan/create?personel=' . $personel['uuid']) ?>"
                    class="btn-primary-custom">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Kendaraan
                </a>
            </div>

            <?php if (empty($personel['kendaraan'])): ?>
            <div class="empty-state">
                <i class="bi bi-car-front"></i>
                <div>Belum ada kendaraan terdaftar</div>
                <a href="<?= base_url('admin/kendaraan/create?personel=' . $personel['uuid']) ?>"
                    class="btn-primary-custom mt-3 d-inline-flex">
                    Tambah Kendaraan Sekarang
                </a>
            </div>
            <?php else: ?>
            <div class="kendaraan-grid">
                <?php foreach ($personel['kendaraan'] as $k): ?>
                <div class="kendaraan-card">
                    <!-- Foto Kendaraan -->
                    <div class="kendaraan-foto-wrap">
                        <?php if ($k['foto']): ?>
                        <img src="<?= base_url('uploads/kendaraan/' . $k['foto']) ?>" class="kendaraan-foto" alt="">
                        <?php else: ?>
                        <div class="kendaraan-foto-placeholder">
                            <i class="bi <?= $k['jenis'] === 'motor' ? 'bi-bicycle' : 'bi-car-front' ?>"></i>
                        </div>
                        <?php endif; ?>
                        <span class="kendaraan-jenis-badge <?= $k['jenis'] ?>">
                            <?= ucfirst($k['jenis']) ?>
                        </span>
                    </div>

                    <!-- Info -->
                    <div class="kendaraan-info">
                        <div class="kendaraan-plat"><?= esc($k['plat_nomor']) ?></div>
                        <div class="kendaraan-detail">
                            <?= esc($k['merk']) ?> <?= esc($k['model']) ?>
                        </div>
                        <div class="kendaraan-meta">
                            <span><i class="bi bi-palette me-1"></i><?= esc($k['warna']) ?></span>
                            <span><code><?= esc($k['kendaraan_kode']) ?></code></span>
                        </div>
                        <div class="mt-2">
                            <?php if ($k['status'] === 'aktif'): ?>
                            <span class="badge-status aktif"><span class="badge-dot"></span> Aktif</span>
                            <?php else: ?>
                            <span class="badge-status dibekukan"><span class="badge-dot"></span> Dibekukan</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="kendaraan-actions">
                        <a href="<?= base_url('admin/kendaraan/edit/' . $k['uuid']) ?>" class="btn-action edit"
                            title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
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

.btn-back,
.btn-edit {
    display: inline-flex;
    align-items: center;
    font-size: .82rem;
    padding: .45rem .9rem;
    border-radius: 8px;
    text-decoration: none;
    transition: all .15s
}

.btn-back {
    color: #6b7280;
    border: 1px solid #e5e7eb;
    background: #fff
}

.btn-back:hover {
    background: #f9fafb
}

.btn-edit {
    color: #1d4ed8;
    border: 1px solid #dbeafe;
    background: #eff6ff
}

.btn-edit:hover {
    background: #dbeafe
}

.btn-primary-custom {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: .82rem;
    font-weight: 500;
    padding: .5rem 1rem;
    border-radius: 9px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(22, 101, 52, .25)
}

.btn-primary-custom:hover {
    transform: translateY(-1px);
    color: #fff
}

/* Profile Card */
.profile-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    padding: 1.5rem;
    text-align: center
}

.profile-photo-wrap {
    margin-bottom: 1rem
}

.profile-photo {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e5e7eb
}

.profile-photo-placeholder {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: 2.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto
}

.profile-name {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 3px
}

.profile-pangkat {
    font-size: .82rem;
    color: #6b7280;
    margin-bottom: 2px
}

.profile-nrp {
    font-size: .78rem;
    color: #9ca3af
}

.qr-status-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px
}

.qr-status-badge {
    font-size: .75rem;
    font-weight: 500;
    padding: 4px 12px;
    border-radius: 99px
}

.qr-status-badge.has-qr {
    background: #dcfce7;
    color: #166534
}

.qr-status-badge.no-qr {
    background: #fef9c3;
    color: #854d0e
}

.qr-link {
    font-size: .75rem;
    color: #16a34a;
    text-decoration: none
}

.qr-link:hover {
    text-decoration: underline
}

.profile-info-list {
    margin-top: 1rem;
    border-top: 1px solid #f3f4f6;
    padding-top: 1rem;
    text-align: left
}

.profile-info-item {
    display: flex;
    justify-content: space-between;
    font-size: .78rem;
    margin-bottom: 8px
}

.info-label {
    color: #9ca3af
}

.info-val {
    color: #374151;
    font-weight: 500;
    text-align: right
}

/* Kendaraan Grid */
.card-custom {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    overflow: hidden
}

.card-custom-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
    flex-wrap: wrap;
    gap: .75rem
}

.card-custom-title {
    font-size: .9rem;
    font-weight: 600;
    color: #111827
}

.card-custom-sub {
    font-size: .75rem;
    color: #9ca3af
}

.kendaraan-grid {
    display: flex;
    flex-direction: column;
    gap: 0
}

.kendaraan-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f9fafb;
    transition: background .15s
}

.kendaraan-card:hover {
    background: #fafafa
}

.kendaraan-card:last-child {
    border-bottom: none
}

.kendaraan-foto-wrap {
    position: relative;
    flex-shrink: 0
}

.kendaraan-foto {
    width: 70px;
    height: 54px;
    border-radius: 9px;
    object-fit: cover;
    border: 1px solid #e5e7eb
}

.kendaraan-foto-placeholder {
    width: 70px;
    height: 54px;
    border-radius: 9px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #9ca3af
}

.kendaraan-jenis-badge {
    position: absolute;
    bottom: 3px;
    right: 3px;
    font-size: .6rem;
    font-weight: 600;
    padding: 1px 6px;
    border-radius: 4px
}

.kendaraan-jenis-badge.motor {
    background: #ede9fe;
    color: #5b21b6
}

.kendaraan-jenis-badge.mobil {
    background: #dbeafe;
    color: #1e40af
}

.kendaraan-info {
    flex: 1;
    min-width: 0
}

.kendaraan-plat {
    font-size: .9rem;
    font-weight: 600;
    color: #111827
}

.kendaraan-detail {
    font-size: .78rem;
    color: #6b7280;
    margin-top: 1px
}

.kendaraan-meta {
    display: flex;
    align-items: center;
    gap: .75rem;
    font-size: .72rem;
    color: #9ca3af;
    margin-top: 4px
}

.kendaraan-actions {
    flex-shrink: 0
}

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .7rem;
    font-weight: 500;
    padding: 2px 9px;
    border-radius: 99px
}

.badge-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    flex-shrink: 0
}

.badge-status.aktif {
    background: #dcfce7;
    color: #166534
}

.badge-status.aktif .badge-dot {
    background: #16a34a
}

.badge-status.nonaktif {
    background: #fee2e2;
    color: #991b1b
}

.badge-status.nonaktif .badge-dot {
    background: #ef4444
}

.badge-status.dibekukan {
    background: #fff7ed;
    color: #c2410c
}

.badge-status.dibekukan .badge-dot {
    background: #f97316
}

.btn-action {
    width: 30px;
    height: 30px;
    border-radius: 7px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
    text-decoration: none;
    transition: all .15s
}

.btn-action.edit {
    background: #eff6ff;
    color: #1d4ed8
}

.btn-action.edit:hover {
    background: #dbeafe
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #9ca3af
}

.empty-state i {
    font-size: 2.5rem;
    display: block;
    margin-bottom: .75rem;
    opacity: .4
}
</style>
<?= $this->endSection() ?>