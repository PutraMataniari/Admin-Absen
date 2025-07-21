<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerizinanResource\Pages;
use App\Models\Absen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Form Components
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;

// Table Columns
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;

// Table Actions
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class PerizinanResource extends Resource
{
    protected static ?string $model = Absen::class;
    protected static ?string $pluralModelLabel = 'Perizinan';
    protected static ?string $navigationLabel = 'Perizinan';
    protected static ?string $navigationGroup = 'Absen';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('jenis', 'izin');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama')
                ->label('Nama')
                ->required()
                ->maxLength(100),

            DateTimePicker::make('waktu_absen')
                ->label('Waktu Absen')
                ->required(),

            TextInput::make('lokasi')
                ->label('Lokasi')
                ->required()
                ->maxLength(255),

            FileUpload::make('gambar')
                ->label('Foto')
                ->image()
                ->directory('perizinan')
                ->required(),

            Select::make('keterangan')
                ->label('Jenis Perizinan')
                ->options([
                    'izin/cuti' => 'Izin/Cuti',
                    'sakit' => 'Sakit',
                    'dinas' => 'Dinas',
                ])
                ->required(),

            FileUpload::make('bukti')
                ->label('Bukti (Foto/File)')
                ->directory('bukti-perizinan')
                ->preserveFilenames()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->label('Nama')->searchable(),

                TextColumn::make('waktu_absen')->label('Waktu')->dateTime(),

                TextColumn::make('lokasi')->label('Lokasi')->wrap(),

                ImageColumn::make('gambar')->label('Foto'),

                BadgeColumn::make('keterangan')
                    ->label('Jenis Perizinan')
                    ->colors([
                        'izin/cuti' => 'primary',
                        'sakit' => 'danger',
                        'dinas' => 'success',
                    ])
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),

                TextColumn::make('bukti')
                    ->label('Bukti Upload')
                    ->formatStateUsing(function ($state) {
                        $extension = pathinfo($state, PATHINFO_EXTENSION);
                        $url = asset('storage/' . $state);

                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array(strtolower($extension), $imageExtensions)) {
                    return '<img src="' . $url . '" style="width: 60px; border-radius: 6px;" />';
                    }

                    // Tampilkan link download untuk non-image
                    return '<a href="' . $url . '" target="_blank">📄 Lihat File</a>';
                })
                    ->html(), // Penting untuk mengizinkan HTML dalam kolom

            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListPerizinan::route('/'),
            'create' => Pages\CreatePerizinan::route('/create'),
            'edit' => Pages\EditPerizinan::route('/{record}/edit'),
        ];
    }
}
