<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsenKeluarResource\Pages;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class AbsenKeluarResource extends Resource
{
    protected static ?string $model = Absen::class;
    protected static ?string $pluralModelLabel = 'Absen Keluar';
    protected static ?string $navigationLabel = 'Absen Keluar';
    protected static ?string $navigationGroup = 'Absen';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square';

    // ✅ Filter hanya data dengan jenis = keluar
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('jenis', 'keluar');
    }

    // ✅ Form input untuk absen keluar
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->directory('absen-keluar')
                    ->required(),
                Textarea::make('keterangan')
                    // ->label('Keterangan (Opsional)')
                    ->maxLength(500),
            ]);
    }

    // ✅ Tabel data absen keluar
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->searchable(),
                TextColumn::make('waktu_absen')->dateTime(),
                TextColumn::make('lokasi')->label('Lokasi')->wrap(),
                ImageColumn::make('gambar')->label('Bukti Foto'),
                TextColumn::make('keterangan')->wrap()->limit(50),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsenKeluars::route('/'),
            'create' => Pages\CreateAbsenKeluar::route('/create'),
            'edit' => Pages\EditAbsenKeluar::route('/{record}/edit'),
        ];
    }
}
