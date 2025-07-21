<?php

namespace App\Filament\Resources\AbsenMasukResource\Pages;

use App\Filament\Resources\AbsenMasukResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAbsen extends CreateRecord
{
    protected static string $resource = AbsenMasukResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['jenis'] = 'masuk';
        return $data;
    }
}
