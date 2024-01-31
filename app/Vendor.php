<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{

    protected $table = 'vendors';


    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;

    }
    public function location()
    {
        return $this->belongsTo(Locations::class,'location_id','id');
    }

    public function getVendorOrdersCount($date)
    {

        return $this->hasMany(Sprint::class,'creator_id','id')
            ->whereNull('deleted_at')
            ->whereIn('status_id', [24,61,111])
            ->whereNotIn('status_id', [36])
            ->count();
    }

    public function getVendorOrdersRouteCount($routeId)
    {
        $routeVendorId = JoeyRouteLocations::where('route_id', $routeId)->pluck('task_id');

        $pickupSprintId = JoeyStorePickup::where('route_id', $routeId)->whereNull('deleted_at')->pluck('sprint_id')->toArray();
        $deliveredSprintId = JoeyStorePickup::where('route_id', $routeId)->whereNotNull('deleted_at')->pluck('sprint_id')->toArray();
        $sprintId = array_merge($pickupSprintId, $deliveredSprintId);

        $sprintCount=0;
        $orderCount=0;
        if(!empty($sprintId)){
            $orderCount = Sprint::whereIn('creator_id',$routeVendorId)
                ->where('status_id',125)
                ->whereIn('id',$sprintId)
                ->whereNull('deleted_at')
                ->whereNotIn('status_id',[36])
                ->count();
        }else{
            $sprintCount =  Sprint::whereIn('creator_id', $routeVendorId)
                ->whereNull('deleted_at')
                ->whereIn('status_id', [24,61,111])
                ->whereNotIn('status_id', [36])
                ->count();
        }
        $count = $orderCount + $sprintCount;
        return $count;

    }

    public function routeOrderCount($routeId, $taskId, $date)
    {
        $orderCount=0;
        $routeDetailsCount=0;
        $vendorId = JoeyRoute::join('joey_route_locations','joey_route_locations.route_id' ,'=', 'joey_routes.id')
                                ->whereNull('joey_route_locations.deleted_at')
                                ->whereNull('joey_routes.deleted_at')
                                ->where('joey_routes.mile_type',1)
                                ->where('joey_routes.id', $routeId)
                                ->where('joey_route_locations.task_id', $taskId)
                                ->pluck('joey_route_locations.task_id');

        $pickupSprintId = JoeyStorePickup::where('route_id', $routeId)->whereNull('deleted_at')->pluck('sprint_id')->toArray();
        $deliveredSprintId = JoeyStorePickup::where('route_id', $routeId)->whereNotNull('deleted_at')->pluck('sprint_id')->toArray();
        $sprintId = array_merge($pickupSprintId, $deliveredSprintId);

        if(!empty($sprintId)){
            $orderCount = Sprint::whereIn('creator_id',$vendorId)
                ->whereIn('id',$sprintId)
                ->whereNull('deleted_at')
                ->whereNotIn('status_id',[36])
                ->count();
        }else{
            $routeDetailsCount = Sprint::whereIn('creator_id',$vendorId)
                ->whereNull('deleted_at')
                ->whereIn('status_id',[24,61,111])
                ->whereNotIn('status_id',[36])
                ->count();
        }



        $routeOrderCount = $routeDetailsCount + $orderCount;

        return $routeOrderCount;
    }

    public function getRouteDetailOrderCount($routeId)
    {
        $routeOrderCount = JoeyStorePickup::where('route_id', $routeId)->whereNotNull('deleted_at')->count();
        return $routeOrderCount;
    }

    public function totalRouteOrderCount($routeId, $date)
    {
        $changeDateFormate = date("Y-m-d", strtotime($date));

        $vendorId = JoeyRoute::join('joey_route_locations','joey_route_locations.route_id' ,'=', 'joey_routes.id')
            ->whereNull('joey_route_locations.deleted_at')
            ->whereNull('joey_routes.deleted_at')
            ->where('joey_routes.mile_type',1)
            ->where('joey_routes.route_completed', 0)
            ->where('joey_routes.id', $routeId)
            ->where('joey_routes.date', 'LIKE', date('Y-m-d').'%')
            ->pluck('joey_route_locations.task_id');

//        $vendorId = JoeyRouteLocations::where('route_id',$routeId)->pluck('task_id');

        $routeOrderCount = Sprint::whereIn('creator_id',$vendorId)
            ->whereIn('status_id',[24,61,111,125])
            ->whereNotIn('status_id',[36])
            ->whereDate('created_at', 'LIKE', date('Y-m-d').'%')
            ->whereNull('deleted_at')
            ->distinct()
            ->count();

        return $routeOrderCount;
    }
}
