<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PegawaiController extends Controller
{
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'foto_profil'   => 'nullable|image|mimes:jpg,jpeg,png',
            'nama'          => 'required|string|max:255',
            'nip'           => 'required|string|unique:pegawai,nip',
            'email'         => 'required|email|unique:pegawai,email|unique:users,email',
            'no_telp'       => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
            'jabatan'       => 'required|string|max:255',
            'bagian'        => 'required|string|max:255',
            'sub_bagian'    => 'required|string|max:255',
            'password'      => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Handle upload foto profil
        $fotoPath = null;
        if ($request->hasFile('foto_profil')) {
            $fotoPath = $request->file('foto_profil')->store('pegawai', 'public');
        }

        // 1. Buat user terlebih dahulu
        $user = User::create([
            'name'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 2. Buat data pegawai dan isi user_id langsung
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
            'user_id'       => $user->id, // ← Penting!
        ]);

        return response()->json([
            'status'   => true,
            'message'  => 'Registrasi berhasil',
            'data'     => $pegawai,
            'foto_url' => $fotoPath ? asset('storage/'.$fotoPath) : null
        ], 201);
    }
}
