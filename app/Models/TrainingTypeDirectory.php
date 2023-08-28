<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingTypeDirectory extends Model
{
    //
    protected $primaryKey = "id";
    protected $table = "setup.training_type_directories";

    public function resources()
    {
        return $this->hasMany(TrainingResource::class, 'directory_id');
    }
}
