<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TestCenter\Api\ApiResponse;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Activities\TrainingTypeActivity;
use Illuminate\Support\Facades\Log;

class TrainingTypeController extends Controller
{
    //
    protected $trainingTypeActivity;
    protected $apiResponse;

	public function __construct(ApiResponse $apiResponse, TrainingTypeActivity $trainingTypeActivity)
    {
        $this->apiResponse = $apiResponse;
    	$this->trainingTypeActivity = $trainingTypeActivity;
    }

    public function index(Request $request)
    {
        try
        {
            return  $this->trainingTypeActivity->listTrainingTypes( $request->post() );
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    }

    
    public function store(Request $request)
    {
        // try
        // {
        //     return  $this->testTypeActivity->addTestType($request->post());
        // }
        // catch(\Exception $e)
        // {
        //     ErrorEvents::ServerErrorOccurred($e);
        //     return $this->apiResponse->serverError();
        // }

    } 

    public function update(Request $request, $id)
    {
        Log::info("Updting");
        try
        {
            return  $this->trainingTypeActivity->updateTrainingType($request->post(), $id);
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    } 

    public function show(Request $request, $id)
    {
        try
        {
            return  $this->trainingTypeActivity->getTrainingType($id);
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    } 


    public function destroy(Request $request, $id)
    {
        // try
        // {
        //     return  $this->testTypeActivity->deleteTestType($id);
        // }
        // catch(\Exception $e)
        // {
        //     ErrorEvents::ServerErrorOccurred($e);
        //     return $this->apiResponse->serverError();
        // }
    } 
}
