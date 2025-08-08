<?php
namespace App\Filament\Resources\AbsenMasukResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Absen;

/**
 * @property AbsenMasuk $resource
 */
class AbsenMasukTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return $this->resource->toArray();
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'jenis' => $this->jenis,
            'waktu_absen' => $this->waktu_absen,
            'lokasi' => $this->lokasi,
            'gambar' => $this->gambar,
            'keterangan' => $this->keterangan,
            'bukti' => $this->bukti


        ];
    }
}
