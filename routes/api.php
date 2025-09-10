<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\AbsenController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PerizinanController;
use App\Http\Controllers\ProfilController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


// 🔹 Upload Config (dipanggil Android supaya tahu batas upload file)
Route::get('/upload-config', function () {
    return response()->json([
        'max_upload_size' => 5 * 1024 * 1024, // 5MB dalam bytes
        'allowed_image_types' => ['jpg', 'jpeg', 'png'],
        'allowed_doc_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']
    ]);
});

// 🔹 Auth
Route::post('register', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // ✅ Logout
    Route::post('logout', [AuthController::class, 'logout']);

    // ✅ Absen
    Route::post('absen/masuk', [AbsenController::class, 'masuk']);
    Route::post('absen/pulang', [AbsenController::class, 'pulang']);
    Route::get('absen/history', [AbsenController::class, 'history']);

    // ✅ Perizinan
    Route::post('absen/izin', [PerizinanController::class, 'store']);

    // ✅ Profil
    Route::get('/profil', [ProfilController::class, 'show']);
    Route::post('/profil/update', [ProfilController::class, 'update']);
});

    // 🔹 UserController (opsional)
    Route::post('users', [UserController::class, 'store']);
