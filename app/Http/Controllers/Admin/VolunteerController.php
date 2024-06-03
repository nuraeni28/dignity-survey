<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DependentDropdownController;
use App\Imports\UsersImport;
use App\Exports\VolunteerLapanganExport;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class VolunteerController extends Controller
{
    // public function __construct()
    // {
    //     // dd(Auth::user());
    //     $this->middleware('can:admin', ['except' => ['index', 'show']]);
    //    $this->middleware('can:super-admin', ['only' => ['edit']]);
    //     $this->middleware('can:admin', ['only' => ['import']]);
    // }
    public function getCities($provinceId)
    {
        $cities = new DependentDropdownController();
        $cities = $cities->citiesData($provinceId);
        // $cities = City::where('province_id', $provinceId)->get();
        return response()->json($cities);
    }
    public function getVillages($districtId)
    {
        $villages = new DependentDropdownController();
        $villages = $villages->villagesData($districtId);
        // $cities = City::where('province_id', $provinceId)->get();
        return response()->json($villages);
    }
    // public function getDistricts($cityId)
    // {
    //     $districts = District::where('city_id', $cityId)->get();
    //     return response()->json($districts);
    // }

    // public function getVillages($districtId)
    // {
    //     $villages = Village::where('district_id', $districtId)->get();
    //     return response()->json($villages);
    // }
    public function index(Request $request)
    {
        if ($roles = Auth::user()->roles) {
            foreach ($roles as $role) {
                if ($role->name !== 'owner' && $role->name !== 'admin' && $role->name !== 'super-admin' && $role->name !== 'koordinator-area') {
                    return abort(403);
                }
            }
        }
        if ($role->name !== 'super-admin') {
            $volunteers = User::where('admin_id', Auth::user()->id)
             ->orWhere('recomended_by', null)
                ->orWhere('owner_id', Auth::user()->id)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'user');
                })
                ->paginate(10);
        } else {
            $volunteers = User::whereHas('roles', function ($q) {
                $q->where('name', 'user');
            })
             ->where('recomended_by', null)
            ->paginate(10);
        }
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'user');
        })->where('recomended_by', null);

        // Handle the search functionality
        $search = $request->input('search');
      if (!empty($search)) {
            $query->where(function ($subquery) use ($search) {
                $subquery->where('name', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%')->orWhere('nik', 'like', '%' . $search . '%');
            });
        }
        


        // If the user is not a super-admin, filter by admin_id or owner_id
        if (Auth::user()->hasRole('super-admin')) {
            $volunteers = $query->paginate(10);
        } 
         elseif (Auth::user()->hasRole('koordinator-area') || Auth::user()->hasRole('admin')) {
            $volunteers = $query
                ->where(function ($subquery) {
                    $subquery->where('indonesia_city_id', Auth::user()->indonesia_city_id);
                })
                ->where('recomended_by', null)
                ->paginate(10);
        }
        else {
            $volunteers = $query
                ->where(function ($subquery) {
                    $subquery->where('admin_id', Auth::user()->id)->orWhere('owner_id', Auth::user()->id);
                })->where('recomended_by', null)
                ->paginate(10);
        }
            $volunteers->appends(['search' => $search]);

        return view('admin.volunteer.index', compact('volunteers'));
    }

    public function create()
    {
        // dd(Auth::user()->province->id);
        return view('admin.volunteer.create');
    }

    public function show($id, User $volunteer)
    {
        // dd($roles = Auth::user()->roles);
        if ($roles = Auth::user()->roles) {
            foreach ($roles as $role) {
                if ($role->name !== 'owner' && $role->name !== 'admin' && $role->name != 'super-admin') {
                    return abort(403);
                }
            }
        }
        $volunteer = User::findOrFail($id);
        $owner = User::findOrFail($volunteer->owner_id);
        $admin = User::findOrFail($volunteer->admin_id);
        // dd($admin);
        return view('admin.volunteer.show', compact('admin', 'owner', 'volunteer'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nik' => ['nullable', 'numeric', 'digits:16', 'unique:users'],
            'phone' => ['nullable', 'numeric', 'digits_between:8,14'],
            'tps' => ['nullable', 'numeric'],
            'gender' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'indonesia_village_id' => ['required'],
        ]);

        $request->merge([
            'password' => Hash::make($request->password),
            'indonesia_province_id' => Auth::user()->indonesia_province_id,
            'indonesia_city_id' => Auth::user()->indonesia_city_id,
            'indonesia_district_id' => Auth::user()->indonesia_district_id,
            'owner_id' => Auth::user()->owner_id,
            'admin_id' => Auth::user()->id,
        ]);
        // dd(Auth::user());
        try {
            $user = User::create($request->only(['name', 'email', 'owner_id', 'admin_id', 'password', 'nik', 'phone', 'address', 'indonesia_province_id', 'indonesia_city_id', 'indonesia_district_id', 'indonesia_village_id', 'tps', 'gender']));
            //    dd($user);
            $role = Role::where('name', 'user')->first();

            $user->assignRole($role);
            $user->update([
                'email_verified_at' => Carbon::now(),
            ]);

            return redirect()
                ->back()
                ->with('success', 'Berhasil membuat relawan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal membuat relawan: ' . $e->getMessage());
        }
    }

    public function edit(User $volunteer, $relawan,  Request $request)
    {
        $page = $request->input('page');
        $volunteer = User::findOrFail($relawan);
        return view('admin.volunteer.edit', compact('volunteer', 'page'));
    }

    public function update(Request $request, User $volunteer, $id)
    {
        $volunteer = User::findOrFail($id);

        // dd($request->email == $volunteer->email || $request->nik == $volunteer->nik);
        // if ($request->email == $volunteer->email || $request->nik == $volunteer->nik) {
        $this->validate($request, [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($volunteer->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'nik' => ['nullable', 'numeric', 'digits:16'],
            'phone' => ['nullable', 'numeric', 'digits_between:8,14'],
            'tps' => ['nullable', 'numeric'],
            'address' => ['nullable', 'string'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
            'indonesia_village_id' => ['required'],
        ]);
        // } else {
        // }
         $indonesiaVillageId = $request->input('indonesia_village_id');
    // dd($indonesiaVillageId == '-');
    if ($indonesiaVillageId == '-') {
        // Set the village ID to NULL
        $indonesiaVillageId = null;
    }

        $volunteer->update($request->only(['name', 'email', 'nik', 'phone', 'address', 'indonesia_district_id', 'tps']));
   $indonesiaDistrictId = $request->input('indonesia_district_id');
        $volunteer->indonesia_village_id = $indonesiaVillageId;

        $user = User::where('indonesia_district_id', $indonesiaDistrictId)
            ->whereNull('admin_id')->whereNotNull('owner_id')
            ->first();
        $volunteer->admin_id = $user->id;
        $volunteer->save();
        if ($request->hasFile('profile_image')) {
            $volunteer->profile_image = $request->file('profile_image')->store('profile_images', 'public');
            $volunteer->save();
        }

        if ($request->password) {
            $volunteer->update([
                'password' => Hash::make($request->password),
            ]);
        }
        // dd($volunteer->email);

            $page = $request->input('page');

          return redirect()
            ->route('relawan.index', ['page'=> $page])
            ->with('success', __('Berhasil mengupdate Relawan.'));
    }

     public function destroy($id, Request $request)
    {
        $page = $request->input('page');
        if ($roles = Auth::user()->roles) {
            foreach ($roles as $role) {
                if ($role->name !== 'admin' && $role->name !== 'super-admin') {
                    return abort(403);
                }
            }
        }
        $volunteer = User::find($id);
        $volunteer->delete();
        return redirect()
            ->route('relawan.index', ['page' => $page])
            ->with('success', 'Berhasil menghapus relawan.');
    }



    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file',
        ]);

         try {
            Excel::import(new UsersImport(), request()->file('file'));
            return redirect()
                ->route('relawan.index')
                ->with('success', __('Berhasil Import relawan.'));
        } catch (\Exception $e) {
            return redirect()
                ->route('relawan.index')
                ->with('error', __('Gagal Import relawan. ' . $e->getMessage()));
        }
    }
  public function getStatus(Request $request, $userId)
    {
        // dd($userId);
        // Gantilah ini dengan kode yang sesuai untuk mengambil status pengguna dengan ID $userId
        $relawan = User::find($userId);
        // dd($request->status);
        $relawan->status = $request->input('status');
        $relawan->save();
        $page = $request->input('page');

        return redirect()
            ->route('relawan.index', ['page' => $page])
            ->with('success', 'Status pengguna berhasil diperbarui.');
    }
       public function getStatusKabupaten(Request $request)
    {
        // dd($userId);
        // Gantilah ini dengan kode yang sesuai untuk mengambil status pengguna dengan ID $userId
        $relawan = User::where('indonesia_city_id',$request->indonesia_cities_id)->where('recomended_by', null)->get();
        foreach($relawan as $relawan){
             $relawan->status = $request->input('status');
             $relawan->save();
        }
        // dd($request->status);
       

        return redirect()
            ->route('relawan.index')
            ->with('success', 'Status pengguna berhasil diperbarui.');
    }
        public function export(Request $request)
    {
        $users = User::where('indonesia_city_id', Auth::user()->indonesia_city_id)
            ->whereNull('recomended_by')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'user');
            })
            ->get();
        // dd($users);

        $cityName = ucwords(strtolower(str_replace('KABUPATEN ', '', Auth::user()->city->name)));
        $fileName = 'Relawan Lapangan ' . $cityName;

        $fileName .= ' - ' . date('d-m-Y') . '.xlsx';
        return Excel::Download(new VolunteerLapanganExport($users), $fileName);
    }
}
