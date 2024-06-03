<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'App\Http\Controllers\Admin',
    'prefix' => 'admin',
    'middleware' => ['auth', 'verified'],
], function () {
    Route::resource('permission', 'PermissionController');
    Route::resource('role', 'RoleController');
    Route::resource('user', 'UserController');
    Route::resource('period', 'PeriodController');
    Route::resource('question', 'QuestionController');
    Route::resource('question-sosmed', 'QuestionControllerSosmed');
    Route::resource('responden', 'CustomerController');
    Route::resource('interview', 'InterviewController');
    Route::resource('interview-sosmed', 'InterviewSosmedController');
    Route::resource('schedule', 'ScheduleController');
    Route::resource('grafik', 'GrafikController');
    Route::resource('owner', 'OwnerController');
    Route::resource('admin', 'AdminController');
    Route::resource('relawan', 'VolunteerController');
    Route::resource('relawan-sosmed', 'VolunteerSosmedController');
    Route::resource('kordinator', 'CoordinatorController');
    Route::resource('otp', 'TestingOtpController');
       Route::resource('pemantapan-data', 'CustomerComitmentController');
    // master data
    Route::resource('income', 'IncomeController');
    Route::resource('income', 'IncomeController');
    Route::resource('occupation', 'OccupationController');
    Route::resource('pendukung', 'SupporterController');
    Route::resource('real-count', 'QuickCountController');

    // import from excel
    Route::post('relawan-sosmed/getstatus/{userId}', 'VolunteerSosmedController@getStatus')->name('relawan-sosmed.getStatus');
    Route::post('relawan-sosmed/getStatusKabupaten', 'VolunteerSosmedController@getStatusKabupaten')->name('relawan-sosmed.getStatusKabupaten');
        Route::post('responden/getStatus', 'CustomerController@getStatus')->name('customer.getStatus');
    Route::post('relawan-sosmed/getStatusRecord', 'VolunteerSosmedController@getStatusAll')->name('relawan-sosmed.getStatusAll');
    Route::post('relawan/import', 'VolunteerController@import')->name('relawan.import');
     Route::post('interview/import', 'InterviewController@import')->name('interview.import');
    Route::put('verify/{user}', 'UserController@verify')->name('user.verify');
    Route::get('edit-account-info', 'UserController@accountInfo')->name('admin.account.info');
    Route::post('edit-account-info', 'UserController@accountInfoStore')->name('admin.account.info.store');
    Route::get('change-password', 'UserController@changePassword')->name('admin.account.password');
    Route::post('change-password', 'UserController@changePasswordStore')->name('admin.account.password.store');
  
});
