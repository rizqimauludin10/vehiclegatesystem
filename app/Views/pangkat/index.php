<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Management Pangkat</h5>
        <p class="page-header-sub">Kelola data pangkat militer dan PNS</p>
    </div>
    <a href="<?= base_url('admin/pangkat/create') ?>" class="btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i> Tambah Pangkat
    </a>
</div>

<!-- Summary -->
<div class="row g-3 mb-4">
    <?php
        $totalMiliter = count(array_filter($pangkat, fn($p) => $p['kategori'] === 'Militer'));
        $totalPNS     = count(array_filter($pangkat, fn($p) => $p['kategori'] === 'PNS'));
        $totalPPPK    = count(array_filter($pangkat, fn($p) => $p['kategori'] === 'PPPK'));
    ?>
    <div class="col-sm-3">
        <div class="summary-card">
            <div class="summary-num"><?= count($pangkat) ?></div>
            <div class="summary-label">Total Pangkat</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="summary-card">
            <div class="summary-num" style="color:#3b82f6;"><?= $totalMiliter ?></div>
            <div class="summary-label">Militer</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="summary-card">
            <div class="summary-num" style="color:#f59e0b;"><?= $totalPNS ?></div>
            <div class="summary-label">PNS</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="summary-card">
            <div class="summary-num" style="color:#10b981;"><?= $totalPPPK ?></div>
            <div class="summary-label">PPPK</div>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card-custom">
        <div class="card-custom-header">
            <div>
                <div class="card-custom-title">
                    <i class="bi bi-award me-2 text-success"></i>Daftar Pangkat
                </div>
                <div class="card-custom-sub"><?= count($pangkat) ?> pangkat terdaftar</div>
            </div>
            <div class="search-wrap">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Cari nama pangkat...">
            </div>
        </div>

        <?php if (empty($pangkat)): ?>
        <div class="empty-state">
            <i class="bi bi-award"></i>
            <div>Belum ada data pangkat</div>
            <a href="<?= base_url('admin/pangkat/create') ?>" class="btn-primary-custom mt-3 d-inline-flex">
                Tambah Pangkat Pertama
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table-custom" id="pangkatTable">
                <thead>
                    <tr>
                        <th width="60">Urutan</th>
                        <th>Nama Pangkat</th>
                        <th>Singkatan</th>
                        <th>Kategori</th>
                        <th>Kelompok</th>
                        <th>Golongan</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Kelompokkan by kelompok untuk tampilan
                    $grouped = [];
                    foreach ($pangkat as $p) {
                        $grouped[$p['kelompok']][] = $p;
                    }
                    $kelompokOrder = ['Perwira', 'Bintara', 'Tamtama', 'PNS', 'PPPK'];
                    ?>
                    <?php foreach ($kelompokOrder as $kelompok): ?>
                    <?php if (!isset($grouped[$kelompok])) continue; ?>
                    <!-- Group Header -->
                    <tr class="group-header-row">
                        <td colspan="7">
                            <span class="group-label <?= strtolower($kelompok) ?>">
                                <?= $kelompok ?>
                            </span>
                        </td>
                    </tr>
                    <?php foreach ($grouped[$kelompok] as $p): ?>
                    <tr>
                        <td>
                            <span class="urutan-badge"><?= $p['urutan'] ?></span>
                        </td>
                        <td class="fw-500"><?= esc($p['nama_pangkat']) ?></td>
                        <td>
                            <code class="code-tag"><?= esc($p['pangkat_singkat']) ?></code>
                        </td>
                        <td>
                            <span class="badge-kategori <?= strtolower($p['kategori']) ?>">
                                <?= $p['kategori'] ?>
                            </span>
                        </td>
                        <td style="font-size:.82rem; color:#6b7280;">
                            <?= esc($p['kelompok']) ?>
                        </td>
                        <td style="font-size:.8rem; color:#6b7280;">
                            <?= esc($p['golongan_militer']) ?: '-' ?>
                        </td>
                        <td>
                            <div class="action-wrap">
                                <a href="<?= base_url('admin/pangkat/edit/' . $p['id']) ?>" class="btn-action edit"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?= base_url('admin/pangkat/delete/' . $p['id']) ?>" method="POST"
                                    class="d-inline delete-form" data-nama="<?= esc($p['nama_pangkat']) ?>"
                                    data-singkat="<?= esc($p['pangkat_singkat']) ?>">
                                    <?= csrf_field() ?>
                                    <button type="button" class="btn-action delete btn-delete" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="modal-icon">
                <i class="bi bi-trash"></i>
            </div>
            <h5>Hapus Pangkat</h5>
            <p id="deleteMessage">Apakah Anda yakin ingin menghapus pangkat ini?</p>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" id="cancelDelete">Batal</button>
                <button type="button" class="btn-confirm-delete" id="confirmDelete">Ya, Hapus</button>
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

    .summary-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 1rem 1.25rem;
        text-align: center
    }

    .summary-num {
        font-size: 1.6rem;
        font-weight: 700;
        color: #111827;
        line-height: 1
    }

    .summary-label {
        font-size: .75rem;
        color: #9ca3af;
        margin-top: 4px
    }

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

    .search-wrap {
        position: relative
    }

    .search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: .85rem
    }

    .search-input {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: .4rem .75rem .4rem 2rem;
        font-size: .82rem;
        width: 220px;
        outline: none
    }

    .search-input:focus {
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, .1)
    }

    .table-custom {
        width: 100%;
        border-collapse: collapse;
        font-size: .83rem
    }

    .table-custom thead tr {
        background: #f9fafb
    }

    .table-custom th {
        padding: .65rem 1.25rem;
        font-size: .7rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .05em;
        white-space: nowrap
    }

    .table-custom td {
        padding: .65rem 1.25rem;
        color: #374151;
        border-bottom: 1px solid #f9fafb;
        vertical-align: middle
    }

    .table-custom tbody tr:hover {
        background: #fafafa
    }

    .fw-500 {
        font-weight: 500
    }

    .code-tag {
        background: #f3f4f6;
        color: #374151;
        padding: 2px 8px;
        border-radius: 5px;
        font-size: .78rem
    }

    /* Group header */
    .group-header-row td {
        background: #f9fafb;
        padding: .4rem 1.25rem;
        border-bottom: none
    }

    .group-label {
        font-size: .68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        padding: 2px 10px;
        border-radius: 99px
    }

    .group-label.perwira {
        background: #dbeafe;
        color: #1e40af
    }

    .group-label.bintara {
        background: #dcfce7;
        color: #166534
    }

    .group-label.tamtama {
        background: #fef9c3;
        color: #854d0e
    }

    .group-label.pns {
        background: #f3e8ff;
        color: #5b21b6
    }


    .group-label.pppk {
        background: #e8ffeb;
        color: #21b65f
    }

    /* Urutan badge */
    .urutan-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        background: #f3f4f6;
        border-radius: 7px;
        font-size: .75rem;
        font-weight: 600;
        color: #374151
    }

    /* Kategori badge */
    .badge-kategori {
        font-size: .7rem;
        font-weight: 600;
        padding: 2px 9px;
        border-radius: 99px
    }

    .badge-kategori.militer {
        background: #dbeafe;
        color: #1e40af
    }

    .badge-kategori.pns {
        background: #fef9c3;
        color: #854d0e
    }

    .badge-kategori.pppk {
        background: #d2fec3;
        color: #1c850e
    }

    .action-wrap {
        display: flex;
        align-items: center;
        gap: 5px
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

    .btn-action.delete {
        background: #fef2f2;
        color: #dc2626
    }

    .btn-action.delete:hover {
        background: #fee2e2
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

    /* DELETE MODAL */
    .custom-modal {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, .55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
    }

    .custom-modal.show {
        display: flex;
    }

    .custom-modal-content {
        background: #fff;
        width: 100%;
        max-width: 430px;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        animation: modalPop .2s ease;
    }

    @keyframes modalPop {
        from {
            opacity: 0;
            transform: scale(.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .modal-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        margin: 0 auto 1rem;
        background: #fef2f2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }

    .custom-modal h5 {
        margin-bottom: .75rem;
        color: #111827;
        font-weight: 600;
    }

    .custom-modal p {
        color: #6b7280;
        font-size: .9rem;
        margin-bottom: 1.5rem;
    }

    .modal-actions {
        display: flex;
        gap: .75rem;
    }

    .btn-cancel,
    .btn-confirm-delete {
        flex: 1;
        border: none;
        border-radius: 10px;
        padding: .7rem;
        font-size: .85rem;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-cancel {
        background: #f3f4f6;
        color: #374151;
    }

    .btn-confirm-delete {
        background: #dc2626;
        color: #fff;
    }

    .btn-confirm-delete:hover {
        background: #b91c1c;
    }
    </style>
    <?= $this->endSection() ?>
    <?= $this->section('scripts') ?>
    <script>
    document.getElementById('searchInput').addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll('#pangkatTable tbody tr:not(.group-header-row)').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(keyword) ? '' : 'none';
        });
    });

    /* ── Delete Modal ── */
    let selectedForm = null;
    const modal = document.getElementById('deleteModal');
    const deleteMessage = document.getElementById('deleteMessage');

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            selectedForm = this.closest('.delete-form');
            const nama = selectedForm.dataset.nama;
            const singkat = selectedForm.dataset.singkat;

            deleteMessage.innerHTML = `
            Apakah Anda yakin ingin menghapus pangkat berikut?
            <div style="margin-top:12px;padding:12px;border-radius:10px;background:#f9fafb;border:1px solid #e5e7eb;">
                <div style="font-weight:600;color:#111827;">${nama}</div>
                <div style="font-size:.85rem;color:#6b7280;margin-top:4px;">${singkat}</div>
            </div>
            <div style="margin-top:12px;color:#dc2626;font-size:.82rem;">
                Tindakan ini tidak dapat dibatalkan.
            </div>
        `;
            modal.classList.add('show');
        });
    });

    document.getElementById('cancelDelete').addEventListener('click', () => {
        modal.classList.remove('show');
        selectedForm = null;
    });

    document.getElementById('confirmDelete').addEventListener('click', () => {
        if (selectedForm) selectedForm.submit();
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('show');
            selectedForm = null;
        }
    });
    </script>
    <?= $this->endSection() ?>