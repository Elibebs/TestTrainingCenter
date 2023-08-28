<?php

namespace App\TestCenter\Traits;


trait TestQuestionTrait{

    protected $apiAddTestQuestionParams = [
        "test_type_id",
        "type",
        "question"
    ];

    protected $apiUpdateTestQuestionParams = [
        "specialty_id",
        "test_type_id",
        "type",
        "question"
    ];

    protected $apideleteTestQuestionParams = [
        "test_question_id"
    ];
}