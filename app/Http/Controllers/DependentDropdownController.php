<?php

namespace App\Http\Controllers;

use App\Models\QuickCount;
use App\Models\User;
use App\Models\Caleg;
use App\Models\Partai;
use App\Models\Occupation;
use App\Models\InterviewSchedule;
use Illuminate\Http\Request;

class DependentDropdownController extends Controller
{
    public function provinces()
    {
        return \Indonesia::allProvinces();
    }

    public function cities(Request $request)
    {
        return \Indonesia::findProvince($request->id, ['cities'])->cities->pluck('name', 'id');
    }

    public function districts(Request $request)
    {
        return \Indonesia::findCity($request->id, ['districts'])->districts->pluck('name', 'id');
    }

    public function villages(Request $request)
    {
        return \Indonesia::findDistrict($request->id, ['villages'])->villages->pluck('name', 'id');
    }
     public function allVillages(Request $request)
    {
        $districts = \Indonesia::findCity($request->id, ['districts'])->districts;

        $allVillages = collect([]);

        foreach ($districts as $district) {
            $villages = $district->villages;
            $allVillages = $allVillages->merge($villages);
        }

        return $allVillages->pluck('name', 'id'); // Mengembalikan hasil dalam bentuk respons JSON
    }
    public function villagesMultiSelect(Request $request)
    {
         $kecamatanIds = $request->input('id', []);

    if (empty($kecamatanIds)) {
        // Jika tidak ada kecamatan yang dipilih, kembalikan pesan kesalahan atau respons yang sesuai.
        return $this->sendError('Tidak ada kecamatan yang dipilih.', [], 400);
    }

    $villageList = [];

    foreach ($kecamatanIds as $kecamatanId) {
        $regions = \Indonesia::findDistrict($kecamatanId, ['villages'])->villages;
        $villageList = $villageList + $regions->pluck('name', 'id')->all(); ///ini untuk ganti array dari 0-dstnya jadi idnya village
    }

    return $villageList;
    }

    public function citiesData($id)
    {
        return \Indonesia::findProvince($id, ['cities'])->cities;
    }

    public function districtsData($id)
    {
        return \Indonesia::findCity($id, ['districts'])->districts;
    }

    public function villagesData($id)
    {
        return \Indonesia::findDistrict($id, ['villages'])->villages;
    }
       public function relawan(Request $request)
    {
         $query = trim($request->term);
            // dd($query);
            $cityId = $request->input('id');
            $data = User::where('indonesia_city_id', $cityId)->select('id', 'email as text')
            ->where('email', 'LIKE', "%" . $query . "%")
            ->whereNotNull('admin_id')
            ->distinct()
            ->simplePaginate(10);
            // dd($data);
            $morePages = true;
            if (empty($data->nextPageUrl())) {
                $morePages = false;
            }
    
            $result = array(
                'results' => $data->items(),
                'pagination' => array(
                    'more' => $morePages
                )
            );
            return response()->json($result);
    }
     public function caleg(Request $request)
{
    // Pastikan request memiliki properti 'id' yang merupakan ID partai yang valid
    if ($request->has('id')) {
        // Ambil data Caleg yang memiliki 'partai_id' sesuai dengan ID partai yang diterima dari permintaan
        $data = Caleg::where('partai_id', $request->id)->get();

        // Ubah data Caleg menjadi array asosiatif dengan 'id' sebagai kunci dan 'name' sebagai nilai
        $result = $data->pluck('name', 'id');

        // Kembalikan data dalam bentuk JSON
        return response()->json($result);
    }

    // Jika request tidak memiliki properti 'id', kembalikan respons kosong
    return response()->json([]);
}
    public function partai(Request $request)
    {
        $partai = Partai::all();

        // Ambil data QuickCount yang sesuai dengan TPS dan ID desa yang diterima dari permintaan
        $quickCounts = QuickCount::where('tps', $request->tps)
            ->where('indonesia_village_id', $request->indonesia_village_id)
            ->pluck('partai_id'); // Ambil hanya ID partai yang sudah ada dalam QuickCount

        // Filter partai yang belum ada dalam QuickCount dengan menggunakan ID partai yang sudah ada
        $filteredPartai = $partai->reject(function ($p) use ($quickCounts) {
            return $quickCounts->contains($p->id);
        });

        // Ubah data partai yang belum ada dalam QuickCount menjadi array asosiatif dengan 'id' sebagai kunci dan 'name' sebagai nilai
        $result = $filteredPartai->pluck('name', 'id');
        // Kembalikan data dalam bentuk JSON
        // dd($request->tps, $request->indonesia_village_id);
        return response()->json($result);
    }
    public function partaiData($tps, $village)
    {
        $partai = Partai::all();

        // Ambil data QuickCount yang sesuai dengan TPS dan ID desa yang diterima dari permintaan
        $quickCounts = QuickCount::where('tps', $tps)->where('indonesia_village_id', $village)->pluck('partai_id'); // Ambil hanya ID partai yang sudah ada dalam QuickCount

        // Filter partai yang belum ada dalam QuickCount dengan menggunakan ID partai yang sudah ada
        $filteredPartai = $partai->reject(function ($p) use ($quickCounts) {
            return $quickCounts->contains($p->id);
        });

        // Ubah data partai yang belum ada dalam QuickCount menjadi array asosiatif dengan 'id' sebagai kunci dan 'name' sebagai nilai
        $result = $filteredPartai->pluck('name', 'id');
        // Kembalikan data dalam bentuk JSON
        // dd($request->tps, $request->indonesia_village_id);
        return $result;
    }
    public function interviews(Request $request)
    {
        $interviews = InterviewSchedule::with(['user', 'interview'])
            ->join('interviews', 'interviews.interview_schedule_id', '=', 'interview_schedules.id')
            ->join('users', 'users.id', '=', 'interview_schedules.user_id')
            ->select('users.indonesia_district_id', \DB::raw('count(*) as interview_count'))
            ->whereNotNull('users.indonesia_district_id');
        $interviews->where('users.indonesia_city_id', $request->input('id'));

        $interviews = $interviews
            ->groupBy('users.indonesia_district_id')
            ->orderByDesc('interview_count') // Urutkan dari terbesar ke terkecil
            ->get();
        $districtIds = $interviews->pluck('indonesia_district_id')->toArray();
        $districtNames = \Indonesia::findDistrict($districtIds)->pluck('name', 'id');

        // Append district names to the interviews
        $interviews->transform(function ($interview) use ($districtNames) {
            $interview->district_name = $districtNames[$interview->indonesia_district_id] ?? 'Unknown District';
            return $interview;
        });

        return response()->json($interviews); // Mengembalikan hasil dalam bentuk respons JSON
    }
    public function interviewByVillages(Request $request)
    {
        $interviews = InterviewSchedule::with(['user', 'interview'])
            ->join('interviews', 'interviews.interview_schedule_id', '=', 'interview_schedules.id')
            ->join('users', 'users.id', '=', 'interview_schedules.user_id')
            ->select('users.indonesia_village_id', \DB::raw('count(*) as interview_count'))
            ->whereNotNull('users.indonesia_village_id');
        $interviews->where('users.indonesia_city_id', $request->input('id'));

      
        $interviews = $interviews
            ->groupBy('users.indonesia_village_id')
            ->orderByDesc('interview_count') // Urutkan dari terbesar ke terkecil
            ->get();
        $villageIds = $interviews->pluck('indonesia_village_id')->toArray();

        // Ambil nama-nama desa berdasarkan id desa yang valid
        $villageNames = \Indonesia::findVillage($villageIds)->pluck('name', 'id');

        // Append village names to the interviews
        $interviews->transform(function ($interview) use ($villageNames) {
            // Pastikan desa ada dalam daftar nama desa sebelum mengakses namanya
            $interview->village_name = $villageNames[$interview->indonesia_village_id] ?? 'Unknown District';
            return $interview;
        });

        return response()->json($interviews); // Mengembalikan hasil dalam bentuk respons JSON
    }
    public function occupations()
    {
        $data = Occupation::all();

        return $data->pluck('name', 'id');
    }
    public function interviewsByOccupation(Request $request)
    {
        $interviews = InterviewSchedule::with(['user', 'interview', 'customer'])
            ->join('interviews', 'interviews.interview_schedule_id', '=', 'interview_schedules.id')
            ->join('users', 'users.id', '=', 'interview_schedules.user_id')
            ->join('customers', 'customers.id', '=', 'interview_schedules.customer_id')
               ->where('customers.indonesia_city_id', $request->input('id'))
             ->whereNotNull('customers.job')
            ->select('customers.job', \DB::raw('count(*) as interview_count'));
      
        $interviews = $interviews
            ->groupBy('customers.job')
            ->orderByDesc('interview_count') // Urutkan dari terbesar ke terkecil
            ->get();

        return response()->json($interviews); // Mengembalikan hasil dalam bentuk respons JSON
    }
    public function interviewsByAge(Request $request)
    {
        $interviews = InterviewSchedule::with(['user', 'interview', 'customer'])
            ->selectRaw(
                '
        CASE
            WHEN FLOOR(DATEDIFF(CURDATE(), customers.dob) / 365) = 17 THEN "17"
            WHEN FLOOR(DATEDIFF(CURDATE(), customers.dob) / 365) BETWEEN 17 AND 27 THEN "17-27"
            WHEN FLOOR(DATEDIFF(CURDATE(), customers.dob) / 365) BETWEEN 28 AND 41 THEN "28-41"
            WHEN FLOOR(DATEDIFF(CURDATE(), customers.dob) / 365) BETWEEN 42 AND 57 THEN "42-57"
            WHEN FLOOR(DATEDIFF(CURDATE(), customers.dob) / 365) BETWEEN 58 AND 76 THEN "58-76"
            WHEN FLOOR(DATEDIFF(CURDATE(), customers.dob) / 365) >77 THEN ">77"
            ELSE "none"
        END AS age_range,
        COUNT(*) AS interview_count
    ',
            )
            ->join('interviews', 'interviews.interview_schedule_id', '=', 'interview_schedules.id')
            ->join('users', 'users.id', '=', 'interview_schedules.user_id')
            ->join('customers', 'customers.id', '=', 'interview_schedules.customer_id')
               ->where('customers.indonesia_city_id', $request->input('id'))
            ->groupBy('age_range')
            ->havingRaw('age_range != "none"') // Menggunakan HAVING untuk menyaring hasil
            ->get();

        $noneRange = [];
        $otherRanges = [];

        foreach ($interviews as $result) {
            if ($result->age_range === '>77') {
                array_push($noneRange, $result);
            } else {
                array_push($otherRanges, $result);
            }
        }

        // Menggabungkan kembali hasil dengan urutan yang diinginkan
        $sortedInterviews = array_merge($otherRanges, $noneRange);

        return response()->json($sortedInterviews); // Mengembalikan hasil dalam bentuk respons JSON
    }
    public function interviewsByEducation(Request $request)
    {
        $interviews = InterviewSchedule::with(['user', 'interview', 'customer'])
            ->join('interviews', 'interviews.interview_schedule_id', '=', 'interview_schedules.id')
            ->join('users', 'users.id', '=', 'interview_schedules.user_id')
            ->join('customers', 'customers.id', '=', 'interview_schedules.customer_id')
            ->where('customers.indonesia_city_id', $request->input('id'))
             ->whereNotNull('customers.education')
             ->where('customers.education', '!=', 'null')
            ->select('customers.education', \DB::raw('count(*) as interview_count'))
            ->groupBy('customers.education')
                
            ->orderByDesc('interview_count') // Urutkan dari terbesar ke terkecil
            ->get();
        return response()->json($interviews); // Mengembalikan hasil dalam bentuk respons JSON
    }
    public function interviewsByFamilyElection(Request $request)
    {
        $interviews = InterviewSchedule::with(['user', 'interview', 'customer'])
            ->join('interviews', 'interviews.interview_schedule_id', '=', 'interview_schedules.id')
            ->join('users', 'users.id', '=', 'interview_schedules.user_id')
            ->join('customers', 'customers.id', '=', 'interview_schedules.customer_id')
            ->where('customers.indonesia_city_id', $request->input('id'))
              ->whereNotNull('customers.family_election')
            ->select('customers.family_election', \DB::raw('count(*) as interview_count'))
            ->groupBy('customers.family_election')
            ->orderByDesc('interview_count') // Urutkan dari terbesar ke terkecil
            ->get();
        return response()->json($interviews); // Mengembalikan hasil dalam bentuk respons JSON
    }

 public function interviewsByTps(Request $request)
    {
        $interviews = InterviewSchedule::with(['user', 'interview', 'customer'])
            ->join('interviews', 'interviews.interview_schedule_id', '=', 'interview_schedules.id')
            ->join('users', 'users.id', '=', 'interview_schedules.user_id')
            ->join('customers', 'customers.id', '=', 'interview_schedules.customer_id')
            ->where('customers.indonesia_city_id', $request->input('id'))
            ->whereNotNull('customers.tps')
            ->select('customers.tps', \DB::raw('count(*) as interview_count'))
            ->groupBy('customers.tps')
            ->orderByDesc('interview_count') // Urutkan dari terbesar ke terkecil
            ->get();
        return response()->json($interviews); // Mengembalikan hasil dalam bentuk respons JSON
    }
     public function interviewsByDate(Request $request)
    {
        $interviews = InterviewSchedule::with(['user', 'interview', 'customer'])
            ->join('interviews', 'interviews.interview_schedule_id', '=', 'interview_schedules.id')
            ->join('users', 'users.id', '=', 'interview_schedules.user_id')
            ->join('customers', 'customers.id', '=', 'interview_schedules.customer_id')
            ->where('customers.indonesia_city_id', $request->input('id'))
            ->whereNotNull('interviews.interview_date')
            ->select(\DB::raw('DATE(interviews.interview_date) as interview_date'), \DB::raw('count(*) as interview_count'))
            ->groupBy(\DB::raw('DATE(interviews.interview_date)'))
            ->orderBy(\DB::raw('DATE(interviews.interview_date)', 'asc')) // Urutkan dari terbesar ke terkecil
            ->get();
        return response()->json($interviews); // Mengembalikan hasil dalam bentuk respons JSON
    }
}
