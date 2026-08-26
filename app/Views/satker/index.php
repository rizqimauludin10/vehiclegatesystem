<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-header-title">Management Satuan Kerja</h5>
        <p class="page-header-sub">Kelola data satuan kerja yang terdaftar</p>
    </div>
    <a href="<?= base_url('admin/satker/create') ?>" class="btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i> Tambah Satker
    </a>
</div>

<div class="card-custom">
    <div class="card-custom-header">
        <div>
            <div class="card-custom-title">
                <i class="bi bi-building me-2 text-success"></i>Daftar Satuan Kerja
            </div>
            <div class="card-custom-sub"><?= count($satker) ?> satker terdaftar</div>
        </div>
        <div class="search-wrap">
            <i class="bi bi-search search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari nama / kode satker...">
        </div>
    </div>

    <?php if (empty($satker)): ?>
    <div class="empty-state">
        <i class="bi bi-building"></i>
        <div>Belum ada satuan kerja terdaftar</div>
        <a href="<?= base_url('admin/satker/create') ?>" class="btn-primary-custom mt-3 d-inline-flex">
            Tambah Satker Pertama
        </a>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table class="table-custom" id="satkerTable">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Kode Satker</th>
                    <th>Nama Singkat</th>
                    <th>Nama Panjang</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($satker as $i => $s): ?>
                <tr>
                    <td class="text-muted"><?= $i + 1 ?></td>
                    <td>
                        <code class="code-tag"><?= esc($s['kode_satker']) ?></code>
                    </td>
                    <td class="fw-500"><?= esc($s['nama_singkat']) ?></td>
                    <td style="font-size:.82rem;color:#6b7280;">
                        <?= esc($s['nama_panjang']) ?>
                    </td>
                    <td>
                        <div class="action-wrap">
                            <a href="<?= base_url('admin/satker/edit/' . $s['id']) ?>" class="btn-action edit"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?= base_url('admin/satker/delete/' . $s['id']) ?>" method="POST"
                                class="d-inline delete-form" data-nama="<?= esc($s['nama_singkat']) ?>"
                                data-kode="<?= esc($s['kode_satker']) ?>">
                                <?= csrf_field() ?>
                                <button type="button" class="btn-action delete btn-delete" title="Hapus">
                                    <i class="bi bi-trash"></i>
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

<!-- Delete Modal -->
<div id="deleteModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="modal-icon">
            <i class="bi bi-trash"></i>
        </div>
        <h5>Hapus Satuan Kerja</h5>
        <p id="deleteMessage">Apakah Anda yakin ingin menghapus satker ini?</p>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" id="cancelDelete">Batal</button>
            <button type="button" class="btn-confirm-delete" id="confirmDelete">Ya, Hapus</button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<!-- <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css"> -->
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
    letter-spacing: .05em
}

.table-custom td {
    padding: .72rem 1.25rem;
    color: #374151;
    border-bottom: 1px solid #f9fafb;
    vertical-align: middle
}

.table-custom tbody tr:hover {
    background: #fafafa
}

.table-custom tbody tr:last-child td {
    border-bottom: none
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

/* DATATABLES TOP & BOTTOM BAR */
div.dataTables_wrapper div.dt-top-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .75rem;
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    margin-bottom: .25rem;
}

div.dataTables_wrapper div.dt-bottom-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .75rem;
    padding: .85rem;
    border-top: 1px solid #f3f4f6;
    margin-top: .25rem;
}

/* SEARCH */
div.dataTables_wrapper div.dataTables_filter label {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .8rem;
    color: #6b7280;
    margin: 0;
    font-weight: 500;
}

div.dataTables_wrapper div.dataTables_filter input {
    width: 220px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: .4rem .75rem;
    font-size: .82rem;
    color: #374151;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    margin-left: 0 !important;
}

div.dataTables_wrapper div.dataTables_filter input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .1);
}

/* LENGTH */
div.dataTables_wrapper div.dataTables_length label {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .8rem;
    color: #6b7280;
    margin: 0;
    font-weight: 500;
    white-space: nowrap;
}

div.dataTables_wrapper div.dataTables_length select {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: .82rem;
    color: #374151;
    outline: none;
    cursor: pointer;
    padding: .35rem 2rem .35rem .6rem;
    background-color: #fff;

    /* Custom arrow via SVG */
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .6rem center;
}

/* INFO */
div.dataTables_wrapper div.dataTables_info {
    font-size: .78rem;
    color: #9ca3af;
    padding: 0;
}

/* PAGINATION */
div.dataTables_wrapper div.dataTables_paginate .pagination {
    margin: 0;
    gap: 3px;
    display: flex;
    flex-wrap: wrap;
}

div.dataTables_wrapper div.dataTables_paginate .page-item .page-link {
    border: 1px solid #e5e7eb !important;
    border-radius: 7px !important;
    font-size: .78rem !important;
    padding: .28rem .62rem !important;
    min-width: 32px;
    text-align: center;
    line-height: 1.4;
    cursor: pointer;
    color: #374151 !important;
    background: #fff !important;
    transition: all .15s;
}

div.dataTables_wrapper div.dataTables_paginate .page-item .page-link:hover {
    background: #f0fdf4 !important;
    color: #166534 !important;
    border-color: #bbf7d0 !important;
}

div.dataTables_wrapper div.dataTables_paginate .page-item.active .page-link,
div.dataTables_wrapper div.dataTables_paginate .page-item.active .page-link:hover {
    background: linear-gradient(135deg, #166534, #16a34a) !important;
    color: #fff !important;
    border-color: transparent !important;
    box-shadow: 0 2px 6px rgba(22, 101, 52, .3);
}

div.dataTables_wrapper div.dataTables_paginate .page-item.disabled .page-link,
div.dataTables_wrapper div.dataTables_paginate .page-item.disabled .page-link:hover {
    background: #f9fafb !important;
    color: #d1d5db !important;
    border-color: #f3f4f6 !important;
    cursor: default;
    box-shadow: none;
}

/* SORTING */
table.dataTable thead .sorting,
table.dataTable thead .sorting_asc,
table.dataTable thead .sorting_desc {
    background-image: none !important;
    padding-right: 1.5rem !important;
    position: relative;
    cursor: pointer;
}

table.dataTable thead .sorting::after {
    content: '⇅';
    position: absolute;
    right: .5rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: .65rem;
    opacity: .3;
}

table.dataTable thead .sorting_asc::after {
    content: '↑';
    position: absolute;
    right: .5rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: .65rem;
    opacity: .7;
    color: #16a34a;
}

table.dataTable thead .sorting_desc::after {
    content: '↓';
    position: absolute;
    right: .5rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: .65rem;
    opacity: .7;
    color: #16a34a;
}

/* RESPONSIVE */
@media (max-width: 576px) {
    div.dataTables_wrapper div.dt-top-wrap {
        flex-direction: column;
        align-items: stretch;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        width: 100%;
    }

    div.dataTables_wrapper div.dt-bottom-wrap {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap5.min.js"></script> -->
<script>
$(document).ready(function() {

    $('#satkerTable').DataTable({
        language: {
            search: "",
            searchPlaceholder: "Cari nama / kode satker...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_–_END_ dari _TOTAL_ satker",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ data)",
            zeroRecords: "Tidak ada satker yang cocok",
            emptyTable: "Belum ada data satker",
            paginate: {
                first: "«",
                last: "»",
                next: "›",
                previous: "‹"
            }
        },

        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],

        columnDefs: [
            // Kolom # dan Aksi — tidak sortable & tidak ikut search
            {
                orderable: false,
                searchable: false,
                targets: [0, 4]
            }
        ],

        // Default sort: nama singkat (kolom 2) ascending
        order: [
            [2, 'asc']
        ],

        responsive: true,

        dom: '<"dt-top-wrap"fl>rt<"dt-bottom-wrap"ip>',
    });

    /* ── Delete Modal ── */
    let selectedForm = null;
    const modal = document.getElementById('deleteModal');
    const deleteMessage = document.getElementById('deleteMessage');

    document.addEventListener('click', function(e) {

        const button = e.target.closest('.btn-delete');

        if (!button) return;

        selectedForm = button.closest('.delete-form');

        const nama = selectedForm.dataset.nama;
        const kode = selectedForm.dataset.kode;

        deleteMessage.innerHTML = `
        Apakah Anda yakin ingin menghapus satker berikut?

        <div style="margin-top:12px;padding:12px;border-radius:10px;background:#f9fafb;border:1px solid #e5e7eb;">
            <div style="font-weight:600;color:#111827;">
                ${nama}
            </div>

            <div style="font-size:.85rem;color:#6b7280;margin-top:4px;">
                Kode: ${kode}
            </div>
        </div>

        <div style="margin-top:12px;color:#dc2626;font-size:.82rem;">
            Tindakan ini tidak dapat dibatalkan.
        </div>
    `;

        modal.classList.add('show');

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
});
</script>
<?= $this->endSection() ?>