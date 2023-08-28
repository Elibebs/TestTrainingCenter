<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingResource extends Model
{
    //
    protected $primaryKey = "id";
    protected $table = "setup.training_resources";

    public function directory()
    {
        return $this->belongsTo(TrainingTypeDirectory::class, 'directory_id');
    }

    public function views()
    {
        return $this->hasMany(WorkerResourceView::class, 'resource_id');
    }
}
