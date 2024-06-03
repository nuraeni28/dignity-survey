<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */


Route::get('/', 'App\Http\Controllers\Admin\DashboardController@index')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::post('sort', '\Rutorika\Sortable\SortableController@sort');
Route::post('sort', '\Rutorika\Sortable\SortableController@sort');
Route::post('/user/{userId}/status', 'App\Http\Controllers\Admin\VolunteerController@getStatus')->name('relawan.getstatus');
Route::post('/user/statusKabupaten', 'App\Http\Controllers\Admin\VolunteerController@getStatusKabupaten')->name('relawan.getStatusKabupaten');
Route::get('provinces', 'App\Http\Controllers\DependentDropdownController@provinces')->name('provinces');
Route::get('cities', 'App\Http\Controllers\DependentDropdownController@cities')->name('cities');
Route::get('districts', 'App\Http\Controllers\DependentDropdownController@districts')->name('districts');
Route::get('villages', 'App\Http\Controllers\DependentDropdownController@villages')->name('villages');
Route::get('caleg', 'App\Http\Controllers\DependentDropdownController@caleg')->name('caleg');
Route::get('partai', 'App\Http\Controllers\DependentDropdownController@partai')->name('partai');
Route::get('occupations', 'App\Http\Controllers\DependentDropdownController@occupations')->name('occupations');
Route::get('interviewsByOccupation', 'App\Http\Controllers\DependentDropdownController@interviewsByOccupation')->name('interviewsByOccupation');
Route::get('interviewsByAge', 'App\Http\Controllers\DependentDropdownController@interviewsByAge')->name('interviewsByAge');
Route::get('interviewsByEducation', 'App\Http\Controllers\DependentDropdownController@interviewsByEducation')->name('interviewsByEducation');
Route::get('interviewsByFamilyElection', 'App\Http\Controllers\DependentDropdownController@interviewsByFamilyElection')->name('interviewsByFamilyElection');
Route::get('interviewsByTps', 'App\Http\Controllers\DependentDropdownController@interviewsByTps')->name('interviewsByTps');
Route::get('interviewsByDate', 'App\Http\Controllers\DependentDropdownController@interviewsByDate')->name('interviewsByDate');
Route::get('allVillages', 'App\Http\Controllers\DependentDropdownController@allVillages')->name('allVillages');
Route::get('interviewByVillages', 'App\Http\Controllers\DependentDropdownController@interviewByVillages')->name('interviewByVillages');
Route::get('interviews', 'App\Http\Controllers\DependentDropdownController@interviews')->name('interviews');
Route::get('villagesMultiSelect', 'App\Http\Controllers\DependentDropdownController@villagesMultiSelect')->name('villagesMultiSelect');
Route::get('interview/export', 'App\Http\Controllers\Admin\InterviewController@export')->name('interview.export');
Route::get('interview-sosmed/export', 'App\Http\Controllers\Admin\InterviewSosmedController@export')->name('interview-sosmed.export');
Route::get('performa/export', 'App\Http\Controllers\Admin\DashboardController@export')->name('performa.export');
Route::get('relawan-sosmed/export', 'App\Http\Controllers\Admin\VolunteerSosmedController@export')->name('relawan-sosmed.export');
Route::get('volunteer/export', 'App\Http\Controllers\Admin\VolunteerController@export')->name('relawan.export');
Route::get('interview/edit', 'App\Http\Controllers\Admin\InterviewController@edit')->name('interview.edit');
Route::delete('interview/delete', 'App\Http\Controllers\Admin\InterviewController@deleteAll')->name('interview.deleteAll');
Route::delete('interview-sosmed/delete', 'App\Http\Controllers\Admin\InterviewSosmedController@deleteAll')->name('interview-sosmed.deleteAll');
Route::get('/get-cities/{provinceId}', 'App\Http\Controllers\Admin\VolunteerController@getCities');
Route::get('/get-villages/{districtId}', 'App\Http\Controllers\Admin\VolunteerController@getVillages');
Route::get('/daftar', 'App\Http\Controllers\Admin\RegisterController@index');
Route::post('/daftar', 'App\Http\Controllers\Admin\RegisterController@store')->name('register.store');
Route::get('/sukses-daftar', 'App\Http\Controllers\Admin\RegisterController@success')->name('register.success');
Route::post('/tutorial', 'App\Http\Controllers\Admin\TutorialController@store')->name('tutorial.store');
Route::get('responden/verify-phone', 'App\Http\Controllers\Admin\CustomerController@verifyPhone')->name('responden.verifyPhone');
Route::post('responden/{id}/status', 'App\Http\Controllers\Admin\CustomerController@getStatusCustomer')->name('customer.getStatusCustomer');
Route::get('responden/data-tambahan', 'App\Http\Controllers\Admin\CustomerController@getAdditionalCustomer')->name('responden.getAdditionalCustomer');
Route::get('responden/data-duplikat', 'App\Http\Controllers\Admin\CustomerController@getDuplicateCustomer')->name('responden.getDuplicateCustomer');
Route::get('admin/interview/showImport', 'App\Http\Controllers\Admin\InterviewController@showImport')->name('interview.showImport');
Route::get('relawan', 'App\Http\Controllers\DependentDropdownController@relawan')->name('relawan');
Route::get('/pendukung-aab', 'App\Http\Controllers\Admin\RegisterController@responden');
Route::post('/pendukung-aab', 'App\Http\Controllers\Admin\RegisterController@storeResponden')->name('register.storeResponden');
Route::get('supporter/export', 'App\Http\Controllers\Admin\SupporterController@export')->name('supporter.export');
Route::get('responden/pemantapan-data', 'App\Http\Controllers\Admin\CustomerController@createComitment')->name('responden.createComitment');
Route::post('responden/pemantapan-data', 'App\Http\Controllers\Admin\CustomerController@storeComitment')->name('responden.storeComitment');
Route::get('responden/pemantapan-data/add/{id}', 'App\Http\Controllers\Admin\CustomerController@addComitment')->name('responden.addComitment');
Route::post('responden/pemantapan-data/add', 'App\Http\Controllers\Admin\CustomerController@storeNewComitment')->name('responden.storeNewComitment');
Route::get('responden/export', 'App\Http\Controllers\Admin\CustomerController@export')->name('responden.export');
Route::get('customer/exportManual', 'App\Http\Controllers\Admin\CustomerController@exportManual')->name('customer.exportManual');
Route::get('quick-count/export', 'App\Http\Controllers\Admin\QuickCountController@export')->name('quick-count.export');
Route::get('quick-count/jumlah-suara-caleg', 'App\Http\Controllers\Admin\QuickCountController@countCaleg')->name('quick-count.countCaleg');
Route::get('quick-count/jumlah-perolehan-suara', 'App\Http\Controllers\Admin\QuickCountController@countSumVote')->name('quick-count.countSumVote'); 

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});

 Route::get('/email/verify', function () {
    return 'Verifikasi email';
})->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
     $user = User::find($id);
        
        if (!$user) {
            return 'Pengguna tidak ditemukan.'; // Penanganan jika pengguna tidak ada
        }

        // Pastikan pengguna ditemukan sebelum mengakses properti pengguna
        $isVerified = $user->hasVerifiedEmail();

        if (!$isVerified) {
            $user->markEmailAsVerified();
            auth()->login($user);
            return redirect('https://chat.whatsapp.com/KYTrZlEAeUrLShVAllZf8C');
        }

        return 'Email sudah diverifikasi sebelumnya.';
})->middleware(['signed'])->name('verification.verify');

require __DIR__ . '/auth.php';
// Auth::routes(['register' => false]);
