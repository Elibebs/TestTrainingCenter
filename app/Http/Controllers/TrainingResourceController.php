<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TestCenter\Api\ApiResponse;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Activities\TrainingResourceActivity;
use Illuminate\Support\Facades\Log;

class TrainingResourceController extends Controller
{
    //
    protected TrainingResourceActivity $resourceActivity;
    protected $apiResponse;

	public function __construct(ApiResponse $apiResponse, TrainingResourceActivity $resourceActivity)
    {
        $this->apiResponse = $apiResponse;
    	$this->resourceActivity = $resourceActivity;
    }

    public function index( Request $request )
    {
        try
        {
            return  $this->resourceActivity->listResources( $request->post() );
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    }

    
    public function store( Request $request )
    {
        try
        {
            return  $this->resourceActivity->createResource( $request->post() );
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
            return  $this->resourceActivity->updateResource( $request->post(), $id );
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    } 

    public function show( Request $request, $id )
    {
        try
        {
            return  $this->resourceActivity->getResource( $id );
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
            return  $this->resourceActivity->deleteResource( $id );
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    } 
}
