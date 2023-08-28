<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
  //
  protected $primaryKey = "id";
  protected $table = "sp.workers";

  public function specialties()
  {
    return $this->belongsToMany(Specialty::class,'sp.worker_specialties','worker_id','specialty_id');
	}

  public function tests()
  {
    return $this->hasMany(Test::class, "worker_id");
  }

  public function views()
  {
    return $this->hasMany(WorkerResourceView::class, 'worker_id');
  }

  public function eventBookings()
  {
    return $this->hasMany(WorkerEventBooking::class, 'worker_id');
  }
}
