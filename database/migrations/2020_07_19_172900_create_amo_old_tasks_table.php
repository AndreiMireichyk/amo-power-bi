<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmoOldTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amo_old_tasks', function (Blueprint $table) {
            $table->id();
            $table->text('text')->nullable();
            $table->integer('group_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('duration')->nullable();
            $table->string('entity_type')->nullable();
            $table->integer('entity_id')->nullable();
            $table->boolean('is_completed')->nullable();
            $table->integer('task_type_id')->nullable();
            $table->text('result')->nullable();
            $table->integer('account_id')->nullable();
            $table->integer('responsible_user_id')->nullable();
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
        Schema::dropIfExists('amo_old_tasks');
    }
}
