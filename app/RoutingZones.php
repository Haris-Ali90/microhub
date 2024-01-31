<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoutingZones extends Model
{

    protected $table = 'zones_routing';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'id', 'name', 'created_at','updated_at','deleted_at',
    // ];

    public function zoneType()
    {
        return $this->belongsTo(ZonesTypes::class, 'zone_type','id');
    }

    // new work
    public function joeyRoutes()
    {
        return $this->hasMany(JoeyRoute::class,'zone','id')->whereNull('deleted_at');
    }


}
