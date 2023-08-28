<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileUploadNameToTrainingResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setup.training_resources', function (Blueprint $table) {
            //
            $table->string('upload_file_name')->nullable()->after('training_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setup.training_resources', function (Blueprint $table) {
            //
        });
    }
}
