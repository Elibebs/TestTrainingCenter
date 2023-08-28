<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setup.test_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('question_type')->default('SINGLE');
            $table->text('question');
            $table->text('instructions')->nullable();
            $table->integer('answer_count')->default(1);
            $table->integer('test_type_id');
            $table->integer('specialty_id')->nullable();
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
        Schema::dropIfExists('setup.test_questions');
    }
}
