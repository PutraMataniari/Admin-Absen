<?php

namespace App\Filament\Resources\AbsenKeluarResource\Pages;

use App\Filament\Resources\AbsenKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbsenKeluars extends ListRecords
{
    protected static string $resource = AbsenKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
{
    return [
        route('filament.admin.resources.absen-keluars.index') => 'Absen Keluar',
        'List Absen Keluar',
    ];
}
}
