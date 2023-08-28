<?php

namespace App\TestCenter\Traits;


trait TestTypeTrait{

    protected $apiAddTestTypeParams = [
        "name",
        "duration",
        "number_of_questions"
    ];

    protected $apiUpdateTestTypeParams = [
        "name",
    ];

    protected $apideleteTestTypeParams = [
        "test_type_id"
    ];
    protected $apiEditTestTypeParams = [
        "test_type_id"
    ];
}