<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubTaskUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_task_user', function (Blueprint $table) {
            $table->foreignId('sub_task_id')->constrained('sub_tasks');
            $table->foreignId('user_id')->constrained('users');
            $table->primary(['sub_task_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_task_user');
    }
}
