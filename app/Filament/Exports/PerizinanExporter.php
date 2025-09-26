<?php

namespace App\Filament\Exports;

use App\Models\Absen;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;

class PerizinanExporter extends Exporter
{
    protected static ?string $model = Absen::class;

    public static function getColumns(): array
    {
        return [
            // ExportColumn::make('id')->label('ID'),
            ExportColumn::make('pegawai.nama')->label('Nama'),
            ExportColumn::make('waktu_absen')->label('Waktu Absen')
                ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('H:i d-m-Y')),
            ExportColumn::make('lokasi')->label('Lokasi'),
            ExportColumn::make('gambar')->label('Foto'),
            ExportColumn::make('jenis_izin')->label('Kategori Izin'),
            ExportColumn::make('bukti')->label('Bukti Upload'),
        ];
    }

    /**
     * Custom export ke Excel dengan gambar + hyperlink.
     */
    public function exportToXlsx($filePath, $records)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // header
        $columns = collect(self::getColumns())->pluck('name')->toArray();
        foreach ($columns as $colIndex => $colName) {
            $cellCoordinate = Coordinate::stringFromColumnIndex($colIndex + 1) . '1';
            $sheet->setCellValue($cellCoordinate, ucfirst($colName));
        }

        // isi data
        $row = 2;
        foreach ($records as $record) {
            $col = 1;

            foreach ($columns as $colName) {
                $value = $record->{$colName};
                $cellCoordinate = Coordinate::stringFromColumnIndex($col) . $row;

                if ($colName === 'gambar' && $value) {
                    $imagePath = storage_path("app/public/{$value}");
                    if (file_exists($imagePath)) {
                        $drawing = new Drawing();
                        $drawing->setPath($imagePath);
                        $drawing->setHeight(80);
                        $drawing->setCoordinates($cellCoordinate);
                        $drawing->setWorksheet($sheet);

                        // atur tinggi baris biar pas
                        $sheet->getRowDimension($row)->setRowHeight(80);
                    }
                } elseif ($colName === 'bukti' && $value) {
                    $url = asset("storage/{$value}");
                    $sheet->setCellValue($cellCoordinate, "Lihat Bukti");
                    $sheet->getCell($cellCoordinate)->getHyperlink()->setUrl($url);
                } else {
                     $sheet->setCellValue($cellCoordinate, $value);
                }

                $col++;
            }
            $row++;
        }

        // simpan file
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your perizinan export has completed and ' 
            . number_format($export->successful_rows) . ' ' 
            . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' 
                . str('row')->plural($failedRowsCount) . ' failed to export.';
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
