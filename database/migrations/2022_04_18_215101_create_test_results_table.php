<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test.worker_test_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('test_id');
            $table->integer('test_retake_id')->nullable();
            $table->integer('worker_id');
            $table->integer('total_score');
            $table->integer('grade_id');
            $table->string('grade')->nullable();
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
        Schema::dropIfExists('test.worker_test_results');
    }
}
