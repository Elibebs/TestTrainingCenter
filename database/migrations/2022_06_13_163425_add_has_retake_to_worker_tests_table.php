<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHasRetakeToWorkerTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test.worker_tests', function (Blueprint $table) {
            $table->boolean('has_retake')->default(true)->after('number_of_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test.worker_tests', function (Blueprint $table) {
            //
        });
    }
}
