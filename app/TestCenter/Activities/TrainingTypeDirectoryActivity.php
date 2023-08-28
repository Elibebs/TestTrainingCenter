<?php

namespace App\TestCenter\Activities;

use App\TestCenter\Repos\TrainingTypeRepo;
use App\TestCenter\Repos\TrainingTypeDirectoryRepo;
use App\TestCenter\Api\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\TestCenter\Traits\TestTypeTrait;
use App\TestCenter\Utilities\Constants;
use App\TestCenter\Events\AuthEvents;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Utilities\Validator;
use App\TestCenter\Utilities\Generators;



class TrainingTypeDirectoryActivity extends BaseActivity
{
    use TestTypeTrait;

    protected TrainingTypeRepo $trainingTypeRepo;
    protected TrainingTypeDirectoryRepo $directoryRepo;
    protected $apiResponse;

    public function __construct(TrainingTypeRepo $trainingTypeRepo, ApiResponse $apiResponse, TrainingTypeDirectoryRepo $directoryRepo){
        $this->trainingTypeRepo = $trainingTypeRepo;
        $this->apiResponse = $apiResponse;
        $this->directoryRepo = $directoryRepo;
    }

    public function listDirectories($filters)
    {         
    
        $directories = $this->directoryRepo->listDirectories($filters);
        $message = "Training type directories retrieved successfully";
        
        return $this->apiResponse->success($message, ["data" => $directories]);
    }

    public function getDirectory($id)
    {
        $directory = $this->directoryRepo->getDirectoryById( $id );

        if( !$directory )
        {
            return $this->apiResponse->notFoundError("Directory not found");
        }

        $message = "Directory retrieved successfully";
        
        return $this->apiResponse->success($message, ["data" => $directory]);   
    }

    public function createDirectory(Array $data)
    {
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams(['name','training_type_id'], $data);
        if(!empty($missingParams))
        {
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));
  
            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        //Get training type
        $training_type = $this->trainingTypeRepo->getTrainingTypeById( $data['training_type_id'] );
        if( !$training_type )
        {
            return $this->apiResponse->notFoundError("Training type not found");
        }

        //Check if directory name already exist
        if( $this->directoryRepo->getDirectoryByName( $data['name'] ) )
        {
            return $this->apiResponse->generalError("Directory with name {$data['name']} already exist");
        }

        if( $directory = $this->directoryRepo->createDirectory($data) )
        {
            return $this->apiResponse->success( "Training type directory created successfully", ['data' => $directory] );
        }

        return $this->apiResponse->generalError("Something went wrong while trying to create directory");

    }

    public function updateDirectory(Array $data, $id)
    {    
        
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams(['name'], $data);
        if(!empty($missingParams))
        {
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));
    
            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        //Get training type directory
        $directory = $this->directoryRepo->getDirectoryById($id);
        if( !$directory )
        {
            return $this->apiResponse->notFoundError("Directory not found");
        }

        //Validate name
        if( isset( $data['name'] ) && $directory->name != $data['name'] )
        {
            if( $this->directoryRepo->getDirectoryByName( $data['name'] ) )
            {
                return $this->apiResponse->generalError("{$data['name']} already exist");
            }
        }

        if( $directory = $this->directoryRepo->updateDirectory($data, $directory) )
        {
            return $this->apiResponse->success("Directory updated successfully", ['data' => $directory ]);
        }

        return $this->apiResponse->generalError("Something went wrong while trying to update");
    }

    public function deleteDirectory( $id )
    {
        //Get training type directory
        $directory = $this->directoryRepo->getDirectoryById($id);
        if( !$directory )
        {
            return $this->apiResponse->notFoundError("Directory not found");
        }

        if( $directory->delete() )
        {
            return $this->apiResponse->success("Directory deleted successfully", ['data' => null ]);
        }

        return $this->apiResponse->generalError("Something went wrong while trying to delete directory");

    }

}