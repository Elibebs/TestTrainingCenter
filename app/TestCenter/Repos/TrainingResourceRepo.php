<?php

namespace App\TestCenter\Repos;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\TrainingResource;
use App\TestCenter\Utilities\Constants;



class TrainingResourceRepo 
{
    

    public function listResources($filters)
    {
        $pageSize = $filters['pageSize'] ?? 15;
        $predicate = TrainingResource::query();
        
        foreach ($filters as $key => $filter) {
            if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
            {
                continue;
            }
    
            $predicate->where($key, $filter);
         }
    
        return $predicate->with(['directory' => function( $query ){
                $query->select('id','name');
        }])->withCount('views')->paginate($pageSize);
    }

    public function fetchWorkerTrainingResources( $filters, $worker_id )
    {
        $pageSize = $filters['pageSize'] ?? 100;
        $predicate = TrainingResource::query();
        
        foreach ($filters as $key => $filter) {
            if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
            {
                continue;
            }
    
            $predicate->where($key, $filter);
         }
    
        return $predicate->withCount('views')->paginate($pageSize);
    }

    public function getResourceByName(String $name)
    {
        return TrainingResource::where('name', $name)->first();
    }

    public function getResourceById($id)
    {
        return TrainingResource::find($id);
    }

    public function createResource(Array $data)
    {
        $resource = new TrainingResource;
        
        $resource->name = $data['name'];
        $resource->description = $data['description']??null;
        $resource->training_type_id = $data['training_type_id'];
        $resource->directory_id = $data['directory_id'];
        $resource->type = $data['type'];
        $resource->url = $data['url'];
        $resource->upload_file_name = $data['file_name']??null;

    	if( $resource->save() )
    	{
    		return $resource;
    	}
    	return null;
    }

    public function updateResource( Array $data, $resource )
    {
        if( isset($data['name']) ) $resource->name = $data['name'];
        if( isset($data['description']) ) $resource->description = $data['description'];
        if( isset($data['training_type_id']) ) $resource->training_type_id = $data['training_type_id'];
        if( isset($data['directory_id']) ) $resource->directory_id = $data['directory_id'];
        if( isset($data['type']) ) $resource->type = $data['type'];
        if( isset($data['url']) ) $resource->url = $data['url'];
        if( isset($data['file_name']) ) $resource->upload_file_name = $data['file_name'];

        if( $resource->update() )
        {
            return $resource;
        }

        return null;
        
    }

}