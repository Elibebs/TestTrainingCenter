<?php

namespace App\TestCenter\Activities;

use App\TestCenter\Repos\TrainingTypeRepo;
use App\TestCenter\Repos\TrainingTypeDirectoryRepo;
use App\TestCenter\Repos\TrainingResourceRepo;
use App\TestCenter\Api\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\TestCenter\Traits\TestTypeTrait;
use App\TestCenter\Utilities\Constants;
use App\TestCenter\Events\AuthEvents;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Utilities\Validator;
use App\TestCenter\Utilities\Generators;
use App\TestCenter\Traits\ImageTrait;



class TrainingResourceActivity extends BaseActivity
{
    use TestTypeTrait, ImageTrait;

    protected TrainingTypeRepo $trainingTypeRepo;
    protected TrainingTypeDirectoryRepo $directoryRepo;
    protected TrainingResourceRepo $resourceRepo;
    protected $apiResponse;

    public function __construct(TrainingTypeRepo $trainingTypeRepo, ApiResponse $apiResponse, TrainingTypeDirectoryRepo $directoryRepo, TrainingResourceRepo $resourceRepo){
        $this->trainingTypeRepo = $trainingTypeRepo;
        $this->apiResponse = $apiResponse;
        $this->directoryRepo = $directoryRepo;
        $this->resourceRepo = $resourceRepo;
    }

    public function listResources($filters)
    {         
    
        $resources = $this->resourceRepo->listResources($filters);
        $message = "Training type resources retrieved successfully";
        
        return $this->apiResponse->success($message, ["data" => $resources]);
    }

    public function getResource( $id )
    {
        $resource = $this->resourceRepo->getResourceById( $id );

        if( !$resource )
        {
            return $this->apiResponse->notFoundError("Resource not found");
        }

        $message = "Resource retrieved successfully";
        
        return $this->apiResponse->success( $message, ["data" => $resource] );   
    }

    public function createResource(Array $data)
    {
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams(['name','description','type','directory_id'], $data);
        if(!empty($missingParams))
        {
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));
  
            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        //Get directory
        $directory = $this->directoryRepo->getDirectoryById( $data['directory_id'] );
        if( !$directory )
        {
            return $this->apiResponse->notFoundError("Directory notsyn found");
        }

        //Get training type
        $data['training_type_id'] = $directory->training_type_id;

        //Check if directory name already exist
        if( $this->resourceRepo->getResourceByName( $data['name'] ) )
        {
            return $this->apiResponse->generalError("Resource with name {$data['name']} already exist");
        }

        //Validate file type & url
        if( strtoupper( $data['type'] ) == Constants::TRAINING_RESOURCE_TYPE_VIDEO )
        {
            if( !isset( $data['url'] ) )
            {
                return $this->apiResponse->generalError("Video url is required");
            }
        }
        else if( strtoupper( $data['type'] ) == Constants::TRAINING_RESOURCE_TYPE_PDF )
        {
            if( !isset( $data['file'] ) && !isset( $data['url'] ) )
            {
                return $this->apiResponse->generalError("PDF should either be a url or file to upload");
            }

            //Check and upload file to AWS S3
            if( isset( $data['file'] ) )
            {
                $this->uploadPdfToS3( $data, $directory );
            }
        }
        else
        {
            return $this->apiResponse->generalError("Type should either be VIDEO or PDF");
        }



        if( $resource = $this->resourceRepo->createResource( $data ) )
        {
            return $this->apiResponse->success( "Training resource created successfully", ['data' => $resource] );
        }

        return $this->apiResponse->generalError("Something went wrong while trying to create resource");

    }

    public function updateResource(Array $data, $id)
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

        //Get training resource
        $resource = $this->resourceRepo->getResourceById($id);
        if( !$resource )
        {
            return $this->apiResponse->notFoundError( "Resource not found" );
        }

        //Validate name
        if( isset( $data['name'] ) && $resource->name != $data['name'] )
        {
            if( $this->resourceRepo->getResourceByName( $data['name'] ) )
            {
                return $this->apiResponse->generalError( "{$data['name']} already exist" );
            }
        }

        //Check if resource is pdf
        if( $resource->type == Constants::TRAINING_RESOURCE_TYPE_PDF )
        {
            if( isset( $data['url'] ) || isset( $data['file'] ) )
            {
                //Delete resource from AWS S3
                $this->deletePDF( $resource->upload_file_name );
            }
        }

        //Get directory
        $directory = null;
        if( isset($data['directory_id'] ) )
        {
            $directory = $this->directoryRepo->getDirectoryById( $data['directory_id'] );
            if( !$directory )
            {
                return $this->apiResponse->notFoundError("Directory not found");
            }
        }

        //Check and upload file to AWS S3
        if( isset( $data['file'] ) )
        {
            $this->uploadPdfToS3( $data, $directory??$resource->directory );
        }


        if( $resource = $this->resourceRepo->updateResource( $data, $resource ) )
        {
            return $this->apiResponse->success( "Resource updated successfully", ['data' => $resource ] );
        }

        return $this->apiResponse->generalError( "Something went wrong while trying to update resource" );
    }

    public function deleteResource( $id )
    {
        //Get training resource
        $resource = $this->resourceRepo->getResourceById( $id );
        if( !$resource )
        {
            return $this->apiResponse->notFoundError( "Resource not found" );
        }

        if( $resource->type == Constants::TRAINING_RESOURCE_TYPE_PDF && $resource->upload_file_name )
        {
            //Delete resource from AWS S3
            $this->deletePDF( $resource->upload_file_name );
        }

        if( $resource->delete() )
        {
            return $this->apiResponse->success("Resource deleted successfully", ['data' => null ]);
        }

        return $this->apiResponse->generalError("Something went wrong while trying to delete resource");

    }

    private function uploadPdfToS3(Array &$data, $directory)
    {
        //Convert and get pdf file
        $pdf = $this->getPDFfromBase64( $data['file'] );
        if( !$pdf )
        {
            return $this->apiResponse->generalError("Could not convert pdf file, please check and try again");
        }

        //Upload pdf file to AWS S3
        $file_name = str_replace(" ", "_", $directory->name)  . "/". time() . '.pdf';
        $path = $this->uploadPDF( $pdf , $file_name );
        if( !$path )
        {
            return $this->apiResponse->generalError("Something went wrong while trying to upload file to AWS S3");
        }
        $data['url'] = $path;
        $data['file_name'] = $file_name;
    }

}