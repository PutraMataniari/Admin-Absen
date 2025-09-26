<?php

namespace App\Filament\Exports;

use App\Models\Absen;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;

class AbsenExporter extends Exporter
{
    protected static ?string $model = Absen::class;

    public static function getColumns(): array
    {
        return [
            // ExportColumn::make('id')
            //     ->label('ID'),
            ExportColumn::make('jenis')
                ->label('Jenis Absen'),
            ExportColumn::make('pegawai.nama')
                ->label('Nama'),
            ExportColumn::make('waktu_absen')
                ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('H:i d-m-Y'))
                ->label('Waktu Absen'),
            ExportColumn::make('lokasi')
                ->label('Lokasi'),
            // ExportColumn::make('gambar')
            //     ->label('Foto'),
            ExportColumn::make('laporan_kinerja')
                ->label('Laporan Kinerja'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your absen export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
     public function getXlsxHeaderCellStyle(): ?Style
    {
        // Define the style for header cells in the exported Excel file
    return (new Style())
        ->setFontBold()
        // ->setFontItalic()
        ->setFontSize(12)
        ->setFontName('Consolas')
        ->setFontColor(Color::rgb(0, 0, 0))
        ->setBackgroundColor(Color::rgb(0, 218, 0))
        ->setCellAlignment(CellAlignment::CENTER)
        ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    }
}
