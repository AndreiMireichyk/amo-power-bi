<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmoCrmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amo_crms', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('client_id')->nullable();
            $table->string('secret')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->string('expires')->nullable();
            $table->string('base_domain')->nullable();
            $table->timestamps();
        });

        \App\Models\AmoCrm\AmoCrm::create([
            'title'=>'old_sanatoriums',
            'slug'=>'old_sanatoriums',
            'client_id'=>'9e9ea0db-58d4-4048-a678-03c9be643aad',
            'secret'=>'qrEd16kVOxvPQsO7xH4ZKFKVbUkND10PUxZBKUO9XahMRTO80gvm9Bq95S0z11Bj',
            'base_domain'=>'sanatorium.amocrm.ru',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amo_crms');
    }
}
