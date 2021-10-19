<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tasks = Task::factory(5)->create();

        foreach ($tasks as $task) {
            $users = User::get()->random(10)->pluck('id');
            $users[] = $task->author_id;

            $task->members()->attach($users);
        }
    }
}
