<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TestCenter\Api\ApiResponse;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Activities\WorkerActivity;
use App\TestCenter\Activities\TestActivity;
use App\TestCenter\Activities\TrainingResourceActivity;


class WorkerController extends Controller
{
    protected $apiResponse;
    protected $workerActivity;
    protected $testActivity;
    protected $trainingResourceActivity;

	public function __construct(ApiResponse $apiResponse,WorkerActivity $workerActivity, TestActivity $testActivity, TrainingResourceActivity $trainingResourceActivity)
    {
        $this->apiResponse = $apiResponse;
    	$this->workerActivity = $workerActivity;
        $this->testActivity = $testActivity;
        $this->trainingResourceActivity = $trainingResourceActivity;
    }

    public function register(Request $request)
    {
        try{
            return  $this->workerActivity->register($request->post());
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function login(Request $request)
    {
        try{
            return  $this->workerActivity->login($request->post());
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function listTestTypes(Request $request)
    {
        try{
            return  $this->workerActivity->getTestTypes();
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function listTestQuestions(Request $request)
    {
        try{
            return  $this->workerActivity->getTestQuestions($request->all());
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function listTests(Request $request)
    {
        try{
            return  $this->testActivity->listWorkerTests();
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function startTest(Request $request)
    {
        try{
            return  $this->testActivity->startTest($request->all());
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        } 
    }

    public function questionAnswer(Request $request)
    {
        try{
            return  $this->workerActivity->questionAnswer($request->post());
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        } 
    }

    public function endTest(Request $request)
    {
        try{
            return  $this->testActivity->endTest($request->post());
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        } 
    }

    public function retakeTest(Request $request)
    {
        try{
            return  $this->testActivity->retakeTest($request->post());
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        } 
    }

    public function uploadImage(Request $request)
    {
        try {
            
            return  $this->workerActivity->uploadImage($request->post());

        } catch (\Exception $e) {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError(); 
        }
    }

    public function listTrainingResources( Request $request )
    {
        try {
            
            return  $this->workerActivity->listWorkerTrainingResources( $request->post() );

        } catch (\Exception $e) {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError(); 
        }
    }

    public function listTrainingTypes( Request $request )
    {
        try {
            
            return  $this->workerActivity->listTrainingTypes( $request->post() );

        } catch (\Exception $e) {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError(); 
        }
    }

    public function viewResource( Request $request )
    {
        try 
        {
            
            return  $this->workerActivity->viewResource( $request->post() );

        } 
        catch (\Exception $e) 
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError(); 
        }
    }
    public function workerDetails(Request $request, $id)
    {
        try
        {
            return  $this->workerActivity->getWorkerDetails($id);
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }
    
}
