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
    ];
}
