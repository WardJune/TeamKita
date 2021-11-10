<?php

use App\Events\TaskEvent;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskNotification;
use Illuminate\Support\Facades\Route;


Route::get('coba/notif', function () {
    $user = User::whereId(68)->first();
    $task = Task::whereId(13)->first();

    $notif = $user->notify(new TaskNotification($task, $user, 'test'));

    return ['notify sent'];
});

Route::get('email-verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('email.verify');
