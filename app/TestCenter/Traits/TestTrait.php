<?php

namespace App\TestCenter\Traits;
use App\TestCenter\Utilities\Generators;


trait TestTrait{

    protected $apiCreateTestParams = [
        "test_type_id",
    ];

    protected $apiEndTestParams = [
        "test_id",
    ];

    protected $apiTestRetakeParams = [
        "test_id",
    ];

    private function getNewTestCode()
    {
        $code = null;
        do{
            $code = Generators::generateTestCode();
        }while($this->testRepo->getTestByCode($code));

        return $code;
    }

    private function updateTestAttempts($test)
    {
        $test->attempt += 1;

        if( count($test->retakes) >= 2)
        {
            $test->has_retake = false;
        }

        $test->update();
    }

}