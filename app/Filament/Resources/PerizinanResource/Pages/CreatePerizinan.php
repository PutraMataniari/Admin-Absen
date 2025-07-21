<?php

namespace App\Filament\Resources\PerizinanResource\Pages;

use App\Filament\Resources\PerizinanResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePerizinan extends CreateRecord
{
    protected static string $resource = PerizinanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['jenis'] = 'izin';
        return $data;
    }
}
