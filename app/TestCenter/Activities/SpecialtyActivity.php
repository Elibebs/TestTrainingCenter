<?php

namespace App\TestCenter\Activities;

use App\TestCenter\Repos\SpecialtyRepo;
use App\TestCenter\Api\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\TestCenter\Utilities\Constants;
use App\TestCenter\Events\AuthEvents;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Utilities\Validator;
use App\TestCenter\Utilities\Generators;



class SpecialtyActivity extends BaseActivity
{

    protected $specialtyRepo;
    protected $apiResponse;

    public function __construct(SpecialtyRepo $specialtyRepo, ApiResponse $apiResponse){
        $this->specialtyRepo = $specialtyRepo;
        $this->apiResponse = $apiResponse;
    }

    public function listSpecialties($filters)
    {         
    
        $specialties = $this->specialtyRepo->listSpecialties($filters);
        $message = "Specialties retrieved successfully";
        
        return $this->apiResponse->success($message, ["data" => $specialties]);
    }

    public function getSpecialty($id)
    {
        $specialty = $this->specialtyRepo->getSpecialty($id);
    
        if(!$specialty)
        {
           $message = "Specialty with id {$id} does not exist";
           ErrorEvents::apiErrorOccurred($message, "warning");
           return $this->apiResponse->notFoundError($message);
        }

        $message = "Specialty : retrieved successfully";
        Log::notice($message);
        return $this->apiResponse->success($message, ["data" => $specialty] );
    }

    public function createSpecialty($data){
       
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams(["name", "ayuda_specialty_id"], $data);
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
            if($this->specialtyRepo->getSpecialtyByName($data['name']))
            {
                $message = "The specified specialty {$data['name']} already exists";
                ErrorEvents::apiErrorOccurred($message, "warning");
                return $this->apiResponse->generalError($message);
            }
        }

        if($specialty = $this->specialtyRepo->createSpecialty($data)){
            $message = "Specialty : {$data['name']} added successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $specialty]);
        }

        $message = "Unable to add specialty {$data['name']}";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
    }



    public function updateSpecialty($data,$id){
       
        Log::info($data);

        // Validate request parameters
        $missingParams = Validator::validateRequiredParams(["name", "ayuda_specialty_id"], $data);
        if(!empty($missingParams))
        {
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));
  
            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }
  
        $specialty = $this->specialtyRepo->getSpecialty($id);

        if(!$specialty) return ApiResponse::notFoundError("Specialty not found, please check and try again");

        // Check if test type name exists if test type name is specified
        if(isset($data['name']) && $data['name'] != $specialty->name) 
        {
            if($this->specialtyRepo->getSpecialtyByName($data['name']))
            {
                $message = "The specified specialty name {$data['name']} already exists";
                ErrorEvents::apiErrorOccurred($message, "warning");
                return $this->apiResponse->generalError($message);
            }
        }

  
        $specialty = $this->specialtyRepo->updateSpecialty($data, $specialty);
        if($specialty)
        {
            $message = "Specialty : {$data['name']} updated successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $specialty] );
        }

        $message = "Unable to update specialty{$data['name']}";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
        
      }


      public function deleteSpecialty($id)
      {

        $specialty = $this->specialtyRepo->getSpecialty($id);
        
        //check if specialty exist
        if(!$specialty){
            $message = "Specialty not found";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->notFoundError($message);
        }

        if($specialty->delete())
        {
            $message = "Specialty : deleted successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => null] );
        }

        $message = "Unable to delete specialty";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
      }

}