<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\IncomeResource;
use App\Models\Income;
use App\Models\Occupation;
use Illuminate\Support\Facades\Auth;

class MasterController extends BaseController
{
    public function incomes()
    {
        $incomes = Income::where('admin_id', Auth::user()->admin_id)->orWhere('owner_id', Auth::user()->owner_id)->get();
        return $this->sendResponse(IncomeResource::collection($incomes), 'Berhasil mengambil data pendapatan');
    }

    public function occupations()
    {
        $occupations = Occupation::where('admin_id', Auth::user()->admin_id)->orWhere('owner_id', Auth::user()->owner_id)->get();
        return $this->sendResponse(IncomeResource::collection($occupations), 'Berhasil mengambil data pekerjaan');
    }
}
