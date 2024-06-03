<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Occupation;
use App\Models\Partai;
use App\Models\Caleg;
use App\Models\QuickCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\QuickCountExport;

class QuickCountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Gate::check('koordinator-area') && !Gate::check('super-admin')) {
            return abort('403');
        }

        if (Gate::check('koordinator-area')) {
            $quickCounts = QuickCount::select('*')
                ->join(DB::raw('(SELECT partai_id, tps, indonesia_city_id, indonesia_district_id, indonesia_village_id, MIN(id) as min_id FROM quick_count GROUP BY partai_id, tps, indonesia_city_id, indonesia_district_id, indonesia_village_id) as subq'), function ($join) {
                    $join->on('quick_count.partai_id', '=', 'subq.partai_id')->on('quick_count.tps', '=', 'subq.tps')->on('quick_count.indonesia_district_id', '=', 'subq.indonesia_district_id')->on('quick_count.indonesia_city_id', '=', 'subq.indonesia_city_id')->on('quick_count.indonesia_village_id', '=', 'subq.indonesia_village_id')->on('quick_count.id', '=', 'subq.min_id');
                })
                ->where('quick_count.indonesia_city_id', Auth::user()->indonesia_city_id)
                ->latest()
                ->paginate(10)
                ->onEachSide(2);
        } else {
            $quickCounts = QuickCount::select('*')
                ->join(DB::raw('(SELECT partai_id, tps, indonesia_city_id, indonesia_district_id, indonesia_village_id, MIN(id) as min_id FROM quick_count GROUP BY partai_id, tps, indonesia_city_id, indonesia_district_id, indonesia_village_id) as subq'), function ($join) {
                    $join->on('quick_count.partai_id', '=', 'subq.partai_id')->on('quick_count.tps', '=', 'subq.tps')->on('quick_count.indonesia_district_id', '=', 'subq.indonesia_district_id')->on('quick_count.indonesia_city_id', '=', 'subq.indonesia_city_id')->on('quick_count.indonesia_village_id', '=', 'subq.indonesia_village_id')->on('quick_count.id', '=', 'subq.min_id');
                })
                ->latest()
                ->paginate(10)
                ->onEachSide(2);
        }

        return view('admin.quick-count.index', compact('quickCounts'));
    }
    public function create()
    {
        if (!Gate::check('koordinator-area')) {
            return abort('403');
        }
        // Mengambil nilai-nilai dari sesi

        $quickCounts = QuickCount::latest()->paginate(10)->onEachSide(2);

        // dd(Auth::user());
        return view('admin.quick-count.create');
    }
    public function store(Request $request)
    {
        if (!Gate::check('koordinator-area')) {
            return abort('403');
        }
        // dd($request->all());
        $this->validate(
            $request,
            [
                'tps' => ['required', 'numeric'],
                'indonesia_city_id' => ['nullable'],
                'indonesia_district_id' => ['required'],
                'indonesia_village_id' => ['required'],
                'partai_id' => ['required'],
                
            ],
            [
                'indonesia_district_id.required' => 'Kecamatan wajib diisi',
                'indonesia_village_id.required' => 'Desa wajib diisi',
                'partai_id.required' => 'Partai wajib diisi',
               
            ]
        );
        // Periksa apakah entri yang sama sudah ada
        $existingPartai = QuickCount::where('tps', $request->tps)
            ->where('partai_id', $request->partai_id)
            ->where('indonesia_village_id', $request->indonesia_village_id)
            ->exists();

        $namePartai = Partai::select('name')
            ->where('id', $request->partai_id)
            ->first();
        $nameDistrict = \Laravolt\Indonesia\Models\District::select('name')
            ->where('id', $request->indonesia_district_id)
            ->first();
        $nameVillage = \Laravolt\Indonesia\Models\Village::select('name')
            ->where('id', $request->indonesia_village_id)
            ->first();

        // Jika entri sudah ada, kembalikan pesan kesalahan
        if ($existingPartai) {
            return redirect()
                ->back()
                ->with('error', 'Partai ' . $namePartai->name . ' TPS ' . $request->tps . ' Kecamatan ' . $nameDistrict->name . ' Kelurahan/Desa ' . $nameVillage->name . ' sudah ada');
        }

        $photo = $request->file('picture');
        if ($photo != null) {
            $photoname = date('YmdHi') . $photo->getClientOriginalName();
            $photo->move(public_path('public/quick_count'), $photoname);
        } else {
            $photoname = null;
        }

   $calegIds = Caleg::where('partai_id', $request->partai_id)->pluck('id');
        if ($request->partai_id == 2) {
            foreach ($request->id_caleg as $key => $caleg) {
                $data = new QuickCount();
                $data->tps = $request->tps;
                $data->admin = $request->admin;
                $data->partai_id = $request->partai_id;
                $data->jumlah_suara_partai = $request->jumlah_suara_partai;
                $data->foto = $photoname;
                $data->caleg_id = $caleg;
                $data->jumlah_suara_caleg = $request->jumlah_suara_caleg[$key];
                $data->indonesia_province_id = Auth::user()->province->id;
                $data->indonesia_city_id = Auth::user()->city->id;
                $data->indonesia_district_id = $request->indonesia_district_id;
                $data->indonesia_village_id = $request->indonesia_village_id;
                $data->save();
            }
        } else {
            foreach ($calegIds as $key => $caleg) {
                $data = new QuickCount();
                $data->tps = $request->tps;
                $data->admin = $request->admin;
                $data->partai_id = $request->partai_id;
                $data->jumlah_suara_partai = $request->jumlah_suara_partai;
                $data->foto = $photoname;
                $data->caleg_id = $caleg;
                $data->jumlah_suara_caleg = 0;
                $data->indonesia_province_id = Auth::user()->province->id;
                $data->indonesia_city_id = Auth::user()->city->id;
                $data->indonesia_district_id = $request->indonesia_district_id;
                $data->indonesia_village_id = $request->indonesia_village_id;
                $data->save();
            }
        }
        $totalPartaiDiTPS = QuickCount::where('tps', $request->tps)
            ->where('indonesia_village_id', $request->indonesia_village_id)
            ->distinct('partai_id')
            ->count('partai_id');

        // Ambil total jumlah partai yang tersedia
        $totalPartaiTersedia = Partai::count();
        if ($totalPartaiDiTPS < $totalPartaiTersedia) {
            session()->flash('indonesia_district_id', $request->indonesia_district_id);
            session()->flash('indonesia_village_id', $request->indonesia_village_id);
            session()->flash('tps', $request->tps);

            return redirect()->route('real-count.create')->with('success', __('Berhasil membuat real count'));
        }
        return redirect()->route('real-count.index')->with('success', __('Berhasil membuat real count'));
    }
    public function show($id)
    {
        $data = QuickCount::findOrFail($id);
        $dataCaleg = QuickCount::where('tps', $data->tps)
            ->where('partai_id', $data->partai_id)->where('indonesia_village_id', $data->indonesia_village_id)
            ->get();
        // dd($dataCaleg);

        return view('admin.quick-count.show', compact('data', 'dataCaleg'));
    }
    public function destroy($id, Request $request)
    {
        $page = $request->input('page');
        $data = QuickCount::where('id', $id)->first();

        $dataCaleg = QuickCount::where('tps', $data->tps)
            ->where('partai_id', $data->partai_id)
            ->get();

        foreach ($dataCaleg as $caleg) {
            $caleg->forceDelete();
        }

        return redirect()
            ->route('real-count.index', ['page' => $page])
            ->with('success', __('Berhasil menghapus real count'));
    }
    public function countCaleg(Request $request)
    {
        
        if (!Gate::check('koordinator-area')) {
            return abort('403');
        }

     $quickCounts = QuickCount::select('tps', 'indonesia_village_id')
            ->selectRaw('COUNT(partai_id) as total_partai')
            ->where('indonesia_district_id', $request->indonesia_district_id)
            ->where('indonesia_village_id', $request->indonesia_village_id)
            ->groupBy('tps', 'indonesia_village_id') // Mengelompokkan hasil berdasarkan nomor TPS
          
            ->get();
          

        // Ambil total jumlah partai yang tersedia
        $totalPartaiTersedia = Partai::count();  
        $countTps = QuickCount::select('indonesia_city_id')
    ->selectSub(function ($query) {
        $query->selectRaw('COUNT(DISTINCT tps) as total_tps')
            ->from('quick_count as qc2')
            ->whereColumn('qc2.indonesia_village_id', '=', 'quick_count.indonesia_village_id');
    }, 'total_tps')
  
    ->groupBy('indonesia_city_id', 'indonesia_village_id')
    ->get();
        $countTps = $countTps
            ->groupBy('indonesia_city_id')
            ->map(function ($group) {
                return [
                    'indonesia_city_id' => $group->first()->indonesia_city_id,
                    'total_tps' => $group->sum('total_tps'),
                ];
            })
            ->values();

        return view('admin.quick-count.count-caleg', compact('quickCounts', 'totalPartaiTersedia', 'countTps'));
    }
      public function export(Request $request)
    {
        $quickCounts = QuickCount::select('*')
            ->join(DB::raw('(SELECT partai_id, tps, indonesia_city_id, indonesia_district_id, indonesia_village_id, MIN(id) as min_id FROM quick_count GROUP BY partai_id, tps, indonesia_city_id, indonesia_district_id, indonesia_village_id) as subq'), function ($join) {
                $join->on('quick_count.partai_id', '=', 'subq.partai_id')->on('quick_count.tps', '=', 'subq.tps')->on('quick_count.indonesia_district_id', '=', 'subq.indonesia_district_id')->on('quick_count.indonesia_city_id', '=', 'subq.indonesia_city_id')->on('quick_count.indonesia_village_id', '=', 'subq.indonesia_village_id')->on('quick_count.id', '=', 'subq.min_id');
            })
            ->get();
        // $dataCaleg = QuickCount::where('tps', $data->tps)
        //     ->where('partai_id', $data->partai_id)
        //     ->get();

        $fileName = 'Real Count';

        $fileName .= ' - ' . date('d-m-Y') . '.xlsx';

        return Excel::Download(new QuickCountExport($quickCounts), $fileName);
    }
       public function update(Request $request, $id)
    {
        if (!Gate::check('koordinator-area')) {
            return abort('403');
        }

        $quickCount = QuickCount::findOrFail($id);
        if ($request->hasFile('picture')) {
            $photo = $request->file('picture');
            $photoname = date('YmdHi') . $photo->getClientOriginalName();
            $photo->move(public_path('public/image'), $photoname);
            $quickCount->foto = $photoname;
        } else {
            $photoname = $quickCount->foto;
        }
        // dd($request->jumlah_suara_caleg);
        $calegIds = Caleg::where('partai_id', $quickCount->partai_id)->pluck('id');
        if ($quickCount->partai_id == 2) {
            foreach ($request->id_caleg as $key => $caleg) {
                $data = QuickCount::firstOrNew(['caleg_id' => $caleg, 'partai_id' => $quickCount->partai_id, 'tps' => $quickCount->tps, 'indonesia_village_id' => $quickCount->indonesia_village_id]);
                $data->tps = $request->tps;
                $data->partai_id = $quickCount->partai_id; // Menggunakan partai_id dari data yang sudah ada
                $data->jumlah_suara_partai = $request->jumlah_suara_partai;
                $data->foto = $photoname;
                $data->jumlah_suara_caleg = $request->jumlah_suara_caleg[$key];
                $data->indonesia_province_id = Auth::user()->province->id;
                $data->indonesia_city_id = Auth::user()->city->id;
                $data->indonesia_district_id = $request->indonesia_district_id;
                $data->indonesia_village_id = $request->indonesia_village_id;
                $data->save();
            }
        } else {
            foreach ($calegIds as $key => $caleg) {
                $data = QuickCount::firstOrNew(['caleg_id' => $caleg, 'partai_id' => $quickCount->partai_id, 'tps' => $quickCount->tps, 'indonesia_village_id' => $quickCount->indonesia_village_id]);
                $data->tps = $request->tps;
                $data->admin = $request->admin;
                $data->partai_id = $quickCount->partai_id;
                $data->jumlah_suara_partai = $request->jumlah_suara_partai;
                $data->foto = $photoname;
                $data->caleg_id = $caleg;
                $data->jumlah_suara_caleg = 0;
                $data->indonesia_province_id = Auth::user()->province->id;
                $data->indonesia_city_id = Auth::user()->city->id;
                $data->indonesia_district_id = $request->indonesia_district_id;
                $data->indonesia_village_id = $request->indonesia_village_id;
                $data->save();
            }
        }

        return redirect()->route('real-count.index')->with('success', __('Berhasil edit quick count'));
    }
        public function edit($id)
    {
        if (!Gate::check('koordinator-area')) {
            return abort('403');
        }

        $quickCount = QuickCount::findOrFail($id);
        $dataCaleg = QuickCount::where('tps', $quickCount->tps)
            ->where('partai_id', $quickCount->partai_id)
            ->where('indonesia_village_id', $quickCount->indonesia_village_id)
            ->where('indonesia_district_id', $quickCount->indonesia_district_id)
            ->where('indonesia_city_id', $quickCount->indonesia_city_id)
            ->get();

        return view('admin.quick-count.edit', compact('quickCount', 'dataCaleg'));
    }
      public function countSumVote(Request $request)
    {
        if (!Gate::check('super-admin')) {
            return abort('403');
        }
        // dd($request->indonesia_district_id);

        $quickCounts = QuickCount::select('partai_id', 'tps', 'indonesia_village_id', DB::raw('MAX(jumlah_suara_partai) as total_suara_partai'))->groupBy('partai_id', 'tps', 'indonesia_village_id')->get();

        $quickCounts = $quickCounts
            ->groupBy('partai_id')
            ->map(function ($group) {
                return [
                    'partai_id' => $group->first()->partai_id,
                    'total_suara_partai' => $group->sum('total_suara_partai'),
                ];
            })
            ->values();
        $countCaleg = QuickCount::where('partai_id', 2)->select('caleg_id', DB::raw('SUM(jumlah_suara_caleg) as total_suara_caleg'))->groupBy('caleg_id')->get();
        // dd($countCaleg);

        return view('admin.quick-count.count-sum-vote', compact('quickCounts', 'countCaleg'));
    }
}
