<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'uuid', 'nama', 'username', 'password',
        'role', 'status'
    ];

    protected bool $allowEmptyInserts = false;

    // Timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nama'     => 'required|min_length[3]|max_length[100]',
        'username' => 'required|min_length[4]|max_length[50]|is_unique[users.username,id,{id}]',
        'role'     => 'required|in_list[superadmin,admin,petugas]',
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Username sudah digunakan, pilih username lain.',
        ],
    ];

    // ── Helper Methods ─────────────────────────────────────

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)
                    ->where('status', 'aktif')
                    ->first();
    }

    public function findByUuid(string $uuid): ?array
    {
        return $this->where('uuid', $uuid)->first();
    }

    public function getAllPetugas(): array
    {
        return $this->where('role', 'petugas')
                    ->orderBy('nama', 'ASC')
                    ->findAll();
    }
}