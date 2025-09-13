<?php

namespace App\Filament\Widgets;

use App\Models\Absen;
use App\Models\Perizinan;
use Filament\Tables;
use Filament\Widgets\TableWidget;;
use Carbon\Carbon;

class ActivePerizinanTable extends TableWidget
{
    protected static ?string $heading = 'Tabel Perizinan Aktif Hari Ini';

    public static function canView(): bool
    {
        return true;
    }

    protected int | string | array $columnSpan = 'full';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                Absen::query()
                    ->where('jenis', 'izin')
                    ->where('status', 'pending')
                    ->whereDate('waktu_absen', Carbon::today())
            )
            ->columns([
                Tables\Columns\TextColumn::make('pegawai.nama')->label('Nama Pegawai'),
                Tables\Columns\TextColumn::make('jenis_izin')
                    ->label('Jenis Izin')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cuti'  => 'primary', // biru
                        'sakit' => 'danger',  // merah
                        'dinas' => 'success', // hijau
                        default => 'gray',    // default abu-abu kalau tidak cocok
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cuti'  => '🗓️ Cuti',
                        'sakit' => '💊 Sakit',
                        'dinas' => '🏢 Dinas',
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('waktu_absen')
                    ->label('Waktu Pengajuan')
                    ->dateTime('d M Y H:i', 'Asia/Jakarta'),
            ]);
    }
}
