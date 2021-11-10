<?php

namespace App\Notifications;

use App\Models\SubTask;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubTaskNotifiation extends Notification
{
    use Queueable;

    protected $subTask, $user, $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SubTask $subTask, User $user, $messsage)
    {
        $this->subTask = $subTask;
        $this->user = $user;
        $this->message = $messsage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
            ],
            'subTask' => [
                'id' => $this->subTask->id,
                'title' => $this->subTask->title,
            ],
            'message' => $this->message,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
            ],
            'subTask' => [
                'id' => $this->subTask->id,
                'title' => $this->subTask->title,
            ],
            'message' => $this->message,
        ]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('subtask.message.' . $this->user->id);
    }
}
