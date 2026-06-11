<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Header -->
<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Daftar User Petugas</h5>
        <p class="page-header-sub">Kelola akun petugas yang bertugas di pos jaga</p>
    </div>
    <a href="<?= base_url('admin/users/create') ?>" class="btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i> Tambah Petugas
    </a>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <?php
        $totalAktif    = count(array_filter($petugas, fn($p) => $p['status'] === 'aktif'));
        $totalNonaktif = count($petugas) - $totalAktif;
    ?>
    <div class="col-sm-4">
        <div class="summary-card">
            <div class="summary-num"><?= count($petugas) ?></div>
            <div class="summary-label">Total Petugas</div>
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

<!-- Tabel Petugas -->
<div class="card-custom">
    <div class="card-custom-header">
        <div>
            <div class="card-custom-title">
                <i class="bi bi-people me-2 text-success"></i>User Petugas
            </div>
            <div class="card-custom-sub"><?= count($petugas) ?> petugas terdaftar</div>
        </div>
        <!-- Search -->
        <div class="search-wrap">
            <i class="bi bi-search search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari nama / username...">
        </div>
    </div>

    <?php if (empty($petugas)): ?>
    <div class="empty-state">
        <i class="bi bi-people"></i>
        <div>Belum ada petugas terdaftar</div>
        <a href="<?= base_url('admin/users/create') ?>" class="btn-primary-custom mt-3 d-inline-flex">
            Tambah Petugas Pertama
        </a>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table-custom" id="petugasTable">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Nama Petugas</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th width="160">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($petugas as $i => $p): ?>
                <tr>
                    <td class="text-muted"><?= $i + 1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-initial">
                                <?= strtoupper(substr($p['nama'], 0, 1)) ?>
                            </div>
                            <span class="fw-500"><?= esc($p['nama']) ?></span>
                        </div>
                    </td>
                    <td>
                        <code class="code-tag"><?= esc($p['username']) ?></code>
                    </td>
                    <td>
                        <?php if ($p['status'] === 'aktif'): ?>
                        <span class="badge-status aktif">
                            <span class="badge-dot"></span> Aktif
                        </span>
                        <?php else: ?>
                        <span class="badge-status nonaktif">
                            <span class="badge-dot"></span> Nonaktif
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted" style="font-size:.8rem;">
                        <?= date('d M Y', strtotime($p['created_at'])) ?>
                    </td>
                    <td>
                        <div class="action-wrap">
                            <!-- Edit -->
                            <a href="<?= base_url('admin/users/edit/' . $p['uuid']) ?>" class="btn-action edit"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <!-- Toggle Aktif/Nonaktif -->
                            <form action="<?= base_url('admin/users/toggle/' . $p['uuid']) ?>" method="POST"
                                class="d-inline"
                                onsubmit="return confirmToggle(this, '<?= $p['status'] ?>', '<?= esc($p['nama']) ?>')">
                                <?= csrf_field() ?>
                                <button type="submit"
                                    class="btn-action <?= $p['status'] === 'aktif' ? 'deactivate' : 'activate' ?>"
                                    title="<?= $p['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                    <i
                                        class="bi <?= $p['status'] === 'aktif' ? 'bi-toggle-on' : 'bi-toggle-off' ?>"></i>
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
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(22, 101, 52, .25);
}

.btn-primary-custom:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(22, 101, 52, .35);
    color: #fff;
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
    transition: border-color .15s;
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

.fw-500 {
    font-weight: 500;
}

.avatar-initial {
    width: 32px;
    height: 32px;
    border-radius: 9px;
    background: linear-gradient(135deg, #1d4ed8, #3b82f6);
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
    flex-shrink: 0;
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
    transition: all .15s;
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
// ── Live Search ───────────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', function() {
    const keyword = this.value.toLowerCase();
    const rows = document.querySelectorAll('#petugasTable tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(keyword) ? '' : 'none';
    });
});

// ── Confirm Toggle Status ─────────────────────────────────
function confirmToggle(form, currentStatus, nama) {
    const action = currentStatus === 'aktif' ? 'menonaktifkan' : 'mengaktifkan';
    const confirm = window.confirm(`Yakin ingin ${action} akun petugas "${nama}"?`);
    return confirm;
}
</script>
<?= $this->endSection() ?>