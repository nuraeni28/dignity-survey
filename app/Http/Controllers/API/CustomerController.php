<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\ListCustomerResource;
use App\Http\Resources\InterviewScheduleResource;
use App\Http\Resources\EvidenceComitmentResource;
use App\Models\Customer;
use App\Models\EvidenceComitment;
use App\Models\InterviewSchedule;
use App\Models\Interview;
use App\Models\InterviewData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class CustomerController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::user()->admin_id;
        $surveyor_id = Auth::user()->id;

        // dd($id, $surveyor_id);

        $customers = Customer::where('admin_id', $id)->where('surveyor_id', $surveyor_id)->get();
        return $this->sendResponse(CustomerResource::collection($customers), 'Berhasil mengambil data responden');
    }
    //   public function customer()
    // {
    //     $customers = Customer::with('province', 'city', 'district', 'village')->get();
    //     return $this->sendResponse(CustomerResource::collection($customers), 'Berhasil mengambil data responden');
    // }
         public function customer()
    {
       $customers = Customer::with('province', 'city', 'district', 'village', 'schedules.interview')->get(); 
        return $this->sendResponse(CustomerResource::collection($customers), 'Berhasil mengambil data responden');
    }
           public function customerSecond(Request $request)
    {
        $query = $request->get('query');

        // Gunakan Eloquent Query Builder untuk menambahkan kondisi pencarian
        $customers = Customer::with('province', 'city', 'district', 'village', 'schedules.interview')
            ->where('nik', 'like', "%$query%")
            // Tambahkan kondisi pencarian sesuai dengan kolom yang Anda inginkan
            ->get();
        return $this->sendResponse(CustomerResource::collection($customers), 'Berhasil mengambil data responden');
    }
    

    /**
     * Display a interview schedule.
     *  
     * @return \Illuminate\Http\Response
     */
    public function schedule(Request $request)
    {
        $user = auth('sanctum')->user();
        $schedule = InterviewSchedule::where('period_id', $request->period_id)->where('user_id', $user->id)->get();
        return $this->sendResponse(InterviewScheduleResource::collection($schedule), 'Berhasil mengambil jadwal');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $userId = Auth::user()->id;

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'unique:customers'],
            'phone' => ['nullable', 'string', 'unique:customers'],
            'address' => ['required', 'string'],
            'nik' => ['nullable', 'numeric', 'digits:16', 'unique:customers'],
             'nik' => ['nullable', 'numeric', 'digits:16', 'unique:customers'],
            //  'no_kk' => ['required', 'numeric', 'digits:16', function ($attribute, $value, $fail) use ($userId) {
            // // Check if the 'no_kk' is already associated with a different surveyor
            // $existingNoKKOtherSurveyor = Customer::where('no_kk', $value)
            //     ->where('surveyor_id', '!=', $userId)
            //     ->first();

            // if ($existingNoKKOtherSurveyor) {
            //     return $fail('NO KK sudah terdaftar untuk surveyor lain');
            // }
        // }],
            'phone' => ['nullable', 'numeric', 'digits_between:8,14'],
            'indonesia_province_id' => ['required'],
            'indonesia_city_id' => ['required'],
            'indonesia_district_id' => ['required'],
            'indonesia_village_id' => ['required'],
            'age' => ['nullable', 'numeric'],
            'religion'  => ['nullable', 'string', 'max:255'],
            'education'  => ['nullable', 'string', 'max:255'],
            'job'  => ['nullable', 'string', 'max:255'],
            'family_member'  => ['nullable', 'numeric'],
            'family_election'  => ['nullable', 'numeric'],
            'marrital_status'  => ['nullable', 'string', 'max:255'],
            'monthly_income'  => ['nullable', 'string', 'max:255'],
            'status'  => ['nullable', 'string',],
            'dob'  => ['nullable', 'date',],
            'tps'  => ['nullable', 'numeric',],
        ]);

        if ($validator->fails()) {
            // if email already exists
            if ($validator->errors()->has('email')) {
                return $this->sendError('Gagal membuat responden email sudah terdaftar', $validator->errors());
            } else if ($validator->errors()->has('phone')) {
                return $this->sendError('Gagal membuat responden nomor telepon sudah terdaftar', $validator->errors());
            } else if ($validator->errors()->has('nik')) {
                return $this->sendError('Gagal membuat responden NIK sudah terdaftar', $validator->errors());
            } 
            else {
                return $this->sendError('Gagal membuat responden', $validator->errors());
            }
        }

        $request->merge([
            'admin_id'  => Auth::user()->admin_id,
            'owner_id'  => Auth::user()->owner_id,
            'surveyor_id'  => Auth::user()->id,
        ]);

        $customer = Customer::create($request->all());
        return $this->sendResponse(new CustomerResource($customer), 'Berhasil membuat responden');
    }
     public function storeNew(Request $request)
    {
        $input = $request->all();
        $userId = Auth::user()->id;

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'unique:customers'],
            'phone' => ['nullable', 'string', 'unique:customers'],
            'address' => ['required', 'string'],
            'nik' => ['nullable', 'numeric', 'digits:16', 'unique:customers'],
             'nik' => ['nullable', 'numeric', 'digits:16', 'unique:customers'],
             'no_kk' => ['required', 'numeric', 'digits:16', function ($attribute, $value, $fail) use ($userId) {
            // Check if the 'no_kk' is already associated with a different surveyor
            $existingNoKKOtherSurveyor = Customer::where('no_kk', $value)
                ->where('surveyor_id', '!=', $userId)
                ->first();

            if ($existingNoKKOtherSurveyor) {
                return $fail('NO KK sudah terdaftar untuk surveyor lain');
            }
        }],
            'phone' => ['nullable', 'numeric', 'digits_between:8,14'],
            'indonesia_province_id' => ['required'],
            'indonesia_city_id' => ['required'],
            'indonesia_district_id' => ['required'],
            'indonesia_village_id' => ['required'],
            'age' => ['nullable', 'numeric'],
            'religion'  => ['nullable', 'string', 'max:255'],
            'education'  => ['nullable', 'string', 'max:255'],
            'job'  => ['nullable', 'string', 'max:255'],
            'family_member'  => ['nullable', 'numeric'],
            'family_election'  => ['nullable', 'numeric'],
            'marrital_status'  => ['nullable', 'string', 'max:255'],
            'monthly_income'  => ['nullable', 'string', 'max:255'],
            'status'  => ['nullable', 'string',],
            'dob'  => ['nullable', 'date',],
            'tps'  => ['nullable', 'numeric',],
        ]);

        if ($validator->fails()) {
            // if email already exists
            if ($validator->errors()->has('email')) {
                return $this->sendError('Gagal membuat responden email sudah terdaftar', $validator->errors());
            } else if ($validator->errors()->has('phone')) {
                return $this->sendError('Gagal membuat responden nomor telepon sudah terdaftar', $validator->errors());
            } else if ($validator->errors()->has('nik')) {
                return $this->sendError('Gagal membuat responden NIK sudah terdaftar', $validator->errors());
            }  else if ($validator->errors()->has('no_kk')) {
                return $this->sendError('Gagal update responden NO KK sudah terdaftar', $validator->errors());
            }
            else {
                return $this->sendError('Gagal membuat responden', $validator->errors());
            }
        }

        $request->merge([
            'admin_id'  => Auth::user()->admin_id,
            'owner_id'  => Auth::user()->owner_id,
            'surveyor_id'  => Auth::user()->id,
        ]);

        $customer = Customer::create($request->all());
        return $this->sendResponse(new CustomerResource($customer), 'Berhasil membuat responden');
    }

public function storeSecond(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'unique:customers'],
            'phone' => ['nullable', 'string', 'unique:customers'],
            'address' => ['required', 'string'],
            'nik' => ['nullable', 'numeric', 'digits:16', 'unique:customers'],
            'phone' => ['nullable', 'numeric', 'digits_between:8,14'],
            'indonesia_province_id' => ['required'],
            'indonesia_city_id' => ['required'],
            'indonesia_district_id' => ['required'],
            'indonesia_village_id' => ['required'],
            'age' => ['nullable', 'numeric'],
            'religion'  => ['nullable', 'string', 'max:255'],
            'education'  => ['nullable', 'string', 'max:255'],
            'job'  => ['nullable', 'string', 'max:255'],
            'family_member'  => ['nullable', 'numeric'],
            'family_election'  => ['nullable', 'numeric'],
            'marrital_status'  => ['nullable', 'string', 'max:255'],
            'monthly_income'  => ['nullable', 'string', 'max:255'],
            'status'  => ['nullable', 'string',],
            'dob'  => ['nullable', 'date',],
            'tps'  => ['nullable', 'numeric',],
        ]);

        if ($validator->fails()) {
            // if email already exists
            if ($validator->errors()->has('email')) {
                return $this->sendError('Gagal membuat responden email sudah terdaftar', $validator->errors());
            } else if ($validator->errors()->has('phone')) {
                return $this->sendError('Gagal membuat responden nomor telepon sudah terdaftar', $validator->errors());
            } else if ($validator->errors()->has('nik')) {
                return $this->sendError('Gagal membuat responden NIK sudah terdaftar', $validator->errors());
            } else {
                return $this->sendError('Gagal membuat responden', $validator->errors());
            }
        }

        $request->merge([
            'admin_id'  => Auth::user()->admin_id,
            'owner_id'  => Auth::user()->owner_id,
            'surveyor_id'  => Auth::user()->id,
        ]);

        $customer = Customer::create($request->all());
        return $this->sendResponse(new CustomerResource($customer), 'Berhasil membuat responden');
    }
    public function updateCustomer(Request $request)
    {
        
        $input = $request->all();
        $customerId = $request->input('id');
        $noKK = $request->input('no_kk');
        $customer = Customer::find($customerId);
        $userId = Auth::user()->id;
       

        $validator = Validator::make($input, [
            'id' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => [  'sometimes',
                function ($attribute, $value, $fail) use ($customer) {
                    // If the phone number is not empty, check for uniqueness
                    if (!empty($value)) {
                        $isUnique = Customer::where(function ($query) use ($customer, $value) {
                            // Include customers with the same 'no_kk'

                            // Exclude the current customer by ID
                            $query->where('no_kk', '!=', $customer->no_kk);
                        })
                            ->whereNotNull('phone')
                            ->where('phone', $value)
                            ->doesntExist();

                        if (!$isUnique) {
                            return $fail('Nomor HP sudah digunakan oleh responden lain');
                        }
                    }
                },
                function ($attribute, $value, $fail) use ($customer, $noKK) {
                    // Check if there's at least one other customer with the same 'no_kk' and a phone number
                    $otherCustomers = Customer::where(function ($query) use ($customer, $noKK) {
                        // Include customers with the same 'no_kk'
                        $query->where('no_kk', $noKK);

                        // Exclude the current customer by ID
                        $query->where('id', '!=', $customer->id);
                    })
                        ->whereNotNull('phone')
                        ->exists();

                    if (empty($value)) {
                        // If the phone is empty, ensure there's no other customer with the same 'no_kk' and a phone number
                        if (!$otherCustomers) {
                            return $fail('NO HP Wajib Diisi');
                        }
                    } elseif ($otherCustomers) {
                        // If the phone is not empty, ensure there's at least one other customer with the same 'no_kk'
                        return;
                    }
                },],
            'address' => ['required', 'string'],
             'nik' => ['sometimes', 'numeric', 'digits:16', 'unique:customers,nik,' . $customer->id],
            'no_kk' => ['required', 'numeric', 'digits:16', function ($attribute, $value, $fail) use ($userId, $customer) {
                    // Custom validation to check if no_kk is unique for the current user
                    $existingNoKK = Customer::where('no_kk', $value)
                        ->where('surveyor_id', $userId)
                        ->where('id', '!=', $customer->id)
                        ->first();

                    if ($existingNoKK) {
                        // Allow the same no_kk for the same surveyor
                        return;
                    }
                    // Check if the no_kk is already associated with a different surveyor
                    $existingNoKKOtherSurveyor = Customer::where('no_kk', $value)
                        ->where('surveyor_id', '!=', $userId)
                        ->first();

                    if ($existingNoKKOtherSurveyor) {
                       return $fail('NO KK sudah terdaftar untuk surveyor lain');
                    }
                },],
            'indonesia_province_id' => ['sometimes'],
            'indonesia_city_id' => ['sometimes'],
            'indonesia_district_id' => ['sometimes'],
            'indonesia_village_id' => ['sometimes'],
            'age' => ['nullable', 'numeric'],
            'religion' => ['nullable', 'string', 'max:255'],
            'education' => ['nullable', 'string', 'max:255'],
            'job' => ['nullable', 'string', 'max:255'],
            'family_member' => ['nullable', 'numeric'],
            'family_election' => ['nullable', 'numeric'],
            'marrital_status' => ['nullable', 'string', 'max:255'],
            'monthly_income' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
            'dob' => ['nullable', 'date'],
            'tps' => ['nullable', 'numeric'],
        ]);


        if ($validator->fails()) {
             $errorMessages = $validator->errors();
            // if email already exists
           if ($errorMessages->has('phone')) {
                return $this->sendError($errorMessages->first('phone'), $errorMessages);
            }
            else if ($validator->errors()->has('nik')) {
                return $this->sendError('Gagal update responden NIK sudah terdaftar', $validator->errors());
            } 
            else if ($validator->errors()->has('no_kk')) {
                return $this->sendError('Gagal update responden NO KK sudah terdaftar', $validator->errors());
            }
            else {
                return $this->sendError('Gagal update responden', $validator->errors());
            }
        }
        // dd($customer->surveyor_id);
       
         $customer->update($request->all());

    
        return $this->sendResponse(new ListCustomerResource($customer), 'Berhasil update responden');
    
}
 public function checkPhoneCustomer(Request $request)
    {
        
        $input = $request->all();
        $customerId = $request->input('id');
        $noKK = $request->input('no_kk');
        $customer = Customer::find($customerId);
        $userId = Auth::user()->id;
       

        $validator = Validator::make($input, [
            'id' => ['required'],
        'no_kk' => ['required', 'numeric', 'digits:16', function ($attribute, $value, $fail) use ($userId, $customer) {
                    // Custom validation to check if no_kk is unique for the current user
                    $existingNoKK = Customer::where('no_kk', $value)
                        ->where('surveyor_id', $userId)
                        ->where('id', '!=', $customer->id)
                        ->first();

                    if ($existingNoKK) {
                        // Allow the same no_kk for the same surveyor
                        return;
                    }
                    // Check if the no_kk is already associated with a different surveyor
                    $existingNoKKOtherSurveyor = Customer::where('no_kk', $value)
                        ->where('surveyor_id', '!=', $userId)
                        ->first();

                    if ($existingNoKKOtherSurveyor) {
                       return $fail('NO KK sudah terdaftar untuk surveyor lain');
                    }
                },],
            'phone' => [  'sometimes',
                function ($attribute, $value, $fail) use ($customer) {
                    // If the phone number is not empty, check for uniqueness
                    if (!empty($value)) {
                        $isUnique = Customer::where(function ($query) use ($customer, $value) {
                            // Include customers with the same 'no_kk'

                            // Exclude the current customer by ID
                            $query->where('no_kk', '!=', $customer->no_kk);
                        })
                            ->whereNotNull('phone')
                            ->where('phone', $value)
                            ->doesntExist();

                        if (!$isUnique) {
                            return $fail('Nomor HP sudah digunakan oleh responden lain');
                        }
                    }
                },
                function ($attribute, $value, $fail) use ($customer, $noKK) {
                    // Check if there's at least one other customer with the same 'no_kk' and a phone number
                    $otherCustomers = Customer::where(function ($query) use ($customer, $noKK) {
                        // Include customers with the same 'no_kk'
                        $query->where('no_kk', $noKK);

                        // Exclude the current customer by ID
                        $query->where('id', '!=', $customer->id);
                    })
                        ->whereNotNull('phone')
                        ->exists();

                    if (empty($value)) {
                        // If the phone is empty, ensure there's no other customer with the same 'no_kk' and a phone number
                        if (!$otherCustomers) {
                            return $fail('NO HP Wajib Diisi');
                        }
                    } elseif ($otherCustomers) {
                        // If the phone is not empty, ensure there's at least one other customer with the same 'no_kk'
                        return;
                    }
                },],
        ]);


        if ($validator->fails()) {
             $errorMessages = $validator->errors();
            
           if ($errorMessages->has('phone')) {
                return $this->sendError($errorMessages->first('phone'), $errorMessages);
            }
             else if ($validator->errors()->has('no_kk')) {
                return $this->sendError('NO KK sudah terdaftar', $validator->errors());
            }
            else {
                return $this->sendError('Gagal update responden', $validator->errors());
            }
        }

    
        return $this->sendResponse(new ListCustomerResource($customer), 'Berhasil update responden');
    
}
  public function updateOfflineCustomer(Request $request)
    {
        
        $input = $request->all();
        $customerId = $request->input('id');
        $customer = Customer::find($customerId);
        $userId = Auth::user()->id;
       

        $validator = Validator::make($input, [
            'id' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email'],
            'phone' => ['nullable'],
            'address' => ['required', 'string'],
             'nik' => ['sometimes', 'numeric', 'digits:16', 'unique:customers,nik,' . $customer->id],
            'no_kk' => ['required', 'numeric', 'digits:16', function ($attribute, $value, $fail) use ($userId, $customer) {
                    // Custom validation to check if no_kk is unique for the current user
                    $existingNoKK = Customer::where('no_kk', $value)
                        ->where('surveyor_id', $userId)
                        ->where('id', '!=', $customer->id)
                        ->first();

                    if ($existingNoKK) {
                        // Allow the same no_kk for the same surveyor
                        return;
                    }
                    // Check if the no_kk is already associated with a different surveyor
                    $existingNoKKOtherSurveyor = Customer::where('no_kk', $value)
                        ->where('surveyor_id', '!=', $userId)
                        ->first();

                    if ($existingNoKKOtherSurveyor) {
                       return $fail('NO KK sudah terdaftar untuk surveyor lain');
                    }
                },],
            'indonesia_province_id' => ['sometimes'],
            'indonesia_city_id' => ['sometimes'],
            'indonesia_district_id' => ['sometimes'],
            'indonesia_village_id' => ['sometimes'],
            'age' => ['nullable', 'numeric'],
            'religion' => ['nullable', 'string', 'max:255'],
            'education' => ['nullable', 'string', 'max:255'],
            'job' => ['nullable', 'string', 'max:255'],
            'family_member' => ['nullable', 'numeric'],
            'family_election' => ['nullable', 'numeric'],
            'marrital_status' => ['nullable', 'string', 'max:255'],
            'monthly_income' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
            'dob' => ['nullable', 'date'],
            'tps' => ['nullable', 'numeric'],
        ]);


        if ($validator->fails()) {
             $errorMessages = $validator->errors();
            // if email already exists
            if ($validator->errors()->has('email')) {
                return $this->sendError('Gagal update responden email sudah terdaftar', $validator->errors());
            }  
            else if ($validator->errors()->has('nik')) {
                return $this->sendError('Gagal update responden NIK sudah terdaftar', $validator->errors());
            } 
            else if ($validator->errors()->has('no_kk')) {
                return $this->sendError('Gagal update responden NO KK sudah terdaftar', $validator->errors());
            }
            else {
                return $this->sendError('Gagal update responden', $validator->errors());
            }
        }
        // dd($customer->surveyor_id);
       
         $customer->update($request->all());

    
        return $this->sendResponse(new ListCustomerResource($customer), 'Berhasil update responden');
    
}
    public function storeThird(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'unique:customers'],
            'phone' => ['nullable', 'string', 'unique:customers'],
            'address' => ['required', 'string'],
            'nik' => ['nullable', 'numeric', 'digits:16', 'unique:customers'],
             'no_kk' => ['required', 'numeric', 'digits:16', function ($attribute, $value, $fail) use ($userId, $customer) {
                    // Custom validation to check if no_kk is unique for the current user
                    $existingNoKK = Customer::where('no_kk', $value)
                        ->where('surveyor_id', $userId)
                        ->where('id', '!=', $customer->id)
                        ->first();

                    if ($existingNoKK) {
                        // Allow the same no_kk for the same surveyor
                        return;
                    }
                    // Check if the no_kk is already associated with a different surveyor
                    $existingNoKKOtherSurveyor = Customer::where('no_kk', $value)
                        ->where('surveyor_id', '!=', $userId)
                        ->first();

                    if ($existingNoKKOtherSurveyor) {
                       return $fail('NO KK sudah terdaftar untuk surveyor lain');
                    }
                },],
            'phone' => ['nullable', 'numeric', 'digits_between:8,14'],
            'indonesia_province_id' => ['required'],
            'indonesia_city_id' => ['required'],
            'indonesia_district_id' => ['required'],
            'indonesia_village_id' => ['required'],
            'age' => ['nullable', 'numeric'],
            'religion'  => ['nullable', 'string', 'max:255'],
            'education'  => ['nullable', 'string', 'max:255'],
            'job'  => ['nullable', 'string', 'max:255'],
            'family_member'  => ['nullable', 'numeric'],
            'family_election'  => ['nullable', 'numeric'],
            'marrital_status'  => ['nullable', 'string', 'max:255'],
            'monthly_income'  => ['nullable', 'string', 'max:255'],
            'status'  => ['nullable', 'string',],
            'dob'  => ['nullable', 'date',],
            'tps'  => ['nullable', 'numeric',],
        ]);

        if ($validator->fails()) {
            // if email already exists
            if ($validator->errors()->has('email')) {
                return $this->sendError('Gagal membuat responden email sudah terdaftar', $validator->errors());
            } else if ($validator->errors()->has('phone')) {
                return $this->sendError('Gagal membuat responden nomor telepon sudah terdaftar', $validator->errors());
            } else if ($validator->errors()->has('nik')) {
                return $this->sendError('Gagal membuat responden NIK sudah terdaftar', $validator->errors());
            }     else if ($validator->errors()->has('no_kk')) {
                return $this->sendError('Gagal update responden NO KK sudah terdaftar', $validator->errors());
            }
            else {
                return $this->sendError('Gagal membuat responden', $validator->errors());
            }
        }

        $request->merge([
            'admin_id'  => Auth::user()->admin_id,
            'owner_id'  => Auth::user()->owner_id,
            'surveyor_id'  => Auth::user()->id,
        ]);

        $customer = Customer::create($request->all());
        return $this->sendResponse(new CustomerResource($customer), 'Berhasil membuat responden');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createSchedule(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'customer_id' => ['required'],
            'period_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Gagal membuat jadwal', $validator->errors());
        }

        $user = auth('sanctum')->user();

        $currentSchedule = InterviewSchedule::where('period_id', $request->period_id)
            ->where('user_id', $user->id)
            ->where('customer_id', $request->customer_id)
            ->first();
        if ($currentSchedule) {
            return $this->sendResponse(new InterviewScheduleResource($currentSchedule), 'Berhasil mengambil jadwal');
        } else {
            if ($request->continue == 1) {
                $data = [
                    'period_id' => $request->period_id,
                    'user_id' => $user->id,
                    'customer_id' => $request->customer_id,
                ];
             if ($user->recomended_by !== null) {
                $data['type'] = 'sosmed';
            }

                $schedule = InterviewSchedule::create($data);
                return $this->sendResponse(new InterviewScheduleResource($schedule), 'Berhasil membuat jadwal');
            } else {
                return $this->sendError('Anda belum dijadwalkan interview responden ini', null);
            }
        }
    }
      public function listCustomer(Request $request)
    {
        $idSurveyor = $request->get('id_surveyor');

        // dd($id, $surveyor_id);

       $customers = Customer::with(['schedules' => function ($query) {
            $query->whereHas('interview');
        }])
        ->where('surveyor_id', $idSurveyor)
        ->whereHas('schedules', function ($query) {
            $query->whereHas('interview');
        })
        ->orderBy('name', 'asc')
        ->get();
        return $this->sendResponse(ListCustomerResource::collection($customers), 'Berhasil mengambil data responden');
    }
   public function storeEvidenceComitment(Request $request)
{
    try {
        $input = $request->all();
        $photo = $request->file('photo');
        
        if (!$photo) {
            throw new \Exception('No photo uploaded.');
        }
        
        $photoname = date('YmdHi') . $photo->getClientOriginalName();
        $photo->move(public_path('public/image'), $photoname);

        $date = Carbon::now();
        $input['id_surveyor'] = $request->id_surveyor;
        $input['id_customer'] = $request->id_customer;
        $input['photo'] = $photoname;
        $input['location'] = $request->location;
        $input['lat'] = $request->lat;
        $input['long'] = $request->long;

        $evidence = EvidenceComitment::create($input);
        
         $customer = Customer::find($request->id_customer);
        if ($customer) {
            $customer->status_kunjungan = 'Sudah';
            $customer->save();
        }
        $interviewSchedule = $customer->schedules->first();
            
            if ($interviewSchedule) {
                // Akses Interview yang berelasi dengan InterviewSchedule jika ada
                $interview = $interviewSchedule->interview;

                if ($interview) {
                    $interview->photo = $photoname;
                    $interview->long = $request->long;
                    $interview->lat = $request->lat;
                    $interview->location = $request->location;
                    
                    $interview->save();
                }
            }

        return $this->sendResponse(new EvidenceComitmentResource($evidence), 'Berhasil membuat pemantapan data');
    } catch (\Exception $e) {
        // Handle the exception here
        return $this->sendError('Error creating evidence commitment: ' . $e->getMessage());
    }
}
  public function storeNewResponden(Request $request)
{
  $input = $request->all();

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email'],
            'phone' => ['nullable', 'string', 'unique:customers'],
            'address' => ['required', 'string'],
            'nik' => ['nullable', 'numeric', 'digits:16', 'unique:customers'],
            'phone' => ['nullable', 'numeric', 'digits_between:8,14'],
            'indonesia_province_id' => ['required'],
            'indonesia_city_id' => ['required'],
            'indonesia_district_id' => ['required'],
            'indonesia_village_id' => ['required'],
            'age' => ['nullable', 'numeric'],
            'religion'  => ['nullable', 'string', 'max:255'],
            'education'  => ['nullable', 'string', 'max:255'],
            'job'  => ['nullable', 'string', 'max:255'],
            'family_member'  => ['nullable', 'numeric'],
            'family_election'  => ['nullable', 'numeric'],
            'marrital_status'  => ['nullable', 'string', 'max:255'],
            'monthly_income'  => ['nullable', 'string', 'max:255'],
            'status'  => ['nullable', 'string',],
            'dob'  => ['nullable', 'date',],
            'tps'  => ['nullable', 'numeric',],
        ]);

      
        if ($validator->fails()) {
            // if email already exists
            if ($validator->errors()->has('email')) {
                return $this->sendError('Gagal membuat responden email sudah terdaftar', $validator->errors());
            } else if ($validator->errors()->has('phone')) {
                return $this->sendError('Gagal membuat responden nomor telepon sudah terdaftar', $validator->errors());
            } else if ($validator->errors()->has('nik')) {
                return $this->sendError('Gagal membuat responden NIK sudah terdaftar', $validator->errors());
            } else {
                return $this->sendError('Gagal membuat responden', $validator->errors());
            }
        }

        $request->merge([
            'admin_id'  => Auth::user()->admin_id,
            'owner_id'  => Auth::user()->owner_id,
            'surveyor_id'  => Auth::user()->id,
            'type' => 'pemantapan'
        ]);

        $customer = Customer::create($request->all());
        return $this->sendResponse(new CustomerResource($customer), 'Berhasil membuat responden'); 
}
     public function getAdditionalDataUser(Request $request)
    {
        $userId = $request->input('user_id');

        $customers = Customer::where('surveyor_id', $userId)->where('type', 'pemantapan')
            ->get();
        // $query = InterviewSchedule::whereHas('user', function ($query) use ($userId) {
        //     $query->where('id', $userId);
        // })->whereHas('interview');

        // dd($query->toSql());

        if ($customers->isNotEmpty()) {
            $customerCount = $customers->count();
            return $this->sendResponse(['Jumlah data tambahan' => $customerCount], 'Berhasil mengambil data tambahan');
        } else {
            return $this->sendError('Data tidak ditemukan', 404);
        }
    }
}
