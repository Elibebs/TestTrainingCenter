<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

use App\TestCenter\Api\ApiResponse;
use App\TestCenter\Events\ErrorEvents;
use Illuminate\Http\Request;
use App\TestCenter\Activities\TestTypeActivity;


class TestTypeController extends Controller
{
    protected $testTypeActivity;
    protected $apiResponse;

	public function __construct(ApiResponse $apiResponse,TestTypeActivity $testTypeActivity)
    {
        $this->apiResponse = $apiResponse;
    	$this->testTypeActivity = $testTypeActivity;
    }

    public function index(Request $request)
    {
        try
        {
            return  $this->testTypeActivity->listTestTypes($request->post());
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
            return  $this->testTypeActivity->addTestType($request->post());
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
            return  $this->testTypeActivity->updateTestType($request->post(), $id);
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
            return  $this->testTypeActivity->getTestType($id);
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
            return  $this->testTypeActivity->deleteTestType($id);
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    } 

}
