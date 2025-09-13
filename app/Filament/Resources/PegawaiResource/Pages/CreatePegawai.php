<?php

namespace App\Filament\Resources\PegawaiResource\Pages;

use App\Filament\Resources\PegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreatePegawai extends CreateRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ambil password dari form, atau gunakan default
        $password = $data['password'] ?? 'password123';

        // Buat user baru
        $user = User::create([
            'name'     => $data['nama'],
            'email'    => $data['email'],
            'password' => Hash::make($password),
            'role'     => 'pegawai', // role default
        ]);

        // Hubungkan user ke pegawai
        $data['user_id'] = $user->id;

        // Jangan simpan field password ke tabel pegawai
        unset($data['password']);

        return $data;
    }
}
