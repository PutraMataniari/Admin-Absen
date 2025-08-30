<?php

namespace App\Filament\Widgets;

use App\Models\Pegawai;
use App\Models\Absen;
use App\Models\User;
use Filament\Support\Enums\IconPosition;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            //
            Stat::make('Pegawai', Pegawai::count())
            ->description('Jumlah Pegawai')
            ->color('success'),
            Stat::make('Absen', Absen::count()),
            Stat::make('Users', User::count()),
            Stat::make('Unique views', '192.1k')
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success'),
        ];
    }
}
