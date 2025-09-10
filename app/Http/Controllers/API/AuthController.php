<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pegawai;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Registrasi user baru + otomatis buat data pegawai
     */
    public function signup(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users|unique:pegawai,email',
            'password'      => 'required|string|min:6|confirmed',
            'nip'           => 'required|string|unique:pegawai,nip',
            'no_telp'       => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
            'jabatan'       => 'required|string|max:255',
            'bagian'        => 'required|string|max:255',
            'sub_bagian'    => 'required|string|max:255',
            'foto_profil'   => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Pendaftaran gagal',
                'errors'  => $validator->errors()
            ], 400);
        }

        // Upload foto profil jika ada
        $fotoPath = null;
        if ($request->hasFile('foto_profil')) {
            $fotoPath = $request->file('foto_profil')->store('pegawai', 'public');
        }

        // Buat user baru
        $user = User::create([
            'name'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Buat data pegawai otomatis
        $pegawai = Pegawai::create([
            'foto_profil'   => $fotoPath,
            'nama'          => $request->nama,
            'nip'           => $request->nip,
            'email'         => $request->email,
            'no_telp'       => $request->no_telp,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jabatan'       => $request->jabatan,
            'bagian'        => $request->bagian,
            'sub_bagian'    => $request->sub_bagian,
            'user_id'       => $user->id, // ← penting!
        ]);

        return response()->json([
            'status'   => true,
            'message'  => 'Pendaftaran berhasil',
            'data'     => [
                'user'    => $user,
                'pegawai' => $pegawai,
                'foto_url' => $fotoPath ? asset('storage/'.$fotoPath) : null
            ]
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Login gagal',
                'errors'  => $validator->errors()
            ], 400);
        }

        // Cari user
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Buat token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login berhasil',
            'token'   => $token,
            'data'    => [
                'id'    => $user->id,
                'email' => $user->email,
                'name'  => $user->name, // dari tabel users
                'nama'  => $user->pegawai->nama ?? $user->name, // fallback
            ]
        ], 200);


        // return response()->json([
        //     'status'  => true,
        //     'message' => 'Login berhasil',
        //     'token'   => $token,
        //     'data'    => $user
        // ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logout berhasil'
        ], 200);
    }
}
