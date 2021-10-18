<?php

namespace Database\Seeders;

use App\Models\Task;
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
        $data = [
            [
                'title' => 'First Task',
                'slug' => 'first-task',
                'code' => \Str::random(6)
            ],
            [
                'title' => 'Second Task',
                'slug' => 'second-task',
                'code' => \Str::random(6)
            ],
            [
                'title' => 'Third Task',
                'slug' => 'third-task',
                'code' => \Str::random(6)
            ],
        ];

        Task::insert($data);
    }
}
