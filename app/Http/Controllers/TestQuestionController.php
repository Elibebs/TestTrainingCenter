<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

use App\TestCenter\Api\ApiResponse;
use App\TestCenter\Events\ErrorEvents;
use Illuminate\Http\Request;
use App\TestCenter\Activities\TestQuestionActivity;


class TestQuestionController extends Controller
{
    protected $testQuestionActivity;
    protected $apiResponse;

	public function __construct(ApiResponse $apiResponse,TestQuestionActivity $testQuestionActivity)
    {
        $this->apiResponse = $apiResponse;
    	$this->testQuestionActivity = $testQuestionActivity;
    }

    public function index(Request $request)
    {
        try{
            return  $this->testQuestionActivity->listTestQuestion($request->post());
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function show(Request $request, $id)
    {
        try{
            return  $this->testQuestionActivity->viewTestQuestion($id);
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function store(Request $request)
    {
        try{
            return  $this->testQuestionActivity->addTestQuestion($request->post());
        }
        catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function update(Request $request, $id)
    {
        try{
            return  $this->testQuestionActivity->updateTestQuestion($request->post(), $id);
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function destroy(Request $request, $id)
    {
        try{
            return  $this->testQuestionActivity->deleteTestQuestion($id);
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    // public function listTestSpecialties(Request $request)
    // {
    //     try
    //     {
    //         return  $this->testQuestionActivity->listTestSpecialties($request->post());
    //     }
    //     catch(\Exception $e)
    //     {
    //         ErrorEvents::ServerErrorOccurred($e);
    //         return $this->apiResponse->serverError();
    //     }

    // }

    public function listTestQuestionByTestTypeId(Request $request, $id)
    {
        try
        {
            return  $this->testQuestionActivity->listTestQuestionByTestTypeId($request->post(),$id);
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    }


    public function searchSkillsLists(Request $request)
    {
        try
        {
            return  $this->testQuestionActivity->searchSkillsLists($request->post());
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    }
}
