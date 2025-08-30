<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PegawaiExporter;
use App\Filament\Resources\PegawaiResource\Pages;
use App\Filament\Resources\PegawaiResource\RelationManagers;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Group;
use Filament\Tables\Actions\ExportAction;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;
    protected static ?string $navigationLabel = 'Pegawai';
    protected static ?string $pluralModelLabel = 'Pegawai';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('foto_profil')
                    ->image()
                    ->directory('pegawai')
                    ->required(),
                TextInput::make('nama')
                    ->required()
                    ->maxLength(100),
                TextInput::make('nip')
                    ->label('NIP')
                    ->required()
                    ->numeric()
                    ->helperText('Masukkan NIP tanpa spasi atau tanda baca.')
                    ->maxLength(100),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('no_telp')
                    ->tel()
                    ->required()
                    ->maxLength(20),
                DatePicker::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->required()
                    ->maxDate(now())
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection(),
                TextInput::make('jabatan')
                    ->required()
                    ->maxLength(100),
                TextInput::make('bagian')
                    ->required()
                    ->maxLength(100),
                TextInput::make('sub_bagian')
                    ->required()
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_profil')->label('Foto Profil'),
                TextColumn::make('nama')->searchable(),
                TextColumn::make('nip'),
                TextColumn::make('email'),
                TextColumn::make('no_telp'),
                TextColumn::make('tanggal_lahir'),
                TextColumn::make('jabatan'),
                TextColumn::make('bagian'),
                TextColumn::make('sub_bagian'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->exporter(PegawaiExporter::class)
                        ->label('Ekspor Pegawai'),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                ExportAction::make()
                    ->exporter(PegawaiExporter::class)
                    ->label('Ekspor Pegawai')
                    ->icon('heroicon-o-arrow-down-tray'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Section::make()
                ->schema([
                    Fieldset::make('Biodata Pegawai')
                        ->schema([
                            Split::make([
                                // KIRI: Foto Profil
                                ImageEntry::make('foto_profil')
                                    ->hiddenLabel()
                                    ->grow(false)
                                    ->size(100), // ukuran foto (opsional)

                                // KANAN: Dua kolom data
                                Grid::make(2)
                                    ->schema([
                                        // Kolom kiri
                                        Group::make([
                                            TextEntry::make('nama')->label('Nama'),
                                            TextEntry::make('nip')->label('NIP'),
                                            TextEntry::make('email')->label('Email'),
                                            TextEntry::make('no_telp')->label('No Telp'),
                                        ])
                                        ->columns(1)
                                        ->inlineLabel(),

                                        // Kolom kanan
                                        Group::make([
                                            TextEntry::make('tanggal_lahir')->label('Tanggal Lahir'),
                                            TextEntry::make('jabatan')->label('Jabatan'),
                                            TextEntry::make('bagian')->label('Bagian'),
                                            TextEntry::make('sub_bagian')->label('Sub Bagian'),
                                        ])
                                        ->columns(1)
                                        ->inlineLabel(),
                                    ])
                            ])
                        ])
                        ->columns(1), // Fieldset hanya 1 kolom
                ])
                ->columns(2), // Section: 2 kolom (foto + data)
        ]);
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'view' => Pages\ViewPegawai::route('/{record}'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
