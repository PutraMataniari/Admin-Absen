<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    // Request OTP
    public function requestReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan'
            ], 404);
        }

        // generate OTP random 6 digit
        $otp = rand(100000, 999999);

        // hapus OTP lama (kalau ada)
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // simpan OTP baru
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $otp,
            'created_at' => Carbon::now()
        ]);

        // kirim email
        Mail::raw("Kode OTP reset password Anda: $otp (berlaku 5 menit)", function ($msg) use ($request) {
            $msg->to($request->email)->subject('Reset Password OTP');
        });

        return response()->json([
            'success' => true,
            'message' => 'OTP baru telah dikirim ke email Anda (berlaku selama 5 menit)'
        ]);
    }


    // Reset Password dengan OTP
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'new_password' => 'required|min:6'
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();
        if (!$reset) {
            return response()->json([
                'success' => false,
                'message' => 'OTP salah'], 400);
        }

       // cek expired (misalnya 5 menit)
        if (Carbon::parse($reset->created_at)->addMinutes(5)->isPast()) {
            // hapus OTP biar tidak bisa dipakai lagi
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json([
                'success' => false,
                'message' => 'OTP sudah kadaluwarsa'
            ], 400);
        }

        // update password user
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->new_password);
        $user->save();

        // hapus OTP biar 1x pakai
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset']);
    }
}
