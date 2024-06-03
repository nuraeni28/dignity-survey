<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SupporterExport;
use App\Http\Controllers\Controller;
use App\Models\Supporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class SupporterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin')) {
            return abort('403');
        }
        $supporter = Auth::user();
        $query = Supporter::query();

        // Handle search input
        $search = $request->input('search');
        $statusKunjungan = $request->input('statusKunjungan');
        if ($search) {
            $query->where(function ($subquery) use ($search) {
                $subquery
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%')
                    ->orWhere('no_kk', 'like', '%' . $search . '%');
                $subquery->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('email', 'like', '%' . $search . '%');
                });
            });
        }
        if ($statusKunjungan) {
            if ($statusKunjungan === 'Belum') {
                $query->whereNull('status_kunjungan');
            } elseif ($statusKunjungan === 'Sudah') {
                $query->whereNotNull('status_kunjungan');
            }
        }

        if (!Gate::check('super-admin')) {
            $query->where(function ($subquery) use ($supporter) {
                $subquery
                    ->where('admin_id', $supporter->id)
                    ->orWhere('owner_id', $supporter->id)
                    ->orWhere('indonesia_city_id', $supporter->indonesia_city_id);
            });
        }

        $supporters = $query->paginate(10);
        $supporters->appends(['search' => $search, 'statusKunjungan' => $statusKunjungan]);

        return view('admin.supporter.index', compact('supporters'));
    }
      public function export(Request $request)
    {
         $supporters = Supporter::all();
        // dd($users);

    

        $fileName = 'Pendukung AAB -' . date('d-m-Y') . '.xlsx';
        return Excel::Download(new SupporterExport($supporters), $fileName);
    }
        public function show($id)
    {
       

       $supporter =  Supporter::where('id', $id)->first();
    //   dd($supporter);
      

        // dd($interviews);
        return view('admin.supporter.show', compact('supporter'));
    }
}