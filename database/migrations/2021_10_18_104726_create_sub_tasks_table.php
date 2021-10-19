<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Task::class);
            $table->foreignId('author_id');
            $table->string('title');
            $table->string('slug');
            $table->boolean('status')->default(false);
            $table->timestamp('date_start')->nullable();
            $table->timestamp('date_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_tasks');
    }
}
