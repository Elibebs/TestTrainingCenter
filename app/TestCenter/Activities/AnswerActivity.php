<?php

namespace App\TestCenter\Activities;

use App\TestCenter\Repos\TestQuestionRepo;
use App\TestCenter\Repos\AnswerRepo;
use App\Models\Question;
use App\Models\Answer;
use App\TestCenter\Api\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\TestCenter\Traits\AnswerTrait;
use App\TestCenter\Utilities\Constants;
use App\TestCenter\Events\AuthEvents;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Utilities\Validator;
use App\TestCenter\Utilities\Generators;



class AnswerActivity extends BaseActivity
{
    use AnswerTrait;

    protected $testQuestionRepo;
    protected $answerRepo;
    protected $apiResponse;

    public function __construct(TestQuestionRepo $testQuestionRepo,AnswerRepo $answerRepo,ApiResponse $apiResponse){
        $this->answerRepo = $answerRepo;
        $this->testQuestionRepo = $testQuestionRepo;
        $this->apiResponse = $apiResponse;
    }

    public function addAnswer($data){
       
  	// Validate request parameters
      $missingParams = Validator::validateRequiredParams($this->apiAddAnswerParams, $data);
      if(!empty($missingParams))
      {
          $errors = Validator::convertToRequiredValidationErrors($missingParams);
          ErrorEvents::apiErrorOccurred("Validation error, " . join(";", $errors));

          return $this->apiResponse->validationError(
              ["errors" => $errors]
          );
      }

      $testquestion = $this->testQuestionRepo->getTestQuestionById($data["question_id"]);
      //check if test question exist
      if(!$testquestion){
          $message = "test question not found";
          ErrorEvents::apiErrorOccurred($message);
          return $this->apiResponse->notFoundError($message);
      }

    //   $test_question = $this->testQuestionRepo->getTestQuestionById($id);

      $addAnswer = $this->answerRepo->createAnswer($data);
      if($addAnswer)
      {
          $message = "Answer :  added successfully";
          Log::notice($message);
          return $this->apiResponse->success($message, ["data" => $addAnswer->toArray()] );
      }
      else
      {
          $message = "Unable to add Answer";
          ErrorEvents::apiErrorOccurred($message);
          return $this->apiResponse->generalError($message);
      } 
    }



    public function updateAnswer($data, $id){

        $answer = $this->answerRepo->getAnswerById($id);
        // Check if answer exists if answer_id is specified
        if(!$answer){
            $message = "Answer not found";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->notFoundError($message);
        }

        $updateAnswer= $this->answerRepo->updateAnswer($data, $answer);
        if($updateAnswer)
        {
            $message = "Answer : updated successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => $updateAnswer->toArray()] );
        }
        else
        {
            $message = "Unable to update answer}";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->generalError($message);
        } 
      }

      public function deleteAnswer($id)
      {

        $answer = $this->answerRepo->getAnswer($id);
        //check if test answer exist
        if(!$answer){
            $message = "test answer not found";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->notFoundError($message);
        }

        if($answer->delete())
        {
            $message = "Answer : deleted successfully";
            Log::notice($message);
            return $this->apiResponse->success($message, ["data" => null] );
        }
        else
        {
            $message = "Unable to delete answer";
            ErrorEvents::apiErrorOccurred($message);
            return $this->apiResponse->generalError($message);
        }

      }


      public function listAnswers($filters)
      {         
          // fetching answers
          $testanswers = $this->answerRepo->listAnswers($filters);
          if($testanswers)
          {
              $message = "answers List";
              Log::notice($message);
              return $this->apiResponse->success($message, ["data" => $testanswers]);
          }
  
          $errMsg = "Could not get test answer listings";
          ErrorEvents::apiErrorOccurred($errMsg);
          return $this->apiResponse->generalError($errMsg);
      }


      public function viewAnswer($data, $id)
      {
          // fetching test answer details
          $answer = $this->answerRepo->getAnswer($id);
          if($answer)
          {
              $message = "test answer details";
              Log::notice($message);
              return $this->apiResponse->success($message, ["data" => $answer]);
          }
  
          $errMsg = "Could not get answer details";
          ErrorEvents::apiErrorOccurred($errMsg);
          return $this->apiResponse->generalError($errMsg);
      }

}