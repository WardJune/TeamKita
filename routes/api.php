<?php

use App\Http\Controllers\Auth\{
    EmailVerificationNotificationController,
    LoginController,
    LogoutController,
    NewPasswordController,
    RegisterController,
    ResetPasswordController,
};
use App\Http\Controllers\MeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum', 'verified')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', RegisterController::class);
Route::post('login', LoginController::class);
Route::post('reset-password', ResetPasswordController::class);
Route::post('new-password', NewPasswordController::class);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('me', [MeController::class, 'showMe']);
    Route::post('email-verify', EmailVerificationNotificationController::class);
    Route::post('logout', LogoutController::class);
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile/update', [ProfileController::class, 'update']);
    Route::post('profile/change-password', [ProfileController::class, 'changePassword']);
    Route::delete('profile/delete', [ProfileController::class, 'destroy']);
});
