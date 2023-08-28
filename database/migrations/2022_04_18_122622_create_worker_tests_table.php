<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkerTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test.worker_tests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('worker_id');
            $table->integer('test_type_id');
            $table->integer('specialty_id')->nullable();
            $table->string('code')->nullable();
            $table->integer('attempt')->default(1);
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
        Schema::dropIfExists('test.worker_tests');
    }
}
