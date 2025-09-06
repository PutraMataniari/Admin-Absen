<?php

use App\Http\Controllers\AbsenController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PerizinanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// 🔹 Register
Route::post('register', [PegawaiController::class, 'register']);

// 🔹 Login
Route::post('login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('Mobile App')->plainTextToken;

        return response()->json([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'token' => $token
        ], 200);
    }

    return response()->json([
        'message' => 'Email atau password salah'
    ], 401);
});

// 🔹 Semua route yang butuh login pakai Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    // ✅ AbsenController
    Route::post('absen/masuk', [AbsenController::class, 'masuk']);
    Route::post('absen/pulang', [AbsenController::class, 'pulang']);
    Route::get('absen/history', [AbsenController::class, 'history']);

    // ✅ PerizinanController (izin dipisah)
    Route::post('absen/izin', [PerizinanController::class, 'store']);

    // ✅ Cek user login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// 🔹 UserController (opsional)
Route::post('users', [UserController::class, 'store']);
