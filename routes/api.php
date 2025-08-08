<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // ⬅️ Wajib!
use App\Http\Controllers\API\UserController;

// 🔹 Login (bisa dipindah ke controller nanti)
Route::post('users/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('Mobile App')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token
        ], 200);
    }

    return response()->json([
        'message' => 'Email atau password salah'
    ], 401);
});

// 🔹 Route yang butuh login
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 🔹 History absen
    Route::get('/absen/history', function () {
        return response()->json([
            [
                'id' => 1,
                'nama' => 'Putra',
                'jenis' => 'Masuk',
                'waktuabsen' => now()->format('Y-m-d H:i:s'),
                'lokasi' => 'Kantor Pusat',
                'gambar' => 'data:image/png;base64,iVBORw0KGgoAAAANSUh...',
                'keterangan' => 'Hadir',
                'bukti' => 'Selfie'
            ]
        ]);
    });

    // 🔹 Absen masuk
    Route::post('/absen/masuk', function (Request $request) {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'lokasi' => 'required|string',
            // 'keterangan' => 'nullable|string',
            'gambar' => 'nullable|string', // base64
        ]);

        // Di sini nanti simpan ke database
        // $absen = Absen::create($validated);

        return response()->json([
            'message' => 'Absen masuk berhasil',
            'data' => $validated
        ], 201);
    });
});

// 🔹 UserController (opsional, bisa dipakai untuk register, dll)
Route::post('users', [UserController::class, 'store']);
// Route::post('users/login', [UserController::class, 'login']); // Hapus jika sudah pakai /login