<?php

namespace App\Controllers;

use App\Models\PangkatModel;

/**
 * Pangkat Controller
 * CRUD management data pangkat
 * Routes prefix: /admin/pangkat
 */
class Pangkat extends BaseController
{
    protected PangkatModel $pangkatModel;
    protected $db;


    public function __construct()
    {
        $this->pangkatModel = new PangkatModel();
        $this->db = \Config\Database::connect();
    }

    // ──────────────────────────────────────────────────────────
    // INDEX
    // GET /admin/pangkat
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        $data = [
            'title'   => 'Management Pangkat',
            'pangkat' => $this->pangkatModel->getAllOrdered(),
        ];

        return view('pangkat/index', $data);
    }

    // ──────────────────────────────────────────────────────────
    // CREATE
    // GET /admin/pangkat/create
    // ──────────────────────────────────────────────────────────
    public function create(): string
    {
        // Auto suggest urutan berikutnya
        $lastUrutan = $this->pangkatModel
                        ->selectMax('urutan')
                        ->first();

        $data = [
            'title'      => 'Tambah Pangkat',
            'nextUrutan' => ($lastUrutan['urutan'] ?? 0) + 1,
            'validation' => \Config\Services::validation(),
        ];

        return view('pangkat/create', $data);
    }

    // ──────────────────────────────────────────────────────────
    // STORE
    // POST /admin/pangkat/store
    // ──────────────────────────────────────────────────────────
    public function store()
    {
        $rules = [
            'urutan'           => 'required|is_natural_no_zero',
            'nama_pangkat'     => 'required|max_length[100]|is_unique[pangkat.nama_pangkat]',
            'pangkat_singkat'  => 'required|max_length[20]',
            'kategori'         => 'required|in_list[Militer,PNS,PPPK,PHL]',
            'kelompok'         => 'required|in_list[Perwira,Bintara,Tamtama,PNS,PPPK,PHL]',
            'golongan_militer' => 'permit_empty|max_length[50]',
        ];

        $messages = [
            'nama_pangkat' => [
                'is_unique' => 'Nama pangkat sudah terdaftar.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        $this->pangkatModel->insert([
            'urutan'           => $this->request->getPost('urutan'),
            'nama_pangkat'     => trim($this->request->getPost('nama_pangkat')),
            'pangkat_singkat'  => trim($this->request->getPost('pangkat_singkat')),
            'kategori'         => $this->request->getPost('kategori'),
            'kelompok'         => $this->request->getPost('kelompok'),
            'golongan_militer' => trim($this->request->getPost('golongan_militer')),
        ]);

        return redirect()->to('/admin/pangkat')
                        ->with('success', 'Pangkat berhasil ditambahkan.');
    }

    // ──────────────────────────────────────────────────────────
    // EDIT
    // GET /admin/pangkat/edit/{id}
    // ──────────────────────────────────────────────────────────
    public function edit(int $id): string
    {
        $pangkat = $this->pangkatModel->find($id);

        if (!$pangkat) {
            return redirect()->to('/admin/pangkat')
                            ->with('error', 'Pangkat tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Pangkat',
            'pangkat'    => $pangkat,
            'validation' => \Config\Services::validation(),
        ];

        return view('pangkat/edit', $data);
    }

    // ──────────────────────────────────────────────────────────
    // UPDATE
    // POST /admin/pangkat/update/{id}
    // ──────────────────────────────────────────────────────────
    public function update(int $id)
    {
        $pangkat = $this->pangkatModel->find($id);

        if (!$pangkat) {
            return redirect()->to('/admin/pangkat')
                            ->with('error', 'Pangkat tidak ditemukan.');
        }

        $rules = [
            'urutan'           => 'required|is_natural_no_zero',
            'nama_pangkat'     => "required|max_length[100]|is_unique[pangkat.nama_pangkat,id,{$id}]",
            'pangkat_singkat'  => 'required|max_length[20]',
            'kategori'         => 'required|in_list[Militer,PNS,PPPK,PHL]',
            'kelompok'         => 'required|in_list[Perwira,Bintara,Tamtama,PNS,PPPK,PHL]',
            'golongan_militer' => 'permit_empty|max_length[50]',
        ];

        $messages = [
            'nama_pangkat' => [
                'is_unique' => 'Nama pangkat sudah digunakan.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        $this->pangkatModel->update($id, [
            'urutan'           => $this->request->getPost('urutan'),
            'nama_pangkat'     => trim($this->request->getPost('nama_pangkat')),
            'pangkat_singkat'  => trim($this->request->getPost('pangkat_singkat')),
            'kategori'         => $this->request->getPost('kategori'),
            'kelompok'         => $this->request->getPost('kelompok'),
            'golongan_militer' => trim($this->request->getPost('golongan_militer')),
        ]);

        return redirect()->to('/admin/pangkat')
                        ->with('success', 'Pangkat berhasil diperbarui.');
    }

    // ──────────────────────────────────────────────────────────
    // DELETE
    // POST /admin/pangkat/delete/{id}
    // ──────────────────────────────────────────────────────────
    public function delete(int $id)
    {
        $pangkat = $this->pangkatModel->find($id);

        if (!$pangkat) {
            return redirect()->to('/admin/pangkat')
                            ->with('error', 'Pangkat tidak ditemukan.');
        }

        // Cek apakah pangkat masih dipakai personel
        $used = $this->db->table('personel')
                        ->where('pangkat_id', $id)
                        ->countAllResults();

        if ($used > 0) {
            return redirect()->to('/admin/pangkat')
                            ->with('error', "Pangkat tidak bisa dihapus karena masih digunakan oleh {$used} personel.");
        }

        $this->pangkatModel->delete($id);

        return redirect()->to('/admin/pangkat')
                        ->with('success', 'Pangkat berhasil dihapus.');
    }
}