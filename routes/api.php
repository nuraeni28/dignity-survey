<?php

use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\InterviewController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MasterController;
use App\Http\Controllers\API\RegionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerificationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('forgot', 'forgot');
});

Route::get('provinces', [RegionController::class, 'provinces']);
Route::get('cities', [RegionController::class, 'cities']);
Route::get('districts', [RegionController::class, 'districts']);
Route::get('villages', [RegionController::class, 'villages']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('questions', [QuestionController::class, 'index']);
    Route::get('questionsSosmed', [QuestionController::class, 'questionSosmed']);
    Route::get('master/incomes', [MasterController::class, 'incomes']);
    Route::get('master/occupations', [MasterController::class, 'occupations']);
    Route::post('profile', [AuthController::class, 'updateUser']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::put('profile/password', [AuthController::class, 'updatePassword']);
    Route::get('customers', [CustomerController::class, 'index']);
    Route::post('customers', [CustomerController::class, 'store']);
   
    Route::post('customersSecond', [CustomerController::class, 'storeSecond']);
     Route::post('customersThird', [CustomerController::class, 'store']);
    Route::get('schedule', [CustomerController::class, 'schedule']);
    Route::post('create_schedule', [CustomerController::class, 'createSchedule']);
    Route::post('interviews', [InterviewController::class, 'store']);
    Route::post('interviewsSecond', [InterviewController::class, 'storeSecond']);
    Route::get('active_period', [InterviewController::class, 'period']);
    Route::get('user-target-interviews', [AuthController::class, 'userTargetInterviews']);
    Route::post('sendOTP', [AuthController::class, 'sendOTP']);
    Route::post('checkNumberPhone', [AuthController::class, 'checkNumberPhone']);
    Route::post('sendOTPAgain', [AuthController::class, 'sendOTPAgain']);
    Route::post('checkOTP', [AuthController::class, 'checkOTP']);
    Route::post('checkOTPNew', [AuthController::class, 'checkOTPNew']);
    Route::post('checkOTPSecond', [AuthController::class, 'checkOTPSecond']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('interviewByUser', [InterviewController::class, 'getInterviewDataUser']);
    Route::post('checkStatus', [AuthController::class, 'checkStatus']);
    Route::post('sendOTPWhatsApp', [AuthController::class, 'sendOTPWhatsApp']);
    Route::post('storeEvidence', [CustomerController::class, 'storeEvidenceComitment']);
     Route::get('listCustomer', [CustomerController::class, 'listCustomer']);
    //   Route::post('updateCustomer', [CustomerController::class, 'updateCustomer']);
        Route::post('updateOfflineCustomer', [CustomerController::class, 'updateOfflineCustomer']);
       Route::post('customersNew', [CustomerController::class, 'storeNew']);
        Route::get('customersNew', [CustomerController::class, 'customerSecond']);
   Route::post('updateCustomer', [CustomerController::class, 'updateCustomer']);
   Route::post('checkPhoneCustomer', [CustomerController::class, 'checkPhoneCustomer']);
    Route::post('storeNewResponden', [CustomerController::class, 'storeNewResponden']);
 Route::post('additionalDataByUser', [CustomerController::class, 'getAdditionalDataUser']);

 
});
       

 Route::get('customersSecond', [CustomerController::class, 'customer']);
   
 Route::get('customersThird', [CustomerController::class, 'customerSecond']);

 Route::get('profileSecond', [AuthController::class, 'profileSecond']);
 Route::post('interviewResponden', [InterviewController::class, 'getInterviewDataResponden']);
    Route::post('sendOTPSecond', [AuthController::class, 'sendOTPSecond']);
      Route::post('checkNumberPhoneSecond', [AuthController::class, 'checkNumberPhoneSecond']);
    

// Route::get('verify-email/{id}/{hash}', [VerificationController::class, 'verify'])
//     ->middleware(['signed', 'throttle:6,1'])
//     ->name('verification.verify');

