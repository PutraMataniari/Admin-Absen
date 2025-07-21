<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsenMasukResource\Pages;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class AbsenMasukResource extends Resource
{
    protected static ?string $model = Absen::class;
    protected static ?string $pluralModelLabel = 'Absen Masuk';
    protected static ?string $navigationLabel = 'Absen Masuk';
    protected static ?string $navigationGroup = 'Absen';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square';

    // ✅ Filter hanya data dengan jenis = masuk
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('jenis', 'masuk');
    }

    // ✅ Form untuk absen masuk
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
                    ->directory('absen-masuk')
                    ->required(),
                // Forms\Components\Textarea::make('keterangan')
                //     ->maxLength(500)
                //     ->label('Keterangan (Opsional)'),
            ]);
    }

    // ✅ Tabel data absen masuk
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->searchable(),
                TextColumn::make('waktu_absen')->dateTime(),
                TextColumn::make('lokasi')->label('Lokasi')->wrap(),
                ImageColumn::make('gambar')->label('Bukti Foto'),
                // TextColumn::make('keterangan')->wrap()->limit(50),
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
            'index' => Pages\ListAbsen::route('/'),
            'create' => Pages\CreateAbsen::route('/create'),
            'edit' => Pages\EditAbsen::route('/{record}/edit'),
        ];
    }
}
