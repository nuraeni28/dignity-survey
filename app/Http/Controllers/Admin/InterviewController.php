<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InterviewExport;
use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\Occupation;
use App\Models\Income;
use App\Models\InterviewSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InterviewImport;

class InterviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        session()->forget('kecamatan');
        session()->forget('desa');
        $kecamatan = $request->indonesia_districts_id;
        $desa = $request->indonesia_villages_id;
        session()->put('kecamatan', $kecamatan);
        session()->put('desa', $desa);
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin') && !Gate::check('koordinator-area')) {
            return abort('403');
        }
        $id = Auth::user()->id;
        if (request()->has('nik_relawan')) {
            // dd(request()->all());
            // if()
            $nik_relawan = request()->input('nik_relawan');
            $nik_responden = request()->input('nik_responden');
            $alamat = request()->input('alamat');
            $date = request()->input('date');
            $interviews = (new InterviewSchedule())->newQuery()->whereHas('interview', function ($query) use ($id) {
                if (!Gate::check('super-admin')) {
                    $query->where('owner_id', $id)->orWhere('admin_id', $id);
                } elseif (Gate::check('koordinator-area') || Gate::check('admin')) {
                    $query->where('owner_id', Auth::user()->owner_id);
                }
            });

            if ($nik_relawan != null) {
                $interviews = $interviews
                    ->whereHas('user', function ($query) use ($nik_relawan) {
                        $query->where('nik', 'like', '%' . $nik_relawan . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($nik_responden != null) {
                $interviews = $interviews
                    ->whereHas('respondent', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($alamat != null) {
                $interviews = $interviews
                    ->whereHas('interview', function ($query) use ($alamat) {
                        $query->where('location', 'like', '%' . $alamat . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($date != null) {
                $interviews = $interviews
                    ->whereHas('interview', function ($query) use ($date) {
                        $query->where('interview_date', 'like', '%' . $date . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($nik_relawan != null && $nik_responden != null) {
                $interviews = $interviews
                    ->whereHas('user', function ($query) use ($nik_relawan) {
                        $query->where('nik', 'like', '%' . $nik_relawan . '%');
                    })
                    ->whereHas('respondent', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($nik_relawan != null && $alamat != null) {
                $interviews = $interviews
                    ->whereHas('user', function ($query) use ($nik_relawan) {
                        $query->where('nik', 'like', '%' . $nik_relawan . '%');
                    })
                    ->whereHas('interview', function ($query) use ($alamat) {
                        $query->where('location', 'like', '%' . $alamat . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($nik_relawan != null && $date != null) {
                $interviews = $interviews
                    ->whereHas('user', function ($query) use ($nik_relawan) {
                        $query->where('nik', 'like', '%' . $nik_relawan . '%');
                    })
                    ->whereHas('interview', function ($query) use ($date) {
                        $query->where('interview_date', 'like', '%' . $date . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($nik_responden != null && $alamat != null) {
                $interviews = $interviews
                    ->whereHas('respondent', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->whereHas('interview', function ($query) use ($alamat) {
                        $query->where('location', 'like', '%' . $alamat . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($nik_responden != null && $date != null) {
                $interviews = $interviews
                    ->whereHas('respondent', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->whereHas('interview', function ($query) use ($date) {
                        $query->where('interview_date', 'like', '%' . $date . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($alamat != null && $date != null) {
                $interviews = $interviews
                    ->whereHas('interview', function ($query) use ($alamat) {
                        $query->where('location', 'like', '%' . $alamat . '%');
                    })
                    ->whereHas('interview', function ($query) use ($date) {
                        $query->where('interview_date', 'like', '%' . $date . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($nik_relawan != null && $nik_responden != null && $alamat != null) {
                $interviews = $interviews
                    ->whereHas('user', function ($query) use ($nik_relawan) {
                        $query->where('nik', 'like', '%' . $nik_relawan . '%');
                    })
                    ->whereHas('respondent', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->whereHas('interview', function ($query) use ($alamat) {
                        $query->where('location', 'like', '%' . $alamat . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($nik_relawan != null && $nik_responden != null && $date != null) {
                $interviews = $interviews
                    ->whereHas('user', function ($query) use ($nik_relawan) {
                        $query->where('nik', 'like', '%' . $nik_relawan . '%');
                    })
                    ->whereHas('respondent', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->whereHas('interview', function ($query) use ($date) {
                        $query->where('interview_date', 'like', '%' . $date . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($nik_relawan != null && $alamat != null && $date != null) {
                $interviews = $interviews
                    ->whereHas('user', function ($query) use ($nik_relawan) {
                        $query->where('nik', 'like', '%' . $nik_relawan . '%');
                    })
                    ->whereHas('interview', function ($query) use ($alamat) {
                        $query->where('location', 'like', '%' . $alamat . '%');
                    })
                    ->whereHas('interview', function ($query) use ($date) {
                        $query->where('interview_date', 'like', '%' . $date . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($nik_responden != null && $alamat != null && $date != null) {
                $interviews = $interviews
                    ->whereHas('respondent', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->whereHas('interview', function ($query) use ($alamat) {
                        $query->where('location', 'like', '%' . $alamat . '%');
                    })

                    ->whereHas('interview', function ($query) use ($date) {
                        $query->where('interview_date', 'like', '%' . $date . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($nik_relawan != null && $nik_responden != null && $alamat != null && $date != null) {
                $interviews = $interviews
                    ->whereHas('user', function ($query) use ($nik_relawan) {
                        $query->where('nik', 'like', '%' . $nik_relawan . '%');
                    })
                    ->whereHas('respondent', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->whereHas('interview', function ($query) use ($alamat) {
                        $query->where('location', 'like', '%' . $alamat . '%');
                    })
                    ->whereHas('interview', function ($query) use ($date) {
                        $query->where('interview_date', 'like', '%' . $date . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }

            if ($nik_relawan == null && $nik_responden == null && $alamat == null && $date == null) {
                $interviews = (new InterviewSchedule())->newQuery();
                $interviews = $interviews
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            }
        } else {
            if (Gate::check('koordinator-area') || Gate::check('admin')) {
                $interviews = InterviewSchedule::whereHas('respondent', function ($respondentQuery) use ($id) {
                    $respondentQuery->where('indonesia_city_id', Auth::user()->indonesia_city_id);
                })
                    ->whereHas('interview', function ($query) use ($id) {
                        $query->where('owner_id', Auth::user()->owner_id);
                    })
                    ->whereNull('type');
                $tanggalMulai = $request->input('tanggal_mulai');
                $tanggalSelesai = $request->input('tanggal_selesai');

                if ($tanggalMulai && $tanggalSelesai) {
                    $tanggalMulai = \Carbon\Carbon::parse($tanggalMulai)->startOfDay();
                    $tanggalSelesai = \Carbon\Carbon::parse($tanggalSelesai)->endOfDay();

                    $interviews->whereBetween('updated_at', [$tanggalMulai, $tanggalSelesai]);
                }
            } elseif (Gate::check('super-admin')) {
                $interviews = InterviewSchedule::query()
                    ->whereNull('type')
                    ->has('interview')
                    ->has('user')
                    ->has('respondent')
                    ->with('period', 'user', 'respondent', 'interview');
            } else {
                $interviews = (new InterviewSchedule())
                    ->newQuery()
                    ->whereHas('interview', function ($query) use ($id) {
                        $query->where('owner_id', $id)->orWhere('admin_id', $id);
                    })
                    ->whereNull('type');
            }
        }

    

        $search = $request->input('search');
        $kota = $request->input('indonesia_cities_id');
        $kecamatan = $request->input('indonesia_districts_id') ?? null;
        $desa = $request->input('indonesia_villages_id') ?? null;

        if ($request->has('duplicate')) {
            $duplicatedInterviews = InterviewSchedule::select('respondent_id')
                ->has('interview')
                ->groupBy('respondent_id')
                ->havingRaw('COUNT(respondent_id) > 1')
                ->pluck('respondent_id');

            $interviews = InterviewSchedule::whereIn('respondent_id', $duplicatedInterviews)
                ->whereHas('interview')
                ->has('user')
                ->has('respondent')
                ->with('period', 'user', 'respondent', 'interview')
                ->orderBy('respondent_id');
        }

        if ($search) {
            $interviews = $interviews
                ->where(function ($query) use ($search) {
                    $query
                        ->whereHas('user', function ($subquery) use ($search) {
                            $subquery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('respondent', function ($subquery) use ($search) {
                            $subquery->whereHas('district', function ($districtSubquery) use ($search) {
                                $districtSubquery->where('name', 'like', '%' . $search . '%');
                            });
                        })
                        ->orWhereHas('respondent', function ($subquery) use ($search) {
                            $subquery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('user', function ($subquery) use ($search) {
                            $subquery->where('email', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('respondent', function ($subquery) use ($search) {
                            $subquery->where('phone', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('user', function ($subquery) use ($search) {
                            $subquery->where('phone', 'like', '%' . $search . '%');
                        });
                })
                ->orderBy('updated_at', 'desc')
                ->latest()
                ->paginate(10)
                ->onEachSide(2);
        } elseif ($kota || $kecamatan || $desa || $tanggalMulai || $tanggalSelesai) {
            $interviews = $interviews->whereHas('respondent', function ($query) use ($kota, $kecamatan, $desa, $tanggalMulai, $tanggalSelesai) {
                if ($kota) {
                    $query->whereHas('city', function ($cityQuery) use ($kota) {
                        $cityQuery->where('id', $kota);
                    });
                }

                if ($kecamatan) {
                    $query->whereHas('district', function ($districtQuery) use ($kecamatan) {
                        $districtQuery->whereIn('id', $kecamatan);
                    });
                }

                if ($desa) {
                    $query->whereHas('village', function ($villageQuery) use ($desa) {
                        $villageQuery->whereIn('id', $desa);
                    });
                }
                //               if ($tanggalMulai && $tanggalSelesai) {
                //     $query->where(function ($dateQuery) use ($tanggalMulai, $tanggalSelesai) {
                //           if ($tanggalMulai != $tanggalSelesai) {
                //                     $dateQuery->whereBetween('updated_at', [$tanggalMulai, $tanggalSelesai]);
                //                 } else {
                //                     $dateQuery->whereDate('updated_at', $tanggalMulai);
                //                 }
                //     });
                // }
            });
            if ($tanggalMulai && $tanggalSelesai) {
                $tanggalMulai = \Carbon\Carbon::parse($tanggalMulai)->startOfDay();
                $tanggalSelesai = \Carbon\Carbon::parse($tanggalSelesai)->endOfDay();

                if ($tanggalMulai != $tanggalSelesai) {
                    $interviews->whereBetween('updated_at', [$tanggalMulai, $tanggalSelesai]);
                } else {
                    $interviews->whereDate('updated_at', $tanggalMulai);
                }
            }

            $interviews = $interviews
                ->orderBy('updated_at', 'desc')
                ->latest()
                ->paginate(10)
                ->onEachSide(2);
            // ->get();
        } else {
            $interviews = $interviews
                ->latest()
                ->paginate(10)
                ->onEachSide(2);
            // ->get();
        }
        // dd($interviews->count());
        return view('admin.interview.index', compact('interviews'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin') && !Gate::check('koordinator-area')) {
            return abort('403');
        }
        // $schedule = InterviewSchedule::;

        // get interview schedule with period user and respondent

        // $schedules = InterviewSchedule::all();
        $schedule = InterviewSchedule::with('user', 'interview', 'respondent')->findOrFail($id);
        // dd($findScheduleWithId);
        // dd($schedule->start_date);

        return view('admin.interview.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(InterviewSchedule $interview)
    {
        // if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin')) {
        //     return abort('403');
        // }
        $id = Auth::user()->id;
        $interviews = InterviewSchedule::where('id', '<>', $interview->id)->get();
        // dd($question);
        return view('admin.interview.edit', compact(['interview', 'interviews']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $page = $request->input('page');
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin')) {
            return abort('403');
        }
        $interviewSchedule = InterviewSchedule::find($id);
        $customer = $interviewSchedule->customer;
        $interviewSchedule->forceDelete();
        $interviewSchedule->interview->forceDelete();
        // Update the status_kunjungan to null
        $customer->update(['status_kunjungan' => null]);
        // $interviewSchedule->customer->forceDelete();
        return redirect()
            ->route('interview.index', ['page' => $page])
            ->with('success', 'Berhasil menghapus wawancara');
    }
    public function export(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;
        $search = $request->search;
        if ($request->cities) {
            $kota = $request->cities;
        } else {
            $kota = $request->indonesia_cities_id;
        }
        $cityId = null;
        $cityName = null;

        // Pemeriksaan sebelum explode
        if (isset($kota) && strpos($kota, '|') !== false) {
            // Memisahkan id dan name menggunakan delimiter '|'
            [$cityId, $cityName] = explode('|', $kota);
        }
        $kecamatan = session()->get('kecamatan');
        $desa = session()->get('desa');

        $id = Auth::user()->id;
        if ($request->has('duplicate') && $request->duplicate == 1) {
            $filename = 'Data Duplikat';
            $duplicatedInterviews = InterviewSchedule::select('customer_id')
                ->where('type', null)
                ->has('interview')
                ->groupBy('customer_id')
                ->havingRaw('COUNT(customer_id) > 1')
                ->pluck('customer_id');

            $interview = InterviewSchedule::whereIn('customer_id', $duplicatedInterviews)
                ->whereHas('interview')
                ->has('user')
                ->has('customer')
                ->with('period', 'user', 'customer', 'interview')
                ->orderBy('customer_id');
        } else {
            $filename = 'Hasil DTDC';
            if ($cityName) {
                $cityName = ucwords(strtolower(str_replace('KABUPATEN ', '', $cityName)));
                $filename .= ' - ' . $cityName;
            }
            if ($search) {
                $filename = 'Hasil DTDC';
                $filename .= ' - ' . $search;
            }

            // dd($search);
            if (Gate::check('koordinator-area') || Gate::check('admin')) {
                $interview = InterviewSchedule::whereHas('customer', function ($customerQuery) use ($id) {
                    $customerQuery->where('indonesia_city_id', Auth::user()->indonesia_city_id);
                })
                    ->whereHas('interview', function ($query) use ($id) {
                        $query->where('owner_id', Auth::user()->owner_id);
                    })
                    ->where('type', null)
                    ->with('period', 'user', 'customer', 'interview');
                if ($tanggalMulai && $tanggalSelesai) {
                    $tanggalMulai = \Carbon\Carbon::parse($tanggalMulai)->startOfDay();
                    $tanggalSelesai = \Carbon\Carbon::parse($tanggalSelesai)->endOfDay();

                    // if ($tanggalMulai && $tanggalSelesai) {
                    //     // dd($tanggalSelesai);
                    //     $interview->whereBetween('updated_at', [$tanggalMulai, $tanggalSelesai]);
                }
            } elseif (Gate::check('super-admin')) {
                $interview = InterviewSchedule::query()
                    ->where('type', null)
                    ->has('interview')
                    ->has('user')
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            } else {
                $interview = (new InterviewSchedule())
                    ->newQuery()
                    ->whereHas('interview', function ($query) use ($id) {
                        $query->where('owner_id', $id)->orWhere('admin_id', $id);
                    })
                    ->where('type', null);
            }
            if ($search) {
                $interview = $interview->where(function ($query) use ($search) {
                    $query->WhereHas('user', function ($subquery) use ($search) {
                        $subquery->where('email', 'like', '%' . $search . '%');
                    });
                });
            }

            $interview = $interview->whereHas('customer', function ($query) use ($kota, $kecamatan, $desa, $tanggalMulai, $tanggalSelesai) {
                if ($kota) {
                    $query->whereHas('city', function ($cityQuery) use ($kota) {
                        $cityQuery->where('id', $kota);
                    });
                }
                if ($kecamatan) {
                    $query->whereHas('district', function ($districtQuery) use ($kecamatan) {
                        $districtQuery->whereIn('id', $kecamatan);
                    });
                }

                if ($desa) {
                    $query->whereHas('village', function ($villageQuery) use ($desa) {
                        $villageQuery->whereIn('id', $desa);
                    });
                }
                //         if ($tanggalMulai && $tanggalSelesai) {
                //     $query->where(function ($dateQuery) use ($tanggalMulai, $tanggalSelesai) {
                //           if ($tanggalMulai != $tanggalSelesai) {
                //                     $dateQuery->whereBetween('updated_at', [$tanggalMulai, $tanggalSelesai]);
                //                 } else {
                //                     $dateQuery->whereDate('updated_at', $tanggalMulai);
                //                 }
                //     });
                // }
            });
        }
        if ($tanggalMulai && $tanggalSelesai) {
            $tanggalMulai = \Carbon\Carbon::parse($tanggalMulai)->startOfDay();
            $tanggalSelesai = \Carbon\Carbon::parse($tanggalSelesai)->endOfDay();

            if ($tanggalMulai != $tanggalSelesai) {
                $interview->whereBetween('updated_at', [$tanggalMulai, $tanggalSelesai]);
            } else {
                $interview->whereDate('updated_at', $tanggalMulai);
            }
        }

        $interview = $interview->orderBy('updated_at', 'asc')->get();
        //   dd($interview);

        return Excel::Download(new InterviewExport($interview), $filename . ' - ' . date('d-m-Y') . '.xlsx');
        session()->forget('kecamatan');
        session()->forget('desa');
    }
    public function deleteAll(Request $request)
    {
        $selectedIds = $request->input('selected_ids');
        // dd($selectedIds);
        foreach ($selectedIds as $id) {
            if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin')) {
                return abort('403');
            }
            // dd($id);
            $interviewSchedule = InterviewSchedule::find($id);
            $interviewSchedule->interview->forceDelete();
            $interviewSchedule->forceDelete();
            // $interviewSchedule->customer->forceDelete();
        }
        return redirect()
            ->route('interview.index')
            ->with('success', 'Berhasil menghapus interview');
    }
      public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file',
        ]);

        try {
            Excel::import(new InterviewImport(), request()->file('file'));
            return redirect()
                ->route('interview.index')
                ->with('success', __('Berhasil Import interview.'));
        } catch (\Exception $e) {
            return redirect()
                ->route('interview.index')
                ->with('error', __('Gagal Import interview. ' . $e->getMessage()));
        }
    }
      public function showImport()
    {
       return view('admin.interview.import');
    }
       public function formManual()
    {
         $incomes = Income::where('owner_id', 130)
                ->latest()
                ->get();
            $occupations = Occupation::where('owner_id', 130)
                ->latest()
                ->get();
        return view('admin.interview.form-manual',  compact('incomes', 'occupations'));
    }
}
