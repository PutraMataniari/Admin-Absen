<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absen;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsenController extends Controller
{
    // Absen Masuk
    public function masuk(Request $request)
    {
        $validated = $request->validate([
            'lokasi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // max 5MB
        ]);

        $pegawai = $request->user()->pegawai;

        // Jika data pegawai tidak ditemukan
        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai tidak ditemukan'
            ], 404);
        }

        // Cek apakah sudah absen masuk hari ini
        $sudahAbsen = Absen::where('pegawai_id', $pegawai->id)
            ->whereDate('waktu_absen', Carbon::today())
            ->where('jenis', 'Masuk')
            ->first();

        if ($sudahAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen masuk hari ini'
            ], 409);
        }

        // Upload foto masuk
        $fotoPath = null;
        if ($request->hasFile('gambar')) {
            $fotoPath = $request->file('gambar')->store('absen', 'public');
        }

        // Simpan data absen masuk
        $absen = Absen::create([
            'pegawai_id'      => $pegawai->id,
            'jenis'           => 'Masuk',
            'nama'            => $pegawai->nama,
            'waktu_absen'     => now(),
            'lokasi'          => $validated['lokasi'],
            'gambar'          => $fotoPath,
            'laporan_kinerja' => null,
            'bukti'           => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen masuk berhasil',
            'data'    => $absen
        ], 201);
    }

    // Absen Pulang
    public function pulang(Request $request)
    {
        $validated = $request->validate([
            'lokasi'           => 'required|string',
            'gambar'           => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // max 5MB
            'laporan_kinerja'  => 'required|string',
            'bukti'            => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        $pegawai = $request->user()->pegawai;

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai tidak ditemukan'
            ], 404);
        }

        // Cek apakah sudah absen pulang hari ini
        $sudahAbsenPulang = Absen::where('pegawai_id', $pegawai->id)
            ->whereDate('waktu_absen', Carbon::today())
            ->where('jenis', 'Pulang')
            ->first();

        if ($sudahAbsenPulang) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah absen pulang hari ini'
            ], 409);
        }

        // Upload foto pulang
        $fotoPath = null;
        if ($request->hasFile('gambar')) {
            $fotoPath = $request->file('gambar')->store('absen', 'public');
        }

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('bukti', 'public');
        }

        // Simpan data absen pulang
        $absen = Absen::create([
            'pegawai_id'      => $pegawai->id,
            'jenis'           => 'Pulang',
            'nama'            => $pegawai->nama,
            'waktu_absen'     => now(),
            'lokasi'          => $validated['lokasi'],
            'gambar'          => $fotoPath,
            'laporan_kinerja' => $validated['laporan_kinerja'],
            'bukti'           => $buktiPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen pulang berhasil',
            'data'    => $absen
        ], 201);
    }

    // History Absen
    public function history(Request $request)
    {
        $pegawai = $request->user()->pegawai;

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai tidak ditemukan'
            ], 404);
        }

        // Ambil riwayat absensi + relasi pegawai
        $absen = Absen::where('pegawai_id', $pegawai->id)
            ->with('pegawai')
            ->orderBy('waktu_absen', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat absen ditemukan',
            'data'    => $absen
        ]);
    }
}
