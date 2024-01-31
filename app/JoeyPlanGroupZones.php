<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoeyPlanGroupZones extends Model
{


    use SoftDeletes;
    /**
     * Table name.
     *
     * @var array
     */
    public $table = 'joey_plan_group_zones';

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    /**
     * ORM Relation
     *
     * @var array
     */

    /**
     * Get zones
     */
    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'joey_plan_group_zones_details', 'joey_plan_group_zone_ref', 'zone_ref_id');
    }

    /**
     * Get Zones Routing
     */
    public function ZonesRouting()
    {
        return $this->belongsToMany(ZoneRouting::class, 'joey_plan_group_zones_details', 'joey_plan_group_zone_ref','zone_routing_ref_id' );
    }
}

