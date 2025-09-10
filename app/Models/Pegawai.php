<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
     protected $table = 'pegawai';

    protected $fillable = [
    'foto_profil',
    'nama',
    'nip',
    'email',
    'no_telp',
    'tanggal_lahir',
    'jabatan',
    'bagian',
    'sub_bagian',
    'user_id'
    ];

    
    public function absens()
    {
        return $this->hasMany(Absen::class);
    }

    // app/Models/Pegawai.php
    public function user()
    {
        return $this->belongsTo(User::class);
    // atau pakai user_id kalau sudah ditambahkan kolom user_id
    }

    // Otomatis set user_id berdasarkan email saat create/update
    protected static function booted()
    {
        static::saving(function ($pegawai) {
            $user = \App\Models\User::where('email', $pegawai->email)->first();
            if ($user) {
                $pegawai->user_id = $user->id;
            }
        });
    }

}
