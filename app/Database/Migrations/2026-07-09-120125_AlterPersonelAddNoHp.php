<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPersonelAddNoHp extends Migration
{
    public function up()
    {
        $this->forge->addColumn('personel', [
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'jabatan',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('personel', 'no_hp');
    }
}