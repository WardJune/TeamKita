<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Resources\MessageCollection;
use App\Http\Resources\MessageResource;
use App\Models\ChatMessage;
use App\Models\Task;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function messages($taskId)
    {
        $messages = ChatMessage::with('user')
            ->whereTaskId($taskId)
            ->latest()
            ->get();

        return (new MessageCollection($messages));
    }

    public function sendMessage(Request $request)
    {
        $message = auth()
            ->user()
            ->messages()
            ->create([
                'task_id' => $request->task_id,
                'message' => $request->message
            ]);

        broadcast(new MessageSent($message))->toOthers();

        return (new MessageResource($message));
    }
}
