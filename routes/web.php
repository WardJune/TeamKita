<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\SubTaskController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('email-verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('email.verify');

// Route::get('/test', function () {
//     $member = [1, 2, 3, 4];

//     return view('coba', [
//         'member' => $member
//     ]);
// });

// Route::post('/test', function () {
//     $member = request()->member;

//     return $member;
// })->name('coba');
