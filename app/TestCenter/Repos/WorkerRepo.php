<?php

namespace App\TestCenter\Repos;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Question;
use App\Models\Worker;
use App\Models\QuestionAnswer;
use App\Models\WorkerResourceView;

use App\TestCenter\Utilities\Constants;



class WorkerRepo
{
    
    public function getWorkerById( $id )
    {
        return Worker::find( $id );
    }

    public function getWorkerByEmail(String $email)
    {
        return Worker::where('email', $email)->first();
    }

    public function getWorkerByPhoneNumber(String $phone)
    {
        return Worker::where('phone_number', $phone)->first();
    }

    public function getWorkerByAccessToken(String $access_token)
    {
        return Worker::where('access_token', $access_token)->first();
    }

    public function getWorkerBySessionId(String $session_id)
    {
        return Worker::where('session_id', $session_id)->first();
    }

    public function getWorkerResourceView( $worker_id, $resource_id )
    {
        return WorkerResourceView::where('worker_id', $worker_id)
                                ->where('resource_id', $resource_id)
                                ->first();
    }

    public function isAccessTokenValid(String $access_token)
    {
        if($this->getWorkerByAccessToken($access_token)) 
            return true;
        else
            return false;
        
    }

    public function isSessionIdValid(String $session_id){
        
        if( $this->getWorkerBySessionId($session_id) )
            return true;
        else 
            return false;
    }

    public function createWorker(Array $data)
    {
        $worker = new Worker;
        
        $worker->name = $data['name'];
        $worker->email = $data['email'];
        $worker->phone_number = $data['phone_number'];
        $worker->country = $data['country'];
        $worker->access_token = $data['access_token'];
        $worker->session_id = $data['session_id'];
        $worker->session_id_time = date('Y-m-d H:i:s',strtotime("+".env('SESSION_ID_LIFETIME_DAYS', 1)." days",time()));
        $worker->last_logged_in = date("Y-m-d H:i:s");

    	if($worker->save())
    	{
    		return $worker;
    	}
    	return null;
    }

    public function login(Array $data, Worker $worker)
    {
        $worker->access_token = $data['access_token'];
        $worker->session_id = $data['session_id'];
        $worker->session_id_time = date('Y-m-d H:i:s',strtotime("+".env('SESSION_ID_LIFETIME_DAYS', 1)." days",time()));
        $worker->last_logged_in = date("Y-m-d H:i:s");

        if($worker->save()){
            return $worker;
        }

        return null;
    }

    public function getAnsweredQuestion($test_id, $worker_id, $question_id, $test_retake_id)
    {
        return QuestionAnswer::where([
                    ['test_id', $test_id],
                    ['worker_id', $worker_id],
                    ['question_id', $question_id],
                    ['test_retake_id', $test_retake_id],
                 ])->first();
    }

    public function getQuestionAnswers($test_id, $worker_id, $test_retake_id)
    {
        return QuestionAnswer::where([
            ['test_id', $test_id],
            ['worker_id', $worker_id],
            ['test_retake_id', $test_retake_id],
         ])->get();
    }

    public function answerQuestion(Array $data)
    {
        $question_answer = new QuestionAnswer;

        $question_answer->test_id = $data['test_id'];
        $question_answer->question_id = $data['question_id'];
        $question_answer->answer_id = $data['answer_id'];
        $question_answer->worker_id = $data['worker_id'];
        $question_answer->question = $data['question'];
        $question_answer->answer = $data['answer'];
        $question_answer->score_value = $data['score_value'];
        $question_answer->test_retake_id = $data['test_retake_id']??null;

        if($question_answer->save()){
            return $question_answer;
        }

        return null;
    }

    public function viewResource( Array $data )
    {
        $resource_view = new WorkerResourceView;

        $resource_view->worker_id = $data['worker_id'];
        $resource_view->resource_id = $data['training_resource_id'];

        if( $resource_view->save() )
        {
            return $resource_view;
        }

        return null;
    }

    // public function getWhereAccessTokenAndSessionId()
    // {
    //     $entity = Worker::get();
    //     if(isset($entity) && isset($entity->image)) {
    //         $entity['image_url'] = url("/api/image/". $entity->image->name);
    //         //unset($entity['image']);
    //     }
    // Log::notice($entity);
    //     return $entity;
    // }

    public function getWorkerDetails($id){
        $worker = Worker::where('id',$id)->get();
        //Log::info($worker);

        return $worker;
    }

}