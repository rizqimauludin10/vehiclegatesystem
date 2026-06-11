<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'uuid'       => Uuid::uuid4()->toString(),
                'nama'       => 'Administrator',
                'username'   => 'admin',
                'password'   => password_hash('admin123', PASSWORD_BCRYPT),
                'role'       => 'admin',
                'status'     => 'aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid'       => Uuid::uuid4()->toString(),
                'nama'       => 'Petugas',
                'username'   => 'petugas',
                'password'   => password_hash('petugas123', PASSWORD_BCRYPT),
                'role'       => 'petugas',
                'status'     => 'aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $this->db->table('users')->insert($data);
    }
}