<?php

namespace App\Actions\Admin\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UpdateUser
{
    public function handle(Request $request, User $user): User
    {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nik' => $request->nik,
            'indonesia_province_id' => $request->indonesia_province_id,
            'indonesia_city_id' => $request->indonesia_city_id,
            'indonesia_district_id' => $request->indonesia_district_id,
            'indonesia_village_id' => $request->indonesia_village_id,
        ]);

        if ($request->password) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $role = [$request->role ?? 'user'];
        $user->syncRoles($role);

        return $user;
    }
}
