<?php

namespace App\Imports;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if($row[0] == null) return null;
          $row[4] = ltrim($row[4], "'");
           $currentUser = User::where('email', $row[1])->first();
           
        if($currentUser){
             throw new \Exception('Email Sudah Terdaftar');
        }

        // Memeriksa apakah 'nik' setelah dibersihkan dimulai dengan 0
        if (substr($row[4], 0, 1) === '0') {
            // "nik" starts with 0, handle this case accordingly
            // Misalnya, lemparkan pengecualian dengan pesan kesalahan
            throw new \Exception('NIK Tidak Boleh Dimulai Dengan Angka 0');
        }
        // Memeriksa apakah 'nik' memiliki panjang yang sesuai (contoh: 16 digit)
        if (strlen($row[4]) !== 16) {
            throw new \Exception('NIK Harus Memiliki Panjang 16 Digit');
        }
        // Memeriksa apakah 'email' memiliki format yang benar
        if (!filter_var($row[1], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email Harus Memiliki Format yang Benar');
        }
        if (strlen($row[2]) < 8) {
            throw new \Exception('Password Minimal 8 Karakter');
        }

        
        $user = User::withTrashed()->where('email', $row[1])->first();
          

          $adminId = User::whereNotNull('owner_id')->whereNull('admin_id')->whereNull('indonesia_district_id')->where('indonesia_city_id', Auth::user()->indonesia_city_id)->first();
        if($user) {
            if($user->deleted_at != null) {
                $user->restore();
                $nikValue = substr($row[4], 0, 1) === "'" ? substr($row[4], 1) : $row[4];
                $user->update([
                    'name'     => $row[0],
                    'email'    => $row[1],
                    'password' => Hash::make($row[2]),
                    'phone' => $row[3],
                    'nik' =>  $nikValue,
                    'owner_id' => Auth::user()->owner_id,
                    'admin_id' => $adminId->id,
                    'indonesia_province_id' => Auth::user()->indonesia_province_id,
                    'indonesia_city_id' => Auth::user()->indonesia_city_id,
                    'indonesia_district_id' => Auth::user()->indonesia_district_id,
                    'indonesia_village_id' => Auth::user()->indonesia_village_i,
                    'status' => 'Aktif'
                ]);
                return $user;
            }
            return null;
        }
        $nikValue = substr($row[4], 0, 1) === "'" ? substr($row[4], 1) : $row[4];
        $user =  User::create([
            'name'     => $row[0],
            'email'    => $row[1],
            'password' => Hash::make($row[2]),
            'phone' => $row[3],
            'nik' =>  $nikValue,
            'owner_id' => Auth::user()->owner_id,
             'admin_id' => $adminId->id,
            'indonesia_province_id' => Auth::user()->indonesia_province_id,
            'indonesia_city_id' => Auth::user()->indonesia_city_id,
            'indonesia_district_id' => Auth::user()->indonesia_district_id,
            'indonesia_village_id' => Auth::user()->indonesia_village_id,
            'status' => 'Aktif'
        ]);

        $role = Role::where('name', 'user')->first();

        $user->assignRole($role);
        $user->update([
            'email_verified_at' => Carbon::now(),
        ]);

        return $user;
    }
}
