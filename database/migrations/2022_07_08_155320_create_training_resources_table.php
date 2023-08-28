<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setup.training_resources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->text('description')->nullable();
            $table->string('type');
            $table->text('url')->nullable();
            $table->integer('directory_id')->nullable();
            $table->integer('training_type_id')->nullable();
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
        Schema::dropIfExists('setup.training_resources');
    }
}
