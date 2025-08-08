<?php

namespace App\Filament\Resources\AbsenMasukResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\AbsenMasukResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\AbsenMasukResource\Api\Transformers\AbsenMasukTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = AbsenMasukResource::class;


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
