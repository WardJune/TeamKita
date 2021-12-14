<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubTask\SubTaskCreateRequest;
use App\Http\Resources\SubTaskCollection;
use App\Http\Resources\SubTaskResource;
use App\Jobs\SubtaskNotifJob;
use App\Models\SubTask;
use App\Models\Task;
use App\Notifications\SubTaskNotifiation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class SubTaskController extends Controller
{
    /**
     * get task by task_id and by auth user
     * 
     * @param mixed $id
     * 
     * @return mixed
     */
    public function index($id)
    {
        $exists = Task::whereId($id)->first()
            ->members()
            ->where('user_id', auth()->user()->id)
            ->exists();

        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'user not allowed'
            ], 403);
        }

        $subTasks = SubTask::with('author', 'members')
            ->withCount(['members', 'comments'])
            ->whereTaskId($id)
            ->whereHas('members', function ($q) {
                $q->whereId(auth()->user()->id);
            })
            ->latest()
            ->get();

        return new SubTaskCollection($subTasks->append('comments'));
    }

    /**
     * get spesific subtask
     * 
     * @param mixed $id
     * 
     * @return mixed
     */
    public function show($id)
    {
        $subTask = SubTask::with('author', 'members')
            ->withCount('members')
            ->whereHas('members', function ($q) {
                $q->whereId(auth()->user()->id);
            })
            ->whereId($id)
            ->first();

        if (!$subTask) {
            return response()->json([
                'success' => false,
                'message' => 'subtask not found'
            ], 404);
        }

        return new SubTaskResource($subTask);
    }

    /**
     * store subtask
     * @param SubTaskCreateRequest $request
     * 
     * @return JsonResponse
     */
    public function store(SubTaskCreateRequest $request): JsonResponse
    {
        // check apakah member terdaftar di task (?)
        $validate = $request->validated(); //validate request
        $user = auth()->user(); // auth user
        $member_id = $validate['member_id']; // array member id
        $task = Task::with(['members'])->whereId($validate['task_id'])->first(); // get task by id
        $member_id[] = $user->id; // add author to member

        // check apakah user ada di task
        foreach ($member_id as $member) {
            if (!$task->members()->where('user_id', $member)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "user_id {$member} is not registered in task"
                ], 404);
            }
        }

        //create subtask
        $subTask = SubTask::create([
            'title' => $validate['title'],
            'slug' => \Str::slug($validate['title']),
            'task_id' => $validate['task_id'],
            'author_id' => $user->id,
            'date_start' => $validate['date_start'],
            'date_end' => $validate['date_end']
        ]);
        $subTask->members()->attach($member_id);

        //! notify member
        SubtaskNotifJob::dispatch($subTask, "You have been added to subtask '$subTask->title'");

        return response()->json([
            'success' => true,
            'message' => 'subtask succesfully created'
        ], 201);
    }

    /**
     * update specific subtask and member as well
     * 
     * @param SubTaskCreateRequest $request
     * @param SubTask $subTask
     * 
     * @return JsonResponse
     */
    public function update(SubTaskCreateRequest $request, SubTask $subTask): JsonResponse
    {
        // check user == author (?)

        $user = auth()->user();
        $validate = $request->validated();
        $member_id = $validate['member_id'];
        $task = Task::with(['members'])->whereId($subTask->task_id)->first();
        $member_id[] = $user->id; // add author to member

        //if authorize
        if (!Gate::forUser($user)->allows('subtask', $subTask)) {
            return response()->json([
                'success' => false,
                'message' => 'user not allowed'
            ], 403);
        }

        // check apakah user ada di task
        foreach ($member_id as $member) {
            if (!$task->members()->where('user_id', $member)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "user_id {$member} is not registered in task"
                ], 404);
            }
        }

        //update data
        $subTask->update([
            'title' => $validate['title'],
            'slug' => \Str::slug($validate['title']),
            'date_start' => $validate['date_start'],
            'date_end' => $validate['date_end'],
        ]);

        //! notify member

        $subTask->members()->sync($member_id);

        return response()->json([
            'success' => true,
            'message' => 'subtask succesfully updated'
        ], 200);
    }

    /**
     * update status subtask
     * 
     * @param SubTask $subTask
     * 
     * @return JsonResponse
     */
    public function updateStatus(SubTask $subTask): JsonResponse
    {
        $user = auth()->user();

        //if authorize
        if (!Gate::forUser($user)->allows('subtask', $subTask)) {
            return response()->json([
                'success' => false,
                'message' => 'user not allowed'
            ], 403);
        }

        $subTask->update([
            'status' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'subtask status succesfully updated'
        ], 200);
    }

    /**
     * @param SubTask $subTask
     * 
     * @return JsonResponse
     */
    public function destroy(SubTask $subTask): JsonResponse
    {
        $user = auth()->user();

        //if authorize
        if (!Gate::forUser($user)->allows('subtask', $subTask)) {
            return response()->json([
                'success' => false,
                'message' => 'user not allowed'
            ], 403);
        }
        //! notify member
        SubtaskNotifJob::dispatch($subTask, "subtask '$subTask->title' has been deleted");

        $subTask->members()->detach();
        $subTask->delete();

        return response()->json([
            'success' => true,
            'message' => 'subtask succesfully deleted'
        ], 200);
    }

    /**
     * request to leave subtask
     * 
     * @param SubTask $subTask
     * 
     * @return JsonResponse
     */
    public function leaveSubtask(SubTask $subTask): JsonResponse
    {
        $exists = $subTask->members()->where('user_id', auth()->user()->id)->exists();

        //is user == author
        if ($subTask->author_id == auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => "user is the author, can't leave subtask"
            ], 422);
        }

        //check user terdaftar di subtask
        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'user not allowed'
            ], 403);
        }
        //detach form subtugas
        $subTask->members()->detach(auth()->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'user succesfully leave from subtask'
        ], 200);
    }
}

/* 
// -get sub task based on task id and auth user
// -show spesific task with member
// -create sub task with attach member on it
// -edit sub task with sync member on it
// -delete sub task and member

// request leave task/ subtask
*/
