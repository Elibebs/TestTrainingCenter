<?php

namespace App\TestCenter\Repos;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Grade;
use App\Models\Question;
use App\Models\TestType;
use App\Models\QuestionSpecialty;

use App\TestCenter\Utilities\Constants;



class TestGradeRepo extends AuthRepo
{
    
    public function testGradelist()
    {
        return Grade::get();
    }


    public function createTestGrade(Array $data)
    {
        $testGrade = new Grade;
        
        $testGrade->name = $data['name'];
        $testGrade->test_type_id = $data['test_type_id'];
        $testGrade->lower_grade_tier = $data['lower_grade_tier'];
        $testGrade->upper_grade_tier = $data['upper_grade_tier'];

    	if($testGrade->save())
    	{
    		return $testGrade;
    	}
    	return null;
    }

    public function editTestGrade($data, $id)
    {
        $testGrade = Grade::where('id', $id)->first();
        
        $testGrade->name = $data['name'];
        $testGrade->lower_grade_tier = $data['lower_grade_tier'];
        $testGrade->upper_grade_tier = $data['upper_grade_tier'];

        if($testGrade->update())
            return $testGrade;
        else
            return null;
    }


    private function updateSpeciality(Array $data, $Grade_id){
        $specialities = isset($data['specialty_id']) ? explode(',', $data['specialty_id']) : [];

        //Log::error($specialities);

        GradeSpecialty::where('Grade_id','=',$Grade_id)->delete();

        $saved_specialites=[];
        foreach ($specialities as $speciality) {
            //dd((int)$speciality);
         if(isset($speciality) && ((int)$speciality > 0)){
                $Grade_speciality = GradeSpecialty::where([['Grade_id','=',$Grade_id],['specialty_id','=',(int)$speciality]])->first();
                if(!isset($Grade_speciality)){
                        $Grade_speciality = new GradeSpecialty();
                        $Grade_speciality->Grade_id = $Grade_id;
                }

                $Grade_speciality->specialty_id = $speciality;
                if($Grade_speciality->save()){
                    array_push($saved_specialites, $Grade_speciality);
                }else{
                     array_push($saved_specialites, null);
                }

                //$saved_specialites[]=$Grade_speciality->save()?$Grade_speciality:null;
          }
        }

        return $saved_specialites;
    }


    public function updateTestGrade($data, $id)
    {
        $testGrade = Grade::where("id", $id)->first();
        
        $testGrade->name = $data['name'];
        if(isset($data['lower_grade_tier'])) $testGrade->lower_grade_tier = $data['lower_grade_tier'];
        if(isset($data['upper_grade_tier']))  $testGrade->upper_grade_tier = $data['upper_grade_tier'];


    	if($testGrade->update())
    	{
    		return $testGrade;
    	}
    	return null;
    }

        public function getTestGradeById($id)
    {
        return Grade::where("id", $id)->first();
    }

    public function getGradeById($data, $id)
    {
        return Grade::where("id", $id)->first();
    }



    public function deleteTestGrade($id){

        $testGrade = Grade::where('id',$id)->first();
        return $testGrade->delete();
      }


 public function testGradeExists($name)
 {
    $testGrade = Grade::where('name',$name)->first();
    if($testGrade)
        return true;
    else
        return false;
}

public function testGradeByName($name)
{
   return Grade::where('name',$name)->first();
}

// public function listTestGrade($filters){
//    $testGrade = Grade::get();

//     return $testGrade;
// }

public function listTestGrade($filters){
    $pageSize = $filters['pageSize'] ?? 15;
    $predicate = Grade::query();
    foreach ($filters as $key => $filter) {
        if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
        {
            continue;
        }

        $predicate->where($key, $filter);
     }
    $testGrade = $predicate->with(['testType' => function ($q) {
        $q->select('id','name');
    }])->paginate($pageSize);

    return $testGrade;
}

public function getTestGrade($id){
    return Grade::find($id);
}

public function getTestGradeByTestTypeId($test_type_id){
    return Grade::where('test_type_id', $test_type_id)->get();
}


public function listTestGradeByTestTypeId($data, $id){
    $testGrades = Grade::where("test_type_id", "=" ,$id)->get();

    return $testGrades;
}

public function getTestQuestionById($id)
{
    return GradeSpecialty::where("specialty_id", $id)->first();
}

}