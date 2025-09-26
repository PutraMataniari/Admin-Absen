<?php

namespace App\Filament\Widgets;

use App\Models\Absen;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class MonthlyAbsenChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Absensi Bulanan';

    public static function canView(): bool
    {
        return true;
    }
    

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $records = Absen::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->created_at)->format('d');
            });

        $days = [];
        $masuk = [];
        $pulang = [];
        $izin = [];

        foreach (range(1, $endOfMonth->day) as $day) {
            $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
            $days[] = $dayStr;

            $masuk[] = isset($records[$dayStr]) 
                ? $records[$dayStr]->where('jenis', 'Masuk')->count() 
                : 0;

            $pulang[] = isset($records[$dayStr]) 
                ? $records[$dayStr]->where('jenis', 'Pulang')->count() 
                : 0;

            $izin[] = isset($records[$dayStr]) 
                ? $records[$dayStr]->where('jenis', 'izin')->count() 
                : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Masuk',
                    'data' => $masuk,
                    'backgroundColor' => '#25dd00ff', // hijau
                ],
                [
                    'label' => 'Pulang',
                    'data' => $pulang,
                    'backgroundColor' => '#2173f7ff', // biru
                ],
                [
                    'label' => 'Izin',
                    'data' => $izin,
                    'backgroundColor' => '#f5e50bff', // kuning
                ],
            ],
            'labels' => $days,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bisa diganti "line" kalau mau garis
    }
}
