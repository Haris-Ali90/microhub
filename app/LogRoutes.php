<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogRoutes extends Authenticatable
{
    use SoftDeletes;
  
    protected $table = 'routes_error';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'error'
    ];
    protected $dates = ['deleted_at'];
    // public function JoeyrouteLocations(){
    //     return $this->has_many(new JoeyRouteLocations(),'route_id');
    // }
}

