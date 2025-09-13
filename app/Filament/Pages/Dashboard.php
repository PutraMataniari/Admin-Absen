<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\MonthlyAbsenChart;
use App\Filament\Widgets\TodayAbsenTable;
use App\Filament\Widgets\ActivePerizinanTable;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            TodayAbsenTable::class,
            ActivePerizinanTable::class,
            MonthlyAbsenChart::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2; // grid 2 kolom (stats bisa rapat, tabel full width)
    }
}
