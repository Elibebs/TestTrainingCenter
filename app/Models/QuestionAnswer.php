<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
  //
  protected $primaryKey = "id";
  protected $table = "test.question_answers";

  public function worker(){
    return $this->belongsTo('App\Models\Worker','worker_id');
  }

  public function question(){
    return $this->belongsTo('App\Models\Question', 'question_id');
  }

  public function answer(){
    return $this->belongsTo('App\Models\Answer', 'answer_id');
  }
}
