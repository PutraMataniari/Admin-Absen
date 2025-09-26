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
            'status'      => 'proses_verifikasi', // status awal
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Pengajuan izin berhasil disimpan',
            'data'    => [
                'id'               => $izin->id,
                'jenis'            => $izin->jenis,
                'pegawai_id'       => $izin->pegawai_id,
                'waktu_absen'      => $izin->waktu_absen,
                'waktu_konfirmasi' => $izin->waktu_konfirmasi, // ✅ null dulu
                'lokasi'           => $izin->lokasi,
                'gambar'           => $izin->gambar,
                'jenis_izin'       => $izin->jenis_izin,
                'bukti'            => $izin->bukti,
                'bukti_asli'       => $izin->bukti_asli,
                'status'           => $izin->status,
                'catatan_admin'    => $izin->catatan_admin,
            ]
        ], 201);
    }

    // ✅ Konfirmasi izin oleh admin
    public function konfirmasi(Request $request, Absen $izin)
    {
        $validated = $request->validate([
            'status'        => 'required|in:disetujui,ditolak',
            'catatan_admin' => 'nullable|string'
        ]);

        $izin->update([
            'status'           => $validated['status'],
            'catatan_admin'    => $validated['catatan_admin'],
            'waktu_konfirmasi' => now(), // ✅ catat kapan admin konfirmasi
        ]);

       return response()->json([
            'status'  => true,
            'message' => 'Izin berhasil ' . $validated['status'],
            'data'    => [
                'id'               => $izin->id,
                'jenis'            => $izin->jenis,
                'pegawai_id'       => $izin->pegawai_id,
                'waktu_absen'      => $izin->waktu_absen,
                'waktu_konfirmasi' => $izin->waktu_konfirmasi, // ✅ sudah ada nilainya
                'lokasi'           => $izin->lokasi,
                'gambar'           => $izin->gambar,
                'jenis_izin'       => $izin->jenis_izin,
                'bukti'            => $izin->bukti,
                'bukti_asli'       => $izin->bukti_asli,
                'status'           => $izin->status,
                'catatan_admin'    => $izin->catatan_admin,
            ]
        ]);
    }
}
