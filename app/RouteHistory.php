<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteHistory extends Model {

    protected $table = 'route_history';
    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
    ];

    public function getJoey()
    {
        return $this->hasOne(Joey::class,'id','joey_id');
    }


}
