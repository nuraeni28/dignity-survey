<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\InterviewData;
use App\Models\InterviewSchedule;
use App\Exports\PerformaVolunteerExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($roles = Auth::user()->roles) {
            foreach ($roles as $role) {
                if ($role->name !== 'owner' && $role->name !== 'super-admin' && $role->name !== 'koordinator-area' && $role->name !== 'admin') {
                    return abort(403);
                }
            }
        }
        $kota = $request->input('indonesia_cities_id');
        $interviews = InterviewSchedule::with(['user', 'interview'])
            ->join('interviews', 'interviews.interview_schedule_id', '=', 'interview_schedules.id') // Adjust the join condition based on your schema
            ->has('user')
            ->has('interview')
            ->select('user_id', \DB::raw('count(*) as interview_count'), \DB::raw('count(distinct interviews.interview_date) as frequency'), \DB::raw('MIN(interviews.interview_date) as first_interview_date'));

        if (Gate::check('koordinator-area')) {
            $interviews->join('users', 'users.id', '=', 'interview_schedules.user_id');
            $interviews->where(function ($query) {
        $query->where('users.indonesia_city_id', Auth::user()->indonesia_city_id)
            ->where('users.recomended_by', '=', null);
    });
        }
        if ($kota) {
            $interviews->join('users', 'users.id', '=', 'interview_schedules.user_id');
            $interviews->where('users.indonesia_city_id', $kota);
        }

        $interviews = $interviews->groupBy('user_id')->get();

        $excludedDates = ['2023-10-10', '2023-10-17']; // Tanggal-tanggal yang ingin dikecualikan
        $interviews->each(function ($interview) use ($excludedDates) {
            $firstInterviewDate = Carbon::parse($interview->first_interview_date);
            $now = Carbon::now();

            // Menghitung selisih hari tanpa memasukkan tanggal-tanggal tertentu
            $daysSinceFirstInterview = $firstInterviewDate->diffInDaysFiltered(function (Carbon $date) use ($excludedDates) {
                return !in_array($date->format('Y-m-d'), $excludedDates);
            }, $now);

            $interview->days_since_first_interview = $daysSinceFirstInterview;
        });

        // dd($kota);
        // if ($kota) {
        //     $interviews->join('users', 'users.id', '=', 'interview_schedules.user_id');
        //     $interviews->where('users.indonesia_city_id', $kota);
        // }
        return view('dashboard', compact('interviews'));
    }
    public function export(Request $request)
    {
        $interviews = InterviewSchedule::with(['user', 'interview'])
            ->join('interviews', 'interviews.interview_schedule_id', '=', 'interview_schedules.id') // Adjust the join condition based on your schema
            ->has('user')
            ->has('interview')
            ->select('user_id', \DB::raw('count(*) as interview_count'), \DB::raw('count(distinct interviews.interview_date) as frequency'), \DB::raw('MIN(interviews.interview_date) as first_interview_date'));

        if ($request->cities) {
            $kota = $request->cities;
        } else {
            $kota = $request->indonesia_cities_id;
        }
        // dd($kota);
         // Inisialisasi $cityId dan $cityName untuk menghindari error jika tidak di-set
        $cityId = null;
        $cityName = null;

        // Pemeriksaan sebelum explode
        if (isset($kota) && strpos($kota, '|') !== false) {
            // Memisahkan id dan name menggunakan delimiter '|'
            [$cityId, $cityName] = explode('|', $kota);
        }
        if ($kota) {
            // Hapus penyaringan kota sebelumnya dan gantilah dengan yang baru
            $interviews->join('users', 'users.id', '=', 'interview_schedules.user_id')->where('users.indonesia_city_id', $kota);
        } elseif (Gate::check('koordinator-area')) {
            // Tambahkan kondisi untuk memastikan bahwa jika bukan koordinator area dan tidak ada kota yang dipilih,
            // maka tetap menggunakan kriteria kota dari user yang sedang login
            
            $interviews->join('users', 'users.id', '=', 'interview_schedules.user_id')->where('users.indonesia_city_id', Auth::user()->indonesia_city_id)->where('recomended_by','=', null);
        }

        $interviews = $interviews->groupBy('user_id')->get();
        // dd($interviews);
        $excludedDates = ['2023-10-10', '2023-10-17']; // Tanggal-tanggal yang ingin dikecualikan
        $interviews->each(function ($interview) use ($excludedDates) {
            $firstInterviewDate = Carbon::parse($interview->first_interview_date);
            $now = Carbon::now();

            // Menghitung selisih hari tanpa memasukkan tanggal-tanggal tertentu
            $daysSinceFirstInterview = $firstInterviewDate->diffInDaysFiltered(function (Carbon $date) use ($excludedDates) {
                return !in_array($date->format('Y-m-d'), $excludedDates);
            }, $now);

            $interview->days_since_first_interview = $daysSinceFirstInterview;
        });
         $fileName = 'Performa Relawan';
        if ($cityName) {
            $cityName = ucwords(strtolower(str_replace('KABUPATEN ', '', $cityName)));
            $fileName .= ' - ' . $cityName;
        }
        $fileName .= ' - ' . date('d-m-Y') . '.xlsx';
        return Excel::Download(new PerformaVolunteerExport($interviews), $fileName);
    }
}
