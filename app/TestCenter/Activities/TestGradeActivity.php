<?php

namespace App\TestCenter\Activities;

use App\TestCenter\Repos\TestQuestionRepo;
use App\TestCenter\Repos\TestGradeRepo;
use App\TestCenter\Repos\TestTypeRepo;
use App\Models\Grade;
use App\Models\Question;
use App\TestCenter\Api\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\TestCenter\Traits\TestGradeTrait;
use App\TestCenter\Utilities\Constants;
use App\TestCenter\Events\AuthEvents;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Utilities\Validator;
use App\TestCenter\Utilities\Generators;



class TestGradeActivity extends BaseActivity
{
    use TestGradeTrait;

    protected $testGradeRepo;
    protected $testTypeRepo;
    protected $apiResponse;

    public function __construct(TestGradeRepo $testGradeRepo,TestTypeRepo $testTypeRepo,ApiResponse $apiResponse){
        $this->testTypeRepo = $testTypeRepo;
        $this->testGradeRepo = $testGradeRepo;
        $this->apiResponse = $apiResponse;
    }

    public function addTestGrade($data){
       
  	// Validate request parameters
      $missingParams = Validator::validateRequiredParams($this->apiAddTestGradeParams, $data);
      if(!empty($missingParams))
      {
          $errors = Validator::convertToRequiredValidationErrors($missingParams);
          ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));

          return $this->apiResponse->validationError(
              ["errors" => $errors]
          );
      }

      $testtype = $this->testTypeRepo->getTestTypeById($data["test_type_id"]);
      //check if test type exist
      if(!$testtype){
          $message = "test type not found";
          ErrorEvents::apiErrorOccurred($message);
          return $this->apiResponse->notFoundError($message);
      }

    //   $test_Grade = $this->testGradeRepo->getTestGradeById($id);

      $addTestGrade= $this->testGradeRepo->createTestGrade($data);
      if($addTestGrade)
      {
          $message = "TestGrade :  added successfully";
          Log::notice($message);
          return $this->apiResponse->success($message, ["data" => $addTestGrade->toArray()] );
      }
      else
      {
          $message = "Unable to add testGrade";
          ErrorEvents::apiErrorOccurred($message);
          return $this->apiResponse->generalError($message);
      } 
    }



    public function updateTestGrade($data, $id){
       
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams($this->apiUpdateTestGradeParams, $data);
        if(!empty($missingParams))
        {
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));
  
            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        $testGrade = $this->testGradeRepo->getTestGradeById($id);

        // Check if test Grade exists if name is specified
        if(!$testGrade){
            $message = "test type not found";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->notFoundError($message);
        }

  
        $addTestGrade= $this->testGradeRepo->updateTestGrade($data, $testGrade->id);
        if($addTestGrade)
        {
            $message = "TestGrade : {$data['name']} updated successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $addTestGrade] );
        }

        $message = "Unable to update testGrade{$data['test_Grade_id']}";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
      }

      public function getTestGradeById($data, $id)
      {
          // fetching test grade by test type Id 
          $testGrade = $this->testGradeRepo->getGradeById($data, $id);
          if($testGrade)
          {
              $message = "test grade details";
              Log::notice($message);
              return $this->apiResponse->success($message, ["data" => $testGrade]);
          }
  
          $errMsg = "Could not get grade details";
          ErrorEvents::apiErrorOccurred($errMsg);
          return $this->apiResponse->generalError($errMsg);
      }

      public function deleteTestGrade($id)
      {

        $testGrade = $this->testGradeRepo->getTestGradeById($id);
        //check if test Grade exist
        if(!$testGrade){
            $message = "Test Grade not found";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->notFoundError($message);
        }

        if($testGrade->delete())
        {
            $message = "Test Grade : deleted successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => null] );
        }

        $message = "Unable to delete for {$data['test_Grade_id']}";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);

      }


      public function listTestGrade($filters)
      {         
          // fetching Grades
          $testGrades = $this->testGradeRepo->listTestGrade($filters);
          if($testGrades)
          {
              $message = "test Grade List";
              Log::notice($message);
              return $this->apiResponse->success($message, ["data" => $testGrades]);
          }
  
          $errMsg = "Could not get test Grade listings";
          ErrorEvents::apiErrorOccurred($errMsg);
          return $this->apiResponse->generalError($errMsg);
      }


      public function getTestGrade($id)
      {
          // fetching test Grade details
          $testGrade = $this->testGradeRepo->getTestGrade($id);
          if($testGrade)
          {
              $message = "test Grade details";
              Log::notice($message);
              return $this->apiResponse->success($message, ["data" => $testGrade]);
          }
  
          $errMsg = "Could not get Grade details";
          ErrorEvents::apiErrorOccurred($errMsg);
          return $this->apiResponse->generalError($errMsg);
      }


      public function listTestGradeByTestTypeId($data, $id)
      {
          // fetching test Grades by test type Id 
          $testGrade = $this->testGradeRepo->listTestGradeByTestTypeId($data, $id);
          if($testGrade)
          {
              $message = "test Grade details";
              Log::notice($message);
              return $this->apiResponse->success($message, ["data" => $testGrade]);
          }
  
          $errMsg = "Could not get Grade details";
          ErrorEvents::apiErrorOccurred($errMsg);
          return $this->apiResponse->generalError($errMsg);
      }


}