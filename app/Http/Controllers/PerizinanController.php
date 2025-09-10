<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PerizinanController extends Controller
{
    // ✅ Pengajuan izin (cuti, sakit, dinas)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'lokasi'     => 'required|string',
            'jenis_izin' => 'required|in:cuti,Cuti,sakit,Sakit,dinas,Dinas',
            'gambar'     => 'required|image|mimes:jpg,jpeg,png|max:5120', // max 5MB
            'bukti'      => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120' // max 5MB
        ]);

        // Ambil data pegawai dari user yang sedang login
        $pegawai = Auth::user()->pegawai;

        if (!$pegawai) {
            return response()->json([
                'status'  => false,
                'message' => 'Pegawai tidak ditemukan'
            ], 404);
        }

        // Upload foto selfie
        $gambarPath = $request->file('gambar')->store('absen/gambar', 'public');

        // Upload lampiran bukti
        $buktiPath = $request->file('bukti')->store('absen/bukti', 'public');
        $buktiAsli = $request->file('bukti')->getClientOriginalName();

        // Simpan data izin
        $izin = Absen::create([
            'pegawai_id'  => $pegawai->id,
            'jenis'       => 'izin',
            'waktu_absen' => now(),
            'lokasi'      => $validated['lokasi'],
            'gambar'      => $gambarPath,
            'jenis_izin'  => $validated['jenis_izin'],
            'bukti'       => $buktiPath,
            'bukti_asli'  => $buktiAsli,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Pengajuan izin berhasil disimpan',
            'data'    => $izin
        ], 201);
    }
}
