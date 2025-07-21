<?php

namespace App\Filament\Resources\AbsenMasukResource\Pages;

use App\Filament\Resources\AbsenMasukResource;
use Filament\Resources\Pages\EditRecord;

class EditAbsen extends EditRecord
{
    protected static string $resource = AbsenMasukResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['jenis'] = 'masuk';
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
