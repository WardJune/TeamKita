<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\TaskCreateRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function get()
    {
        $tasks = Task::with('author')
            ->whereHas('members', function ($q) {
                $q->whereId(auth()->user()->id);
            })
            ->get();

        return new TaskCollection($tasks);
    }
    /**
     * Show task by id / code
     * 
     * @param mixed $id
     * @param bool $code
     * 
     * @return mixed
     */
    public function show($id, $code = false)
    {
        if ($code) {
            $task = Task::with('author')->withCount('members')->whereCode($id)->first();
            $append = 'count';
        } else {
            $task = Task::with(['author', 'members'])->whereId($id)->first();
            $append = 'members';
        }

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'task not found'
            ], 404);
        }

        return new TaskResource($task->append($append));
    }

    /**
     * Store task request
     * @param TaskCreateRequest $request
     * 
     * @return JsonResponse 
     */
    public function store(TaskCreateRequest $request): JsonResponse
    {
        $validate = $request->validated();
        $user = auth()->user();

        DB::beginTransaction();

        try {
            $task = Task::create([
                'author_id' => $user->id,
                'title' => $validate['title'],
                'slug' => \Str::slug($validate['title']),
                'code' => \Str::random(6),
            ]);

            $task->members()->attach($user->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'task succesfully created',
                'data' => new TaskResource($task)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    /**
     * Update task request
     * @param Task $task
     * @param TaskCreateRequest $request
     * 
     * @return JsonResponse
     */
    public function update(Task $task, TaskCreateRequest $request): JsonResponse
    {
        $user = auth()->user();
        $validate = $request->validated();

        // is user authorize
        if (!Gate::forUser($user)->allows('task', $task)) {
            return response()->json([
                'success' => false,
                'message' => 'user not allowed'
            ], 403);
        }

        $task->update([
            'title' => $validate['title'],
            'slug' => \Str::slug($validate['title'])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'task succesfully updated'
        ], 200);
    }
    //destroy
    public function destroy(Task $task)
    {
        $user = auth()->user();

        // is user authorize
        if (!Gate::forUser($user)->allows('task', $task)) {
            return response()->json([
                'success' => false,
                'message' => 'user not allowed'
            ], 403);
        }

        $task->members()->detach();
        $task->delete();


        return response()->json([
            'success' => true,
            'message' => 'task succesfully deleted'
        ], 200);
    }

    //invite user/ add member
    public function addMember($code, $user)
    {
        $user = User::whereId($user)->first();
        $task = Task::whereCode($code)->first();

        //is $user && $task exists
        if (!$user || !$task) {
            return response()->json([
                'success' => false,
                'message' => 'task / user not found'
            ], 404);
        }


        $exists = $task->members()->where('user_id', $user->id)->exists();

        // is user already joined the task
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'user already joined the task'
            ], 422);
        }

        //attach user to task
        $task->members()->attach($user);

        return response()->json([
            'success' => true,
            'message' => 'user succesfully added to task'
        ], 200);
    }
}
