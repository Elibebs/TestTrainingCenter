<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

use App\TestCenter\Api\ApiResponse;
use App\TestCenter\Events\ErrorEvents;
use Illuminate\Http\Request;
use App\TestCenter\Activities\EventActivity;


class EventController extends Controller
{
    protected $eventActivity;
    protected $apiResponse;

	public function __construct(ApiResponse $apiResponse, EventActivity $eventActivity)
    {
        $this->apiResponse = $apiResponse;
    	$this->eventActivity = $eventActivity;
    }

    public function index(Request $request)
    {
        try{
            return  $this->eventActivity->listEvents( $request->post() );
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function getJsonEvents( Request $request )
    {
        try{
            return  $this->eventActivity->listEventsJson( $request->post() );
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function upcomingEvents( Request $request )
    {
        try{
            return  $this->eventActivity->listUpcomingEvents();
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
            return  $this->eventActivity->createEvent( $request->post() );
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function update(Request $request, $id)
    {
        try{
            return  $this->eventActivity->updateEvent($request->post(), $id);
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function destroy(Request $request, $id)
    {
        try{
            return  $this->eventActivity->deleteEvent($id);
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function bookEvent( Request $request )
    {
        try{
            return  $this->eventActivity->bookEvent( $request->post() );
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }
}
