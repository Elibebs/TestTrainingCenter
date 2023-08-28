<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

use App\TestCenter\Api\ApiResponse;
use App\TestCenter\Events\ErrorEvents;
use Illuminate\Http\Request;
use App\TestCenter\Activities\AnswerActivity;


class AnswerController extends Controller
{
    protected $answerActivity;
    protected $apiResponse;

	public function __construct(ApiResponse $apiResponse,AnswerActivity $answerActivity)
    {
        $this->apiResponse = $apiResponse;
    	$this->answerActivity = $answerActivity;
    }

    public function index(Request $request)
    {
        try{
            return  $this->answerActivity->listAnswers($request->post());
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function show(Request $request, $id)
    {
        try{
            return  $this->answerActivity->viewAnswer($request->post(),$id);
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function store(Request $request)
    {
        try{
            return  $this->answerActivity->addAnswer($request->post());
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function update(Request $request, $id)
    {
        try{
            return  $this->answerActivity->updateAnswer($request->post(),$id);
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function destroy(Request $request, $id)
    {
        try{
            return  $this->answerActivity->deleteAnswer($id);
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }
}
