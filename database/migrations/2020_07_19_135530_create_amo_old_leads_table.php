<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmoOldLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amo_old_leads', function (Blueprint $table) {
            $table->id();

            $table->integer('amo_old_contact_id');
            $table->text('name')->nullable();
            $table->text('price')->nullable();
            $table->text('responsible_user_id')->nullable();
            $table->text('group_id')->nullable();
            $table->text('status_id')->nullable();
            $table->text('pipeline_id')->nullable();
            $table->text('loss_reason_id')->nullable();
            $table->text('source_id')->nullable();
            $table->text('created_by')->nullable();
            $table->text('updated_by')->nullable();
            $table->text('closest_task_at')->nullable();
            $table->text('is_deleted')->nullable();
            $table->text('score')->nullable();
            $table->text('account_id')->nullable();
            $table->text('created_at')->nullable();
            $table->text('updated_at')->nullable();
            $table->text('closed_at')->nullable();
            $table->longText('raw')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amo_old_leads');
    }
}
