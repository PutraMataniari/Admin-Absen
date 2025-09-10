<?php

namespace App\Models;

use Filament\Http\Middleware\Authenticate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Absen extends Model
{
    protected $table = 'absen';

    protected $fillable = [
        'pegawai_id',
        'jenis',    //masuk, pulang, izin
        // 'nama',
        'waktu_absen',
        'lokasi',
        'gambar',
        'jenis_izin', //cuti, sakit, dinas
        'laporan_kinerja',
        'bukti',
        'bukti_asli', // kolom baru untuk menyimpan nama file asli
        'status',
        'catatan_admin' 
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
        
    }
}

