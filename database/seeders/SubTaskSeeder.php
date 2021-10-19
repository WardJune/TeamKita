<?php

namespace Database\Seeders;

use App\Models\SubTask;
use App\Models\Task;
use Illuminate\Database\Seeder;

class SubTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subtasks = SubTask::factory(1)->create();

        foreach ($subtasks as $subtask) {
            $users = Task::whereId($subtask->task_id)->first()->members()->get()->random(5)->pluck(['id']);
            $users[] = $subtask->author_id;

            $subtask->members()->attach($users);
        }
    }
}
