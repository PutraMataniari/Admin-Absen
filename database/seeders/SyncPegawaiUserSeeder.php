<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\User;

class SyncPegawaiUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $pegawais = Pegawai::all();

        foreach ($pegawais as $pegawai) {
            $user = User::where('email', $pegawai->email)->first();

            if ($user) {
                $pegawai->update(['user_id' => $user->id]);
                $this->command->info("Pegawai {$pegawai->nama} berhasil dihubungkan ke User {$user->email}");
            } else {
                $this->command->warn("Pegawai {$pegawai->nama} belum punya user dengan email {$pegawai->email}");
            }
        }
    }
}
