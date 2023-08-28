<?php

namespace App\TestCenter\Traits;

use App\TestCenter\Utilities\Generators;


trait WorkerTrait{

    protected $registerParams = [
        "name",
        "email",
        "phone_number",
        "country",
        "specialties"
    ];

    protected $loginParams = [
        "phone"
    ];

    protected $answerQuestionParams = [
        "question_id",
        "test_id",
        "answer_id"
    ];

    private function getNewAccessToken()
    {
        $access_token = null;
        do{
            $access_token = Generators::generateAccessToken();
        }while($this->workerRepo->getWorkerByAccessToken($access_token));

        return $access_token;
    }

    private function getNewSessionId()
    {
        $session_id = null;
        do{
            $session_id = Generators::generateSessionId();
        }while($this->workerRepo->getWorkerBySessionId($session_id));

        return $session_id;
    }

    private function getAuthWorker()
    {
        return $this->workerRepo->getWorkerByAccessToken(\Request::header('access-token'));
    }

    private function getTestScore($test_id, $worker_id, $test_retake_id)
    {
        return $this->workerRepo->getQuestionAnswers($test_id, $worker_id, $test_retake_id)->sum('score_value');
    }

    private function getWorkerTests($worker)
    {
        $worker_completed_tests = $this->testRepo->getWorkerCompletedTests($worker->id);
        $worker_completed_retake_tests= $this->testRepo->getWorkerCompletedRetakeTests($worker->id);

        $worker_ongoing_tests = $this->testRepo->getWorkerOngoingTests($worker->id);
        $worker_ongoing_retake_tests = $this->testRepo->getWorkerOngoingRetakeTests($worker->id);

        $worker['completed_tests'] = array_merge( $worker_completed_tests->toArray(), $worker_completed_retake_tests->toArray() );
        $worker['ongoing_tests'] = array_merge( $worker_ongoing_tests->toArray(), $worker_ongoing_retake_tests->toArray() );

        return $worker;
    }

    private function hasPassedTest( $worker )
    {
        //
        $specialties = $worker->specialties;
        foreach( $specialties as $specialty )
        {
            //Get worker

        }
    }


}