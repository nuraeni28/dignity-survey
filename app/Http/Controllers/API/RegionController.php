<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\RegionResource;
use Illuminate\Http\Request;

class RegionController extends BaseController
{
    public function provinces()
    {
        $regions = \Indonesia::allProvinces();
        return $this->sendResponse(RegionResource::collection($regions), 'Berhasil mengambil data provinsi');
    }

    public function cities(Request $request)
    {
        $regions = \Indonesia::findProvince($request->id, ['cities'])->cities;
        return $this->sendResponse(RegionResource::collection($regions), 'Berhasil mengambil data kota/kabupaten');
    }

    public function districts(Request $request)
    {
        $regions = \Indonesia::findCity($request->id, ['districts'])->districts;
        return $this->sendResponse(RegionResource::collection($regions), 'Berhasil mengambil data kecamatan');
    }

    public function villages(Request $request)
    {
        $regions = \Indonesia::findDistrict($request->id, ['villages'])->villages;
        return $this->sendResponse(RegionResource::collection($regions), 'Berhasil mengambil data desa');
    }
}