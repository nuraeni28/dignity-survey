<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Role;
use App\Models\User;
use App\Models\Supporter;
use App\Models\Customer;
use App\Models\QuestionTutorial;
use App\Models\VolunteerResponse;
use App\Models\VolunteerResponseDetail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function index()
    {
        $questions = QuestionTutorial::all();
        return view('admin.register.index', compact('questions'));
    }
 public function success()
    {
        return view('admin.register.success-register');
    }
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['required', 'numeric', 'digits_between:8,14'],
                'indonesia_city_id' => ['required'],
                'indonesia_district_id' => ['required'],
                'recomended_by' => ['required'],
            ],
            [
                'name.required' => 'Nama harus diisi',
                'email.required' => 'Email harus diisi',
                'email.unique' => 'Email telah digunakan',
                'phone.required' => 'Nomor telpon harus diisi',
                'phone.numeric' => 'No telpon harus berupa angka',
                'phone.digits_between' => 'No telpon harus memiliki 8-14 digit',
                'indonesia_city_id.required' => 'Kabupaten harus dipilih',
                'indonesia_district_id.required' => 'Kecamatan harus dipilih',
                'recomended_by.required' => 'Bergabung menjadi relawan atas harus dipilih'
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $districtId = $request->input('indonesia_district_id');
        // $disctrictId = $request->input('district_disctrict_id');
        $admin = User::where('admin_id', null)
            ->where('indonesia_district_id', $districtId)
            ->first();

        $request->merge([
            'password' => Hash::make('inside567'),
            'indonesia_province_id' => 27,
            'owner_id' => $admin->owner_id,
            'admin_id' => $admin->id,
        ]);

        $user = User::create($request->only(['name', 'email', 'owner_id', 'admin_id', 'password', 'phone', 'indonesia_province_id', 'indonesia_city_id', 'indonesia_district_id', 'recomended_by']));
        $user->register_by = 'web'; // Atur nilai register_by
        $user->status = 'Non-Aktif';
        $user->save();
        $role = Role::findByName('user');
        $user->assignRole($role);
        event(new Registered($user));

        return response()->json(['success' => true]);
    }
     public function responden()
    {
        return view('admin.register.responden');
    }
       public function storeResponden(Request $request)
    {
        $nik = $request->input('nik');
        $readyCustomer = Customer::where('nik', $nik)->first();

        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                'nik' => ['required', 'numeric', 'digits:16', 'unique:supporters'],
                'phone' => ['required', 'numeric', 'digits_between:8,14'],
                'indonesia_city_id' => ['required'],
                'indonesia_district_id' => ['required'],
                'indonesia_village_id' => ['required'],
            ],
            [
                'name.required' => 'Nama harus diisi',
                'nik.required' => 'NIK harus diisi',
                'nik.unique' => 'NIK telah digunakan',
                'email.required' => 'Email harus diisi',
                'phone.required' => 'Nomor telepon harus diisi',
                'phone.numeric' => 'No telepon harus berupa angka',
                'phone.digits_between' => 'No telepon harus memiliki 8-14 digit',
                'indonesia_city_id.required' => 'Kabupaten harus dipilih',
                'indonesia_district_id.required' => 'Kecamatan harus dipilih',
                'indonesia_village_id.required' => 'Kecamatan harus dipilih',
            ]
        );

        if ($validator->fails()) {
            if ($readyCustomer) {
                $validator->errors()->add('nik', 'NIK telah digunakan');
            }
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
          if ($readyCustomer) {
            // Handle the case where NIK already exists (add your specific logic here)
            $validator->errors()->add('nik', 'NIK telah digunakan');
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
      
        $request->merge([
            'indonesia_province_id' => 27,
        ]);

        $supporter = Supporter::create($request->only(['name', 'nik', 'phone', 'indonesia_province_id', 'indonesia_city_id', 'indonesia_district_id', 'indonesia_village_id']));
        $supporter->save();

        return response()->json(['success' => true, 'data' => $readyCustomer]);
    }
    
}
