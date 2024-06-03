<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class CoordinatorController extends Controller
{
      public function index()
    {
        if ($roles = Auth::user()->roles) {
            foreach ($roles as $role) {
                if ($role->name !== 'owner'&& $role->name !== 'super-admin') {
                    return abort(403);
                }
            }
        }
        if (Gate::check('owner')) {
            $coordinators = User::where('owner_id', Auth::user()->id)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'koordinator-area');
                })
                ->paginate(10);
        } else {
            $coordinators = User::whereHas('roles', function ($q) {
                $q->where('name', 'koordinator-area');
            })->paginate(10);
        }

        return view('admin.coordinator.index', compact('coordinators'));
    }

      public function create()
    {
        return view('admin.coordinator.create');
    }
  public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nik' => ['nullable', 'numeric', 'digits:16', 'unique:users'],
            'phone' => ['nullable', 'numeric', 'digits_between:8,14'],
            'address' => ['nullable', 'string'],
            'indonesia_city_id' => ['required'],
        ]);

        $request->merge([
            'password' => Hash::make($request->password),
            'indonesia_province_id' => Auth::user()->province->id,
            'owner_id' => Auth::user()->id,
        ]);

        $user = User::create($request->only(['name', 'email', 'owner_id', 'password', 'nik', 'phone', 'address', 'indonesia_province_id', 'indonesia_city_id']));

        $role = Role::where('name', 'koordinator-area')->first();

        $user->assignRole($role);
        $user->update([
            'email_verified_at' => Carbon::now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Berhasil membuat kordinator area.');
    }
      public function show($id, User $coordinator)
    {
        $coordinator = User::findOrFail($id);
        $owner = User::findOrFail($coordinator->owner_id);
        
        // dd($owner);
        return view('admin.coordinator.show', compact('coordinator', 'owner'));
    }

     public function edit(User $coordinator, $id)
    {
        // dd($admin);
         $coordinator = User::findOrFail($id);
        return view('admin.coordinator.edit', compact('coordinator'));
    }

     public function update(Request $request, User $coordinator, $id)
    {
        $coordinator = User::findOrFail($id);
        $this->validate($request, [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($coordinator->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'nik' => ['nullable', 'numeric', 'digits:16', 'unique:users,nik,' . $coordinator->id],
            'phone' => ['nullable', 'numeric', 'digits_between:8,14'],
            'address' => ['nullable', 'string'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
            'indonesia_city_id' => ['required'],
        
        ]);

        $coordinator->update($request->only(['name', 'email', 'nik', 'phone', 'address', 'indonesia_city_id']));

        if ($request->hasFile('profile_image')) {
            $coordinator->profile_image = $request->file('profile_image')->store('profile_images', 'public');
            $coordinator->save();
        }

        if ($request->password) {
            $coordinator->update([
                'password' => Hash::make($request->password),
            ]);
        }
        // dd($coordinator);
        return redirect()
            ->back()
            ->with('success', 'Berhasil mengupdate kordinator.');
    }
    public function destroy($id)
    {
        $coordinator= User::find($id);
        $coordinator->forceDelete();
        return redirect()
            ->back()
            ->with('success', 'Berhasil menghapus kordinator.');
    }


}
