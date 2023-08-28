<?php

namespace App\TestCenter\Repos;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Question;
use App\Models\Answer;

use App\TestCenter\Utilities\Constants;



class AnswerRepo extends AuthRepo
{
    
    public function createAnswer(Array $data)
    {
        $answer = new Answer;
        
        $answer->question_id = $data['question_id'];
        $answer->answer = $data['answer'];
        $answer->value = $data['score_value'];

    	if($answer->save())
    	{
    		return $answer;
    	}
    	return null;
    }


    public function updateAnswer($data, $answer)
    {   
        Log:info($data);
        if(isset($data['question_id'])) $answer->question_id = $data['question_id'];
        if(isset($data['answer']))  $answer->answer = $data['answer'];
        if(isset($data['value']))  $answer->value = $data['value'];

    	if($answer->update())
    	{

    		return $answer;
    	}
    	return null;
    }

        public function getAnswerById($id)
    {
        return Answer::where("id", $id)->first();
    }



    public function deleteAnswer($id){

        $answer = Answer::where('id',$id)->first();
        return $answer->delete();
      }


 public function testAnswerExists($name)
 {
    $answer = Answer::where('name',$name)->first();
    if($answer)
        return true;
    else
        return false;
}

public function testAnswerByName($name)
{
   return Answer::where('name',$name)->first();
}

// public function listTestQuestion($filters){
//    $testquestion = Question::get();

//     return $testquestion;
// }

public function listAnswers($filters){
    $pageSize = $filters['pageSize'] ?? 15;
    $predicate = Answer::query();
    foreach ($filters as $key => $filter) {
        if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
        {
            continue;
        }

        $predicate->where($key, $filter);
     }

    $answer = $predicate->paginate($pageSize);

    return $answer;
}

public function getAnswer($id){
    return Answer::find($id);
}

}