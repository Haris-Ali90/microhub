<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoeyStorePickup extends Model
{

    public $table = 'joey_storepickup';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','joey_id','route_id','tracking_id','sprint_id','task_id','status_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

	public function JoeyName()
    {
        return $this->belongsTo( Joey::class,'joey_id', 'id');
    }

    }


