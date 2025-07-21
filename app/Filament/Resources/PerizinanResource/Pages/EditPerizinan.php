<?php

namespace App\Filament\Resources\PerizinanResource\Pages;

use App\Filament\Resources\PerizinanResource;
use Filament\Resources\Pages\EditRecord;

class EditPerizinan extends EditRecord
{
    protected static string $resource = PerizinanResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['jenis'] = 'izin';
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
