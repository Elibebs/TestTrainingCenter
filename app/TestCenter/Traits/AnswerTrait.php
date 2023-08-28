<?php

namespace App\TestCenter\Traits;


trait AnswerTrait{

    protected $apiAddAnswerParams = [
        "question_id",
        "answer",
        "score_value"
    ];

    protected $apiUpdateAnswerParams = [
        "answer",
        "score_value",
        "answer_id"
    ];

    protected $apideleteAnswerParams = [
        "answer_id"
    ];
}