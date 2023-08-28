<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingType extends Model
{
    //
    protected $primaryKey = "id";
    protected $table = "setup.training_types";

    public function directories()
    {
        return $this->hasMany(TrainingTypeDirectory::class, 'training_type_id');
    }
}
