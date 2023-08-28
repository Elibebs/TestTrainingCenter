<?php

namespace App\TestCenter\Repos;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\TrainingType;
use App\TestCenter\Utilities\Constants;



class TrainingTypeRepo 
{
    

    public function listTrainingTypes($filters){
        $pageSize = $filters['pageSize'] ?? 15;
        $predicate = TrainingType::query();
        
        foreach ($filters as $key => $filter) {
            if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
            {
                continue;
            }
    
            $predicate->where($key, $filter);
         }
    
        return $predicate->paginate($pageSize);
    }

    public function fetchTrainingTypesWithDirectories( $filters )
    {
        $pageSize = $filters['pageSize'] ?? 15;
        $predicate = TrainingType::query();
        
        foreach ($filters as $key => $filter) {
            if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
            {
                continue;
            }
    
            $predicate->where($key, $filter);
         }
    
        return $predicate->with('directories.resources')->paginate($pageSize);   
    } 

    public function getTrainingTypeByName(String $name)
    {
        return TrainingType::where('name', $name)->first();
    }

    public function getTrainingTypeById($id)
    {
        return TrainingType::find($id);
    }

    public function createTrainingType(Array $data)
    {
        $training_type = new TrainingType;
        
        $training_type->name = $data['name'];
        $training_type->description = $data['description']??null;

    	if($training_type->save())
    	{
    		return $training_type;
    	}
    	return null;
    }

    public function updateTrainingType(Array $data, $training_type)
    {
        if( isset($data['name']) ) $training_type->name = $data['name'];
        if( isset($data['description']) ) $training_type->description = $data['description'];

        if( $training_type->update() )
        {
            return $training_type;
        }

        return null;
        
    }

}