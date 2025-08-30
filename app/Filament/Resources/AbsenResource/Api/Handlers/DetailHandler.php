<?php

namespace App\Filament\Resources\AbsenResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\AbsenResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\AbsenResource\Api\Transformers\AbsenTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = AbsenResource::class;


    /**
     * Show AbsenMasuk
     *
     * @param Request $request
     * @return AbsenMasukTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');
        
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new AbsenMasukTransformer($query);
    }
}
