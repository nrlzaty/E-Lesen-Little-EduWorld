<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class DefaultRole extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Pentadbir',
                'code_name' => 'Admin',
                'is_active' => 1,
            ],

            [
                'name' => 'Pegawai Penyemakan',
                'code_name' => 'Pegawai Penyemakan',
                'is_active' => 1,
            ],
            [
                'name' => 'Pegawai Perlulusan',
                'code_name' => 'Pegawai Perlulusan',
                'is_active' => 1,
            ],
            [
                'name' => 'Kerani',
                'code_name' => 'Kerani',
                'is_active' => 1,
            ],
        ];

        foreach ($roles as $role) {
            // Ensure role is created only if it does not exist
            Role::firstOrCreate(
                ['code_name' => $role['code_name']],
                $role
            );
        }
    }
}
