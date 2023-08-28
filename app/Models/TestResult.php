<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    //
    protected $primaryKey = "id";
    protected $table = "test.worker_test_results";

    public function test(){
        return $this->belongsTo(Test::class, "test_id")->where("test_retake_id", null);
    }

    public function testRetake(){
        return $this->belongsTo(TestRetake::class, "test_retake_id");
    }

    
}
