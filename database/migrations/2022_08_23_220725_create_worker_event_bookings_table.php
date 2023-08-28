<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkerEventBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training.worker_event_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('worker_id');
            $table->integer('event_id');
            $table->string('status')->default('Booked');
            $table->softDeletes();
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
        Schema::dropIfExists('training.worker_event_bookings');
    }
}
