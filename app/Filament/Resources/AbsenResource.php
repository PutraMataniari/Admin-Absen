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
use App\Filament\Exports\AbsenExporter;
use Illuminate\Support\Facades\Storage;


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
        ->whereIn('jenis', ['masuk', 'pulang']);
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
                        'success' => 'masuk',
                        'danger' => 'pulang',
                    ])
                    ->sortable(),
                TextColumn::make('nama')->searchable(),
                TextColumn::make('waktu_absen')->dateTime(),
                TextColumn::make('lokasi')->label('Lokasi')->wrap(),
                ImageColumn::make('gambar')
                    ->label('Bukti Foto')
                    ->disk('public')
                    // ->url(fn ($record) => $record->gambar ? Storage::url($record->gambar) : null)
                    ->placeholder('No photo'),
                TextColumn::make('laporan_kinerja')
                    ->wrap()->limit(100),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->options([
                        'masuk' => 'Masuk',
                        'pulang' => 'Pulang',
                    ])
                    ->label('Filter Jenis Absen'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->exporter(AbsenExporter::class)
                        ->label('Ekspor Absen'),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                ExportAction::make()
                    ->exporter(AbsenExporter::class)
                    ->label('Ekspor Absen')
                    ->icon('heroicon-o-arrow-down-tray'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsens::route('/'),
            'create' => Pages\CreateAbsen::route('/create'),
            'edit' => Pages\EditAbsen::route('/{record}/edit'),
        ];
    }
}
