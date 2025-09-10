<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

         // Seeder untuk admin
        \App\Models\User::updateOrCreate(
            ['email' => 'adminsiagakpu@gmail.com'], // email unik
            [
                'name' => 'Admin',
                'password' => bcrypt('adminsiagakpu123@'), // ubah sesuai kebutuhan
                'role' => 'admin',
            ]
        );
    }
}
