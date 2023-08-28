<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkerTestRetakesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test.worker_test_retakes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('test_id');
            $table->integer('worker_id');
            $table->datetimetz('start_time')->nullable();
            $table->datetimetz('end_time')->nullable();
            $table->integer('duration');
            $table->integer('number_of_questions');
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
        Schema::dropIfExists('test.worker_test_retakes');
    }
}
