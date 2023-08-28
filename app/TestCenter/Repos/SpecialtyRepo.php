<?php

namespace App\TestCenter\Repos;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Specialty;
use App\TestCenter\Utilities\Constants;



class SpecialtyRepo
{
    
    public function listSpecialties($filters){
        $pageSize = $filters['pageSize'] ?? 15;
        $predicate = Specialty::query();
        $predicate->withCount('testQuestions');
        foreach ($filters as $key => $filter) {
            if(in_array($key, Constants::FILTER_PARAM_IGNORE_LIST))
            {
                continue;
            }
    
            $predicate->where($key, $filter);
         }
    
        return $predicate->paginate($pageSize);
    }

    public function getSpecialtiesByIds(Array $ids)
    {
        return Specialty::whereIn("id", $ids)->get();
    }

    public function getSpecialty($id)
    {
        return Specialty::find($id);
    }

    public function getSpecialtyByName(String $name)
    {
        return Specialty::where('name', $name)->first();
    }

    public function createSpecialty(Array $data)
    {
        $specialty = new Specialty;
        
        $specialty->name = $data['name'];
        $specialty->description = $data['description']??null;
        $specialty->id = $data['ayuda_specialty_id'];

    	if($specialty->save())
    	{
    		return $specialty;
    	}
    	return null;
    }

    public function updateSpecialty($data, $specialty)
    {
        $specialty->name = $data['name'];
        if(isset($data['description'])) $specialty->description = $data['description'];
        if(isset($data['ayuda_specialty_id'])) $specialty->ayuda_specialty_id = $data['ayuda_specialty_id'];

    	if($specialty->update()){
    		return $specialty;
    	}
    	return null;
    }

}