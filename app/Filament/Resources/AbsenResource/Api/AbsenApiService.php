<?php
namespace App\Filament\Resources\AbsenResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\AbsenResource;
use Illuminate\Routing\Router;


class AbsenApiService extends ApiService
{
    protected static string | null $resource = AbsenResource::class;

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
