<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InterviewExport;
use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\InterviewSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class InterviewSosmedController extends Controller
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
                } elseif (Gate::check('koordinator-area')) {
                    $query->where('owner_id', Auth::user()->owner_id);
                }
                $query->where('type', 'sosmed');
            });

            if ($nik_relawan != null) {
                $interviews = $interviews
                    ->whereHas('user', function ($query) use ($nik_relawan) {
                        $query->where('nik', 'like', '%' . $nik_relawan . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            }

            if ($nik_responden != null) {
                $interviews = $interviews
                    ->whereHas('customer', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            }

            if ($alamat != null) {
                $interviews = $interviews
                    ->whereHas('interview', function ($query) use ($alamat) {
                        $query->where('location', 'like', '%' . $alamat . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            }

            if ($date != null) {
                $interviews = $interviews
                    ->whereHas('interview', function ($query) use ($date) {
                        $query->where('interview_date', 'like', '%' . $date . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            }

            if ($nik_relawan != null && $nik_responden != null) {
                $interviews = $interviews
                    ->whereHas('user', function ($query) use ($nik_relawan) {
                        $query->where('nik', 'like', '%' . $nik_relawan . '%');
                    })
                    ->whereHas('customer', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
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
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
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
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            }

            if ($nik_responden != null && $alamat != null) {
                $interviews = $interviews
                    ->whereHas('customer', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->whereHas('interview', function ($query) use ($alamat) {
                        $query->where('location', 'like', '%' . $alamat . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            }

            if ($nik_responden != null && $date != null) {
                $interviews = $interviews
                    ->whereHas('customer', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->whereHas('interview', function ($query) use ($date) {
                        $query->where('interview_date', 'like', '%' . $date . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
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
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            }

            if ($nik_relawan != null && $nik_responden != null && $alamat != null) {
                $interviews = $interviews
                    ->whereHas('user', function ($query) use ($nik_relawan) {
                        $query->where('nik', 'like', '%' . $nik_relawan . '%');
                    })
                    ->whereHas('customer', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->whereHas('interview', function ($query) use ($alamat) {
                        $query->where('location', 'like', '%' . $alamat . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            }

            if ($nik_relawan != null && $nik_responden != null && $date != null) {
                $interviews = $interviews
                    ->whereHas('user', function ($query) use ($nik_relawan) {
                        $query->where('nik', 'like', '%' . $nik_relawan . '%');
                    })
                    ->whereHas('customer', function ($query) use ($nik_responden) {
                        $query->where('nik', 'like', '%' . $nik_responden . '%');
                    })
                    ->whereHas('interview', function ($query) use ($date) {
                        $query->where('interview_date', 'like', '%' . $date . '%');
                    })
                    ->has('interview')
                    ->has('user')
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
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
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            }

            if ($nik_responden != null && $alamat != null && $date != null) {
                $interviews = $interviews
                    ->whereHas('customer', function ($query) use ($nik_responden) {
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
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            }

            if ($nik_relawan != null && $nik_responden != null && $alamat != null && $date != null) {
                $interviews = $interviews
                    ->whereHas('user', function ($query) use ($nik_relawan) {
                        $query->where('nik', 'like', '%' . $nik_relawan . '%');
                    })
                    ->whereHas('customer', function ($query) use ($nik_responden) {
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
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            }

            if ($nik_relawan == null && $nik_responden == null && $alamat == null && $date == null) {
                $interviews = (new InterviewSchedule())->newQuery();
                $interviews = $interviews
                    ->has('interview')
                    ->has('user')
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            }
        } else {
            if (Gate::check('koordinator-area')) {
                $interviews = InterviewSchedule::whereHas('customer', function ($customerQuery) use ($id) {
                    $customerQuery->where('indonesia_city_id', Auth::user()->indonesia_city_id);
                })
                    ->whereHas('interview', function ($query) use ($id) {
                        $query->where('owner_id', Auth::user()->owner_id);
                    })
                    ->where('type', 'sosmed');
            } elseif (Gate::check('super-admin')) {
                $interviews = InterviewSchedule::query()
                    ->where('type', 'sosmed')
                    ->has('interview')
                    ->has('user')
                    ->has('customer')
                    ->with('period', 'user', 'customer', 'interview');
            } else {
                $interviews = (new InterviewSchedule())
                    ->newQuery()
                    ->whereHas('interview', function ($query) use ($id) {
                        $query->where('owner_id', $id)->orWhere('admin_id', $id);
                    })
                    ->where('type', 'sosmed');
            }
        }

        // dd(count($interviews));

        // dd($interviews->first()->interview->data);
        // dd($interviews);

        // dd($interviews);

        $search = $request->input('search');
        $kota = $request->input('indonesia_cities_id');
        $kecamatan = $request->input('indonesia_districts_id') ?? null;
        $desa = $request->input('indonesia_villages_id') ?? null;
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        if ($request->has('duplicate')) {
            $duplicatedInterviews = InterviewSchedule::select('customer_id')
                ->has('interview')
                ->groupBy('customer_id')
                ->havingRaw('COUNT(customer_id) > 1')
                ->pluck('customer_id');

            $interviews = InterviewSchedule::whereIn('customer_id', $duplicatedInterviews)
                ->whereHas('interview')
                ->has('user')
                ->has('customer')
                ->with('period', 'user', 'customer', 'interview')
                ->orderBy('customer_id');
        }

        if ($search) {
            $interviews = $interviews
                ->where(function ($query) use ($search) {
                    $query
                        ->whereHas('user', function ($subquery) use ($search) {
                            $subquery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('customer', function ($subquery) use ($search) {
                            $subquery->whereHas('district', function ($districtSubquery) use ($search) {
                                $districtSubquery->where('name', 'like', '%' . $search . '%');
                            });
                        })
                        ->orWhereHas('customer', function ($subquery) use ($search) {
                            $subquery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('user', function ($subquery) use ($search) {
                            $subquery->where('email', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('customer', function ($subquery) use ($search) {
                            $subquery->where('phone', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('user', function ($subquery) use ($search) {
                            $subquery->where('phone', 'like', '%' . $search . '%');
                        });
                })
                ->latest()
                ->paginate(10)
                ->onEachSide(2);
        } elseif ($kota || $kecamatan || $desa || $tanggalMulai || $tanggalSelesai) {
            $interviews = $interviews->whereHas('customer', function ($query) use ($kota, $kecamatan, $desa, $tanggalMulai, $tanggalSelesai) {
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
              if ($tanggalMulai && $tanggalSelesai) {
                    if ($tanggalMulai != $tanggalSelesai) {
                        $query->whereHas('schedules', function ($interviewQuery) use ($tanggalMulai, $tanggalSelesai) {
                            $interviewQuery->whereBetween('updated_at', [$tanggalMulai, $tanggalSelesai]);
                        });
                    } else {
                        $query->whereHas('schedules', function ($interviewQuery) use ($tanggalMulai) {
                            $interviewQuery->whereDate('updated_at', $tanggalMulai);
                        });
                    }
                }
            });

            $interviews = $interviews
                ->latest()
                ->paginate(10)
                ->onEachSide(2);
            //  dd($interviews);
        } else {
            $interviews = $interviews
                ->latest()
                ->paginate(10)
                ->onEachSide(2);
        }
        return view('admin.interview-sosmed.index', compact('interviews'));
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

        // get interview schedule with period user and customer

        // $schedules = InterviewSchedule::all();
        $schedule = InterviewSchedule::with('user', 'interview', 'customer')->findOrFail($id);
        // dd($findScheduleWithId);
        // dd($schedule->start_date);

        return view('admin.interview-sosmed.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(InterviewSchedule $interview, $id)
    {
        // if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin')) {
        //     return abort('403');
        // }
       
        $interviews = InterviewSchedule::where('id', '<>', $interview->id)->get();
        $interview = InterviewSchedule::where('id', $id)->first();
        // dd($question);
        return view('admin.interview-sosmed.edit', compact(['interview', 'interviews']));
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
        $interviewSchedule->forceDelete();
        $interviewSchedule->interview->forceDelete();
        // $interviewSchedule->customer->forceDelete();
        return redirect()
            ->route('interview-sosmed.index', ['page' => $page])
            ->with('success', 'Berhasil menghapus wawancara');
    }
        public function getStatusAll(Request $request)
    {
        // dd($userId);
        // Gantilah ini dengan kode yang sesuai untuk mengambil status pengguna dengan ID $userId
        $selectedIds = $request->input('selected_ids');

        if (!is_null($selectedIds)) {
            foreach ($selectedIds as $id) {
                $relawan = User::find($id);

                if (!is_null($relawan)) {
                    $relawan->status = $request->input('status');
                    $relawan->save();
                } else {
                }
            }
        }

        // dd($request->status);

        return redirect()
            ->route('relawan-sosmed.index')
            ->with('success', 'Status pengguna berhasil diperbarui.');
    }
    public function export(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;
        if ($request->cities) {
            $kota = $request->cities;
        } else {
            $kota = $request->indonesia_cities_id;
        }
        // Inisialisasi $cityId dan $cityName untuk menghindari error jika tidak di-set
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
                ->whereNotNull('type')
                ->has('interview')
                ->groupBy('customer_id')
                ->havingRaw('COUNT(customer_id) > 1')
                ->pluck('customer_id');

            $interview = InterviewSchedule::whereIn('customer_id', $duplicatedInterviews)
                ->whereNotNull('type')
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
            if (Gate::check('koordinator-area')) {
                $interview = InterviewSchedule::whereHas('customer', function ($customerQuery) use ($id) {
                    $customerQuery->where('indonesia_city_id', Auth::user()->indonesia_city_id);
                })
                    ->whereHas('interview', function ($query) use ($id) {
                        $query->where('owner_id', Auth::user()->owner_id);
                    })
                    ->whereNotNull('type');
            } elseif (Gate::check('super-admin')) {
                $interview = InterviewSchedule::query()
                    ->whereNotNull('type')
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
                    ->whereNotNull('type');
            }

            $interview = $interview->whereHas('customer', function ($query) use ($kota, $kecamatan, $desa, $tanggalMulai, $tanggalSelesai) {
                if ($kota) {
                    $query->whereHas('city', function ($cityQuery) use ($kota) {
                        $cityQuery->where('id', $kota);
                    });
                }
                   if ($tanggalMulai && $tanggalSelesai) {
                    if ($tanggalMulai != $tanggalSelesai) {
                        $query->whereHas('schedules', function ($interviewQuery) use ($tanggalMulai, $tanggalSelesai) {
                            $interviewQuery->whereBetween('updated_at', [$tanggalMulai, $tanggalSelesai]);
                        });
                    } else {
                        $query->whereHas('schedules', function ($interviewQuery) use ($tanggalMulai) {
                            $interviewQuery->whereDate('updated_at', $tanggalMulai);
                        });
                    }
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
            });
        }

        $interview = $interview->get();
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
            ->route('interview-sosmed.index')
            ->with('success', 'Berhasil menghapus interview');
    }
}
