<?php

namespace App\TestCenter\Traits;


trait TestGradeTrait{

    protected $apiAddTestGradeParams = [
        "name",
        "test_type_id",
        "lower_grade_tier",
        "upper_grade_tier"
    ];

    protected $apiUpdateTestGradeParams = [
        "name"
    ];

    protected $apideleteTestQuestionParams = [
        "test_grade_id"
    ];
}