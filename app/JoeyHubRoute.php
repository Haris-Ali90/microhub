<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoeyHubRoute extends Authenticatable
{
    use SoftDeletes;
  
    protected $table = 'hub_routes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'joey_id','route_id'
    ];
    protected $dates = ['deleted_at'];
    // public function zone()
    // {
    //     return $this->belongsTo('App\Zone');
    // }
    // public function category()
    // {
    //     return $this->belongsTo('App\Category');
    // }
    // public function area()
    // {
    //     return $this->belongsTo('App\Area');
    // }
    
}
