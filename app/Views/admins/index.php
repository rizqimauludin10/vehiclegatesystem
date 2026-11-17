<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
    $totalAktif    = count(array_filter($admins, static fn ($a) => $a['status'] === 'aktif'));
    $totalNonaktif = count($admins) - $totalAktif;
?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Akun Admin</h5>
        <p class="page-header-sub">Kelola akun admin yang bertugas menginput dan mengelola data</p>
    </div>
    <a href="<?= base_url('admin/admins/create') ?>" class="btn-primary-custom">
        <i class="bi bi-person-plus me-1"></i> Tambah Admin
    </a>
</div>

<!-- Hak akses -->
<div class="role-info mb-4">
    <i class="bi bi-info-circle"></i>
    <div>
        <strong>Admin</strong> dapat mengelola personel, kendaraan, QR Code, petugas, data referensi, laporan, dan
        backup.
        Menu <strong>Akun Admin</strong> dan <strong>Mode Maintenance</strong> hanya tersedia untuk Super Admin.
    </div>
</div>

<!-- Ringkasan -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-num"><?= count($admins) ?></div>
            <div class="summary-label">Total Admin</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-num text-success"><?= $totalAktif ?></div>
            <div class="summary-label">Aktif</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-num text-danger"><?= $totalNonaktif ?></div>
            <div class="summary-label">Nonaktif</div>
        </div>
    </div>
</div>

<!-- Tabel -->
<div class="card-custom">
    <div class="card-custom-header">
        <div>
            <div class="card-custom-title">
                <i class="bi bi-person-gear me-2 text-success"></i>Daftar Admin
            </div>
            <div class="card-custom-sub"><?= count($admins) ?> akun admin terdaftar</div>
        </div>
        <?php if (!empty($admins)): ?>
        <div class="search-wrap">
            <i class="bi bi-search search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari nama / username...">
        </div>
        <?php endif; ?>
    </div>

    <?php if (empty($admins)): ?>
    <div class="empty-state">
        <i class="bi bi-person-gear"></i>
        <div>Belum ada akun admin</div>
        <a href="<?= base_url('admin/admins/create') ?>" class="btn-primary-custom mt-3 d-inline-flex">
            Tambah Admin Pertama
        </a>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table-custom" id="adminTable">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Nama Admin</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Terakhir diubah</th>
                    <th width="110">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $i => $a): ?>
                <tr class="<?= $a['status'] !== 'aktif' ? 'row-muted' : '' ?>">
                    <td class="text-muted"><?= $i + 1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-initial"><?= esc(strtoupper(substr($a['nama'], 0, 1))) ?></div>
                            <span class="fw-500"><?= esc($a['nama']) ?></span>
                        </div>
                    </td>
                    <td><code class="code-tag"><?= esc($a['username']) ?></code></td>
                    <td>
                        <?php if ($a['status'] === 'aktif'): ?>
                        <span class="badge-status aktif"><span class="badge-dot"></span> Aktif</span>
                        <?php else: ?>
                        <span class="badge-status nonaktif"><span class="badge-dot"></span> Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small-date">
                        <?= $a['created_at'] ? esc(date('d M Y', strtotime($a['created_at']))) : '-' ?>
                    </td>
                    <td class="text-muted small-date">
                        <?= $a['updated_at'] ? esc(date('d M Y, H:i', strtotime($a['updated_at']))) : '-' ?>
                    </td>
                    <td>
                        <div class="action-wrap">
                            <a href="<?= base_url('admin/admins/edit/' . $a['uuid']) ?>" class="btn-action edit"
                                title="Edit / reset password">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <?php $aktif = $a['status'] === 'aktif'; ?>
                            <form action="<?= base_url('admin/admins/toggle/' . $a['uuid']) ?>" method="POST"
                                class="d-inline"
                                data-confirm-title="<?= $aktif ? 'Nonaktifkan akun admin?' : 'Aktifkan akun admin?' ?>"
                                data-confirm="<?= esc($aktif
                                    ? $a['nama'] . ' akan langsung keluar dari aplikasi dan tidak bisa login sampai akunnya diaktifkan kembali.'
                                    : $a['nama'] . ' akan bisa login dan memakai aplikasi kembali.', 'attr') ?>"
                                data-confirm-ok="<?= $aktif ? 'Ya, nonaktifkan' : 'Ya, aktifkan' ?>"
                                data-confirm-variant="<?= $aktif ? 'danger' : 'success' ?>">
                                <?= csrf_field() ?>
                                <button type="submit"
                                    class="btn-action <?= $a['status'] === 'aktif' ? 'deactivate' : 'activate' ?>"
                                    title="<?= $a['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                    <i
                                        class="bi <?= $a['status'] === 'aktif' ? 'bi-toggle-on' : 'bi-toggle-off' ?>"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
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
    box-shadow: 0 2px 8px rgba(22, 101, 52, .25);
    transition: transform .2s, box-shadow .2s;
}

.btn-primary-custom:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(22, 101, 52, .35);
    color: #fff;
}

.role-info {
    display: flex;
    align-items: flex-start;
    gap: .65rem;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e3a8a;
    border-radius: 12px;
    padding: .8rem 1rem;
    font-size: .8rem;
    line-height: 1.55;
}

.role-info i {
    font-size: 1rem;
    margin-top: 1px;
    color: #1d4ed8;
}

.summary-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 1rem 1.25rem;
    text-align: center;
}

.summary-num {
    font-size: 1.6rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

.summary-label {
    font-size: .75rem;
    color: #9ca3af;
    margin-top: 4px;
}

.card-custom {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.card-custom-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
    flex-wrap: wrap;
    gap: .75rem;
}

.card-custom-title {
    font-size: .9rem;
    font-weight: 600;
    color: #111827;
}

.card-custom-sub {
    font-size: .75rem;
    color: #9ca3af;
}

.search-wrap {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: .85rem;
}

.search-input {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .4rem .75rem .4rem 2rem;
    font-size: .82rem;
    width: 220px;
    outline: none;
}

.search-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1);
}

.table-custom {
    width: 100%;
    border-collapse: collapse;
    font-size: .83rem;
}

.table-custom thead tr {
    background: #f9fafb;
}

.table-custom th {
    padding: .65rem 1.25rem;
    font-size: .7rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
}

.table-custom td {
    padding: .75rem 1.25rem;
    color: #374151;
    border-bottom: 1px solid #f9fafb;
    vertical-align: middle;
}

.table-custom tbody tr:hover {
    background: #fafafa;
}

.table-custom tbody tr:last-child td {
    border-bottom: none;
}

.row-muted td {
    opacity: .6;
}

.small-date {
    font-size: .8rem;
    white-space: nowrap;
}

.fw-500 {
    font-weight: 500;
}

.avatar-initial {
    width: 32px;
    height: 32px;
    border-radius: 9px;
    background: linear-gradient(135deg, #166534, #16a34a);
    color: #fff;
    font-size: .8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.code-tag {
    background: #f3f4f6;
    color: #374151;
    padding: 2px 8px;
    border-radius: 5px;
    font-size: .78rem;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .72rem;
    font-weight: 500;
    padding: 3px 10px;
    border-radius: 99px;
}

.badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.badge-status.aktif {
    background: #dcfce7;
    color: #166534;
}

.badge-status.aktif .badge-dot {
    background: #16a34a;
}

.badge-status.nonaktif {
    background: #fee2e2;
    color: #991b1b;
}

.badge-status.nonaktif .badge-dot {
    background: #ef4444;
}

.action-wrap {
    display: flex;
    align-items: center;
    gap: 5px;
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
    transition: background-color .15s;
}

.btn-action.edit {
    background: #eff6ff;
    color: #1d4ed8;
}

.btn-action.edit:hover {
    background: #dbeafe;
}

.btn-action.deactivate {
    background: #fff7ed;
    color: #c2410c;
}

.btn-action.deactivate:hover {
    background: #ffedd5;
}

.btn-action.activate {
    background: #f0fdf4;
    color: #166534;
}

.btn-action.activate:hover {
    background: #dcfce7;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #9ca3af;
}

.empty-state i {
    font-size: 2.5rem;
    display: block;
    margin-bottom: .75rem;
    opacity: .4;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ── Pencarian ──
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll('#adminTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(keyword) ? '' : 'none';
        });
    });
}

// Konfirmasi aktif/nonaktif memakai popup global dari layout (atribut data-confirm di form)
</script>
<?= $this->endSection() ?>