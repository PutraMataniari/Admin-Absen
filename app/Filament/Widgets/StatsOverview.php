<?php

namespace App\Filament\Widgets;

use App\Models\Pegawai;
use App\Models\Absen;
use App\Models\User;
use App\Models\Perizinan;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    // protected int | string | array $columnSpan = 'full'; // full width

     protected function getColumns(): int
     {
        return 4; // tampilkan 4 stat box sejajar
     }

    protected function getStats(): array
    {
        $today = Carbon::today();


        // Hitung absen
        $absenMasuk = Absen::where('jenis', 'masuk')
            ->whereDate('waktu_absen', $today)
            ->count();

        $absenPulang = Absen::where('jenis', 'pulang')
            ->whereDate('waktu_absen', $today)
            ->count();

        // Hitung izin per jenis
        $izinDinas = Absen::where('jenis', 'izin')
                    ->where('status', 'pending')
                    ->where('jenis_izin', 'dinas')
                    ->whereDate('waktu_absen', $today)
                    ->count();

        $izinSakit = Absen::where('jenis', 'izin')
                    ->where('status', 'pending')
                    ->where('jenis_izin', 'sakit')
                    ->whereDate('waktu_absen', $today)
                    ->count();

        $izinCuti = Absen::where('jenis', 'izin')
                    ->where('status', 'pending')
                    ->where('jenis_izin', 'cuti')
                    ->whereDate('waktu_absen', $today)
                    ->count();

        return [
            Stat::make('Pegawai', Pegawai::count())
                ->description('Jumlah Pegawai')
                ->icon('heroicon-o-users')
                ->color('success'),

            // ✅ Absen Masuk & Pulang dalam 1 box
            stat::make('Absen Hari Ini', $absenMasuk + $absenPulang)
                ->description("Masuk: {$absenMasuk} | Pulang: {$absenPulang}")
                ->icon('heroicon-o-check-circle')
                ->color('info'),


            Stat::make('Perizinan Hari Ini', $izinDinas + $izinSakit + $izinCuti)
                ->icon('heroicon-o-document-text')
                ->description("Dinas: {$izinDinas} | Sakit: {$izinSakit} | Cuti: {$izinCuti}")
                ->color('warning'),


            Stat::make('Users', User::count())
                ->description('Jumlah akun user')
                ->icon('heroicon-o-user-circle')
                ->color('primary'),
        ];
    }
}
