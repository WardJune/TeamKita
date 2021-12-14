<?php

namespace App\Jobs;

use App\Notifications\TaskNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $task, $message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($task, $message)
    {
        $this->task = $task;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // return false;
        foreach ($this->task->members as $member) {
            $member->notify(new TaskNotification($this->task, $member, $this->message));
        }
    }
}
