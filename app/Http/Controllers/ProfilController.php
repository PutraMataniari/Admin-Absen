<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    //
     public function show(Request $request)
    {
        $user = Auth::user(); // user login dari tabel users
        $pegawai = Pegawai::where('email', $user->email)->first();

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data pegawai berhasil diambil',
            'data' => [
                'pegawai' => [
                        'id'            => $pegawai->id,
                        'nama'          => $pegawai->nama,
                        'nip'           => $pegawai->nip,
                        'jabatan'       => $pegawai->jabatan,
                        'bagian'        => $pegawai->bagian,
                        'sub_bagian'    => $pegawai->sub_bagian,
                        'no_telp'       => $pegawai->no_telp,
                        'email'         => $pegawai->email,
                        'tanggal_lahir' => $pegawai->tanggal_lahir,
                        'foto_profil'   => $pegawai->foto_profil 
                                        ? asset('storage/' . $pegawai->foto_profil) 
                                        : null,
                ],
                'user' => [
                    'id'    => $user->id,
                    'nama'  => $user->name,
                    'email' => $user->email,
                ]
            ]
        ]);
    }

     // 🔹 Update profil user
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $pegawai = Pegawai::where('email', $user->email)->first();

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama'          => 'sometimes|required|string|max:255',
            'nip'           => 'sometimes|required|string|max:50',
            'jabatan'       => 'sometimes|required|string|max:255',
            'bagian'        => 'sometimes|required|string|max:255',
            'sub_bagian'    => 'nullable|string|max:255',
            'no_telp'       => 'sometimes|required|string|max:20',
            'email'         => 'sometimes|required|email',
            'tanggal_lahir' => 'sometimes|required|date',
            'foto_profil'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // update data
            // if (isset($validated['nama'])) {
            //     $pegawai->nama = $validated['nama'];
            // }
            // if (isset($validated['nip'])) {
            //     $pegawai->nip = $validated['nip'];
            // }
            // if (isset($validated['jabatan'])) {
            //     $pegawai->jabatan = $validated['jabatan'];
            // }
            // if (isset($validated['bagian'])) {
            //     $pegawai->bagian = $validated['bagian'];
            // }
            // if (isset($validated['sub_bagian'])) {
            //     $pegawai->sub_bagian = $validated['sub_bagian'];
            // }
            // if (isset($validated['no_telp'])) {
            //     $pegawai->no_telp = $validated['no_telp'];
            // }
            // if (isset($validated['email'])) {
            //     $pegawai->email = $validated['email'];
            // }
            // if (isset($validated['tanggal_lahir'])) {
            //     $pegawai->tanggal_lahir = $validated['tanggal_lahir'];
            // }

        // update data pegawai
            $pegawai->fill($validated);

        // jika user upload foto baru
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($pegawai->foto_profil && Storage::disk('public')->delete($pegawai->foto_profil)) {
                // Foto lama dihapus
            }
            // Simpan foto baru
            $path = $request->file('foto_profil')->store('foto_profil', 'public');
            $pegawai->foto_profil = $path;
        }

        $pegawai->save();

        
        // 🔹 update juga tabel users (sinkron nama + email)
        if (isset($validated['nama'])) {
            $user->name = $validated['nama'];
        }
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }
        $user->save();

       return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data'    => [
                'pegawai' => [
                        'id'            => $pegawai->id,
                        'nama'          => $pegawai->nama,
                        'nip'           => $pegawai->nip,
                        'jabatan'       => $pegawai->jabatan,
                        'bagian'        => $pegawai->bagian,
                        'sub_bagian'    => $pegawai->sub_bagian,
                        'no_telp'       => $pegawai->no_telp,
                        'email'         => $pegawai->email,
                        'tanggal_lahir' => $pegawai->tanggal_lahir,
                        'foto_profil'   => $pegawai->foto_profil 
                                        ? asset('storage/' . $pegawai->foto_profil) 
                                        : null,
                ],
                'user'    => [
                        'id'    => $user->id,
                        'nama'  => $user->nama,
                        'email' => $user->email,
        ]
            ]
        ]);
    }
}
