<?php
namespace App\Filament\Resources\AbsenMasukResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\AbsenMasukResource;
use Illuminate\Routing\Router;


class AbsenMasukApiService extends ApiService
{
    protected static string | null $resource = AbsenMasukResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
