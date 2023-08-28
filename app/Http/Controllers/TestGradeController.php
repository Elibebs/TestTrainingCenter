<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

use App\TestCenter\Api\ApiResponse;
use App\TestCenter\Events\ErrorEvents;
use Illuminate\Http\Request;
use App\TestCenter\Activities\TestGradeActivity;


class TestGradeController extends Controller
{
    protected $testGradeActivity;
    protected $apiResponse;

	public function __construct(ApiResponse $apiResponse,TestGradeActivity $testGradeActivity)
    {
        $this->apiResponse = $apiResponse;
    	$this->testGradeActivity = $testGradeActivity;
    }

    public function index(Request $request)
    {
        try{
            return  $this->testGradeActivity->listTestGrade($request->post());
        }
        catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    }

    
    public function store(Request $request)
    {
        try
        {
            return  $this->testGradeActivity->addTestGrade($request->post());
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
            return  $this->testGradeActivity->updateTestGrade($request->post(),$id);
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    }
    
    public function getTestGradeById(Request $request, $id)
    {
        try
        {
            return  $this->testGradeActivity->getTestGradeById($request->post(),$id);
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    }

    public function updateTestGrade(Request $request)
    {
        try
        {
            return  $this->testGradeActivity->updateTestGrade($request->post());
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
            return  $this->testGradeActivity->deleteTestGrade($id);
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
            return  $this->testGradeActivity->getTestGrade($id);
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    }

}
