<?php

namespace App\TestCenter\Activities;

use App\TestCenter\Repos\WorkerRepo;
use App\TestCenter\Api\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\TestCenter\Traits\WorkerTrait;
use App\TestCenter\Utilities\Constants;
use App\TestCenter\Events\AuthEvents;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Utilities\Validator;
use App\TestCenter\Repos\SpecialtyRepo;
use App\TestCenter\Repos\TestTypeRepo;
use App\TestCenter\Repos\TestQuestionRepo;
use App\TestCenter\Repos\TestRepo;
use Illuminate\Support\Facades\Storage;
use App\TestCenter\Traits\ImageTrait;
use App\TestCenter\Repos\TrainingResourceRepo;
use App\TestCenter\Repos\TrainingTypeRepo;




class WorkerActivity extends BaseActivity
{
    use WorkerTrait, ImageTrait;

    protected $workerRepo;
    protected $apiResponse;
    protected $specialtyRepo;
    protected $testTypeRepo;
    protected $testQuestionRepo;
    protected $testRepo;
    protected $trainingResourceRepo;

    public function __construct(WorkerRepo $workerRepo, ApiResponse $apiResponse, SpecialtyRepo $specialtyRepo, 
                                TestTypeRepo $testTypeRepo, TestQuestionRepo $testQuestionRepo, TestRepo $testRepo,
                                TrainingResourceRepo $trainingResourceRepo, TrainingTypeRepo $trainingTypeRepo){
        $this->workerRepo = $workerRepo;
        $this->apiResponse = $apiResponse;
        $this->specialtyRepo = $specialtyRepo;
        $this->testTypeRepo = $testTypeRepo;
        $this->testQuestionRepo = $testQuestionRepo;
        $this->testRepo = $testRepo;
        $this->trainingResourceRepo = $trainingResourceRepo;
        $this->trainingTypeRepo = $trainingTypeRepo;
    }

    /*********************************************************************
     * START GETTERS
     * 
     *********************************************************************/
    public function getTestTypes()
    {
        $worker = $this->getAuthWorker();

        //Get test types
        $testTypes = $this->testTypeRepo->getTestTypelist();

        //Traverse and set specialties for skills
        foreach($testTypes as $key => $testType){
            
            if(strtolower($testType->name) == 'skills'){
                $testTypes[$key]['specialties'] = $worker->specialties;

                foreach($testTypes[$key]['specialties'] as $k => $specialty) {
                    $test = $this->testRepo->getWorkerTestBySpecialtyId($worker->id, $specialty->id);
                    if($test){
                        $test['retakes'] = $this->testRepo->getWorkerRetakeTests($worker->id, $test->id);
                    }
                    $testTypes[$key]['specialties'][$k]['test'] = $test;
                }
            } else {
                $test = $this->testRepo->getWorkerTestByTestTypeId($worker->id, $testType->id);
                if($test){
                    $test['retakes'] = $this->testRepo->getWorkerRetakeTests($worker->id, $test->id);
                }
                $testTypes[$key]['test'] = $test;
            }
        }

        $message = "Test types retrieved successfully";
        Log::notice($message);
        return $this->apiResponse->success($message, ["data" => $testTypes]);
    }


    public function listTrainingTypes( $filters )
    {
        $trainingTypes = $this->trainingTypeRepo->fetchTrainingTypesWithDirectories( $filters );

        $message = "Training types retrieved successfully";
        Log::notice($message);
        return $this->apiResponse->success($message, ["data" => $trainingTypes]);
    }


    public function listWorkerTrainingResources( $filters )
    {
        $worker = $this->getAuthWorker();

        $trainingResources = $this->trainingResourceRepo->fetchWorkerTrainingResources( $filters, $worker->id );

        $message = "Training resources retrieved successfully";
        Log::notice($message);
        return $this->apiResponse->success($message, ["data" => $trainingResources]);
    }

    public function viewResource( Array $data )
    {
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams(['training_resource_id'], $data);
        if(!empty($missingParams)){
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));

            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        //Get Resource
        $resource = $this->trainingResourceRepo->getResourceById( $data['training_resource_id'] );
        if( !$resource )
        {
            return $this->apiResponse->notFoundError("Resource not found ");
        }

        //Get worker
        $worker = $this->getAuthWorker();

        //Check if worker already viewed this resource
        if( $this->workerRepo->getWorkerResourceView( $worker->id, $resource->id ) )
        {
            return $this->apiResponse->generalError("Sorry, this resource has already been viewed by sp");
        }

        $data['worker_id'] = $worker->id;
        if( $resource_view = $this->workerRepo->viewResource( $data ) )
        {
            return $this->apiResponse->success("Resource view successful", ['data' => $resource_view]);
        }

        return $this->apiResponse->generalError("Something went wrong while trying save resource view");
    }


    public function getTestQuestions($filters)
    {

        $testQuestions = $this->testQuestionRepo->getWorkerTestQuestions($filters);
        
        $question_count = 0;
        foreach($testQuestions as $key => $testQuestion){
            $testQuestions[$key]['question_number'] = $question_count += 1;
        }

        $message = "Test types retrieved successfully";
        Log::notice($message);
        return $this->apiResponse->success($message, ["data" => $testQuestions]);

    }
 
    /*********************************************************************
     * END GETTERS
     * BEGIN ACTIONS
     *********************************************************************/

    public function register(Array $data){
       
  	    // Validate request parameters
        $missingParams = Validator::validateRequiredParams($this->registerParams, $data);
        if(!empty($missingParams)){
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));

            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        if($this->workerRepo->getWorkerByEmail($data['email'])){
            $message = "Email already exist";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->generalError($message);
        }

        if($this->workerRepo->getWorkerByEmail($data['phone_number'])){
            $message = "Phone number already exist";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->generalError($message);
        }

        if(!isset($data['access_token'])){
            $data['access_token'] = $this->getNewAccessToken();
        }

        if(!isset($data['session_id'])){
            $data['session_id'] = $this->getNewSessionId();
        }

        //Get specialties
        $worker_specialties = explode(",", $data['specialties']);
        $specialties = $this->specialtyRepo->getSpecialtiesByIds($worker_specialties);
        if(!$specialties || count($specialties) <= 0) return $this->apiResponse->notFoundError("Worker specialty is required");

        if($worker = $this->workerRepo->createWorker($data)){
            $worker->specialties()->attach($worker_specialties);
            $message = "Account registered successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $worker]);
        }

        $message = "Something went wrong while trying to register account";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
        
    }

    public function login(Array $data)
    {
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams($this->loginParams, $data);
        if(!empty($missingParams)){
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));

            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        $worker = $this->workerRepo->getWorkerByPhoneNumber($data['phone']);
        if(!$worker){
            $message = "Account with phone number does not exist";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->notFoundError($message);
        }

        if(!isset($data['access_token'])){
            $data['access_token'] = $this->getNewAccessToken();
        }

        if(!isset($data['session_id'])){
            $data['session_id'] = $this->getNewSessionId();
        }

        if($worker = $this->workerRepo->login($data, $worker))
        {
            $worker = $this->getWorkerTests($worker);

            $message = "Account session generated successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $worker]);
        }

        $message = "Something went wrong while trying to activate account session";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
    }

    /*********************************************************************
    * WORKER ANSWER QUESTION
    * questionAnswer(Array $data)
    *********************************************************************/
    public function questionAnswer(Array $data)
    {
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams($this->answerQuestionParams, $data);
        if(!empty($missingParams)){
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));

            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        //Get worker
        $worker = $this->getAuthWorker();

        //Get the test taking by worker
        $test = $this->testRepo->getTestById($data['test_id']);
        if(!$test || $test->worker_id != $worker->id) 
            return $this->apiResponse->notFoundError("Sorry, test not found, please check and try again");

        Log::info('---------- Test --------------');
        Log::info($test);

        //Get the question worker is about to answer
        $question = $this->testQuestionRepo->getQuestionWithAnswersById($data['question_id']);
        Log::info('---------- Question --------------');
        Log::info($question);
        if(!$question || $test->test_type_id != $question->test_type_id) 
            return $this->apiResponse->notFoundError("Sorry,wrong question selected, please check and try again");

        //Check if question already has an answer
        if($this->workerRepo->getAnsweredQuestion($test->id, $worker->id, $question->id, ($data['test_retake_id']??null) ))
            return $this->apiResponse->notFoundError("Sorry, question already answered, please check and try again");  
        
        //Get the answer worker is about to
        $answer = null; 
        foreach($question->answers as $ans){
            if($ans->id == $data['answer_id']){
                $answer = $ans;
                break;
            }
        }
        if(!$answer) return $this->apiResponse->notFoundError("Sorry, the selected answer could be not found");

        //init data
        $data['question'] = $question->question;
        $data['answer'] = $answer->answer;
        $data['score_value'] = $answer->value;
        $data['worker_id'] = $worker->id;
        $data['test_retake_id'] = $data['test_retake_id']??null;

        if($question_answer = $this->workerRepo->answerQuestion($data)){
            $message = "Question answered successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $question_answer]);
        }

        $message = "Something went wrong while trying to answer question";
        ErrorEvents::apiErrorOccurred($message);
        return $this->apiResponse->generalError($message);
    }


    /*********************************************************************
    * WORKER UPLOAD TEST IMAGE
    * uploadImage(Request $request)
    *********************************************************************/
    public function uploadImage(Array $data)
    {
        // Validate request parameters
        $missingParams = Validator::validateRequiredParams($this->imageUploadParams, $data);
        if(!empty($missingParams))
        {
            $errors = Validator::convertToRequiredValidationErrors($missingParams);
            ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));

            return $this->apiResponse->validationError(
                ["errors" => $errors]
            );
        }

        $test_image_path = "test_".$data['test_id'];

        if( isset( $data['test_retake_id'] ) )
        {
            $test_image_path = $test_image_path . "/test_retake_" . $data["test_retake_id"]; 
        }

        $image = $this->getImage( $data['image'] );

        $image_name = "gh/$test_image_path/" . "sp_test_image_" . time() . ".jpg";

        $path = $this->uploadImageS3( $image_name , $image );
        
        return $this->apiResponse->success("Image Upload successful", ['data' => ['image_url' => $path]]);
    }


    public function getWorkerDetails($id)
    {
        // fetching worker details
        $worker = $this->workerRepo->getWorkerDetails($id);
        if($worker)
        {
            $message = "Worker Details";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $worker]);
        }

        $errMsg = "Could not get worker details";
        ErrorEvents::apiErrorOccurred($errMsg);
        return $this->apiResponse->generalError($errMsg);
    } 

}