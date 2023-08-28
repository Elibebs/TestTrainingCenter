<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{

    protected $primaryKey = "id";
    protected $table = "setup.test_type_grades";

    public function testType()
    {
    	return $this->belongsTo('App\Models\TestType', 'test_type_id');
    }
}
