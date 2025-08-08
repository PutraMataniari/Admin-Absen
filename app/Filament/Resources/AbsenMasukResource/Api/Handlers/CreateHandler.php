<?php
namespace App\Filament\Resources\AbsenMasukResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\AbsenMasukResource;
use App\Filament\Resources\AbsenMasukResource\Api\Requests\CreateAbsenMasukRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = AbsenMasukResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create AbsenMasuk
     *
     * @param CreateAbsenMasukRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateAbsenMasukRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}