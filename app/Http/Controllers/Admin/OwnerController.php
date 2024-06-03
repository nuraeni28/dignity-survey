<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class OwnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:super-admin,user list', ['only' => ['index', 'show']]);
        $this->middleware('can:super-admin,user create', ['only' => ['create', 'store']]);
        $this->middleware('can:super-admin,user edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:super-admin,user delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $owners = User::whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'owner');
            }
        )->paginate(10);
        return view('admin.owner.index', compact('owners'));
    }

    public function create()
    {
        return view('admin.owner.create');
    }

    public function show(User $owner)
    {
        return view('admin.owner.show', compact('owner'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nik'   => ['nullable', 'numeric', 'digits:16', 'unique:users'],
            'phone' => ['nullable', 'numeric', 'digits_between:8,14'],
            'address' => ['nullable', 'string'],
            'indonesia_province_id' => ['required'],
        ]);

        $request->merge([
            'password' => Hash::make($request->password)
        ]);

        $user = User::create($request->only(['name', 'email', 'password', 'nik', 'phone', 'address', 'indonesia_province_id']));

        $role = Role::where('name', 'owner')->first();

        $user->assignRole($role);
        $user->update([
            'email_verified_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Berhasil membuat owner.');
    }

    public function edit(User $owner)
    {
        return view('admin.owner.edit', compact('owner'));
    }

    public function update(Request $request, User $owner)
    {
        $this->validate($request, [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $owner->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'nik'   => ['nullable', 'numeric', 'digits:16', 'unique:users,nik,' . $owner->id],
            'phone' => ['nullable', 'numeric', 'digits_between:8,14'],
            'address' => ['nullable', 'string'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
            'indonesia_province_id' => ['required'],
        ]);

        $owner->update($request->only(['name', 'email', 'nik', 'phone', 'address', 'indonesia_province_id']));

        if ($request->hasFile('profile_image')) {
            $owner->profile_image = $request->file('profile_image')->store('profile_images', 'public');
            $owner->save();
        }

        if ($request->password) {
            $owner->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->back()->with('success', 'Berhasil mengupdate owner.');
    }

    public function destroy(User $owner)
    {
        $owner->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus pengguna.');
    }
}
