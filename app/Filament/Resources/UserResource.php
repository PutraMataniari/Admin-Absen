<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\UserResource\Api\Transformers\UserTransformer;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->label('Nama')
                ->required(),
                TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),
                TextInput::make('password')
                ->password()
                ->dehydrated(fn ($state) => filled($state)) // Hanya dehidrasi jika diisi
                ->required(fn (string $context): bool => $context === 'create') // Wajib diisi saat membuat
                ->minLength(6)
                ->same('passwordConfirmation')
                ->label('Password'),
                TextInput::make('passwordConfirmation')
                ->password()
                ->dehydrated(false) // Jangan dehidrasi
                ->required(fn (string $context): bool => $context === 'create') // Wajib diisi saat membuat
                ->minLength(6)
                ->label('Konfirmasi Password'),
                TextInput::make('role')
                ->label('Role')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role') // ✅ tambahkan ini
                    ->label('Role')
                    ->sortable(),
                // TextColumn::make('password')
                //     ->label('Password'),
                    // ->hidden(), // Sembunyikan kolom password
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->button() // ✅ tombol
                    ->color('warning')
                    ->icon('heroicon-o-pencil-square'),
                DeleteAction::make()
                    ->button() // ✅ tombol
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
     public static function getApiTransformer()
    {
        return UserTransformer::class;
    }
}
