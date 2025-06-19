<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Ensure user is created or updated, not duplicated
        \App\Models\User::updateOrCreate(
            ['email' => 'nurulizzati1377@gmail.com'],
            [
                'name' => 'Pentadbir',
                'password' => bcrypt('12345678'),
                'role' => 'Admin' // Make sure your User model/table has a 'role' column
            ]
        );
    }
}
