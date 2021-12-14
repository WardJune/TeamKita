<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\TaskCreateRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Jobs\NotifJob;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index()
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
            $task = Task::with(['author', 'members'])
                ->withCount('members')
                ->whereId($id)
                ->first();
            $append = ['members', 'count'];
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

        //! notify user if task updated
        NotifJob::dispatch($task, "$task->title has been updated");

        return response()->json([
            'success' => true,
            'message' => 'task succesfully updated'
        ], 200);
    }
    //destroy
    /**
     * destroy task
     * @param Task $task
     * 
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        $user = auth()->user();

        // is user authorize
        if (!Gate::forUser($user)->allows('task', $task)) {
            return response()->json([
                'success' => false,
                'message' => 'user not allowed'
            ], 403);
        }
        //! notify members
        NotifJob::dispatch($task, "$task->title has been deleted");

        //detach member
        $task->members()->detach();

        //delete related subtasks and detach member
        foreach ($task->subtasks()->get() as $subtask) {
            $subtask->members()->detach();
            $subtask->delete();
        }
        //delete task
        $task->delete();


        return response()->json([
            'success' => true,
            'message' => 'task succesfully deleted'
        ], 200);
    }

    //invite user/ add member
    /**
     * @param mixed $code
     * @param mixed $user
     * 
     * @return mixed
     */
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

        //! notify this user 
        $user->notify(new TaskNotification($task, $user, "You have been added to $task->title"));

        return response()->json([
            'success' => true,
            'message' => 'user succesfully added to task'
        ], 200);
    }

    /**
     * leave task
     * @param Task $task
     * 
     * @return JsonResponse
     */
    public function leaveTask(Task $task): JsonResponse
    {
        $user = auth()->user();
        $exists = $task->members()->where('user_id', $user->id)->exists();
        //is auth user == author
        if ($task->author_id == $user->id) {
            return response()->json([
                'success' => false,
                'message' => "user is the author, can't leave task"
            ], 422);
        }

        //check user terdaftar di task
        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'user not allowed'
            ], 403);
        }

        //leave task
        $task->members()->detach($user->id);

        $subtasks = $task->subtasks()->get();
        foreach ($subtasks as $sub) {
            if ($sub->members()->where('user_id', $user->id)->exists()) {
                $sub->members()->detach($user->id);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'user successfully leave from task and subtask'
        ], 200);

        //if user has created sub task on it ? delete all subtask ?
        //  iyo , leave dari member
        //  ora, masih jadi member di subtask meski sudah leave task
    }
}
