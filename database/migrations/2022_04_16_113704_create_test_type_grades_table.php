<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestTypeGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setup.test_type_grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('lower_grade_tier');
            $table->integer('upper_grade_tier');
            $table->integer('test_type_id');
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
        Schema::dropIfExists('setup.test_type_grades');
    }
}
