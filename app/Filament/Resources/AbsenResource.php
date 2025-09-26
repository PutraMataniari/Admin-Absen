<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsenResource\Pages;
use App\Models\Absen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Filament\Tables\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use App\Filament\Exports\AbsenExporter;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Infolist;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;


class AbsenResource extends Resource
{
    protected static ?string $model = Absen::class;
    protected static ?string $pluralModelLabel = 'Absen';
    protected static ?string $navigationLabel = 'Absen';
    protected static ?string $navigationGroup = 'Absen';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    // ✅ Query default (bisa diubah untuk filter global jika perlu)
    public static function getEloquentQuery(): EloquentBuilder
    {
        return parent::getEloquentQuery()
            ->whereIn('jenis', ['masuk', 'pulang'])
            ->orderByRaw(
                "MONTH(waktu_absen) = 1 DESC,
                MONTH(waktu_absen) = 2 DESC, 
                MONTH(waktu_absen) = 3 DESC,
                MONTH(waktu_absen) = 4 DESC,
                MONTH(waktu_absen) = 5 DESC,
                MONTH(waktu_absen) = 6 DESC,
                MONTH(waktu_absen) = 7 DESC,
                MONTH(waktu_absen) = 8 DESC,
                MONTH(waktu_absen) = 9 DESC,
                MONTH(waktu_absen) = 10 DESC, 
                MONTH(waktu_absen) = 11 DESC, 
                MONTH(waktu_absen) DESC, waktu_absen DESC");
    }

    // ✅ Form input gabungan
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('jenis')
                    ->options([
                        'masuk' => 'Masuk',
                        'pulang' => 'Pulang',
                    ])
                    ->required()
                    ->label('Jenis Absen'),
                TextInput::make('nama')
                    ->required()
                    ->maxLength(100),
                DateTimePicker::make('waktu_absen')
                    ->required(),
                TextInput::make('lokasi')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('gambar')
                    ->image()
                    ->directory('absen')
                    ->disk('public')
                    ->required(),
                Textarea::make('laporan_kinerja')
                    ->maxLength(500)
                    ->label('Laporan Kinerja'),
            ]);
    }

    // ✅ Tabel gabungan
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->colors([
                        'success' => 'Masuk',
                        'danger' => 'Pulang',
                    ])
                    ->sortable(),
                TextColumn::make('pegawai.nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        $parts = explode(' ', $state);
                        if (count($parts) > 3) {
                            // Gabungkan 2 kata pertama, kata ke-3 dan seterusnya di baris bawah
                            return implode(' ', array_slice($parts, 0, 2)) . '<br>' . implode(' ', array_slice($parts, 2));
                        }
                        return $state;
                    })
                    ->html()
                    ->wrap(),
                TextColumn::make('waktu_absen')
                    ->formatStateUsing(function ($state) {
                        $date = \Carbon\Carbon::parse($state);
                        return  $date->format('d M Y') . '<br>' .$date->format('H:i');
                    })
                    ->html(),
                TextColumn::make('lokasi')->label('Lokasi')->wrap(),
                ImageColumn::make('gambar')
                    ->label('Bukti Foto')
                    ->disk('public')
                    // ->url(fn ($record) => $record->gambar ? Storage::url($record->gambar) : null)
                    ->placeholder('No photo')
                    ->width(80)       // atur lebar
                    ->height(80)      // optional kalau mau fixed height
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']), // kasih radius
                TextColumn::make('laporan_kinerja')
                    ->wrap()->limit(100),
            ])
            ->filters([
            Tables\Filters\SelectFilter::make('jenis')
                ->options([
                    'Masuk' => 'Masuk',
                    'Pulang' => 'Pulang',
                ]),
            Filter::make('created_at')        
                ->label('Filter Bulan')
                ->form([
                    DatePicker::make('waktu_absen_from'),
                    DatePicker::make('waktu_absen_until'),
            ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                ->when(
                    $data['waktu_absen_from'],
                    fn (Builder $query, $date): Builder => $query->whereDate('waktu_absen', '>=', $date),
                )
                ->when(
                    $data['waktu_absen_until'],
                    fn (Builder $query, $date): Builder => $query->whereDate('waktu_absen', '<=', $date),
                );
            })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                if ($data['waktu_absen_from'] ?? null) {
                    $indicators[] = Indicator::make('waktu_absen_from ' . Carbon::parse($data['waktu_absen_from'])
                ->toFormattedDateString())
                ->removeField('waktu_absen_from');
                }

                if ($data['waktu_absen_until'] ?? null) {
                    $indicators[] = Indicator::make('waktu_absen_until ' . Carbon::parse($data['waktu_absen_until'])
                ->toFormattedDateString())
                ->removeField('waktu_absen_until');
            }

            return $indicators;
        }),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                ViewAction::make()
                    ->button() // ✅ jadi tombol
                    ->color('info')
                    ->icon('heroicon-o-eye'),
                DeleteAction::make()
                    ->button() // ✅ tombol
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->color('success')
                        ->formats([
                            ExportFormat::Xlsx
                            // ExportFormat::Pdf
                        ])
                        ->exporter(AbsenExporter::class)
                        ->label('Ekspor Absen'),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                ExportAction::make()
                    ->color('success')
                    ->exporter(AbsenExporter::class)
                    ->label('Ekspor Absen')
                    ->formats([
                        ExportFormat::Xlsx,
                        // ExportFormat::Pdf,
                    ])
                    ->icon('heroicon-o-arrow-down-tray'),
            ]);
    }

// ...existing code...
public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Fieldset::make('Detail Absen')
                ->schema([
                    Split::make([
                        // KIRI: Foto
                        ImageEntry::make('gambar')
                            ->hiddenLabel()
                            ->grow(false)
                            ->size(100),

                        // KANAN: Dua kolom data
                        Grid::make(2)
                            ->schema([
                                // Kolom kiri
                                Group::make([
                                    TextEntry::make('jenis')
                                        ->label('Jenis Absen')
                                        ->badge()
                                        ->colors([
                                            'success' => 'masuk',
                                            'danger' => 'pulang',
                                        ]),
                                    TextEntry::make('waktu_absen')->label('Waktu Absen'),
                                    TextEntry::make('pegawai.nama')->label('Nama'),
                                ])
                                ->columns(1)
                                ->inlineLabel(),

                                // Kolom kanan
                                Group::make([
                                    TextEntry::make('lokasi')->label('Lokasi'),
                                    TextEntry::make('laporan_kinerja')->label('Laporan Kinerja'),
                                ])
                                ->columns(1)
                                ->inlineLabel(),
                            ]),
                    ]),
                ])
                ->columns(1), // Fieldset hanya 1 kolom
        ]);
}
// ...existing code...

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsens::route('/'),
            'create' => Pages\CreateAbsen::route('/create'),
            'view' => Pages\ViewAbsen::route('/{record}'),
            // 'edit' => Pages\EditAbsen::route('/{record}/edit'),
        ];
    }
}
