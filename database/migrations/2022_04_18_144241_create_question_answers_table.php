<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test.question_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('test_id');
            $table->integer('test_retake_id')->nullable();
            $table->integer('worker_id');
            $table->integer('question_id');
            $table->text('question')->nullable();
            $table->integer('answer_id');
            $table->text('answer')->nullable();
            $table->integer('score_value')->nullable();
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
        Schema::dropIfExists('test.question_answers');
    }
}
