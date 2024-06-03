<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Income;
use App\Models\InterviewSchedule;
use App\Models\Interview;
use App\Models\InterviewData;
use App\Models\Occupation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Exports\ComitmentCustomerExport;
use App\Exports\ManualCustomerExport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
    {
     if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin') && !Gate::check('koordinator-area')) {
            return abort('403');
        }
        $customer = Auth::user();
     $query = Customer::query();
     
        // Handle search input
        $search = $request->input('search');
         $statusKunjungan = $request->input('statusKunjungan');
        if ($search) {
            $query->where(function ($subquery) use ($search) {
                $subquery
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%')
                    ->orWhere('no_kk', 'like', '%' . $search . '%');
                    $subquery->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('email', 'like', '%' . $search . '%');
                });
                 
            });
        }
   if ($statusKunjungan) {
        if ($statusKunjungan === 'Belum') {
            $query->whereNull('status_kunjungan');
        } elseif ($statusKunjungan === 'Sudah') {
            $query->whereNotNull('status_kunjungan');
        }
    }
       

       if (!Gate::check('super-admin')) {
        $query->where(function ($subquery) use ($customer) {
            $subquery
                ->where('admin_id', $customer->id)
                ->orWhere('owner_id', $customer->id)
                ->orWhere('indonesia_city_id', $customer->indonesia_city_id);
        });
    }

         $customers = $query->paginate(10);
               $customers->appends(['search' => $search, 'statusKunjungan' => $statusKunjungan]);



        return view('admin.customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
       public function create()
    {
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('koordinator-area')) {
            return abort('403');
        }
        if (Gate::check('owner')) {
            $incomes = Income::where('owner_id', Auth::user()->id)
                ->latest()
                ->get();
            $occupations = Occupation::where('owner_id', Auth::user()->id)
                ->latest()
                ->get();
        }
        if (Gate::check('admin') || Gate::check('koordinator-area')) {
            $incomes = Income::where('admin_id', Auth::user()->id)
                ->orWhere('owner_id', Auth::user()->owner_id)
                ->latest()
                ->get();
            $occupations = Occupation::where('admin_id', Auth::user()->id)
                ->orWhere('owner_id', Auth::user()->owner_id)
                ->latest()
                ->get();
        }
        // dd(Auth::user());
        return view('admin.customer.create', compact('incomes', 'occupations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\StoreCustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
   public function store(Request $request)
    {
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('koordinator-area')) {
            return abort('403');
        }
        $this->validate(
            $request,
            [
                'name' => ['required', 'string', 'max:255'],
                'address' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'email'],
                'phone' => ['nullable', 'numeric', 'unique:customers'],
                'nik' => ['required', 'numeric', 'digits:16', 'unique:customers'],
                'indonesia_city_id' => ['nullable'],
                'indonesia_district_id' => ['required'],
                'indonesia_village_id' => ['required'],
                'age' => ['nullable', 'numeric'],
                'tps' => ['nullable', 'numeric'],
                'religion' => ['nullable', 'string', 'max:255'],
                'education' => ['nullable', 'string', 'max:255'],
                'job' => ['nullable', 'string', 'max:255'],
                'family_member' => ['nullable', 'numeric'],
                'family_election' => ['nullable', 'numeric'],
                'marrital_status' => ['nullable', 'string', 'max:255'],
                'monthly_income' => ['nullable', 'string', 'max:255'],
                'status' => ['nullable', 'string'],
                'dob' => ['nullable', 'date'],
                'surveyor_id' => ['required'],
            ],
            [
                'nik.digits' => 'NIK harus terdiri dari 16 digit',
                'indonesia_district_id.required' => 'Kecamatan wajib diisi',
                'indonesia_village_id.required' => 'Desa wajib diisi',
                'surveyor_id.required' => 'Wajib pilih relawan',
                'nik.unique' => 'NIK sudah terdaftar',
                'phone.unique' => 'Nomor HP sudah terdaftar',
            ]);
        // if ($validator->fails()) {
        //     // if email already exists
        //     if ($validator->errors()->has('phone')) {
        //         return $this->sendError('Gagal membuat responden nomor telepon sudah terdaftar', $validator->errors());
        //     } elseif ($validator->errors()->has('nik')) {
        //         return $this->sendError('Gagal membuat responden NIK sudah terdaftar', $validator->errors());
        //     } else {
        //         return $this->sendError('Gagal membuat responden', $validator->errors());
        //     }
        // }

        $request->merge([
            'indonesia_province_id' => Auth::user()->province->id,
        ]);

        if (Gate::check('owner')) {
            $request->merge([
                'owner_id' => Auth::user()->id,
            ]);
        }
        if (Gate::check('admin')) {
            $request->merge([
                'indonesia_province_id' => Auth::user()->province->id,
                'indonesia_city_id' => Auth::user()->city->id,
                'indonesia_district_id' => Auth::user()->district->id,
                'admin_id' => Auth::user()->id,
                'owner_id' => Auth::user()->owner_id,
            ]);
        }
        if (Gate::check('koordinator-area')) {
            $request->merge([
                'indonesia_province_id' => Auth::user()->province->id,
                'indonesia_city_id' => Auth::user()->city->id,
                'owner_id' => Auth::user()->owner_id,
            ]);
        }
        // dd($request->surveyor_id);
        $user = Auth::user();
        $customer = Customer::create($request->all());
        $data = [
            'period_id' => 37,
            'user_id' => $request->input('surveyor_id'),
            'customer_id' => $customer->id,
            'interview_date' => now(),
        ];

        $schedule = InterviewSchedule::create($data);
        $dataInterview = ['interview_date' => now(), 'owner_id' => $user->owner_id, 'admin_id' => $user->admin_id, 'interview_schedule_id' => $schedule->id];

        $interview = Interview::create($dataInterview);

        $questions = [
            [
                'question' => 'Apakah anda bersedia memilih Ahmad Abdy Baramuli pada pemilu 14 februari mendatang?',
                'type' => 'option', // Gantilah dengan tipe data yang sesuai
                'answer' => '[Ya, Tidak, Tidak Tahu/ Tidak Jawab]', // Gantilah dengan jawaban yang sesuai
                'customer_answer' => 'Ya', // Gantilah dengan jawaban pelanggan yang sesuai
                'question_id' => 115, // Gantilah dengan ID pertanyaan yang sesuai
                'interview_id' => $interview->id,
            ],
            [
                'question' => 'Kalau boleh tahu, apakah alasan Anda memilih Ahmad Abdy Baramuli?',
                'type' => 'option', // Gantilah dengan tipe data yang sesuai
                'answer' => '[Suka Dengan Profilnya, Suka Dengan Visi-Misinya, Tidak Tahu/ Tidak Jawab]', // Gantilah dengan jawaban yang sesuai
                'customer_answer' => 'Suka Dengan Profilnya', // Gantilah dengan jawaban pelanggan yang sesuai
                'question_id' => 115, // Gantilah dengan ID pertanyaan yang sesuai
                'interview_id' => $interview->id,
            ],
        ];
        foreach ($questions as $question) {
            $interviewData = InterviewData::create($question);
        }

        return redirect()
            ->route('responden.index')
            ->with('success', __('Berhasil membuat responden.'));
    }
 public function verifyPhone(Request $request)
    {
        if (!Gate::check('koordinator-area')) {
            return abort('403');
        }

        $customer = Auth::user();

        $customers = Customer::with(['schedules.interview'])
            ->join('interview_schedules', 'customers.id', '=', 'interview_schedules.customer_id')
            ->join('interviews', 'interview_schedules.id', '=', 'interviews.interview_schedule_id')
            ->select('customers.id', 'customers.name', 'customers.email', 'customers.nik', 'customers.no_kk', 'customers.phone', 'customers.status_verified')
            ->where(function ($query) use ($customer) {
                $query->orWhere('customers.indonesia_city_id', $customer->indonesia_city_id);
            })
             ->where(function ($query) use ($customer) {
                $query->orWhere('customers.status_kunjungan', 'Sudah');
            })
            ->where(function ($query) use ($customer) {
                $query->where('interviews.owner_id', $customer->owner_id);
            })
            ->where(function ($query){
                $query->whereNull('interviews.location');
            })
            ->where(function ($query) {
                $query->whereNull('interview_schedules.type');
            })
    //         ->whereNotIn('customers.phone', function ($subquery) {
    //     $subquery->select('number_phone')->from('otp'); // Tabel 'otp' adalah tabel yang berisi informasi OTP
    // })
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('nik', 'like', '%' . $search . '%');
                });
            })
            ->groupBy('customers.id', 'customers.name', 'customers.email', 'customers.nik', 'customers.no_kk', 'customers.phone', 'customers.status_verified')
            ->paginate(10);

        // dd($customers);

        return view('admin.customer.verify-phone', compact('customers'));
    }
    public function getStatusCustomer(Request $request, $id)
    {
        // dd($userId);
        // Gantilah ini dengan kode yang sesuai untuk mengambil status pengguna dengan ID $userId
        $customer = Customer::find($id);
        // dd($request->status);
        $customer->status_verified = $request->input('status');
        $customer->save();
        $page = $request->input('page');

        return redirect()
            ->route('responden.verifyPhone', ['page' => $page])
            ->with('success', 'Status responden berhasil diperbarui.');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
   public function show($id)
{
    // Dapatkan objek Customer berdasarkan ID yang diterima
    $customer = Customer::findOrFail($id);

    // Buat kueri untuk mendapatkan jadwal wawancara yang terkait dengan pelanggan
    $interviews = InterviewSchedule::where('customer_id', $id)
        ->with('user', 'customer', 'period', 'interview')
        ->get();

    // Kembalikan tampilan bersama dengan data pelanggan dan jadwal wawancara
    return view('admin.customer.show', compact('customer', 'interviews'));
}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer, $id,  Request $request)
    {
         $page = $request->input('page');
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('super-admin')  && Auth::user()->id == 2583 && Auth::user()->id == 2580) {
            return abort('403');
        }
        if (Gate::check('owner')) {
            $incomes = Income::where('owner_id', Auth::user()->id)
                ->latest()
                ->get();
            $occupations = Occupation::where('owner_id', Auth::user()->id)
                ->latest()
                ->get();
        }
        if (Gate::check('admin')) {
            $incomes = Income::where('admin_id', Auth::user()->id)
                ->orWhere('owner_id', Auth::user()->owner_id)
                ->latest()
                ->get();
            $occupations = Occupation::where('admin_id', Auth::user()->id)
                ->orWhere('owner_id', Auth::user()->owner_id)
                ->latest()
                ->get();
        } else {
            $incomes = Income::all();
            $occupations = Occupation::all();
        }
         
        $customer = Customer::findOrFail($id);
        

// dd($customer);
        return view('admin.customer.edit', compact('customer', 'incomes', 'occupations', 'page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\UpdateCustomerRequest  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $customer = Customer::findOrFail($id);
        $userId = Auth::user()->id;
        $noKK = $request->input('no_kk');

        $this->validate(
            $request,
            [
                'name' => ['sometimes', 'string', 'max:255'],
                'address' => ['sometimes', 'string', 'max:255'],
                'email' => ['sometimes', 'string', 'email'],
                'nik' => ['sometimes', 'numeric', 'digits:16', 'unique:customers,nik,' . $customer->id],
                'phone' => [
                    'required',
                    function ($attribute, $value, $fail) use ($customer) {
                        
                        // Jika nomor telepon tidak kosong, periksa keunikan
                        if (!empty($value)) {
                            if(Auth::user()->id != 2579 && Auth::user()->id != 2581){
                            $existingCustomer = Customer::where('phone', $value)
                                ->where('id', '!=', $customer->id)
                                ->exists();

                            if ($existingCustomer) {
                                return $fail('Nomor HP sudah digunakan oleh responden lain');
                            }
                            }
                            else{
                                return;
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
                    },
                ],
                'no_kk' => [
                    'required',
                    'numeric',
                    'digits:16',
                 function ($attribute, $value, $fail) use ($userId, $customer) {
                        // Custom validation to check if no_kk is unique for the current user
                        if (Auth::user()->id != 2579 && Auth::user()->id != 2581) {
                            $existingNoKK = Customer::where('no_kk', $value)
                                ->where('surveyor_id', $userId)
                                ->where('id', '!=', $customer->id)
                                ->first();

                            if ($existingNoKK) {
                                // Allow the same no_kk for the same surveyor
                                return;
                            }
                            // Check if the no_kk is already associated with a different surveyor
                            $existingNoKKOtherSurveyor = Customer::where('no_kk', $value)->where('surveyor_id', '!=', $userId)->first();

                            if ($existingNoKKOtherSurveyor) {
                                return $fail('NO KK sudah terdaftar untuk responden lain');
                            }
                        } else {
                            return;
                        }
                    },
                ],
                'indonesia_city_id' => ['nullable'],
                'indonesia_district_id' => ['sometimes'],
                'indonesia_village_id' => ['nullable'],
            ],
            [
                'nik.unique' => 'Nomor NIK sudah digunakan oleh responden lain.',
                'phone.unique' => 'Nomor HP sudah digunakan oleh responden lain.',
                'no_kk.unique' => 'Nomor KK sudah digunakan oleh responden lain.',
                'no_kk.required' => 'Nomor KK wajib diisi.',
            ]
        );

       $data = $request->all();
        $data['status_kunjungan'] = 'Sudah'; // Menambahkan status_kunjungan ke data yang akan diupdate
        
        $customer->update($data);
        $page = $request->input('page');

        return redirect()
            ->route('responden.index', ['page' => $page])
            ->with('success', __('Berhasil mengupdate Responden.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
   public function destroy($id,  Request $request)
    {
          $page = $request->input('page');
        $customer = Customer::with('schedules')
            ->where('id', $id)
            ->first();
        // // dd($customer->schedules->count() > 0);

        if ($customer->schedules->count() > 0) {
            // Loop melalui setiap interview_schedule dan hapus interview yang terkait
            foreach ($customer->schedules as $schedule) {
                // // Hapus semua interview yang terkait dengan schedule secara permanen
                // $interviews = Interview::where('interview_schedule_id', $schedule->id)->get();

                // foreach ($interviews as $interview) {
                //     $interview->forceDelete();
                // }
                // Hapus interview_schedule itu sendiri
                $schedule->forceDelete();
            }
        }

        // Hapus customer itu sendiri
        $customer->forceDelete();

        return redirect()
            ->route('responden.index', ['page' => $page])
            ->with('success', __('Berhasil menghapus Responden'));
    }
    public function getStatus(Request $request)
{
    $statusKunjungan = $request->input('statusKunjungan');

      // Ambil semua pelanggan dengan status kunjungan yang dipilih atau semua pelanggan jika null
    $customers = Customer::when($statusKunjungan === 'Belum', function ($query) {
        return $query->whereNull('status_kunjungan');
    })->when($statusKunjungan === 'Sudah', function ($query) {
        return $query->whereNotNull('status_kunjungan');
    })->paginate(10);

    return view('admin.customer.index', ['customers' => $customers]);
}
    public function getAdditionalCustomer(Request $request)
    {
        // Ambil semua pelanggan dengan status kunjungan yang dipilih atau semua pelanggan jika null
        if(Gate::check('koordinator-area')){
            $customers = Customer::where('type', 'pemantapan')->where('indonesia_city_id',Auth::user()->indonesia_city_id)->paginate(10);
        }
        else{
             $customers = Customer::where('type', 'pemantapan')->paginate(10);
        }
       
       

        return view('admin.customer.index', ['customers' => $customers]);
    }
      public function getDuplicateCustomer(Request $request)
    {
        // Ambil semua pelanggan dengan nomor telepon yang sama
        $duplicatedCustomers = Customer::select('phone')
            ->groupBy('phone')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('phone');

        // Ambil semua pelanggan dengan nomor telepon yang ada di dalam $duplicatedCustomers
        $customers = Customer::whereIn('phone', $duplicatedCustomers)
            ->whereNull('type')
            ->whereNull('status_kunjungan')
            ->orderBy('phone')
            ->paginate(10);

        return view('admin.customer.index', ['customers' => $customers]);
    }
      public function createComitment()
    {
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('koordinator-area')) {
            return abort('403');
        }
        if (Gate::check('owner')) {
            $incomes = Income::where('owner_id', Auth::user()->id)
                ->latest()
                ->get();
            $occupations = Occupation::where('owner_id', Auth::user()->id)
                ->latest()
                ->get();
        }
        if (Gate::check('admin') || Gate::check('koordinator-area')) {
            $incomes = Income::where('admin_id', Auth::user()->id)
                ->orWhere('owner_id', Auth::user()->owner_id)
                ->latest()
                ->get();
            $occupations = Occupation::where('admin_id', Auth::user()->id)
                ->orWhere('owner_id', Auth::user()->owner_id)
                ->latest()
                ->get();
        }
        // dd(Auth::user());
        return view('admin.customer.comitment', compact('incomes', 'occupations'));
    }
    public function storeComitment(Request $request)
    {
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('koordinator-area')) {
            return abort('403');
        }
        $userId = Auth::user()->id;
        $this->validate(
            $request,
            [
                'name' => ['required', 'string', 'max:255'],
                'address' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'email'],
                'phone' => ['nullable', 'numeric', 'unique:customers'],
                'nik' => ['required', 'numeric', 'digits:16', 'unique:customers'],
                'no_kk' => [
                    'required',
                    'numeric',
                    'digits:16',
                    function ($attribute, $value, $fail) use ($userId) {
                        // Check if the 'no_kk' is already associated with a different surveyor
                        $existingNoKKOtherSurveyor = Customer::where('no_kk', $value)
                            ->where('surveyor_id', '!=', $userId)
                            ->first();

                        if ($existingNoKKOtherSurveyor) {
                            return $fail('NO KK sudah terdaftar untuk surveyor lain');
                        }
                    },
                ],
                'indonesia_city_id' => ['nullable'],
                'indonesia_district_id' => ['required'],
                'indonesia_village_id' => ['required'],
                'age' => ['nullable', 'numeric'],
                'tps' => ['nullable', 'numeric'],
                'religion' => ['nullable', 'string', 'max:255'],
                'education' => ['nullable', 'string', 'max:255'],
                'job' => ['nullable', 'string', 'max:255'],
                'family_member' => ['nullable', 'numeric'],
                'family_election' => ['nullable', 'numeric'],
                'marrital_status' => ['nullable', 'string', 'max:255'],
                'monthly_income' => ['nullable', 'string', 'max:255'],
                'status' => ['nullable', 'string'],
                'dob' => ['nullable', 'date'],
            
            ],
            [
                'nik.digits' => 'NIK harus terdiri dari 16 digit',
                'indonesia_district_id.required' => 'Kecamatan wajib diisi',
                'indonesia_village_id.required' => 'Desa wajib diisi',
                'nik.unique' => 'NIK sudah terdaftar',
                'phone.unique' => 'Nomor HP sudah terdaftar',
                'no_kk.required' => 'Nomor KK wajib diisi',
                'no_kk.unique' => 'Nomor KK sudah terdaftar untuk surveyor lain',
            ]
        );

        $request->merge([
            'indonesia_province_id' => Auth::user()->province->id,
        ]);

        if (Gate::check('owner')) {
            $request->merge([
                'owner_id' => Auth::user()->id,
            ]);
        }
        if (Gate::check('admin')) {
            $request->merge([
                'indonesia_province_id' => Auth::user()->province->id,
                'indonesia_city_id' => Auth::user()->city->id,
                'indonesia_district_id' => Auth::user()->district->id,
                'admin_id' => Auth::user()->id,
                'owner_id' => Auth::user()->owner_id,
            ]);
        }
        if (Gate::check('koordinator-area')) {
            $request->merge([
                'indonesia_province_id' => Auth::user()->province->id,
                'indonesia_city_id' => Auth::user()->city->id,
                'owner_id' => Auth::user()->owner_id,
                'surveyor_id' => Auth::user()->id,
                'status_kunjungan' => 'Sudah',
                'metode' => 'manual',
                'nik_surveyor' => $request->nik_surveyor
            ]);
        }
        // dd($request->surveyor_id);
        $user = Auth::user();
        $customer = Customer::create($request->all());
        $data = [
            'period_id' => 37,
            'user_id' => Auth::user()->id,
            'customer_id' => $customer->id,
            'interview_date' => now(),
        ];

// dd($request->all());
        $schedule = InterviewSchedule::create($data);
        $photo = $request->file('photo');
        if ($photo != null) {
            $photoname = date('YmdHi') . $photo->getClientOriginalName();
            $photo->move(public_path('public/image'), $photoname);
        } else {
            $photoname = null;
        }

        $dataInterview = ['interview_date' => now(), 'owner_id' => $user->owner_id, 'admin_id' => $user->admin_id, 'interview_schedule_id' => $schedule->id, 'photo' => $photoname];

        $interview = Interview::create($dataInterview);

        $questions = [
            [
                'question' => 'Apakah anda bersedia memilih Ahmad Abdy Baramuli pada pemilu 14 februari mendatang?',
                'type' => 'option', // Gantilah dengan tipe data yang sesuai
                'answer' => '[Ya, Tidak, Tidak Tahu/ Tidak Jawab]', // Gantilah dengan jawaban yang sesuai
                'customer_answer' => 'Ya', // Gantilah dengan jawaban pelanggan yang sesuai
                'question_id' => 115, // Gantilah dengan ID pertanyaan yang sesuai
                'interview_id' => $interview->id,
            ],
            [
                'question' => 'Kalau boleh tahu, apakah alasan Anda memilih Ahmad Abdy Baramuli?',
                'type' => 'option', // Gantilah dengan tipe data yang sesuai
                'answer' => '[Suka Dengan Profilnya, Suka Dengan Visi-Misinya, Tidak Tahu/ Tidak Jawab]', // Gantilah dengan jawaban yang sesuai
                'customer_answer' => 'Suka Dengan Profilnya', // Gantilah dengan jawaban pelanggan yang sesuai
                'question_id' => 115, // Gantilah dengan ID pertanyaan yang sesuai
                'interview_id' => $interview->id,
            ],
        ];
        foreach ($questions as $question) {
            $interviewData = InterviewData::create($question);
        }

       $page = $request->query('page', 'responden.index'); // Ambil halaman referensi dari query string, defaultnya 'customer.index'

        // Dapatkan jumlah halaman total dari objek Paginator untuk halaman yang sesuai
        $totalPages = Customer::where('admin_id', $user->id)
            ->orWhere('owner_id', $user->id)
            ->orWhere('indonesia_city_id', $user->indonesia_city_id)
            ->paginate(10)
            ->lastPage();

        // Sertakan nomor halaman terakhir dalam query string saat mengarahkan kembali pengguna
        return redirect()
            ->route($page, ['page' => $totalPages])
            ->with('success', __('Berhasil membuat responden.'));
    }
       public function addComitment(Customer $customer, Request $request, $id)
    {
        $customer = $customer->where('id', $id)->first();
        // dd($customer);
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('koordinator-area')) {
            return abort('403');
        }
        if (Gate::check('owner')) {
            $incomes = Income::where('owner_id', Auth::user()->id)
                ->latest()
                ->get();
            $occupations = Occupation::where('owner_id', Auth::user()->id)
                ->latest()
                ->get();
        }
        if (Gate::check('admin') || Gate::check('koordinator-area')) {
            $incomes = Income::where('admin_id', Auth::user()->id)
                ->orWhere('owner_id', Auth::user()->owner_id)
                ->latest()
                ->get();
            $occupations = Occupation::where('admin_id', Auth::user()->id)
                ->orWhere('owner_id', Auth::user()->owner_id)
                ->latest()
                ->get();
        }
        // dd(Auth::user());
        return view('admin.customer.add-comitment', compact('customer', 'incomes', 'occupations'));
    }
       public function storeNewComitment(Request $request)
    {
        if (!Gate::check('owner') && !Gate::check('admin') && !Gate::check('koordinator-area')) {
            return abort('403');
        }
        // Validasi input
        // dd($request['nik.*']);

        // Ambil objek Customer utama
        $customerUtama = Customer::find($request->idCus);
        // Iterasi untuk setiap elemen dalam array nama dan nik
        foreach ($request->name as $key => $name) {
            $rules = [
                'name.' . $key => ['required', 'string', 'max:255'],
                'nik.' . $key => ['nullable', 'numeric', 'digits:16'],
            ];

            // Perform the validation
            $this->validate($request, $rules);
            // dd($request['nik.0']);
            // Perform additional unique validation
            $validator = Validator::make(
                [$key => $request->nik[$key]],
                [
                    $key => ['nullable', 'numeric', 'digits:16', Rule::unique('customers', 'nik')],
                ],
                [
                    $key . '.unique' => 'NIK ' . $request->nik[$key] . ' untuk ' . $request->name[$key] . ' sudah terdaftar.',
                ]
            );

            // Check if validation fails
            if ($validator->fails()) {
                session()->flash('old_input', $request->only('name', 'nik'));
                return redirect()
                    ->back()
                    ->withErrors($validator->errors());
            }

                $customerUtama = Customer::find($request->idCus);
            $customer = new Customer();
            $customer->name = $name;
            $customer->nik = $request->nik[$key];
            $customer->phone = $customerUtama->phone;
            $customer->no_kk = $customerUtama->no_kk;
            $customer->address = $customerUtama->address;
            $customer->owner_id = $customerUtama->owner_id;
            $customer->admin_id = $customerUtama->admin_id;
            $customer->religion = $customerUtama->religion;
            $customer->family_member = $customerUtama->family_member;
            $customer->family_election = $customerUtama->family_election;
            $customer->monthly_income = $customerUtama->monthly_income;
            $customer->tps = $customerUtama->tps;
            $customer->indonesia_province_id = $customerUtama->indonesia_province_id;
            $customer->indonesia_city_id = $customerUtama->indonesia_city_id;
            $customer->indonesia_district_id = $customerUtama->indonesia_district_id;
            $customer->indonesia_village_id = $customerUtama->indonesia_village_id;
            $customer->type = 'pemantapan';
            $customer->surveyor_id = $customerUtama->surveyor_id;
            $customer->surveyor = $customerUtama->surveyor;
            $customer->nik_surveyor = $customerUtama->nik_surveyor;
             $customer->save();
        }
 $user = Auth::user();
       $page = $request->query('page', 'responden.index'); // Ambil halaman referensi dari query string, defaultnya 'customer.index'

        // Dapatkan jumlah halaman total dari objek Paginator untuk halaman yang sesuai
        $totalPages = Customer::where('admin_id', $user->id)
            ->orWhere('owner_id', $user->id)
            ->orWhere('indonesia_city_id', $user->indonesia_city_id)
            ->paginate(10)
            ->lastPage();

        // Sertakan nomor halaman terakhir dalam query string saat mengarahkan kembali pengguna
        return redirect()
            ->route($page, ['page' => $totalPages])
            ->with('success', __('Berhasil menambah anggota keluarga.'));
    }
    public function export(Request $request)
    {
        $customer = Auth::user();
          $search = $request->search;
        $customers = Customer::with(['schedules.interview', 'user'])
            ->join('interview_schedules', 'customers.id', '=', 'interview_schedules.customer_id')
            ->join('interviews', 'interview_schedules.id', '=', 'interviews.interview_schedule_id')
            ->leftJoin('users', 'customers.surveyor_id', '=', 'users.id')
            ->select('customers.id', 'customers.name', 'customers.nik', 'customers.no_kk', 'customers.phone', 'customers.status_kunjungan', 'customers.indonesia_city_id', 'customers.indonesia_district_id', 'customers.indonesia_village_id', 'customers.surveyor', 'customers.type', 'users.email')
            ->where('interview_schedules.type', '=', null)
            ->where('interviews.owner_id', '=', $customer->owner_id)
            ->where('customers.indonesia_city_id', '=', $customer->indonesia_city_id)
            ->groupBy('customers.id', 'customers.name', 'customers.nik', 'customers.no_kk', 'customers.phone', 'customers.status_kunjungan', 'customers.indonesia_city_id', 'customers.indonesia_district_id', 'customers.indonesia_village_id', 'customers.surveyor', 'customers.type', 'users.email');

        // Kueri untuk data pemantapan
 $customersPemantapan = Customer::with('user')
    ->leftJoin('users', 'customers.surveyor_id', '=', 'users.id')
    ->where('customers.indonesia_city_id', '=', Auth::user()->indonesia_city_id)
    ->where('customers.type', '=', 'pemantapan')
    ->select('customers.id', 'customers.name', 'customers.nik', 'customers.no_kk', 'customers.phone', 'customers.status_kunjungan', 'customers.indonesia_city_id', 'customers.indonesia_district_id', 'customers.indonesia_village_id', 'customers.surveyor', 'customers.type', 'users.email')
    ->orderBy('customers.no_kk');
        $combinedCustomers = $customers->union($customersPemantapan);

        if ($search) {
         $customersSearch = Customer::with(['schedules.interview', 'user'])
                ->join('interview_schedules', 'customers.id', '=', 'interview_schedules.customer_id')
                ->join('interviews', 'interview_schedules.id', '=', 'interviews.interview_schedule_id')
                ->select('customers.surveyor_id', 'customers.id', 'customers.name', 'customers.email', 'customers.nik', 'customers.no_kk', 'customers.phone', 'customers.status_kunjungan', 'customers.indonesia_city_id', 'customers.indonesia_district_id', 'customers.indonesia_village_id', 'customers.surveyor', 'customers.type')
                ->where(function ($query) use ($customer) {
                    $query->orWhere('customers.indonesia_city_id', $customer->indonesia_city_id);
                })

                ->where(function ($query) use ($customer) {
                    $query->where('interviews.owner_id', $customer->owner_id);
                })
                ->where(function ($query) {
                    $query->whereNull('interview_schedules.type');
                })
                ->groupBy('customers.surveyor_id', 'customers.id', 'customers.name', 'customers.email', 'customers.nik', 'customers.no_kk', 'customers.phone', 'customers.status_kunjungan', 'customers.indonesia_city_id', 'customers.indonesia_district_id', 'customers.indonesia_village_id', 'customers.surveyor', 'customers.type');
            $customersPemantapan = Customer::with('user')
                ->where('indonesia_city_id', Auth::user()->indonesia_city_id)
                ->where('type', 'pemantapan')
                ->select('surveyor_id', 'id', 'name', 'email', 'nik', 'no_kk', 'phone', 'status_kunjungan', 'indonesia_city_id', 'indonesia_district_id', 'indonesia_village_id', 'surveyor', 'type') // Selecting same columns as the first query
                ->orderBy('no_kk');

            $combinedCustomers = $customersSearch->union($customersPemantapan)->orderBy('no_kk')->get();
              $searchLower = strtolower($search);
            $customers = $combinedCustomers->filter(function ($customer) use ($searchLower) {
                // Periksa apakah properti user ada sebelum mencoba mengaksesnya
                return $customer->user && $customer->user->email && strpos($customer->user->email, $searchLower) !== false;
            });
        } else {
            $customers = $combinedCustomers->orderBy('no_kk')->get();
        }

        if ($search) {
            $fileName = 'Pemantapan Data ' . $search;
        } else {
            $cityName = ucwords(strtolower(str_replace('KABUPATEN ', '', Auth::user()->city->name)));
            $fileName = 'Pemantapan Data ' . $cityName;
        }

        $fileName .= ' - ' . date('d-m-Y') . '.xlsx';
       
 
        
        return Excel::Download(new ComitmentCustomerExport($customers), $fileName);
    }
     public function exportManual(Request $request)
    {
    
        // dd($customer);
        $customers = Customer::with(['schedules.interview', 'user'])
            ->join('interview_schedules', 'customers.id', '=', 'interview_schedules.customer_id')
            ->join('interviews', 'interview_schedules.id', '=', 'interviews.interview_schedule_id')
            ->leftJoin('users', 'customers.surveyor_id', '=', 'users.id')
            ->select('customers.id', 'customers.name', 'customers.nik', 'customers.no_kk', 'customers.phone', 'customers.status_kunjungan', 'customers.indonesia_city_id', 'customers.indonesia_district_id', 'customers.indonesia_village_id', 'customers.surveyor', 'customers.type', 'users.email', 'customers.dob', 'customers.family_election', 'customers.job', 'customers.tps')
            ->where('interview_schedules.type', '=', null)
            ->whereNull('interviews.record_file')
            ->groupBy('customers.id', 'customers.name', 'customers.nik', 'customers.no_kk', 'customers.phone', 'customers.status_kunjungan', 'customers.indonesia_city_id', 'customers.indonesia_district_id', 'customers.indonesia_village_id', 'customers.surveyor', 'customers.type', 'users.email', 'customers.dob', 'customers.family_election', 'customers.job', 'customers.tps');

        // Kueri untuk data pemantapan
        $nomorKKs = $customers->pluck('no_kk')->toArray();

        $customersPemantapan = Customer::with('user')
            ->leftJoin('users', 'customers.surveyor_id', '=', 'users.id')
            ->where('customers.type', '=', 'pemantapan')
            ->whereIn('customers.no_kk', function ($query) use ($nomorKKs) {
                $query->select('no_kk')->from('customers')->whereIn('no_kk', $nomorKKs);
            })
            ->select('customers.id', 'customers.name', 'customers.nik', 'customers.no_kk', 'customers.phone', 'customers.status_kunjungan', 'customers.indonesia_city_id', 'customers.indonesia_district_id', 'customers.indonesia_village_id', 'customers.surveyor', 'customers.type', 'users.email', 'customers.dob', 'customers.family_election', 'customers.job', 'customers.tps')
            ->orderBy('customers.no_kk');
        $combinedCustomers = $customers->union($customersPemantapan);

        $customers = $combinedCustomers->orderBy('no_kk')->get();

        $fileName = 'Pemantapan Data Manual';

        $fileName .= ' - ' . date('d-m-Y') . '.xlsx';

        return Excel::Download(new ManualCustomerExport($customers), $fileName);
    }
    
}
