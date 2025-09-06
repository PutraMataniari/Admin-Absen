<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use Illuminate\Http\Request;

class PerizinanController extends Controller
{
    // ✅ Pengajuan izin
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'lokasi'     => 'required|string',
            'jenis_izin' => 'required|in:cuti,sakit,dinas',
            'gambar'     => 'required|image|mimes:jpg,jpeg,png',
            'bukti'      => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx'
        ]);

        // Upload foto selfie
        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('absen/gambar', 'public');
        }

        // Upload lampiran (bukti pendukung)
        $buktiPath = null;
        $buktiAsli = null;
        if ($request->hasFile('bukti')) {
            $buktiFile = $request->file('bukti');
            $buktiAsli = $buktiFile->getClientOriginalName(); // Simpan nama file asli
            $buktiPath = $request->file('bukti')->store('absen/bukti', 'public');
        }

        $izin = Absen::create([
            'jenis'       => 'izin', // supaya dibedakan dari masuk/pulang
            'nama'        => $validated['nama'],
            'waktu_absen' => now(),
            'lokasi'      => $validated['lokasi'],
            'gambar'      => $gambarPath,
            'jenis_izin'  => $validated['jenis_izin'],
            'bukti'       => $buktiPath,
            'bukti_asli'  => $buktiAsli, // simpan nama file asli
        ]);

        return response()->json([
            'message' => 'Pengajuan izin berhasil disimpan',
            'data'    => $izin
        ], 201);
    }

    // ✅ Riwayat izin (khusus data izin saja)
    public function history(Request $request)
    {
        $user = $request->user();

        $izin = Absen::where('nama', $user->name)
            ->where('jenis', 'izin') // hanya ambil data izin
            ->orderBy('waktu_absen', 'desc')
            ->get();

        return response()->json([
            'message' => 'Riwayat izin ditemukan',
            'data'    => $izin
        ]);
    }
}
