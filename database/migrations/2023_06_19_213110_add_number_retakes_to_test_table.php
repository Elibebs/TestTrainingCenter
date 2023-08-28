<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNumberRetakesToTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test.worker_tests', function (Blueprint $table) {
            //
            $table->integer('number_of_retakes')->default(2);
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
            $table->drop('number_of_retakes');
        });
    }
}
