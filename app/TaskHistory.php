<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;


class TaskHistory extends Authenticatable
{
    //use SoftDeletes;
    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
    ];

    protected $table = 'sprint__tasks_history';

    public $timestamps = false;

    public function taskMerchants()
    {
        return $this->belongsTo(MerchantIds::class,'sprint__tasks_id','task_id')->where('tracking_id','!=',null);
    }

     public function taskRouteLocation()
    {
        return $this->belongsTo(JoeyRouteLocations::class,'sprint__tasks_id','task_id')->whereNull('deleted_at');
    }

    public function joeyRoute()
    {
        return $this->belongsTo(JoeyRoutes::class,'route_id','id')->whereNull('deleted_at');
    }

    public function getSprint(){
        return $this->belongsTo(Sprint::class,'sprint_id','id');
    }

    public function getSingleSprint(){
        return $this->hasOne(Sprint::class,'id','sprint_id');
    }

    public function getTask(){
        return $this->belongsTo(Task::class,'sprint__tasks_id','id');
    }

    public function getHubOrder(){
        return $this->belongsTo(MicroHubOrder::class,'sprint_id','sprint_id');
    }
}
