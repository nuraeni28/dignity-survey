<?php

namespace App\Http\Controllers\API;

use App\Models\LoginHistory;
use App\Models\TestingOtp;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\UserResource;
use App\Models\InterviewSchedule;
use App\Models\Role;
use App\Models\User;
use App\Models\Otp;
use App\Models\UserTargetInterviews;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules;
use Carbon\Carbon;
use App\Mail\VerificationEmail; // Impor kelas email yang telah Anda buat
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;


class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

public function forgot(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
        'password' => 'required|min:8',
        'cpassword' => 'required|same:password',
    ]);

    if ($validator->fails()) {
        $errorMessages = [
            'email' => $validator->errors()->get('email'),
            'password' => $validator->errors()->get('password'),
            'cpassword' => $validator->errors()->get('cpassword'),
        ];

        $errorMessage = 'Gagal Merubah password akun.';

        if (!empty($errorMessages['email'])) {
            $errorMessage .= ' Email yang dimasukkan belum terdaftar';
        }
         if (!empty($errorMessages['cpassword'])) {
        $errorMessage .= ' Password dan konfirmasi password tidak sama';
    }

        return response()->json([
            'success' => false,
            'message' => $errorMessage,
            'data' => $errorMessages,
        ], 400);
    }

    $user = User::where('email', $request->input('email'))->first();

    if ($user) {
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // You might want to add additional logic here, like sending an email to notify the user.
        $success['token'] = $user->createToken('Inside')->plainTextToken;
        $success['user'] = new UserResource($user);
        return $this->sendResponse($success, 'Password berhasil diubah');
    } else {
        return $this->sendError('Email belum terdaftar', 404);
    }
}

        public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
            'address' => ['required', 'string'],
            'nik' => ['required', 'numeric', 'digits:16', 'unique:users'],
            'phone' => ['required', 'numeric', 'digits_between:8,14', 'unique:users'],
            'indonesia_province_id' => ['required'],
            'indonesia_city_id' => ['required'],
            'indonesia_district_id' => ['required'],
            'indonesia_village_id' => ['required'],
        ]);
            $districtId = $request->input('indonesia_district_id');
        // $disctrictId = $request->input('district_disctrict_id');
        $admin = User::where('admin_id', null)
            ->where('indonesia_district_id', $districtId)
            ->first();


     
         if ($validator->fails()) {
        $errorMessages = [
            'email' => $validator->errors()->get('email'),
            'nik' => $validator->errors()->get('nik'),
            'phone' => $validator->errors()->get('phone'),
        ];

        $errorMessage = 'Gagal membuat akun.';

        if (!empty($errorMessages['nik'])) {
            $errorMessage .= ' Nik yang dimasukkan sudah terdaftar.';
        }

        if (!empty($errorMessages['phone'])) {
            $errorMessage .= ' Nomor telepon yang dimasukkan sudah terdaftar.';
        }
        
        if (!empty($errorMessages['email'])) {
            $errorMessage .= ' Email yang dimasukkan sudah terdaftar.';
        }

        return response()->json([
            'success' => false,
            'message' => $errorMessage,
            'data' => $errorMessages,
        ], 400);
    }
      
        if($admin){
                $input = $request->all();
                $input['password'] = Hash::make($input['password']);
                $user = User::create($input);
                $user->register_by = 'aplikasi'; // Atur nilai register_by
                $user->save();
                
                $photo = $request->file('profile_image');
                 if ($photo) {
                $photoname = date('YmdHi') . $photo->getClientOriginalName();
    
                // Save the image to the 'public' directory
                $photo->move(public_path('public/profile_images'), $photoname);
    
                // Update the user's 'profile_image' column with the file path
                $user->profile_image = 'profile_images/' . $photoname;
        }
        else {
         $user->profile_image = null; // Set to null if no photo is uploaded
    }
        $role = Role::findByName('user');
        $user->assignRole($role);
        $token = $user->createToken('Inside')->plainTextToken;
              $user->update(['admin_id' => $admin->id, 'owner_id' => $admin->owner_id, 'remember_token' => $token]);
              event(new Registered($user));
              $success['token'] = $token;
              $success['user'] = new UserResource($user);
              return $this->sendResponse($success, 'Berhasil Daftar');
        }
        else{
               return $this->sendError('Wilayah penugasan Anda belum tersedia', 404);
        }
      
        // event(new Registered($user));
    //     $verificationData = [
    //         'id' => $user->id, // Sesuaikan dengan kolom yang benar pada model User
    //         'hash' => sha1($user->getEmailForVerification()), // Sesuaikan dengan logika yang sesuai
    //     ];

    //     Mail::to($user->email)->send(new VerificationEmail([
    // 'verification_url' => route('verification.verify', $verificationData),]));
        

    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::findOrFail(Auth::user()->id);
            $role = Role::findByName('user');

            if ($user->hasRole($role)) {
                if ($user) {
                    #  Update nilai mac_address dalam database
                    $user->mac_address = $request->mac_address;
                    $user->save();
                    // dd($user);
                    $historyLogin = new LoginHistory();

                    $historyLogin->id_user = $user->id;
                    // $historyLogin->login_at = now();
                    $historyLogin->save();
                    $success['token'] = $user->createToken('Inside')->plainTextToken;
                    $success['user'] = new UserResource($user);

                    return $this->sendResponse($success, 'Login Berhasil');
                } else {
                    // dd('MAC address not matching:', $user->mac_address, $request->mac_address);
                    return $this->sendError('Anda Tidak Bisa Login, Karena Akun Ini Sedang Dipakai Pada Device Lain', ['error' => 'MAC Address tidak valid']);
                }
            }
        } else {
            // dd('Login failed', $request->all());

            return $this->sendError('Login Gagal', ['error' => 'Unauthorized']);
        }
    }
    /**
     * Logout
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);
        // Hapus MAC address saat pengguna logout
        if ($user) {
            // Hapus MAC address saat pengguna logout
            $user->mac_address = null;
            $user->save();

            return response()->json(['message' => 'Logout Berhasil']);
        } else {
            // Handle jika pengguna tidak ditemukan
            return $this->sendError('Pengguna tidak ditemukan', 404);
        }
    }
    public function profile(Request $request)
    {
        $user = Auth::user();

        $userData = User::where('id', $user->id)
            ->with('province')
            ->with('city')
            ->with('district')
            ->with('village')
            ->first();
        if ($userData) {
            return $this->sendResponse(new UserResource($userData), 'Berhasil mendapatkan data pengguna');
        } else {
            return $this->sendError('Gagal mendapatkan data pengguna');
        }
    }
       public function profileSecond(Request $request)
    {
        $id = $request->input('id');
        $userData = User::where('id', $id)
            ->with('province')
            ->with('city')
            ->with('district')
            ->with('village')
            ->first();
        if ($userData) {
            return $this->sendResponse(new UserResource($userData), 'Berhasil mendapatkan data pengguna');
        } else {
            return $this->sendError('Gagal mendapatkan data pengguna');
        }
    }
    /**
     * Update user
     *
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request)
    {
        $user = Auth::user();

        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'address' => ['sometimes', 'string'],
            'nik' => ['sometimes', 'numeric', 'digits:16', 'unique:users,nik,' . $user->id],
            'phone' => ['sometimes', 'numeric', 'digits_between:8,14'],
            'indonesia_province_id' => ['sometimes'],
            'indonesia_city_id' => ['sometimes'],
            'indonesia_district_id' => ['sometimes'],
            'indonesia_village_id' => ['sometimes'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Gagal memperbarui data pengguna', $validator->errors());
        }

        $user->update($request->all());

     $photo = $request->file('profile_image');
        if ($photo) {
            $photoname = date('YmdHi') . $photo->getClientOriginalName();

            // Save the image to the 'public' directory
            $photo->move(public_path('public/profile_images'), $photoname);

            // Update the user's 'profile_image' column with the file path
            $user->profile_image = 'profile_images/' . $photoname;
        }
         $user->jenis_kelamin = $request->input('jenis_kelamin');
        $user->save();

        return $this->sendResponse(new UserResource($user), 'Berhasil update profile');
    }

    /**
     * Update password
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => ['required'],
            'new_password' => ['required', Password::defaults()],
            'confirm_password' => ['required', 'same:new_password', Password::defaults()],
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($validator->failed()) {
                return;
            }
            if (!Hash::check($request->input('old_password'), Auth::user()->password)) {
                return $this->sendError('Password lama salah', $validator->errors());
            }
        });

        $validator->validateWithBag('password');

        $success = Auth::user()->update([
            'password' => Hash::make($request->input('new_password')),
        ]);
        $user = Auth::user();
        if ($success) {
            return $this->sendResponse(new UserResource($user), 'Berhasil update password');
        } else {
            return $this->sendError('Update password gagal', $validator->errors());
        }
    }

  public function userTargetInterviews()
{
    $user = Auth::user();
    $userData = User::where('id', $user->id)->first();

    if ($userData) {
        $userData->load('targetInterview');
        $userData->load('doneInterviews');
        
        return $this->sendResponse(
            [
                'target' => $userData->targetInterview,
                'done' => count($userData->doneInterviews),
            ],
            'Berhasil mendapatkan data target interview'
        );
    } else {
        return $this->sendError('Gagal mendapatkan data target interview');
    }
}
    public function sendOTP(Request $request)
    {
        $otp = rand(1000, 9999);

        $response = Http::get(env('SMS_API_ENDPOINT'), [
            'ApiKey' => env('SMS_API_KEY'),
            'ClientId' => env('SMS_CLIENT_ID'),
            'SenderId' => env('SMS_SENDER_ID'),
            'Message' => "Kode OTP Anda adalah $otp, berlaku 5 menit.",
            'MobileNumbers' => $request->input('no_hp'),
            'Is_Unicode' => false,
            'Is_Flash' => false,
        ]);

        if ($response->successful()) {
            // Save OTP to database
            $otp = Otp::create([
                'user_id' => $request->input('user_id'),
                'otp_code' => $otp,
                'number_phone' => $request->input('no_hp'),
                'expired_at' => Carbon::now()->addMinutes(5),
            ]);
            // dd($otp);

            return response()->json(['message' => 'OTP sent successfully']);
        } else {
            return response()->json(['error' => 'Failed to send OTP']);
        }
    }
    public function checkOTP(Request $request)
    {
        $phoneNumber = $request->input('no_hp');
        $enteredOTP = $request->input('otp_code');

        // Cek apakah OTP yang dimasukkan benar
        $otp = Otp::where('number_phone', $phoneNumber)
            ->where('otp_code', $enteredOTP)
            ->first();
        if ($otp) {
            if (Carbon::now() < $otp->expired_at) {
                // Jika OTP benar, update kolom 'otp_code' menjadi null
                    $otp->update(['otp_code' => null]);

                return response()->json(['message' => 'Verification OTP successfully']);
            } else {
                return response()->json(['error' => 'Expired OTP']);
            }
        } else {
            return response()->json(['error' => 'Invalid OTP'], 422);
        }
    }
        public function checkOTPNew(Request $request)
    {
        $phoneNumber = $request->input('no_hp');
        $noKK = $request->input('no_kk');
        $enteredOTP = $request->input('otp_code');
        $idCustomer = $request->input('idCustomer');
        
        $customer = Customer::find($idCustomer);

        // Cek apakah OTP yang dimasukkan benar
        $otp = Otp::where('number_phone', $phoneNumber)
            ->where('otp_code', $enteredOTP)
            ->first();
        if ($otp) {
            if (Carbon::now() < $otp->expired_at) {
                // Jika OTP benar, update kolom 'otp_code' menjadi null
                    $otp->update(['otp_code' => null]);
                    $customer->update(['phone' => $phoneNumber, 'no_kk' => $noKK]);

                return response()->json(['message' => 'Verification OTP successfully']);
            } else {
                return response()->json(['error' => 'Expired OTP']);
            }
        } else {
            return response()->json(['error' => 'Invalid OTP'], 422);
        }
    }
    public function checkStatus(Request $request)
    {
        $userId = $request->input('user_id');

        $user = User::find($userId);
        if ($user) {
            $data = $user->status;
            return response()->json(['status' => $data]);
        }
        return response()->json(['error' => 'User tidak ditemukan']);
    }
    public function sendOTPAgain(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $user = User::where('id', $request->input('user_id'))->first();
        $otp = Otp::where('number_phone', $request->input('no_hp'))->first();
        if ($user) {
            if (!$otp) {
                $data = [
                    'msisdn' => $request->input('no_hp'), // Ganti dengan nomor yang sesuai
                    'template' => 'Relawan AAB - Kode OTP Anda $OTP,  berlaku 5 menit.', // Ganti dengan pesan yang sesuai
                    'time_limit' => 300,
                ];
                $headers = [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'App-ID' => '28fe6cb7-6797-4ff6-97c7-7b0837de2b54', // Ganti dengan App ID Anda
                    'API-Key' => 'JSiYrkPcJ7JW2+ipfwGsfUIVcFUAah8G', // Ganti dengan API Key Anda
                ];
                $response = $client->request('POST', 'https://api.verihubs.com/v1/otp/send', [
                    'headers' => $headers,
                    'json' => $data, // Mengirimkan data dalam format JSON
                ]);
                if ($response->getStatusCode() == 201) {
                    $responseJson = json_decode($response->getBody(), true); // Mendapatkan respons dalam bentuk array
                    $otp_code = $responseJson['otp'];
                    // Save OTP to database
                    $otp = Otp::create([
                        'user_id' => $request->input('user_id'),
                        'otp_code' => $otp_code,
                        'number_phone' => $request->input('no_hp'),
                        'expired_at' => Carbon::now()->addMinutes(5),
                    ]);

                    return response()->json(['message' => 'OTP sent successfully']);
                } else {
                    return response()->json(['error' => 'Failed to send OTP']);
                }
            } else {
                return response()->json(['error' => 'Nomor ini sudah pernah dikirimkan OTP'], 404);
            }
        } else {
            return response()->json(['error' => 'User not found'], 404); // Pengguna tidak ditemukan
        }
    }
    public function checkNumberPhone(Request $request)
    {
        $phoneNumber = $request->input('no_hp');
        $userId = $request->input('user_id');

        // Cek apakah OTP yang dimasukkan benar
        $otp = Otp::where('number_phone', $phoneNumber)->first();
        // dd($otp);

        if ($otp) {
            return response()->json(['error' => 'Nomor ini sudah pernah dikirimkan OTP'], 404);
        } else {
            return response()->json(['message' => 'Nomor bisa dikirimkan OTP']);
        }
    }
    public function sendOTPSecond(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $user = User::where('id', $request->input('user_id'))->first();
        if (strpos($request->input('no_hp'), '0') === 0) {
            // Hapus karakter awal '0' dan tambahkan '62' sebagai awalan
            $formattedNumberPhone = '62' . substr($request->input('no_hp'), 1);
        } else {
            // Jika nomor HP sudah dalam format yang diinginkan, gunakan langsung
            $formattedNumberPhone = $request->input('no_hp');
        }
        if ($user) {
                $data = [
                    'msisdn' => $formattedNumberPhone, // Ganti dengan nomor yang sesuai
                    'template' => 'Relawan AAB - Kode OTP Anda $OTP,  berlaku 5 menit.', // Ganti dengan pesan yang sesuai
                    'time_limit' => 300,
                ];
                $headers = [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'App-ID' => '28fe6cb7-6797-4ff6-97c7-7b0837de2b54', // Ganti dengan App ID Anda
                    'API-Key' => 'JSiYrkPcJ7JW2+ipfwGsfUIVcFUAah8G', // Ganti dengan API Key Anda
                ];
                $response = $client->request('POST', 'https://api.verihubs.com/v1/otp/send', [
                    'headers' => $headers,
                    'json' => $data, // Mengirimkan data dalam format JSON
                ]);
                if ($response->getStatusCode() == 201) {
                    $responseJson = json_decode($response->getBody(), true); // Mendapatkan respons dalam bentuk array
                    $otp_code = $responseJson['otp'];
                    // Save OTP to database
                    $otp = Otp::create([
                        'user_id' => $request->input('user_id'),
                        'otp_code' => $otp_code,
                        'number_phone' => $request->input('no_hp'),
                        'expired_at' => Carbon::now()->addMinutes(5),
                         'tipe' => 'sms'
                    ]);

                    return response()->json(['message' => 'OTP sent successfully']);
                } else {
                    return response()->json(['error' => 'Failed to send OTP']);
                }
            } 
        else {
            return response()->json(['error' => 'User not found'], 404); // Pengguna tidak ditemukan
        }
    }
     public function sendOTPWhatsApp(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $user = User::where('id', $request->input('user_id'))->first();
        $otp = rand(1000, 9999);
        if (strpos($request->input('no_hp'), '0') === 0) {
            // Hapus karakter awal '0' dan tambahkan '62' sebagai awalan
            $formattedNumberPhone = '62' . substr($request->input('no_hp'), 1);
        } else {
            // Jika nomor HP sudah dalam format yang diinginkan, gunakan langsung
            $formattedNumberPhone = $request->input('no_hp');
        }
        if ($user) {
                $data = [
                    'msisdn' => $formattedNumberPhone, // Ganti dengan nomor yang sesuai
                    'time_limit' => 600,
                    'otp' => $otp,
                    'template_name'=> 'inside',
                    'lang_code' => 'id'
                ];
                $headers = [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'App-ID' => '28fe6cb7-6797-4ff6-97c7-7b0837de2b54', // Ganti dengan App ID Anda
                    'API-Key' => 'JSiYrkPcJ7JW2+ipfwGsfUIVcFUAah8G', // Ganti dengan API Key Anda
                ];
                $response = $client->request('POST', 'https://api.verihubs.com/v1/whatsapp/otp/send', [
                    'headers' => $headers,
                    'json' => $data, // Mengirimkan data dalam format JSON
                ]);
                if ($response->getStatusCode() == 201) {
                  // Mendapatkan respons dalam bentuk array
                    // $otp_code = $responseJson['otp'];
                    // Save OTP to database
                    $otp = Otp::create([
                        'user_id' => $request->input('user_id'),
                        'otp_code' => $otp,
                        'number_phone' => $request->input('no_hp'),
                        'expired_at' => Carbon::now()->addMinutes(10),
                        'tipe' => 'whatsApp'
                    ]);

                    return response()->json(['message' => 'OTP sent successfully', 'otp'=> $otp]);
                } else {
                    return response()->json(['error' => 'Failed to send OTP']);
                }
            } 
        else {
            return response()->json(['error' => 'User not found'], 404); // Pengguna tidak ditemukan
        }
    }
      public function checkNumberPhoneSecond(Request $request)
    {
        $phoneNumber = $request->input('no_hp');
        $userId = $request->input('user_id');
        $testing = TestingOtp::where('number_phone', $request->input('no_hp'))->first();
        // $otp = Customer::where('phone', $phoneNumber)->first();
        $otp = InterviewSchedule::whereHas('customer', function ($query) use ($phoneNumber) {
            $query->where('phone', $phoneNumber);
        })
            ->whereHas('interview')
            ->exists();
        // dd($otp);

        if ($testing) {
            return response()->json(['message' => 'Nomor bisa dikirimkan OTP']);
        } else {
            if ($otp) {
                return response()->json(['error' => 'Nomor ini telah digunakan pada proses wawancara'], 404);
            } else {
                return response()->json(['message' => 'Nomor bisa dikirimkan OTP']);
            }
        }
    }
}
