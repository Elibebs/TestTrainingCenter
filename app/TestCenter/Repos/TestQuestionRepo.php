<?php

namespace App\TestCenter\Repos;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Question;
use App\Models\Specialty;

use App\TestCenter\Utilities\Constants;



class TestQuestionRepo extends AuthRepo
{
    
    public function listTestQuestion($filters){
        $pageSize = $filters['pageSize'] ?? 1000;
        $predicate = Question::query();
        foreach ($filters as $key => $filter) {
            if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
            {
                continue;
            }

            $predicate->where($key, $filter);
        }

        $testquestion = $predicate->with('answers')->paginate($pageSize);

        return $testquestion;
    }

    public function listTestSpecialties($filters){
        $pageSize = $filters['pageSize'] ?? 15;
        $predicate = Specialty::query();//->with('testQuestionsCount');
        foreach ($filters as $key => $filter) {
            if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
            {
                continue;
            }

            $predicate->where($key, $filter);
        }

        $testSpecialties = $predicate->paginate($pageSize);

        foreach($testSpecialties as $key => $testSpecialty){
            $testSpecialties[$key]['questions'] =  $testSpecialty->testQuestions->count();
        //  Log::info($testSpecialties[$key]['testQuestions']);
            // unset($testSpecialty->testQuestions);
        }

        return $testSpecialties;
    }


    public function getQuestionById($id){
        return Question::with('answers')->find($id);
    }

    public function getQuestionWithAnswersById($id){
        return Question::with('answers')->find($id);
    }

    public function listTestQuestionByTestTypeId($data, $id){
        return Question::with('answers')->where("test_type_id", "=" ,$id)->get();
    }

    public function getTestSpecialtyById($id)
    {
        return QuestionSpecialty::where("specialty_id", $id)->first();
    }

    public function getWorkerTestQuestions($filters){
        $pageSize = $filters['pageSize'] ?? 60;
        $predicate = Question::query();
        foreach ($filters as $key => $filter) {
            if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
            {
                continue;
            }
    
            $predicate->where($key, $filter);
         }
    
        $testquestion = $predicate->with('answers')->inRandomOrder()->paginate($pageSize);
    
        return $testquestion;
    }

    public function testQuestionlist()
    {
        return Question::get();
    }


    public function createTestQuestion(Array $data)
    {
        $testquestion = new Question;
        
        $testquestion->question = $data['question'];
        $testquestion->test_type_id = $data['test_type_id'];
        $testquestion->question_type = $data['type'];
        $testquestion->specialty_id = $data['specialty_id']??null;
        $testquestion->instructions = $data['instructions']??null;
        $testquestion->answer_count = $data['answer_count']??1;
        $testquestion->created_at = Carbon::now();
    	$testquestion->updated_at = Carbon::now();

    	if($testquestion->save())
    	{
    		return $testquestion;
    	}
    	return null;
    }

    public function updateTestQuestion($data, $testquestion)
    {
        if(isset($data['question'])) $testquestion->question = $data['question'];
        if(isset($data['test_type_id'])) $testquestion->test_type_id = $data['test_type_id'];
        if(isset($data['type'])) $testquestion->question_type = $data['type'];
        if(isset($data['specialty_id'])) $testquestion->specialty_id = $data['specialty_id'];
        if(isset($data['instructions'])) $testquestion->instructions = $data['instructions'];
        if(isset($data['answer_count'])) $testquestion->answer_count = $data['answer_count'];
       
    	if($testquestion->update())
    	{
    		return $testquestion;
    	}
    	return null;
    }

        public function getTestQuestionById($id)
    {
        return Question::where("id", $id)->first();
    }



    public function deleteTestQuestion($id){

        $testquestion = Question::where('id',$id)->first();
        return $testquestion->delete();
      }


    public function testQuestionExists($name)
    {
        $testquestion = Question::where('name',$name)->first();
        if($testquestion)
            return true;
        else
            return false;
    }

    public function testQuestionByName($name)
    {
    return Question::with('answers')->where('name',$name)->first();
    }

    public function searchSkillsTest(Array $data){
        $created_at=$data['created_at']??null;
        $service_provider=$data['service_provider']??null;
        $test_type=$data['test_type']??null;

 

        $query= Question::query();

        if(isset($created_at)){
            $query->where('created_at', 'ilike', '%'.$created_at.'%');
        }
        if(isset($service_provider)){
            $query->whereHas('worker', function ($query) use ($service_provider){
                $query->where('name', 'ilike', '%' . $service_provider .'%');
            });
        }
        if(isset($test_type)){
            $query->whereHas('type', function ($query) use ($test_type){
                $query->where('name', 'ilike', '%' . $test_type .'%');
            });
      }


        $testSearchResults = $query->orderBy('created_at', 'asc')->get();

        foreach($testSearchResults as $testWorker){
            $worker = $testWorker->worker->get();
        }
        foreach($testSearchResults as $testType){
            $type = $testType->type->get();
        }
        
        return $testSearchResults;
    }

}