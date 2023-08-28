<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestRetake extends Model
{

    protected $primaryKey = "id";
    protected $table = "test.worker_test_retakes";

    public function test(){
        return $this->belongsTo('App\Models\Test', 'test_id');
    }

    public function worker(){
        return $this->belongsTo('App\Models\Worker','worker_id');
    }

    public function testResult(){
        return $this->hasOne(TestResult::class, "test_retake_id");
    }
}
