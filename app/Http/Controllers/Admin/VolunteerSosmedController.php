<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DependentDropdownController;
use App\Imports\UsersImport;
use App\Exports\VolunteerSosmedExport;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class VolunteerSosmedController extends Controller
{
    public function getCities($provinceId)
    {
        $cities = new DependentDropdownController();
        $cities = $cities->citiesData($provinceId);
        // $cities = City::where('province_id', $provinceId)->get();
        return response()->json($cities);
    }

    public function index(Request $request)
    {
        if ($roles = Auth::user()->roles) {
            foreach ($roles as $role) {
                if ($role->name !== 'owner' && $role->name !== 'admin' && $role->name !== 'super-admin') {
                    return abort(403);
                }
            }
        }
        if ($role->name !== 'super-admin') {
            $volunteers = User::where('admin_id', Auth::user()->id)
                ->whereNotNull('recomended_by')
                ->orWhere('owner_id', Auth::user()->id)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'user');
                })
                ->paginate(10);
        } else {
            $volunteers = User::whereHas('roles', function ($q) {
                $q->where('name', 'user');
            })
                ->whereNotNull('recomended_by')
                ->paginate(10);
        }
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'user');
        })->whereNotNull('recomended_by');

        // Handle the search functionality
        $search = $request->input('search');
        $sumber = $request->input('recomended_id');
        if (!empty($search)) {
            $query->where(function ($subquery) use ($search) {
                $subquery
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }
        if (!empty($sumber)) {
            $query->where('recomended_by', $sumber);
        }

        // If the user is not a super-admin, filter by admin_id or owner_id
        if (Auth::user()->hasRole('super-admin')) {
            $volunteers = $query->paginate(10);
        } else {
            $volunteers = $query
                ->where(function ($subquery) {
                    $subquery->where('admin_id', Auth::user()->id)->orWhere('owner_id', Auth::user()->id);
                })
                ->whereNotNull('recomended_by')
                ->paginate(10);
        }
        $volunteers->appends(['search' => $search, 'recomended_id' => $sumber]);

        return view('admin.volunteer-sosmed.index', compact('volunteers'));
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
        return view('admin.volunteer-sosmed.show', compact('admin', 'owner', 'volunteer'));
    }

    public function edit(User $volunteer, $id, Request $request)
    {
        $page = $request->input('page');
        $volunteer = User::findOrFail($id);
        return view('admin.volunteer-sosmed.edit', compact('volunteer', 'page'));
    }

    public function update(Request $request, User $volunteer, $id)
    {
        $volunteer = User::findOrFail($id);

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

        $volunteer->update($request->only(['name', 'email', 'nik', 'phone', 'address', 'indonesia_village_id', 'tps']));

        if ($request->hasFile('profile_image')) {
            $volunteer->profile_image = $request->file('profile_image')->store('profile_images', 'public');
            $volunteer->save();
        }

        if ($request->password) {
            $volunteer->update([
                'password' => Hash::make($request->password),
            ]);
        }
        $page = $request->input('page');

        return redirect()
            ->route('relawan-sosmed.index', ['page' => $page])
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
            ->route('relawan-sosmed.index', ['page' => $page])
            ->with('success', 'Berhasil menghapus relawan.');
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
            ->route('relawan-sosmed.index', ['page' => $page])
            ->with('success', 'Status pengguna berhasil diperbarui.');
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
    public function getStatusKabupaten(Request $request)
    {
        // dd($userId);
        // Gantilah ini dengan kode yang sesuai untuk mengambil status pengguna dengan ID $userId
        $relawan = User::where('indonesia_city_id', $request->indonesia_cities_id)
            ->whereNotNull('recomended_by')
            ->get();
        foreach ($relawan as $relawan) {
            $relawan->status = $request->input('status');
            $relawan->save();
        }
        // dd($request->status);

        return redirect()
            ->route('relawan-sosmed.index')
            ->with('success', 'Status pengguna berhasil diperbarui.');
    }
        public function export(Request $request)
    {
        $users = User::whereNotNull('recomended_by')->get();
        // dd($users);
   
      
        $fileName = 'Relawan Sosmed - ' . date('d-m-Y') . '.xlsx';
        return Excel::Download(new VolunteerSosmedExport($users), $fileName);
    
}
}
