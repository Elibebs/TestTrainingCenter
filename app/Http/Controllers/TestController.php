<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\TestCenter\Api\ApiResponse;
use App\TestCenter\Events\ErrorEvents;
use App\TestCenter\Activities\TestActivity;


class TestController extends Controller
{
    protected $testQuestionActivity;
    protected $apiResponse;

	public function __construct(ApiResponse $apiResponse,TestActivity $testActivity)
    {
        $this->apiResponse = $apiResponse;
    	$this->testActivity = $testActivity;
    }

    public function index(Request $request)
    {
        try{
            return  $this->testActivity->listTests($request->post());
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }
    }

    public function testAttempts( Request $request, $id )
    {
        try{
            return  $this->testActivity->listTestAttempts( $request->post(), $id );
        }catch(\Exception $e){
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        } 
    }

    public function searchTests(Request $request)
    {
        try
        {
            return  $this->testActivity->searchTests($request->post());
        }
        catch(\Exception $e)
        {
            ErrorEvents::ServerErrorOccurred($e);
            return $this->apiResponse->serverError();
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
