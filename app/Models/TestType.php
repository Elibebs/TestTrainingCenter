<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;

class TestType extends Model
{
    // use LogsActivity;

    protected $primaryKey = "id";
    protected $table = "setup.test_types";

    public function grades(){
        return $this->hasMany('App\Models\Grade', 'test_type_id');
    }

    public function testQuestions(){
    	return $this->hasMany('App\Models\Question', 'test_type_id');
    }
}
