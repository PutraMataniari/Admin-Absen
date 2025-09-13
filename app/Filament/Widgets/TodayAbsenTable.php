<?php

namespace App\Filament\Widgets;

use App\Models\Absen;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class TodayAbsenTable extends BaseWidget
{
    protected static ?string $heading = 'Tabel Absensi Hari Ini';

    public static function canView(): bool
    {
        return true;
    }

    protected int | string | array $columnSpan = 'full';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                Absen::query()->whereDate('created_at', Carbon::today())
            )
            ->columns([
                Tables\Columns\TextColumn::make('pegawai.nama')->label('Nama Pegawai'),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis Absen')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Masuk'  => 'success', // hijau
                        'Pulang' => 'danger',  // merah
                        'izin'   => 'warning', // kuning/oranye
                        default  => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    // ->dateTime('H:i')
                    ->dateTime('d M Y H:i', 'Asia/Jakarta'),
            ]);
    }
}
