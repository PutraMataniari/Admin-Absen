<?php

namespace App\Filament\Resources\AbsenMasukResource\Pages;

use App\Filament\Resources\AbsenMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Facades\Filament;


class ListAbsen extends ListRecords
{
    protected static string $resource = AbsenMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

public function getBreadcrumbs(): array
{
    return [
        route('filament.admin.resources.absen-masuks.index') => 'Absen Masuk',
        'List Absen Masuk',
    ];
}


}
