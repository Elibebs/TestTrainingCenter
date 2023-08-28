<?php

namespace App\TestCenter\Repos;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Question;
use App\Models\TestType;
use App\Models\Test;
use App\Models\Answer;
use App\Models\QuestionSpecialty;
use App\Models\Specialty;
use App\TestCenter\Utilities\Constants;
use App\Models\TestResult;
use App\Models\TestRetake;
use App\Models\QuestionAnswer;



class TestRepo 
{


    public function getTestByCode(String $code)
    {
        return Test::where('code', $code)->first();
    }

    public function getTestById($id)
    {
        return Test::find($id);
    }

    public function getTestRetakeById($id)
    {
        return TestRetake::find($id);
    }

    public function getOngoingTest($worker_id, $test_type_id, $specialty_id)
    {
        return Test::where('test_type_id', $test_type_id)
            ->where('specialty_id', $specialty_id)
            ->where('end_time', null)
            ->where('worker_id', $worker_id)
            ->where('attempt', '>' , 50)
            ->first();
    }

    public function getWorkerTest($worker_id, $test_type_id, $specialty_id)
    {
        return Test::where('test_type_id', $test_type_id)
            ->where('specialty_id', $specialty_id)
            ->where('worker_id', $worker_id)
            ->first();
    }

    public function searchTests(Array $data){
        $created_at=$data['created_at']??null;
        $service_provider=$data['service_provider']??null;
        $test_type=$data['test_type']??null;

 

        $query= Test::query();

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

    public function getOngingTestById($id, $worker_id)
    {
        return Test::where('id', $id)
            ->where('end_time', null)
            ->where('worker_id', $worker_id)
            ->first();
    }

    public function getWorkerTestBySpecialtyId($worker_id, $specialty_id)
    {
        return Test::with('testResult:id,total_score,grade,test_id')
                ->where('worker_id', $worker_id)
                ->where('specialty_id', $specialty_id)
                ->first();
    }

    public function getWorkerTestByTestTypeId($worker_id, $test_type_id)
    {
        return Test::with('testResult:id,total_score,grade,test_id')
                ->where('worker_id', $worker_id)
                ->where('test_type_id', $test_type_id)
                ->first();
    }

    public function getWorkerRetakeTests($worker_id, $test_id)
    {
        return TestRetake::with('testResult:id,total_score,grade,test_retake_id')
                ->where('worker_id', $worker_id)
                ->where('test_id', )
                ->get();
    }

    public function getWorkerCompletedTests($worker_id)
    {
        return Test::with('testResult:id,total_score,grade,test_id')
                ->where('worker_id', $worker_id)
                ->where('end_time', '!=', null)
                ->get();
    }

    public function getWorkerCompletedRetakeTests($worker_id)
    {
        return TestRetake::with('testResult:id,total_score,grade,test_retake_id')
                ->where('worker_id', $worker_id)
                ->where('end_time', '!=', null)
                ->get();
    }

    public function getWorkerOngoingTests($worker_id)
    {
        return Test::where('worker_id', $worker_id)
                ->where('end_time', null)
                ->get();
    }

    public function getWorkerOngoingRetakeTests($worker_id)
    {
        return TestRetake::where('worker_id', $worker_id)
                ->where('end_time', null)
                ->get();
    }


    public function getOngoingTestRetake($test_id)
    {
        return TestRetake::where('test_id', $test_id)
                        ->where('end_time', null)
                        ->first();
    }

    public function getTestQuestionAnswers( $test_id, $test_retake_id = null )
    {
        return QuestionAnswer::where("test_id", $test_id)->get(['id', 'question_id', 'question', 'answer', 'score_value']);
    }

    public function listTests($filters)
    {
        $pageSize = $filters['pageSize'] ?? 15;
        $predicate = Test::query();
        foreach ($filters as $key => $filter) {
            if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
            {
                continue;
            }

            $predicate->where($key, $filter);
        }

        $tests = $predicate->with(['worker' => function ($q) {
            $q->select('id','name', 'email', 'phone_number');
        }])->with(['type' => function ($q) {
            $q->select('id','name');
        }])->with(['specialty' => function ($q) {
            $q->select('id','name');
        }])->with(['testResult' => function ( $q ) {
            $q->select('id','total_score', 'grade');
        }])
        ->paginate($pageSize);

        return $tests;
    }

    public function listTestAttempts( $filters )
    {
        $pageSize = $filters['pageSize'] ?? 15;
        $predicate = TestRetake::query();

        foreach ($filters as $key => $filter) 
        {
            if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
            {
                continue;
            }

            $predicate->where($key, $filter);
        }

        $tests = $predicate->with(['worker' => function ($q) {
            $q->select('id','name', 'email', 'phone_number');
        }])
        ->with(['testResult' => function ( $q ) {
            $q->select('id','total_score', 'grade');
        }])
        ->paginate($pageSize);

        return $tests;
    }



    public function createTest(Array $data)
    {
        $test = new Test;
        $test->test_type_id = $data['test_type_id'];
        $test->specialty_id = $data['specialty_id']??null;
        $test->start_time = $data['start_time'];
        $test->duration = $data['duration'];
        $test->number_of_questions = $data['number_of_questions'];
        $test->worker_id = $data['worker_id'];
        $test->code = $data['code'];

        if($test->save())
            return $test;
        else
            return null;
    }

    public function endTest(Array $data, $test, $test_retake){

        $testResult = new TestResult;
        $testResult->test_id = $data['test_id'];
        $testResult->test_retake_id = $data['test_retake_id']??null;
        $testResult->worker_id = $data['worker_id'];
        $testResult->total_score = $data['total_score'];
        $testResult->grade_id = $data['grade_id'];
        $testResult->grade = $data['grade'];

        if($testResult->save()){

            if( isset( $data['test_retake_id'] ) ){
                $test_retake->end_time = Carbon::now();
                $test_retake->save();
            }else{
                $test->end_time = Carbon::now();
                $test->save();
            }


            return $testResult;
        }

        return null;
    }

    public function createTestRetake(Array $data)
    {
        $test = new TestRetake;
        $test->start_time = $data['start_time'];
        $test->duration = $data['duration'];
        $test->number_of_questions = $data['number_of_questions'];
        $test->worker_id = $data['worker_id'];
        $test->test_id = $data['test_id'];

        if($test->save())
            return $test;
        else
            return null;
    }
}