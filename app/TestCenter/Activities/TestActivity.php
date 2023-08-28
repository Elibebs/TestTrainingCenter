<?php

namespace App\TestCenter\Activities;

use App\TestCenter\Repos\TestRepo;
use App\TestCenter\Repos\WorkerRepo;
use App\TestCenter\Repos\TestTypeRepo;
use App\Models\Question;
use App\Models\Test;
use App\TestCenter\Api\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\TestCenter\Traits\TestTrait;
use App\TestCenter\Traits\WorkerTrait;
use App\TestCenter\Utilities\Constants;
use App\TestCenter\Events\AuthEvents;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Utilities\Validator;
use App\TestCenter\Utilities\Generators;
use Carbon\Carbon;
use App\TestCenter\Repos\TestGradeRepo;



class TestActivity extends BaseActivity
{
    use TestTrait, WorkerTrait;

    protected $testRepo;
    protected $apiResponse;
    protected $workerRepo;
    protected $testTypeRepo;
    protected $gradeRepo;

    public function __construct(ApiResponse $apiResponse,TestRepo $testRepo,WorkerRepo $workerRepo,TestTypeRepo $testTypeRepo, TestGradeRepo $gradeRepo){
        $this->workerRepo = $workerRepo;
        $this->testRepo = $testRepo;
        $this->apiResponse = $apiResponse;
        $this->testTypeRepo = $testTypeRepo;
        $this->gradeRepo  = $gradeRepo;
    }

    public function startTest(Array $data){
       
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams($this->apiCreateTestParams, $data);
        if(!empty($missingParams))
        {
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));

            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        //Get worker
        $worker = $this->getAuthWorker();

        //Get Test Type
        $testType = $this->testTypeRepo->getTestTypeById($data['test_type_id']);
        if(!$testType) return $this->apiResponse->notFoundError("Sorry, test type not found");

        //Check for specialty if test type is skills
        if(strtoupper($testType->name) == Constants::TEST_TYPE_SKILLS && !isset($data['specialty_id']))
            return $this->apiResponse->generalError("A specialty is required to start skills test");


        //Check for ongoing test
        //if($this->testRepo->getWorkerTest($worker->id, $data['test_type_id'], ($data['specialty_id']??null)))
        //    return $this->apiResponse->generalError("Sorry, you already have a test, complete it or do a retake");
            
        //init data
        $data['start_time'] = Carbon::now();
        $data['code'] = $this->getNewTestCode();
        $data['duration'] = $testType->duration;
        $data['number_of_questions'] = $testType->number_of_questions;
        $data['worker_id'] = $worker->id;

        if($test = $this->testRepo->createTest($data)){
            $message = "Test started successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $test]);
        }

        $message = "Something went wrong while trying to start test, please check and try again";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
    }

    public function endTest(Array $data)
    {
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams($this->apiEndTestParams, $data);
        if(!empty($missingParams))
        {
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));

            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        //Get worker
        $worker = $this->getAuthWorker();

        //Get Test
        $test = $this->testRepo->getTestById($data['test_id']);
        if(!$test || ($test->worker_id != $worker->id) ) return $this->apiResponse->notFoundError('Test not found, invalid test id');

        $test_type = $test->type;
        if(!$test_type) return $this->apiResponse->notFoundError('Test type not found, invalid test');

        Log::info('Test Type : ' . $test_type->name . ' ID : ' . $test_type->id);
        $grades = $this->gradeRepo->getTestGradeByTestTypeId($test_type->id);
        Log::info('End Test Grades : ', $grades->toArray());
        if(!$grades || count($grades) < 0) return $this->apiResponse->notFoundError('Grades not found for the test type, invalid test');

        $test_score = $this->getTestScore($test->id, $worker->id, ($data['test_retake_id']??null));

        $grade = null;
        foreach($grades as $gd){
            if($test_score >= $gd->lower_grade_tier && $test_score <= $gd->upper_grade_tier){
                $grade = $gd;
                break;
            }
        }

        if(!$grade) return $this->apiResponse->notFoundError('Testing empty grades here Grades not found for the test score, invalid test');

        //Get test retake
        $test_retake = null;
        if( isset( $data['test_retake_id'] ) )
        {
            $test_retake = $this->testRepo->getTestRetakeById($data['test_retake_id']);
        }

        //init data
        $data['worker_id'] = $worker->id;
        $data['total_score'] = $test_score;
        $data['grade_id'] = $grade->id;
        $data['grade'] = $grade->name;

        if($testResult = $this->testRepo->endTest($data, $test, $test_retake)){
            
            $testResult['test'] = $test;

            $message = "Test ended successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $testResult]);
        }

        $message = "Something went wrong while trying to end test, please check and try again";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
    }

    public function listWorkerTests()
    {
        //Get worker
        $worker = $this->getAuthWorker();

        $worker = $this->getWorkerTests($worker);

        $message = "Test retrived successfully";
        Log::notice($message);
        return $this->apiResponse->success($message, ["data" => ['completed_tests' => $worker['completed_tests'], 'ongoing_tests' => $worker['ongoing_tests'] ] ]);
    }

    public function searchTests(Array $data)
    {
        // $data = $request->post();
      // Attempt to search role
      $searchedTest= $this->testRepo->searchTests($data);
      if($searchedTest)
      {
          $message = "Test : search results";
          Log::notice($message);
          return $this->apiResponse->success($message, ["data" => $searchedTest] );
      }
      else
      {
          $message = "Unable to fetch data";
          ErrorEvents::apiErrorOccurred($message);
          return $this->apiResponse->generalError($message);
      }
    }

    public function listTests($filters)
    {         
        // fetching Questions
        $tests = $this->testRepo->listTests($filters);
        if($tests)
        {
            foreach($tests as $key => $test)
            {
                Log::info("Test ID:");
                Log::info($test->id);
                $test['question_answers'] = $this->testRepo->getTestQuestionAnswers( $test->id, null );
            }
           
            $message = "tests List";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $tests]);
        }

        $errMsg = "Could not get test listings";
        ErrorEvents::apiErrorOccurred($errMsg);
        return $this->apiResponse->generalError($errMsg);
    }

    public function listTestAttempts( $filters, $id )
    {
        $filters['worker_id'] = $id;
        $test_retakes = $this->testRepo->listTestAttempts($filters);
        if( $test_retakes )
        {
            foreach($test_retakes as $key => $test_retake)
            {
                $test_retake['question_answers'] = $this->testRepo->getTestQuestionAnswers( $test_retake->test_id, $test_retake->id );
            }
           
            $message = "Test attempt list";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $test_retakes]);
        }

        $errMsg = "Could not get test retake listings";
        ErrorEvents::apiErrorOccurred($errMsg);
        return $this->apiResponse->generalError($errMsg);
    }


    public function retakeTest(Array $data)
    {
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams($this->apiTestRetakeParams, $data);
        if(!empty($missingParams))
        {
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));

            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        //Get worker
        $worker = $this->getAuthWorker();

        //Get the test
        $test = $this->testRepo->getTestById($data['test_id']);
        if(!$test || $worker->id != $test->worker_id){
            return $this->apiResponse::notFoundError('Test not found');
        }

        //Check if the test can do a retake
        if(!$test->has_retake){
            return $this->apiResponse->generalError("Sorry, looks like you've exausted your test attempts");
        }

        //Check for ongoing retake
        if( $this->testRepo->getOngoingTestRetake($test->id) )
        {
            return $this->apiResponse->generalError("There is an ongoing test retake, kindly complete it.");
        }

        //init data
        $data['start_time'] = Carbon::now();
        $data['duration'] = $test->duration;
        $data['number_of_questions'] = $test->number_of_questions;
        $data['worker_id'] = $worker->id;
        $data['test_id'] = $test->id;

        if($test_retake = $this->testRepo->createTestRetake($data)){

            $this->updateTestAttempts($test);

            $message = "Test started successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $test_retake]);
        }

        $message = "Something went wrong while trying to start test, please check and try again";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
    }


}