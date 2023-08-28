<?php

namespace App\TestCenter\Activities;

use App\TestCenter\Repos\TestTypeRepo;
use App\TestCenter\Api\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\TestCenter\Traits\TestTypeTrait;
use App\TestCenter\Utilities\Constants;
use App\TestCenter\Events\AuthEvents;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Utilities\Validator;
use App\TestCenter\Utilities\Generators;



class TestTypeActivity extends BaseActivity
{
    use TestTypeTrait;

    protected $testTypeRepo;
    protected $apiResponse;

    public function __construct(TestTypeRepo $testTypeRepo,ApiResponse $apiResponse){
        $this->testTypeRepo = $testTypeRepo;
        $this->apiResponse = $apiResponse;
    }

    public function listTestTypes($filters)
    {         
    
        $testtypes = $this->testTypeRepo->listTestTypes($filters);
        $message = "Test types retrieved successfully";
        
        return $this->apiResponse->success($message, ["data" => $testtypes]);
    }

    public function getTestType($id)
    {
        $test_type = $this->testTypeRepo->getTestTypeById($id);
        Log::info($test_type);
        if(!$test_type)
        {
           $message = "Test Type with id {$id} does not exist";
           ErrorEvents::apiErrorOccurred($message, "warning");
           return $this->apiResponse->notFoundError($message);
        }

        $message = "TestType : retrieved successfully";
        Log::notice($message);
        return $this->apiResponse->success($message, ["data" => $test_type->toArray()] );
    }

    public function addTestType($data){
       
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams($this->apiAddTestTypeParams, $data);
        if(!empty($missingParams))
        {
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));

            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        // Check if testType name exists if name is specified
        if(isset($data['name']))
        {
            if($this->testTypeRepo->testTypeExists($data['name']))
            {
                $message = "The specified testtype {$data['name']} already exists";
                ErrorEvents::apiErrorOccurred($message, "warning");
                return $this->apiResponse->generalError($message);
            }
        }

        if($testType = $this->testTypeRepo->createTestType($data)){
            $message = "TestType : {$data['name']} added successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $testType]);
        }

        $message = "Unable to add testtype{$data['name']}";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
    }



    public function updateTestType($data,$id){
       
        Log::info($data);

        // Validate request parameters
        $missingParams = Validator::validateRequiredParams($this->apiUpdateTestTypeParams, $data);
        if(!empty($missingParams))
        {
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));
  
            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }
  
        $testtype = $this->testTypeRepo->getTestTypeById($id);

        if(!$testtype) return ApiResponse::notFoundError("Test type not found, please check and try again");

        // Check if test type name exists if test type name is specified
        if(isset($data['name']) && $data['name'] != $testtype->name) 
        {
            if($this->testTypeRepo->testTypeExists($data['name']))
            {
                $message = "The specified testType name {$data['name']} already exists";
                ErrorEvents::apiErrorOccurred($message, "warning");
                return $this->apiResponse->generalError($message);
            }
        }

  
        $testtype = $this->testTypeRepo->updateTestType($data, $testtype);
        if($testtype)
        {
            $message = "TestType : {$data['name']} updated successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $testtype->toArray()] );
        }

        $message = "Unable to update testtype{$data['name']}";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
        
      }

      public function editTestType($data, $id){
          
        $test_type = $this->testTypeRepo->getTestTypeById($id);

        if(!$test_type)
        {
           $message = "Test Type with id {$id} does not exist";
           ErrorEvents::apiErrorOccurred($message, "warning");
           return $this->apiResponse->notFoundError($message);
        }

       // Check if name exists if name is specified
       if(isset($data['name']) && $data['name'] != $test_type->name) 
       {
           if($this->testTypeRepo->testTypeExists($data['name']))
           {
               $message = "The specified test type {$data['name']} already exists";
               ErrorEvents::apiErrorOccurred($message, "warning");
               return $this->apiResponse->generalError($message);
           }
       }

        // fetching test types
        $testtype = $this->testTypeRepo->editTestType($data, $test_type->id);
        if($testtype)
        {
            $message = "test type updated successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $testtype]);
        }

        $errMsg = "Could not update test type";
        ErrorEvents::apiErrorOccurred($errMsg);
        return $this->apiResponse->generalError($errMsg);
      }
    

      public function deleteTestType($id)
      {

        $testtype = $this->testTypeRepo->getTestTypeById($id);
        //check if test type exist
        if(!$testtype){
            $message = "Test type not found";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->notFoundError($message);
        }

        if($testtype->delete())
        {
            $message = "Test type : deleted successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => null] );
        }

        $message = "Unable to delete for {$data['test_type_id']}";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
      }

}