<?php

namespace App\Actions\Admin\User;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CreateUser
{
    public function handle(Request $request): User
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'nik' => $request->nik,
            'email_verified_at' => Carbon::now(),
            'address' => $request->address,
            'created' => $request->address,
            'indonesia_province_id' => $request->indonesia_province_id,
            'indonesia_city_id' => $request->indonesia_city_id,
            'indonesia_district_id' => $request->indonesia_district_id,
            'indonesia_village_id' => $request->indonesia_village_id,
            
        ]);
        $role = [$request->role ?? 'user'];
        $user->assignRole($role);
        return $user;
    }
}