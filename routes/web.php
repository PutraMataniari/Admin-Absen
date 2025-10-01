<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;


Route::get('/db-test', function () {
    try{
        DB::connection()->getPdo();
        return 'connection to DB: '.DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return 'Connection failed: '. $e->getMessage();
    }
});

    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/send-email',function(){
        $data = [
            'name' => 'Admin SIAGA',
            'body' => 'Testing Kirim Email di Sistem Absensi Karyawan'
        ];
    
        Mail::to('trisaputra195@gmail.com')->send(new SendEmail($data));
    
        dd("Email Berhasil dikirim.");
    });

// Route::get('/test-email', function () {
//     try {
//         Mail::raw('Halo, ini email percobaan dari Laravel + Brevo SMTP 🚀', function ($message) {
//             $message->to('alamat_email_tujuan@gmail.com') // ganti dengan email tujuanmu
//                     ->subject('Test Email dari Laravel');
//         });

//         return '✅ Email berhasil dikirim!';
//     } catch (\Exception $e) {
//         return '❌ Error: ' . $e->getMessage();
//     }
// });