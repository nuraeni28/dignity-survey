<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InterviewExport;
use App\Http\Controllers\Controller;
use App\Models\EvidenceComitment;
use App\Models\InterviewSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class CustomerComitmentController extends Controller
{
     public function index(Request $request)
    {
        // session()->forget('kecamatan');
        // session()->forget('desa');
        // $kecamatan = $request->indonesia_districts_id;
        // $desa = $request->indonesia_villages_id;
        // session()->put('kecamatan', $kecamatan);
        // session()->put('desa', $desa);
        // $tanggalMulai = $request->input('tanggal_mulai');
        // $tanggalSelesai = $request->input('tanggal_selesai');
        // if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin') && !Gate::check('koordinator-area')) {
        //     return abort('403');
        // }
        // $id = Auth::user()->id;
        // if (request()->has('nik_relawan')) {
        //     // dd(request()->all());
        //     // if()
        //     $nik_relawan = request()->input('nik_relawan');
        //     $nik_responden = request()->input('nik_responden');
        //     $alamat = request()->input('alamat');
        //     $date = request()->input('date');
        //     $interviews = (new InterviewSchedule())->newQuery()->whereHas('interview', function ($query) use ($id) {
        //         if (!Gate::check('super-admin')) {
        //             $query->where('owner_id', $id)->orWhere('admin_id', $id);
        //         } elseif (Gate::check('koordinator-area') || Gate::check('admin')) {
        //             $query->where('owner_id', Auth::user()->owner_id);
        //         }
        //     });

        //     if ($nik_relawan != null) {
        //         $interviews = $interviews
        //             ->whereHas('user', function ($query) use ($nik_relawan) {
        //                 $query->where('nik', 'like', '%' . $nik_relawan . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($nik_responden != null) {
        //         $interviews = $interviews
        //             ->whereHas('customer', function ($query) use ($nik_responden) {
        //                 $query->where('nik', 'like', '%' . $nik_responden . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($alamat != null) {
        //         $interviews = $interviews
        //             ->whereHas('interview', function ($query) use ($alamat) {
        //                 $query->where('location', 'like', '%' . $alamat . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($date != null) {
        //         $interviews = $interviews
        //             ->whereHas('interview', function ($query) use ($date) {
        //                 $query->where('interview_date', 'like', '%' . $date . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($nik_relawan != null && $nik_responden != null) {
        //         $interviews = $interviews
        //             ->whereHas('user', function ($query) use ($nik_relawan) {
        //                 $query->where('nik', 'like', '%' . $nik_relawan . '%');
        //             })
        //             ->whereHas('customer', function ($query) use ($nik_responden) {
        //                 $query->where('nik', 'like', '%' . $nik_responden . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($nik_relawan != null && $alamat != null) {
        //         $interviews = $interviews
        //             ->whereHas('user', function ($query) use ($nik_relawan) {
        //                 $query->where('nik', 'like', '%' . $nik_relawan . '%');
        //             })
        //             ->whereHas('interview', function ($query) use ($alamat) {
        //                 $query->where('location', 'like', '%' . $alamat . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($nik_relawan != null && $date != null) {
        //         $interviews = $interviews
        //             ->whereHas('user', function ($query) use ($nik_relawan) {
        //                 $query->where('nik', 'like', '%' . $nik_relawan . '%');
        //             })
        //             ->whereHas('interview', function ($query) use ($date) {
        //                 $query->where('interview_date', 'like', '%' . $date . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($nik_responden != null && $alamat != null) {
        //         $interviews = $interviews
        //             ->whereHas('customer', function ($query) use ($nik_responden) {
        //                 $query->where('nik', 'like', '%' . $nik_responden . '%');
        //             })
        //             ->whereHas('interview', function ($query) use ($alamat) {
        //                 $query->where('location', 'like', '%' . $alamat . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($nik_responden != null && $date != null) {
        //         $interviews = $interviews
        //             ->whereHas('customer', function ($query) use ($nik_responden) {
        //                 $query->where('nik', 'like', '%' . $nik_responden . '%');
        //             })
        //             ->whereHas('interview', function ($query) use ($date) {
        //                 $query->where('interview_date', 'like', '%' . $date . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($alamat != null && $date != null) {
        //         $interviews = $interviews
        //             ->whereHas('interview', function ($query) use ($alamat) {
        //                 $query->where('location', 'like', '%' . $alamat . '%');
        //             })
        //             ->whereHas('interview', function ($query) use ($date) {
        //                 $query->where('interview_date', 'like', '%' . $date . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($nik_relawan != null && $nik_responden != null && $alamat != null) {
        //         $interviews = $interviews
        //             ->whereHas('user', function ($query) use ($nik_relawan) {
        //                 $query->where('nik', 'like', '%' . $nik_relawan . '%');
        //             })
        //             ->whereHas('customer', function ($query) use ($nik_responden) {
        //                 $query->where('nik', 'like', '%' . $nik_responden . '%');
        //             })
        //             ->whereHas('interview', function ($query) use ($alamat) {
        //                 $query->where('location', 'like', '%' . $alamat . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($nik_relawan != null && $nik_responden != null && $date != null) {
        //         $interviews = $interviews
        //             ->whereHas('user', function ($query) use ($nik_relawan) {
        //                 $query->where('nik', 'like', '%' . $nik_relawan . '%');
        //             })
        //             ->whereHas('customer', function ($query) use ($nik_responden) {
        //                 $query->where('nik', 'like', '%' . $nik_responden . '%');
        //             })
        //             ->whereHas('interview', function ($query) use ($date) {
        //                 $query->where('interview_date', 'like', '%' . $date . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($nik_relawan != null && $alamat != null && $date != null) {
        //         $interviews = $interviews
        //             ->whereHas('user', function ($query) use ($nik_relawan) {
        //                 $query->where('nik', 'like', '%' . $nik_relawan . '%');
        //             })
        //             ->whereHas('interview', function ($query) use ($alamat) {
        //                 $query->where('location', 'like', '%' . $alamat . '%');
        //             })
        //             ->whereHas('interview', function ($query) use ($date) {
        //                 $query->where('interview_date', 'like', '%' . $date . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($nik_responden != null && $alamat != null && $date != null) {
        //         $interviews = $interviews
        //             ->whereHas('customer', function ($query) use ($nik_responden) {
        //                 $query->where('nik', 'like', '%' . $nik_responden . '%');
        //             })
        //             ->whereHas('interview', function ($query) use ($alamat) {
        //                 $query->where('location', 'like', '%' . $alamat . '%');
        //             })

        //             ->whereHas('interview', function ($query) use ($date) {
        //                 $query->where('interview_date', 'like', '%' . $date . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($nik_relawan != null && $nik_responden != null && $alamat != null && $date != null) {
        //         $interviews = $interviews
        //             ->whereHas('user', function ($query) use ($nik_relawan) {
        //                 $query->where('nik', 'like', '%' . $nik_relawan . '%');
        //             })
        //             ->whereHas('customer', function ($query) use ($nik_responden) {
        //                 $query->where('nik', 'like', '%' . $nik_responden . '%');
        //             })
        //             ->whereHas('interview', function ($query) use ($alamat) {
        //                 $query->where('location', 'like', '%' . $alamat . '%');
        //             })
        //             ->whereHas('interview', function ($query) use ($date) {
        //                 $query->where('interview_date', 'like', '%' . $date . '%');
        //             })
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }

        //     if ($nik_relawan == null && $nik_responden == null && $alamat == null && $date == null) {
        //         $interviews = (new InterviewSchedule())->newQuery();
        //         $interviews = $interviews
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     }
        // } else {
        //     if (Gate::check('koordinator-area') || Gate::check('admin')) {
        //         $interviews = InterviewSchedule::whereHas('customer', function ($customerQuery) use ($id) {
        //             $customerQuery->where('indonesia_city_id', Auth::user()->indonesia_city_id);
        //         })
        //             ->whereHas('interview', function ($query) use ($id) {
        //                 $query->where('owner_id', Auth::user()->owner_id);
        //             })
        //             ->whereNull('type');
        //         $tanggalMulai = $request->input('tanggal_mulai');
        //         $tanggalSelesai = $request->input('tanggal_selesai');

        //         if ($tanggalMulai && $tanggalSelesai) {
        //             $tanggalMulai = \Carbon\Carbon::parse($tanggalMulai)->startOfDay();
        //             $tanggalSelesai = \Carbon\Carbon::parse($tanggalSelesai)->endOfDay();

        //             $interviews->whereBetween('updated_at', [$tanggalMulai, $tanggalSelesai]);
        //         }
        //     } elseif (Gate::check('super-admin')) {
        //         $interviews = InterviewSchedule::query()
        //             ->whereNull('type')
        //             ->has('interview')
        //             ->has('user')
        //             ->has('customer')
        //             ->with('period', 'user', 'customer', 'interview');
        //     } else {
        //         $interviews = (new InterviewSchedule())
        //             ->newQuery()
        //             ->whereHas('interview', function ($query) use ($id) {
        //                 $query->where('owner_id', $id)->orWhere('admin_id', $id);
        //             })
        //             ->whereNull('type');
        //     }
        // }

        // // dd(count($interviews));

        // // dd($interviews->first()->interview->data);
        // // dd($interviews);

        // // dd($interviews);

        // $search = $request->input('search');
        // $kota = $request->input('indonesia_cities_id');
        // $kecamatan = $request->input('indonesia_districts_id') ?? null;
        // $desa = $request->input('indonesia_villages_id') ?? null;

        // if ($request->has('duplicate')) {
        //     $duplicatedInterviews = InterviewSchedule::select('customer_id')
        //         ->has('interview')
        //         ->groupBy('customer_id')
        //         ->havingRaw('COUNT(customer_id) > 1')
        //         ->pluck('customer_id');

        //     $interviews = InterviewSchedule::whereIn('customer_id', $duplicatedInterviews)
        //         ->whereHas('interview')
        //         ->has('user')
        //         ->has('customer')
        //         ->with('period', 'user', 'customer', 'interview')
        //         ->orderBy('customer_id');
        // }

        // if ($search) {
        //     $interviews = $interviews
        //         ->where(function ($query) use ($search) {
        //             $query
        //                 ->whereHas('user', function ($subquery) use ($search) {
        //                     $subquery->where('name', 'like', '%' . $search . '%');
        //                 })
        //                 ->orWhereHas('customer', function ($subquery) use ($search) {
        //                     $subquery->whereHas('district', function ($districtSubquery) use ($search) {
        //                         $districtSubquery->where('name', 'like', '%' . $search . '%');
        //                     });
        //                 })
        //                 ->orWhereHas('customer', function ($subquery) use ($search) {
        //                     $subquery->where('name', 'like', '%' . $search . '%');
        //                 })
        //                 ->orWhereHas('user', function ($subquery) use ($search) {
        //                     $subquery->where('email', 'like', '%' . $search . '%');
        //                 })
        //                 ->orWhereHas('customer', function ($subquery) use ($search) {
        //                     $subquery->where('phone', 'like', '%' . $search . '%');
        //                 })
        //                 ->orWhereHas('user', function ($subquery) use ($search) {
        //                     $subquery->where('phone', 'like', '%' . $search . '%');
        //                 });
        //         })
        //         ->orderBy('updated_at', 'desc')
        //         ->latest()
        //         ->paginate(10)
        //         ->onEachSide(2);
        // } elseif ($kota || $kecamatan || $desa || $tanggalMulai || $tanggalSelesai) {
        //     $interviews = $interviews->whereHas('customer', function ($query) use ($kota, $kecamatan, $desa, $tanggalMulai, $tanggalSelesai) {
        //         if ($kota) {
        //             $query->whereHas('city', function ($cityQuery) use ($kota) {
        //                 $cityQuery->where('id', $kota);
        //             });
        //         }

        //         if ($kecamatan) {
        //             $query->whereHas('district', function ($districtQuery) use ($kecamatan) {
        //                 $districtQuery->whereIn('id', $kecamatan);
        //             });
        //         }

        //         if ($desa) {
        //             $query->whereHas('village', function ($villageQuery) use ($desa) {
        //                 $villageQuery->whereIn('id', $desa);
        //             });
        //         }
        //         //               if ($tanggalMulai && $tanggalSelesai) {
        //         //     $query->where(function ($dateQuery) use ($tanggalMulai, $tanggalSelesai) {
        //         //           if ($tanggalMulai != $tanggalSelesai) {
        //         //                     $dateQuery->whereBetween('updated_at', [$tanggalMulai, $tanggalSelesai]);
        //         //                 } else {
        //         //                     $dateQuery->whereDate('updated_at', $tanggalMulai);
        //         //                 }
        //         //     });
        //         // }
        //     });
        //     if ($tanggalMulai && $tanggalSelesai) {
        //         $tanggalMulai = \Carbon\Carbon::parse($tanggalMulai)->startOfDay();
        //         $tanggalSelesai = \Carbon\Carbon::parse($tanggalSelesai)->endOfDay();

        //         if ($tanggalMulai != $tanggalSelesai) {
        //             $interviews->whereBetween('updated_at', [$tanggalMulai, $tanggalSelesai]);
        //         } else {
        //             $interviews->whereDate('updated_at', $tanggalMulai);
        //         }
        //     }

        //     $interviews = $interviews
        //         ->orderBy('updated_at', 'desc')
        //         ->latest()
        //         ->paginate(10)
        //         ->onEachSide(2);
        //     // ->get();
        // } else {
        //     $interviews = $interviews
        //         ->latest()
        //         ->paginate(10)
        //         ->onEachSide(2);
        //     // ->get();
        // }
       $interviews = EvidenceComitment::latest()
    ->paginate(10)
    ->onEachSide(2);

// dd($interviews->count());
return view('admin.customer-comitment.index', compact('interviews'));
    }
}