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
                'name' => 'Admin',
                'code_name' => 'Admin',
                'is_active' => 1,
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['code_name' => $role['code_name']],
                $role
            );
        }
    }
}
