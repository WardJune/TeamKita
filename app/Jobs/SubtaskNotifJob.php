<?php

namespace App\Jobs;

use App\Notifications\SubTaskNotifiation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubtaskNotifJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subTask, $message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subTask, $message)
    {
        $this->subTask = $subTask;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->subTask->members as $member) {
            $member->notify(new SubTaskNotifiation($this->subTask, $member, $this->message));
        }
    }
}
