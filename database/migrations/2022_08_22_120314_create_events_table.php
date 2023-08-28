<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training.events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->enum('type', ['Online', 'In-Person']);
            $table->string('location')->nullable();
            $table->date('date');
            $table->time('start_time', $precision = 0);
            $table->time('end_time', $precision = 0);
            $table->string('url')->nullable();
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
        Schema::dropIfExists('training.events');
    }
}
