<?php

namespace App\Filament\Resources\AbsenKeluarResource\Pages;

use App\Filament\Resources\AbsenKeluarResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAbsenKeluar extends CreateRecord
{
    protected static string $resource = AbsenKeluarResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['jenis'] = 'keluar';
        return $data;
    }
}
