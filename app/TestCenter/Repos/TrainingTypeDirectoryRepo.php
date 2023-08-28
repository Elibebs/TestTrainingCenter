<?php

namespace App\TestCenter\Repos;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\TrainingTypeDirectory;
use App\TestCenter\Utilities\Constants;



class TrainingTypeDirectoryRepo 
{
    

    public function listDirectories($filters)
    {
        $pageSize = $filters['pageSize'] ?? 15;
        $predicate = TrainingTypeDirectory::query();
        
        foreach ($filters as $key => $filter) {
            if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
            {
                continue;
            }
    
            $predicate->where($key, $filter);
         }
    
        return $predicate->withCount('resources')->paginate($pageSize);
    }

    public function getDirectoryByName(String $name)
    {
        return TrainingTypeDirectory::where('name', $name)->first();
    }

    public function getDirectoryById($id)
    {
        return TrainingTypeDirectory::find($id);
    }

    public function createDirectory(Array $data)
    {
        $directory = new TrainingTypeDirectory;
        
        $directory->name = $data['name'];
        $directory->training_type_id = $data['training_type_id'];

    	if($directory->save())
    	{
    		return $directory;
    	}
    	return null;
    }

    public function updateDirectory(Array $data, $directory)
    {
        $directory->name = $data['name'];

        if( $directory->update() )
        {
            return $directory;
        }

        return null;
        
    }

}