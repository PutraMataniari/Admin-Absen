<?php

namespace App\Filament\Exports;

use App\Models\Pegawai;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

// use OpenSpout\Common\Entity\Style\CellAlignment;
// use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
// use OpenSpout\Common\Entity\Style\Color;
// use OpenSpout\Common\Entity\Style\Style;


class PegawaiExporter extends Exporter
{
    protected static ?string $model = Pegawai::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('foto_profil'),
            ExportColumn::make('nama'),
            ExportColumn::make('nip'),
            ExportColumn::make('email'),
            ExportColumn::make('no_telp'),
            ExportColumn::make('tanggal_lahir'),
            ExportColumn::make('jabatan'),
            ExportColumn::make('bagian'),
            ExportColumn::make('sub_bagian'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your pegawai export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    // public function getXlsxHeaderCellStyle(): ?Style
    // {
    //     // Define the style for header cells in the exported Excel file
    // return (new Style())
    //     ->setFontBold()
    //     ->setFontItalic()
    //     ->setFontSize(14)
    //     ->setFontName('Consolas')
    //     ->setFontColor(Color::rgb(255, 255, 77))
    //     ->setBackgroundColor(Color::rgb(0, 0, 0))
    //     ->setCellAlignment(CellAlignment::CENTER)
    //     ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    // }
}
