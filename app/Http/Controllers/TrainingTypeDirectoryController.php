<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TestCenter\Api\ApiResponse;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Activities\TrainingTypeDirectoryActivity;
use Illuminate\Support\Facades\Log;

class TrainingTypeDirectoryController extends Controller
{
    //
    protected TrainingTypeDirectoryActivity $directoryActivity;
    protected $apiResponse;

	public function __construct(ApiResponse $apiResponse, TrainingTypeDirectoryActivity $directoryActivity)
    {
        $this->apiResponse = $apiResponse;
    	$this->directoryActivity = $directoryActivity;
    }

    public function index(Request $request)
    {
        try
        {
            return  $this->directoryActivity->listDirectories( $request->post() );
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    }

    
    public function store(Request $request)
    {
        try
        {
            return  $this->directoryActivity->createDirectory( $request->post() );
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    } 

    public function update(Request $request, $id)
    {
        try
        {
            return  $this->directoryActivity->updateDirectory($request->post(), $id);
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
            return  $this->directoryActivity->getDirectory($id);
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    } 


    public function destroy(Request $request, $id)
    {
        try
        {
            return  $this->directoryActivity->deleteDirectory($id);
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    } 
}
