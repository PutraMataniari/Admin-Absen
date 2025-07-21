<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    protected $table = 'absen';
    protected $fillable = [
        'jenis',    //masuk, keluar, izin
        'nama',
        'waktu_absen',
        'lokasi',
        'gambar',
        'keterangan',
        'bukti',
    ];
}
