<?php

namespace App\Filament\Resources\AbsenKeluarResource\Pages;

use App\Filament\Resources\AbsenKeluarResource;
use Filament\Resources\Pages\EditRecord;

class EditAbsenKeluar extends EditRecord
{
    protected static string $resource = AbsenKeluarResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['jenis'] = 'keluar';
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
