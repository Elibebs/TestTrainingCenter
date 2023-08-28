<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{

    protected $primaryKey = "id";
    protected $table = "setup.specialties";


    public function testQuestions(){
    	return $this->hasMany('App\Models\Question', 'specialty_id');
    }

    public function testQuestionsCount(){
        return $this->testQuestions()->count();
    }

    public function workers(){
        return $this->belongsToMany(Worker::class,'sp.worker_specialties','specialty_id','worker_id');
    }
}
