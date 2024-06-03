<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:owner');
    // }

    public function index(Request $request)
    {
        if ($roles = Auth::user()->roles) {
            foreach ($roles as $role) {
                if ($role->name !== 'owner' && $role->name !== 'super-admin') {
                    return abort(403);
                }
            }
        }
        if (Gate::check('owner')) {
            $admins = User::where('owner_id', Auth::user()->id)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'admin');
                })
                ->paginate(10);
        } else {
            $admins = User::whereHas('roles', function ($q) {
                $q->where('name', 'admin');
            })->paginate(10);
        }
         $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        });

        $search = $request->input('search');
        if (!empty($search)) {
            $query->where(function ($subquery) use ($search) {
                $subquery
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%');
            });
            $admins = $query->paginate(10);
        }
        if (Auth::user()->hasRole('owner')) {
            $admins = $query
                ->where(function ($subquery) {
                    $subquery->where('owner_id', Auth::user()->id);
                })
                ->paginate(10);
        } else {
            $admins = $query->paginate(10);
        }
           $admins->appends(['search' => $search]);


        return view('admin.admin.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admin.create');
    }

    public function show(User $admin)
    {
        $owner = User::findOrFail($admin->owner_id);
        return view('admin.admin.show', compact('admin', 'owner'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'gender' => ['nullable', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nik' => ['nullable', 'numeric', 'digits:16', 'unique:users'],
            'phone' => ['nullable', 'numeric', 'digits_between:8,14'],
            'address' => ['nullable', 'string'],
            'indonesia_city_id' => ['required'],
            'indonesia_district_id' => ['required'],
        ]);

        $request->merge([
            'password' => Hash::make($request->password),
            'indonesia_province_id' => Auth::user()->province->id,
            'owner_id' => Auth::user()->id,
        ]);

        $user = User::create($request->only(['name', 'email', 'owner_id', 'password', 'nik', 'phone', 'address', 'indonesia_province_id', 'indonesia_city_id', 'indonesia_district_id', 'gender']));

        $role = Role::where('name', 'admin')->first();

        $user->assignRole($role);
        $user->update([
            'email_verified_at' => Carbon::now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Berhasil membuat admin.');
    }

   public function edit(User $admin, Request $request)
    {
        $page = $request->input('page');
        return view('admin.admin.edit', compact('admin', 'page'));
    }

    public function update(Request $request, User $admin)
    {
        $this->validate($request, [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'nik' => ['nullable', 'numeric', 'digits:16', 'unique:users,nik,' . $admin->id],
            'phone' => ['nullable', 'numeric', 'digits_between:8,14'],
            'address' => ['nullable', 'string'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
            'indonesia_city_id' => ['required'],
            'indonesia_district_id' => ['required'],
            'gender' => ['nullable', 'string'],
        ]);
         $page = $request->input('page');

        $admin->update($request->only(['name', 'email', 'nik', 'phone', 'address', 'indonesia_city_id', 'indonesia_district_id', 'gender']));

        if ($request->hasFile('profile_image')) {
            $admin->profile_image = $request->file('profile_image')->store('profile_images', 'public');
            $admin->save();
        }

        if ($request->password) {
            $admin->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()
            ->route('admin.index', ['page' => $page])
            ->with('success', 'Berhasil mengupdate admin.');
    }

   public function destroy(User $admin,  Request $request)
    {
         $page = $request->input('page');
        $admin->delete();
        return redirect()
            ->route('admin.index', ['page' => $page])
            ->with('success', 'Berhasil menghapus admin.');
    }
}
