<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('login', [AuthController::class, 'login']);
Route::post('send-otp', [AuthController::class, 'sendOTP']);
Route::post('verify-otp', [AuthController::class, 'verifyOTP']);
Route::post('register', [AuthController::class, 'register']);
//  Route::get('registration-form', [AuthController::class, 'registrationForm']);
// Route::post('create-user', [AuthController::class, 'createUser']);
// Route::post('verify-account', [AuthController::class, 'verifyAccount']);
Route::get('get-code', [AuthController::class, 'getCode']); // to be removed
//password reset
Route::prefix('password')->group(function () {
    Route::post('send-code', [AuthController::class, 'sendResetPasswordCode']);   // Send reset code
    Route::post('code/verify', [AuthController::class, 'verifyResetPasswordCode']); // Verify code
    Route::post('set', [AuthController::class, 'setpassword']); // Store new password
});


Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => 'permissions'], function () {
    Route::get('/', [PermissionController::class, 'index']);
    // Route::post('/', [PermissionController::class, 'store']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => 'roles'], function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::get('/{id}', [RoleController::class, 'show']);
    Route::post('/', [RoleController::class, 'store']);
    Route::put('/{id}', [RoleController::class, 'update']);
});
