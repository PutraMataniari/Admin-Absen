<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::updateOrCreate(
            ['email' => 'adminsiagakpu@gmail.com'], // email unik
            [
                'name' => 'Admin',
                'password' => bcrypt('adminsiagakpu123@'), // ubah sesuai kebutuhan
                'role' => 'admin',
            ]
        );
    }
}
