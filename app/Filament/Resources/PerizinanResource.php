<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PerizinanExporter;
use App\Filament\Resources\PerizinanResource\Pages;
use App\Models\Absen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

// Form Components
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;

// Table Columns
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

// Table Actions
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;

//Table View
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\BadgeEntry;

class PerizinanResource extends Resource
{
    protected static ?string $model = Absen::class;
    protected static ?string $pluralModelLabel = 'Perizinan';
    protected static ?string $navigationLabel = 'Perizinan';
    protected static ?string $navigationGroup = 'Absen';
    protected static ?int $navigationSort = 2;
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

            Select::make('jenis_izin')
                ->label('Jenis Perizinan')
                ->options([
                    'cuti' => 'Cuti',
                    'sakit' => 'Sakit',
                    'dinas' => 'Dinas',
                ])
                ->required(),

            FileUpload::make('bukti')
                ->label('Bukti(Foto/File)')
                ->directory('bukti-perizinan')
                ->disk('public')
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

                TextColumn::make('jenis_izin')
                    ->label('Jenis Perizinan')
                    ->badge()
                    ->colors([
                        'primary' => 'cuti',
                        'danger' => 'sakit',
                        'success' => 'dinas',
                    ])
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),

                TextColumn::make('bukti')
                    ->label('Bukti Upload')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'No file';
                    }
                        $extension = strtolower(pathinfo($state, PATHINFO_EXTENSION));
                        $url = asset('storage/' .str_replace(' ', '%20', $state));
                        $publicPath = public_path('storage/' . $state);
                //mengecek apakah file ada
                         if (!file_exists($publicPath)) {
                            return '<span style="color:red;">File Tidak Ditemukan</span>';
                    }      

                // Daftar ekstensi gambar
                         $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (in_array($extension, $imageExtensions)) {
                // Jika file adalah gambar, tampilkan preview gambar
                    return '<img src="' . $url . '" style="max-width: 60px; border-radius: 6px;" />';
                             } else {
                // Jika bukan gambar, tampilkan link download
                    return '<a href="' . $url . '" target="_blank" class="filament-link">📄 Lihat File</a>';
                }
            })
                    ->html(), // Izinkan HTML dalam kolom

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_izin')
                ->label('Jenis Perizinan')
                ->options([
                    'cuti' => 'Cuti',
                    'sakit' => 'Sakit',
                    'dinas' => 'Dinas',
                ])
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->exporter(PerizinanExporter::class)
                        ->label('Ekspor Perizinan'),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(PerizinanExporter::class)
                    ->label('Ekspor Perizinan')
                    ->icon('heroicon-o-arrow-down-tray'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Fieldset::make('Detail Perizinan')
                ->schema([
                    Split::make([
                        ImageEntry::make('gambar')
                            ->label('Foto')
                            ->grow(false)
                            ->size(100),
                        Grid::make(2) // Membuat grid dengan 3 kolom
                            ->schema([
                                //kolom kiri
                                Group::make([
                                    TextEntry::make('jenis_izin')
                                        ->label('Jenis Perizinan')
                                        ->badge()
                                        ->colors([
                                            'primary' => 'cuti',
                                            'danger' => 'sakit',
                                            'success' => 'dinas',
                                        ])
                                        ->formatStateUsing(fn (string $state) => ucfirst($state)),
                                    TextEntry::make('nama')->label('Nama'),
                                    TextEntry::make('waktu_absen')
                                        ->label('Waktu Absen')
                                        ->dateTime('d M Y, H:i'), // Membuat waktu_absen memanjang ke kanan
                                ])
                                ->columns(1)
                                ->inlineLabel(), // Membuat group ini memanjang ke kiri
                                Group::make([
                                    TextEntry::make('lokasi')
                                        ->label('Lokasi'),  
                                    // Tampilkan bukti file (gambar/file)
                                    TextEntry::make('bukti')
                                        ->label('Bukti')
                                        ->formatStateUsing(function ($state) {
                                            if (empty($state)) {
                                                return 'No file';
                                            }
                                            $extension = strtolower(pathinfo($state, PATHINFO_EXTENSION));
                                            $url = asset('storage/' . str_replace(' ', '%20', $state));
                                            $publicPath = public_path('storage/' . $state);
                                            if (!file_exists($publicPath)) {
                                                return '<span style="color:red;">File Tidak Ditemukan</span>';
                                            }
                                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                            if (in_array($extension, $imageExtensions)) {
                                                return '<img src="' . $url . '" style="max-width: 80px; border-radius: 6px;" />';
                                            } else {
                                                return '<a href="' . $url . '" target="_blank" class="filament-link">📄 Lihat File</a>';
                                            }
                                        })
                                        ->html(),
                                ])->columns()
                                  ->inlineLabel(), // Membuat group ini memanjang ke kanan
                            ]),
                    ]),
                ])
                ->columns(1),
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
            'view' => Pages\ViewPerizinan::route('/{record}'),
        ];
    }
}
