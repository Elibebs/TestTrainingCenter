<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

use App\TestCenter\Api\ApiResponse;
use App\TestCenter\Events\ErrorEvents;
use Illuminate\Http\Request;
use App\TestCenter\Activities\SpecialtyActivity;


class SpecialtyController extends Controller
{
    protected $specialtyActivity;
    protected $apiResponse;

	public function __construct(ApiResponse $apiResponse,SpecialtyActivity $specialtyActivity)
    {
        $this->apiResponse = $apiResponse;
    	$this->specialtyActivity = $specialtyActivity;
    }

    public function index(Request $request)
    {
        try
        {
            return  $this->specialtyActivity->listSpecialties($request->post());
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
            return  $this->specialtyActivity->createSpecialty($request->post());
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
            return  $this->specialtyActivity->updateSpecialty($request->post(), $id);
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
            return  $this->specialtyActivity->getSpecialty($id);
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
            return  $this->specialtyActivity->deleteSpecialty($id);
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    } 

}
