<?php

namespace App\TestCenter\Activities;

use App\TestCenter\Repos\TrainingTypeRepo;
use App\TestCenter\Api\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\TestCenter\Traits\TestTypeTrait;
use App\TestCenter\Utilities\Constants;
use App\TestCenter\Events\AuthEvents;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Utilities\Validator;
use App\TestCenter\Utilities\Generators;



class TrainingTypeActivity extends BaseActivity
{
    use TestTypeTrait;

    protected TrainingTypeRepo $trainingTypeRepo;
    protected $apiResponse;

    public function __construct(TrainingTypeRepo $trainingTypeRepo, ApiResponse $apiResponse){
        $this->trainingTypeRepo = $trainingTypeRepo;
        $this->apiResponse = $apiResponse;
    }

    public function listTrainingTypes($filters)
    {         
    
        $training_types = $this->trainingTypeRepo->listTrainingTypes($filters);
        $message = "Training types retrieved successfully";
        
        return $this->apiResponse->success($message, ["data" => $training_types]);
    }

    public function getTrainingType($id)
    {
        $training_type = $this->trainingTypeRepo->getTrainingTypeById($id);
        if( !$training_type )
        {
            return $this->apiResponse->notFoundError("Training type not found");
        }

        $message = "Training type retrieved successfully";
        
        return $this->apiResponse->success($message, ["data" => $training_type]);   
    }

    public function isNameExist(String $name)
    {
        return ( $this->trainingTypeRepo->getTrainingTypeByName($name) ) ? true : false;
    }

    public function createTrainingType(Array $data)
    {
       return $this->trainingTypeRepo->createTrainingType($data);
    }

    public function updateTrainingType(Array $data, $id)
    {
        Log::info("Updating trainning");
        
        //Get training type
        $training_type = $this->trainingTypeRepo->getTrainingTypeById($id);
        if( !$training_type )
        {
            return $this->apiResponse->notFoundError("Training type not found");
        }

        //Validate name
        if( isset( $data['name'] ) && $training_type->name != $data['name'] )
        {
            if( $this->isNameExist( $data['name'] ) )
            {
                return $this->apiResponse->generalError("{$data['name']} already exist");
            }
        }

        if( $training_type = $this->trainingTypeRepo->updateTrainingType($data, $training_type) )
        {
            return $this->apiResponse->success("Training type updated successfully", ['data' => $training_type ]);
        }

        return $this->apiResponse->generalError("Something went wrong while trying to update");
    }

}