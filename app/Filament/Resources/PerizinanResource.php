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
use Filament\Forms\Components\Textarea;
// Table Columns
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

// Table Actions
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Actions\Exports\Enums\ExportFormat;


//Table View
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\BadgeEntry;

use function Laravel\Prompts\select;

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

            select::make('status')
                ->label('Status')
                ->extraAttributes([
                    'style' => 'text-align: center;'
                ])
                ->options([
                    'proses_verifikasi' => 'Proses Verifikasi',
                    'disetujui' => 'Disetujui',
                    'ditolak' => 'Ditolak',
                ])
                ->required()
                ->default('proses_verifikasi'),

            Textarea::make('catatan_admin')
                ->label('Catatan Admin')
                ->maxLength(500)
                ->nullable(),    
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->wrap(), // Supaya <br> bisa dibaca

                TextColumn::make('waktu_absen')
                    ->label('Waktu Pengajuan')
                    ->wrapHeader()
                    ->since()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $date = \Carbon\Carbon::parse($state);
                        return  $date->format('H:i') . '<br>' .$date->format('d M Y');
                    })
                    ->extraAttributes([
                        'style' => 'text-align: center;'
                    ])
                    ->html()
                    ->wrap(),

                TextColumn::make('waktu_konfirmasi')
                    ->label('Waktu Konfirmasi')
                    ->wrapHeader()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-')
                    ->formatStateUsing(function ($state) {
                        $date = \Carbon\Carbon::parse($state);
                        return  $date->format('H:i') . '<br>' .$date->format('d M Y');
                    })
                    ->extraAttributes([
                        'style' => 'text-align: center;'
                    ])
                    ->wrap()
                    ->html(),

                TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->wrap()
                    ->extraAttributes([
                        'style' => '
                            max-width: 250px; 
                            white-space: normal; 
                            word-break: break-word; 
                            line-height: 1.2; 
                            vertical-align: middle;
                        '
                    ])
                     ->extraAttributes([
                        'style' => 'text-align: center; vertical-align: middle;'
                    ]),

                ImageColumn::make('gambar')
                    ->label('Foto')
                    ->width(50)       // atur lebar
                    ->height(50)      // optional kalau mau fixed height
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']), // kasih radius,

                TextColumn::make('jenis_izin')
                    ->label('Jenis Perizinan')
                    ->wrapHeader()
                    ->badge()
                    ->colors([
                        'warning' => 'cuti',
                        'danger' => 'sakit',
                        'success' => 'dinas',
                    ])
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->extraAttributes([
                        'style' => 'text-align: center;'
                    ])
                    ->wrap(),

                TextColumn::make('bukti')
                    ->label('Bukti Upload')
                    ->wrap()
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
                    // return '<img src="' . $url . '" style="max-width: 40px; border-radius: 4px;" />';
                    return '<a href="' . $url . '" target="_blank"><img src="' . $url . '" style="max-width: 40px; border-radius: 4px;" /></a>';
                             } else {
                // Jika bukan gambar, tampilkan link download
                    return '<a href="' . $url . '" target="_blank" class="filament-link">📄 Lihat File</a>';
                }
            })
                    ->extraAttributes([
                        'style' => 'text-align: center; vertical-align: middle;'
            ])
                    ->html(), // Izinkan HTML dalam kolom

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->wrap()
                    ->colors([
                        'warning' => 'proses_verifikasi',
                        'success' => 'disetujui',
                        'danger' => 'ditolak',
                        ])
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),
                
                TextColumn::make('catatan_admin')
                    ->label('Catatan Admin')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap() // agar teks panjang turun ke bawah
                    ->color(function ($record) {
                        return match ($record->status) {
                            'disetujui' => 'success', // hijau
                            'ditolak'   => 'danger',  // merah
                            default     => null,      // default warna bawaan
                    };
                }),

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

                Action::make('setujui')
                    ->label('Setujui')
                    ->button() // ✅ tampil sebagai tombol
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui')
                    ->modalDescription('Apakah anda yakin ingin menyetujui izin ini?')
                    ->modalSubmitActionLabel('Ya, Setujui')
                    ->visible(fn ($record) => $record->status === 'proses_verifikasi') // hanya tampil kalau status pending
                    ->action(fn ($record) => $record->update([
                        'status' => 'disetujui',
                        'catatan_admin' => 'Izin Anda disetujui',
                        'waktu_konfirmasi'  => now(),
                    ])),

                Action::make('tolak')
                    ->label('Tolak')
                    ->button() // ✅ tombol
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->form([
                        Forms\Components\Textarea::make('catatan_admin')
                            ->label('Alasan Ditolak')
                            ->required(),
                    ])
                    ->visible(fn ($record) => $record->status === 'proses_verifikasi') // hanya tampil kalau status pending
                    ->action(fn ($record, array $data) => $record->update([
                        'status' => 'ditolak',
                        'catatan_admin' => $data['catatan_admin'],
                        'waktu_konfirmasi'  => now(), 
                    ])),
                ViewAction::make()
                    ->button() // ✅ jadi tombol
                    ->color('info')
                    ->icon('heroicon-o-eye'),
                // EditAction::make(),
                DeleteAction::make()
                    ->button() // ✅ tombol
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->color('success')
                        ->formats([
                            ExportFormat::Xlsx
                            // ExportFormat::Pdf
                        ])
                        ->exporter(PerizinanExporter::class)
                        ->label('Ekspor Perizinan'),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->color('success')
                    ->exporter(PerizinanExporter::class)
                    ->label('Ekspor Perizinan')
                    ->formats([
                        ExportFormat::Xlsx,
                        // ExportFormat::Pdf,
                    ])
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
                        Grid::make(2) // Membuat grid dengan 2 kolom
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
                                    TextEntry::make('pegawai.nama')->label('Nama'),
                                    TextEntry::make('waktu_absen')
                                        ->label('Waktu Absen')
                                        ->dateTime('d M Y, H:i'), // Membuat waktu_absen memanjang ke kanan
                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->colors([
                                            'warning' => 'proses_verifikasi',
                                            'success' => 'disetujui',
                                            'danger' => 'ditolak',
                                        ]),
                                    TextEntry::make('catatan_admin')
                                        ->label('Catatan Admin')
                                        ->columnSpanFull()
                                        ->color(function ($record) {
                                            return match ($record->status) {
                                                'disetujui' => 'success', // hijau
                                                'ditolak'   => 'danger',  // merah
                                                default     => null,
                                            };
                                        }),    
                                ])
                                ->columns(1)
                                ->inlineLabel(), // Membuat group ini memanjang ke kanan
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
            // 'edit' => Pages\EditPerizinan::route('/{record}/edit'),
            'view' => Pages\ViewPerizinan::route('/{record}'),
        ];
    }
}
