<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\CommentCreateRequest;
use App\Http\Resources\CommentCollection;
use App\Models\Comment;
use App\Models\SubTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //index show comments by subtask id
    public function index($id)
    {
        $comments = Comment::with(['author'])
            ->where('sub_task_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return new CommentCollection($comments);
    }
    /**
     * create/store new comment
     * 
     * @param CommentCreateRequest $request
     * 
    //  * @return JsonResponse
     */
    public function store(CommentCreateRequest $request)
    {
        $validate = $request->validated();

        $exists = SubTask::whereId($validate['sub_task_id'])
            ->first()
            ->members()
            ->where('user_id', auth()->user()->id)
            ->exists();

        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'user not allowed'
            ], 403);
        }

        Comment::create([
            'comment' => $validate['comment'],
            'user_id' => auth()->user()->id,
            'sub_task_id' => $validate['sub_task_id']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'comment succesfully created'
        ], 201);
    }
}
