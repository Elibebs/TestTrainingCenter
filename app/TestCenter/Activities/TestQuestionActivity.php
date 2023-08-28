<?php

namespace App\TestCenter\Activities;

use App\TestCenter\Repos\TestQuestionRepo;
use App\TestCenter\Repos\TestTypeRepo;
use App\Models\Question;
use App\TestCenter\Api\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\TestCenter\Traits\TestQuestionTrait;
use App\TestCenter\Utilities\Constants;
use App\TestCenter\Events\AuthEvents;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Utilities\Validator;
use App\TestCenter\Utilities\Generators;



class TestQuestionActivity extends BaseActivity
{
    use TestQuestionTrait;

    protected $testQuestionRepo;
    protected $testTypeRepo;
    protected $apiResponse;

    public function __construct(TestQuestionRepo $testQuestionRepo,TestTypeRepo $testTypeRepo,ApiResponse $apiResponse){
        $this->testTypeRepo = $testTypeRepo;
        $this->testQuestionRepo = $testQuestionRepo;
        $this->apiResponse = $apiResponse;
    }

    public function addTestQuestion($data){
       
  	// Validate request parameters
      $missingParams = Validator::validateRequiredParams($this->apiAddTestQuestionParams, $data);
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

    //   $test_question = $this->testQuestionRepo->getTestQuestionById($id);

      $addTestQuestion= $this->testQuestionRepo->createTestQuestion($data);
      if($addTestQuestion)
      {
          $message = "TestQuestion :  added successfully";
          Log::notice($message);
          return $this->apiResponse->success($message, ["data" => $addTestQuestion->toArray()] );
      }
      else
      {
          $message = "Unable to add testQuestion";
          ErrorEvents::apiErrorOccurred($message);
          return $this->apiResponse->generalError($message);
      } 
    }



    public function updateTestQuestion($data, $id){
       
        // // Validate request parameters
        // $missingParams = Validator::validateRequiredParams($this->apiUpdateTestQuestionParams, $data);
        // if(!empty($missingParams))
        // {
        //     $errors = Validator::convertToRequiredValidationErrors($missingParams);
        //     ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));
  
        //     return $this->apiResponse->validationError(
        //         ["errors" => $errors]
        //     );
        // }

        $testquestion = $this->testQuestionRepo->getTestQuestionById($id);

        // Check if test question exists if name is specified
        if(!$testquestion){
            $message = "test type not found";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->notFoundError($message);
        }
  
        $testQuestion= $this->testQuestionRepo->updateTestQuestion($data, $testquestion);
        if($testQuestion){
            $message = "TestQuestion : updated successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $testQuestion]);
        }

        $message = "Unable to update testquestion";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
        
      }

      public function deleteTestQuestion($id)
      {
        $testquestion = $this->testQuestionRepo->getTestQuestionById($id);
        //check if test question exist
        if(!$testquestion){
            $message = "Test question not found";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->notFoundError($message);
        }

        if($testquestion->delete()){
            $message = "Test question : deleted successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => null] );
        }

        $message = "Unable to delete test question";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
      }


      public function listTestQuestion($filters)
      {         
          // fetching Questions
          $testquestions = $this->testQuestionRepo->listTestQuestion($filters);
          if($testquestions)
          {
              $message = "test question List";
              Log::notice($message);
              return $this->apiResponse->success($message, ["data" => $testquestions]);
          }
  
          $errMsg = "Could not get test question listings";
          ErrorEvents::apiErrorOccurred($errMsg);
          return $this->apiResponse->generalError($errMsg);
      }

      public function searchSkillsTest(Array $data)
      {
          // $data = $request->post();
        // Attempt to search role
        $searchedTest= $this->testRepo->searchSkillsTest($data);
        if($searchedTest)
        {
            $message = "Test : searched skills tests results";
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


      public function listTestSpecialties($filters)
      {         
          // fetching Specialties
          $testSpecialties = $this->testQuestionRepo->listTestSpecialties($filters);
          if($testSpecialties)
          {
              $message = "test specialties List";
              Log::notice($message);
              return $this->apiResponse->success($message, ["data" => $testSpecialties]);
          }
  
          $errMsg = "Could not get test specialties listings";
          ErrorEvents::apiErrorOccurred($errMsg);
          return $this->apiResponse->generalError($errMsg);
      }


      public function viewTestQuestion($id)
      {
          // fetching test question details
          $testquestion = $this->testQuestionRepo->getQuestionById($id);
          if($testquestion)
          {
              $message = "test question details";
              Log::notice($message);
              return $this->apiResponse->success($message, ["data" => $testquestion]);
          }
  
          $errMsg = "Could not get question details";
          ErrorEvents::apiErrorOccurred($errMsg);
          return $this->apiResponse->generalError($errMsg);
      }


      public function listTestQuestionByTestTypeId($data, $id)
      {
          // fetching test questions by test type Id 
          $testquestion = $this->testQuestionRepo->listTestQuestionByTestTypeId($data, $id);
          if($testquestion)
          {
              $message = "test question details";
              Log::notice($message);
              return $this->apiResponse->success($message, ["data" => $testquestion]);
          }
  
          $errMsg = "Could not get question details";
          ErrorEvents::apiErrorOccurred($errMsg);
          return $this->apiResponse->generalError($errMsg);
      }


}