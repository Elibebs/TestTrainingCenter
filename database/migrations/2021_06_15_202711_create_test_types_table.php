<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTestTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS setup');

        Schema::create('setup.test_types', function (Blueprint $table) {
            $table->integer('id',true);
            $table->string('name');
            $table->integer('duration');
            $table->integer('number_of_questions');
            $table->string('description')->nullable();
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
        Schema::dropIfExists('setup.test_types');
        DB::statement('DROP SCHEMA IF EXISTS setup');
    }
}
