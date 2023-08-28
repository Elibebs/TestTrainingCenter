<?php

namespace App\TestCenter\Repos;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\TestType;
use App\TestCenter\Utilities\Constants;



class TestTypeRepo extends AuthRepo
{
    

    public function listTestTypes($filters){
        $pageSize = $filters['pageSize'] ?? 15;
        $predicate = TestType::query();
        $predicate->withCount('testQuestions');
        foreach ($filters as $key => $filter) {
            if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
            {
                continue;
            }
    
            $predicate->where($key, $filter);
         }
    
        return $predicate->paginate($pageSize);
    }

    public function getTestTypelist()
    {
        return TestType::with('grades')->get();
    }


    public function createTestType(Array $data)
    {
        $test_type = new TestType;
        
        $test_type->name = $data['name'];
        $test_type->duration = $data['duration'];
        $test_type->number_of_questions = $data['number_of_questions'];
        $test_type->description = $data['description']??null;


    	if($test_type->save())
    	{
    		return $test_type;
    	}
    	return null;
    }

    public function updateTestType($data, $testtype)
    {
        $testtype->name = $data['name'];
        if(isset($data['duration'])) $testtype->duration = $data['duration'];
        if(isset($data['number_of_questions'])) $testtype->number_of_questions = $data['number_of_questions'];
        if(isset($data['description'])) $testtype->description = $data['description'];

    	if($testtype->update())
    	{
    		return $testtype;
    	}
    	return null;
    }

    public function editTestType($data, $id)
    {
        $testtype = TestType::where('id', $id)->first();
        
        $testtype->name = $data['name'];
        $testtype->duration = $data['duration'];
        $testtype->pass_score = $data['pass_score'];
        

        if($testtype->update())
            return $testtype;
        else
            return null;
    }

        public function getTestTypeById($id)
    {
        return TestType::where('id', $id)->first();
    }



    public function deleteTestType($id){

        $testtype = TestType::where('id',$id)->first();
        return $testtype->delete();
    }


 public function testTypeExists($name)
 {
    $testtype = TestType::where('name',$name)->first();
    if($testtype)
        return true;
    else
        return false;
}

public function testTypeByName($name)
{
   return TestType::where('name',$name)->first();
}

// public function listTestType($filters){
//    $testtype = TestType::get();

//     return $testtype;
// }



}