<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;

class Question extends Model
{
  //  use HasFactory;
    //use LogsActivity;

    protected $primaryKey = "id";
    protected $table = "setup.test_questions";
    public    $timestamps = false;


    public function testType(){
    	return $this->hasMany('App\Models\TestType', 'question_id');
    }

    public function testQuestions(){
    	return $this->belongsTo('App\Models\Question', 'specialty_id');
    }

    public function testQuestionsCount(){
        return $this->testQuestions()->count();
    }

    public function answers(){
        return $this->hasMany('App\Models\Answer', 'question_id');
    }

}
