<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Test extends Model
{

    protected $primaryKey = "id";
    protected $table = "test.worker_tests";

    public function type(){
        return $this->belongsTo('App\Models\TestType', 'test_type_id');
    }

    public function worker(){
        return $this->belongsTo('App\Models\Worker','worker_id');
    }

    public function specialty(){
        return $this->belongsTo('App\Models\Specialty','specialty_id');
    }

    public function retakes(){
        return $this->hasMany(TestRetake::class, "test_id");
    }

    public function testResult()
    {
        return $this->hasOne(TestResult::class, "test_id")->where('test_retake_id', null);
    }

    public function questionAnswers()
    {
        return $this->hasMany(QuestionAnswer::class, "test_id")->where('test_retake_id', NULL);
    }


}
