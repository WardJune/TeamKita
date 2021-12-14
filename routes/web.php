<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Jobs\NotifJob;
use App\Jobs\SubtaskNotifJob;
use App\Models\{SubTask, Task, User};
use App\Notifications\TaskNotification;
use Illuminate\Support\Facades\Route;


Route::get('coba/notif', function () {
    $user = User::whereId(68)->first();
    $task = Task::whereId(13)->first();
    // $task = SubTask::whereId(8)->first();

    NotifJob::dispatch($task, "$task->title has been updated");
    // SubtaskNotifJob::dispatch($task, "$task->title has been updated");
    // $notif = $user->notify(new TaskNotification($task, $user, 'test'));

    return ['notify sent'];
});

Route::get('email-verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('email.verify');
