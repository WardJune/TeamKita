<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TaskNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $task, $user, $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task, User $user, $message)
    {
        $this->task = $task;
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * @param mixed $notifiable
     * 
     * @return [type]
     */
    public function toArray($notifiable)
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'name' => $this->user->name
            ],
            'task' => [
                'id' => $this->task->id,
                'title' => $this->task->title
            ],
            'message' => $this->message
        ];
    }

    /**
     * @param mixed $notifiable
     * 
     * @return [type]
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'name' => $this->user->name
            ],
            'task' => [
                'id' => $this->task->id,
                'title' => $this->task->title
            ],
            'message' => $this->message

        ]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('task.message.' . $this->user->id);
    }
}
