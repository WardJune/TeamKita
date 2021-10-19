<?php

namespace Database\Factories;

use App\Models\SubTask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubTaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubTask::class;
    public $taskId;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'task_id' => $this->getRandomTask(),
            'author_id' => $this->taskId->members()->get()->random(1)->pluck(['id'])->first(),
            'title' => $this->faker->sentence(3),
            'slug' => \Str::slug($this->faker->sentence(3)),
            'date_start' => now(),
            'date_end' => now()->addDays(2),
        ];
    }

    public function getRandomTask()
    {
        return $this->taskId = Task::get()->random(1)->first();
    }
}
