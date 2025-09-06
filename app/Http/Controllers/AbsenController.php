<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absen;
use Illuminate\Support\Facades\Auth;

class AbsenController extends Controller
{
    // Absen Masuk
    public function masuk(Request $request)
    {
        $validated = $request->validate([
            'nama'   => 'required|string|max:255',
            'lokasi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        // Upload foto masuk
        $fotoPath = null;
        if ($request->hasFile('gambar')) {
            $fotoPath = $request->file('gambar')->store('absen', 'public');
        }

        // Simpan ke database
        $absen = Absen::create([
            'jenis'           => 'Masuk',
            'nama'            => $validated['nama'],
            'waktu_absen'     => now(),
            'lokasi'          => $validated['lokasi'],
            'gambar'          => $fotoPath,
            'laporan_kinerja' => null,
            'bukti'           => null,
        ]);

        return response()->json([
            'message' => 'Absen masuk berhasil',
            'data'    => $absen
        ], 201);
    }

    // Absen Pulang
    public function pulang(Request $request)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:255',
            'lokasi'           => 'required|string',
            'gambar'           => 'nullable|image|mimes:jpg,jpeg,png',
            'laporan_kinerja'  => 'required|string',
            'bukti'            => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        // Upload foto pulang
        $fotoPath = null;
        if ($request->hasFile('gambar')) {
            $fotoPath = $request->file('gambar')->store('absen', 'public');
        }

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('bukti', 'public');
        }

        // Simpan ke database
        $absen = Absen::create([
            'jenis'           => 'Pulang',
            'nama'            => $validated['nama'],
            'waktu_absen'     => now(),
            'lokasi'          => $validated['lokasi'],
            'gambar'          => $fotoPath,
            'laporan_kinerja' => $validated['laporan_kinerja'],
            'bukti'           => $buktiPath,
        ]);

        return response()->json([
            'message' => 'Absen pulang berhasil',
            'data'    => $absen
        ], 201);
    }

    // History
    public function history(Request $request)
    {
        $user = $request->user();

        $absen = Absen::where('nama', $user->name)
            ->orderBy('waktu_absen', 'desc')
            ->get();

        return response()->json([
            'message' => 'Riwayat absen ditemukan',
            'data'    => $absen
        ]);
    }
}
