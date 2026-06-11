<?php

namespace App\Controllers;

use App\Models\SatkerModel;

/**
 * Satker Controller
 * CRUD management data satuan kerja
 * Routes prefix: /admin/satker
 */
class Satker extends BaseController
{
    protected SatkerModel $satkerModel;
    protected $db;

    public function __construct()
    {
        $this->satkerModel = new SatkerModel();
        $this->db = \Config\Database::connect();
    }

    // ──────────────────────────────────────────────────────────
    // INDEX
    // GET /admin/satker
    // ──────────────────────────────────────────────────────────
    public function index(): string
    {
        $data = [
            'title'  => 'Management Satuan Kerja',
            'satker' => $this->satkerModel->orderBy('nama_singkat', 'ASC')->findAll(),
        ];

        return view('satker/index', $data);
    }

    // ──────────────────────────────────────────────────────────
    // CREATE
    // GET /admin/satker/create
    // ──────────────────────────────────────────────────────────
    public function create(): string
    {
        $data = [
            'title'      => 'Tambah Satuan Kerja',
            'validation' => \Config\Services::validation(),
        ];

        return view('satker/create', $data);
    }

    // ──────────────────────────────────────────────────────────
    // STORE
    // POST /admin/satker/store
    // ──────────────────────────────────────────────────────────
    public function store()
    {
        $rules = [
            'kode_satker'  => 'required|max_length[50]|is_unique[satuan_kerja.kode_satker]',
            'nama_singkat' => 'required|max_length[50]',
            'nama_panjang' => 'required|max_length[150]',
        ];

        $messages = [
            'kode_satker' => [
                'is_unique' => 'Kode satker sudah digunakan.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        $this->satkerModel->insert([
            'kode_satker'  => strtoupper(trim($this->request->getPost('kode_satker'))),
            'nama_singkat' => trim($this->request->getPost('nama_singkat')),
            'nama_panjang' => trim($this->request->getPost('nama_panjang')),
        ]);

        return redirect()->to('/admin/satker')
                        ->with('success', 'Satuan kerja berhasil ditambahkan.');
    }

    // ──────────────────────────────────────────────────────────
    // EDIT
    // GET /admin/satker/edit/{id}
    // ──────────────────────────────────────────────────────────
    public function edit(int $id): string
    {
        $satker = $this->satkerModel->find($id);

        if (!$satker) {
            return redirect()->to('/admin/satker')
                            ->with('error', 'Satuan kerja tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Satuan Kerja',
            'satker'     => $satker,
            'validation' => \Config\Services::validation(),
        ];

        return view('satker/edit', $data);
    }

    // ──────────────────────────────────────────────────────────
    // UPDATE
    // POST /admin/satker/update/{id}
    // ──────────────────────────────────────────────────────────
    public function update(int $id)
    {
        $satker = $this->satkerModel->find($id);

        if (!$satker) {
            return redirect()->to('/admin/satker')
                            ->with('error', 'Satuan kerja tidak ditemukan.');
        }

        $rules = [
            'kode_satker'  => "required|max_length[50]|is_unique[satuan_kerja.kode_satker,id,{$id}]",
            'nama_singkat' => 'required|max_length[50]',
            'nama_panjang' => 'required|max_length[150]',
        ];

        $messages = [
            'kode_satker' => [
                'is_unique' => 'Kode satker sudah digunakan.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        $this->satkerModel->update($id, [
            'kode_satker'  => strtoupper(trim($this->request->getPost('kode_satker'))),
            'nama_singkat' => trim($this->request->getPost('nama_singkat')),
            'nama_panjang' => trim($this->request->getPost('nama_panjang')),
        ]);

        return redirect()->to('/admin/satker')
                        ->with('success', 'Satuan kerja berhasil diperbarui.');
    }

    // ──────────────────────────────────────────────────────────
    // DELETE
    // POST /admin/satker/delete/{id}
    // ──────────────────────────────────────────────────────────
    public function delete(int $id)
    {
        $satker = $this->satkerModel->find($id);

        if (!$satker) {
            return redirect()->to('/admin/satker')
                            ->with('error', 'Satuan kerja tidak ditemukan.');
        }

        // Cek apakah satker masih dipakai personel
        $used = $this->db->table('personel')
                        ->where('satker_id', $id)
                        ->countAllResults();

        if ($used > 0) {
            return redirect()->to('/admin/satker')
                            ->with('error', "Satker tidak bisa dihapus karena masih digunakan oleh {$used} personel.");
        }

        $this->satkerModel->delete($id);

        return redirect()->to('/admin/satker')
                        ->with('success', 'Satuan kerja berhasil dihapus.');
    }
}