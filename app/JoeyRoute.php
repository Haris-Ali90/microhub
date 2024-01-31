<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class JoeyRoute extends Model
{
	 public $timestamps = false;

   // use SoftDeletes; //add this line
    protected $table = 'joey_routes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_incomplete'
    ];

    // new work
    public function joeyRouteLocations()
    {
        return $this->hasMany(JoeyRouteLocations::class,'route_id','id')->whereNull('deleted_at')->whereNull('is_unattempted');
    }


    //new model helper function
    public function getConvertedDateAttribute()
    {
        return ConvertTimeZone($this->date,'UTC','America/Toronto');
    }

    public function getTaskIds()
    {

    return $this->hasMany(new JoeyRouteLocations(),'route_id')->whereNull('joey_route_locations.deleted_at')
    ->pluck('task_id')->toArray();

    }
    public function GetAllTaskIds()
    {
        // gating current routs tasks ids
        return $this->RouteLocations()->pluck('task_id')->toArray();
    }
    public function RoutingZone()
    {
        return $this->belongsTo(RoutingZones::class,'zone');
        // ->whereNull('deleted_at');;
    }
    public function Joey()
    {
        return $this->belongsTo(Joey::class,'joey_id');
        // ->whereNull('deleted_at');;
    }
    public function DashboardUsers()
    {
        return $this->belongsTo(User::class,'user_id');
        // ->whereNull('deleted_at');;
    }
    public function TotalOrderDropsCount()
    {
        //return $this->RouteLocations()->count();
        return $this->RouteLocations()->count();
    }
    public function RouteLocations()
    {
        return $this->hasMany( JoeyRouteLocations::class,'route_id', 'id')->whereNull('deleted_at');
    }

    /**
     * Get Total Numbers Of Orders Completed in this Route .
     */
    public function TotalOrderDropsCompletedCount()
    {
        // gating current routs tasks ids
        $tasks_ids = $this->GetAllTaskIds();

        // getting fisrt sort scan status codes
        $status_code =array_values(StatusMap::getStatusCodes()['return_complete']);
        return Task::join('sprint__sprints','sprint__tasks.sprint_id', '=', 'sprint__sprints.id')
            ->whereIn('sprint__sprints.status_id',$status_code)
            ->whereIn('sprint__tasks.id', $tasks_ids)
            ->where('sprint__sprints.deleted_at', null)
            ->distinct('sprint__sprints.id')
            ->count();


    }
    public function TotalKM()  //calculate Actual Total KM
    {
        $data = $this->RouteLocations->sum('distance');
        return round( $data / 1000 ,2);
    }


    /**
     * Get Time Of First Pickup Scan Of Order in this Route .
     */
    public function ActualTotalKM()  //calculate Actual Total KM
    {
        // getting fisrt sort scan status codes
        $status_code = array_values(StatusMap::getStatusCodes()['return_complete']);

        $data = JoeyRouteLocations::join('sprint__tasks' , 'sprint__tasks.id', '=', 'joey_route_locations.task_id')
        ->join('sprint__sprints' , 'sprint__sprints.id', '=', 'sprint__tasks.sprint_id')
        ->whereIn('sprint__sprints.status_id',$status_code)
        ->where('joey_route_locations.route_id',$this->id)
        ->distinct('joey_route_locations.id')
        ->pluck('joey_route_locations.distance','sprint__sprints.id')->toArray();

        $data = round( array_sum($data) / 1000 , 2);
        return $data;
    }
    public function TotalDuration()  //calculate Actual Total KM
    {
        $data = $this->RouteLocations;
        // ->get(['arrival_time','finish_time']);
        $sum=0;
        foreach ($data as $time)
        {
            $to_time = strtotime("2008-12-13 ".$time->finish_time.":00");
            $from_time = strtotime("2008-12-13 ".$time->arrival_time.":00");
            $sum+= round(abs($to_time - $from_time) / 60,2);

            // dd($time->finish_time-$time->arrival_time);
        }

        return round( $sum);
    }


    /**
     * Get Time Of First Pickup Scan Of Order in this Route .
     */
    public function ActualTotalDuration()  //calculate Actual Total KM
    {
        // getting fisrt sort scan status codes
        $status_code = array_values(StatusMap::getStatusCodes()['return_complete']);

        $data = JoeyRouteLocations::join('sprint__tasks' , 'sprint__tasks.id', '=', 'joey_route_locations.task_id')
        ->join('sprint__sprints' , 'sprint__sprints.id', '=', 'sprint__tasks.sprint_id')
        ->whereIn('sprint__sprints.status_id',$status_code)
        ->where('joey_route_locations.route_id',$this->id)
        ->distinct('joey_route_locations.id')
        ->get();

        $sum=0;
        foreach ($data as $time)
        {
            $to_time = strtotime("2008-12-13 ".$time->finish_time.":00");
            $from_time = strtotime("2008-12-13 ".$time->arrival_time.":00");
            $sum+= round(abs($to_time - $from_time) / 60,2);
        }
        return $sum;
    }

    public static  function getCartnoOfRoute($id)
    {
        $zone_route_data=self::
        //join('zones_routing','zones_routing.id','=','joey_routes.zone')->
        where('joey_routes.id','=',$id)
         ->whereNull('joey_routes.deleted_at')
        ->orderby('joey_routes.id')
        ->first(['joey_routes.date','joey_routes.zone','joey_routes.hub']);
       if($zone_route_data==null)
       {
          return null;
       }
        $data=date('Y-m-d',strtotime($zone_route_data->date));
        $routedata=self::where('zone','=',$zone_route_data->zone)->where('date','like',$data."%")->orderby('id')->get();
        $zone_data=RoutingZones::where('hub_id',$zone_route_data->hub)
        ->whereNull('deleted_at')
        ->get();
        // dd($zone_data);
        $j=0;
        $order_range=null;
        foreach($zone_data as $data)
        {
            if($data->id==$zone_route_data->zone)
            {
                $order_range=$data->order_range;
                break;
            }
            $j++;
        }
        $i=65;
        foreach($routedata as $data)
        {
            if($data->id==$id)
            {
                break;
            }
            $i++;
        }
        if($order_range==null)
        {
            $order_range=10;
        }
        //dd($j)
       return ['zone_cart_no'=>($j%26)+65,'route_cart_no'=>$i,'order_range'=>$order_range];
    }

    public static  function getCartnoOfOrder($joey_location_id)
    {
            $zone_route_data=self::join('joey_route_locations','joey_route_locations.route_id','=','joey_routes.id')
            //->join('zones_routing','zones_routing.id','joey_routes.zone')
            ->where('joey_route_locations.id',$joey_location_id)
            ->first(['joey_routes.hub','joey_routes.date','joey_routes.zone','joey_route_locations.route_id','joey_routes.hub','joey_route_locations.ordinal']);
            $date= date('Y-m-d',strtotime($zone_route_data->date));
            $routedata=self::where('zone','=',$zone_route_data->zone)->where('date','like',$date."%")->orderby('id')->get();
            $i=65;
            foreach($routedata as $data)
            {
                if($data->id==$zone_route_data->route_id)
                {
                    break;
                }
                $i++;
            }

            $zone_data=RoutingZones::where('hub_id',$zone_route_data->hub)
            ->whereNull('deleted_at')->orderby('id')
            ->get();
            $j=0;
            $order_range=null;

            foreach($zone_data as $data)
            {
                if($data->id==$zone_route_data->zone)
                {
                    $order_range=$data->order_range;
                    break;
                }
                $j++;
            }
            if($order_range==null)
            {
                $order_range=10;
            }
                return ['OrderCartNo'=>chr(($j%26)+65).chr($i).chr(ceil($zone_route_data->ordinal/$order_range)+64)."-".$zone_route_data->ordinal];
    }

 public static function getTotalDistance($routeId)
    {
        $totalDistance = 0;
        $joeyRouteLocations = JoeyRouteLocations::where('route_id', $routeId)->get();
        foreach($joeyRouteLocations as $location){
            $totalDistance += $location->distance;
        }
        return $totalDistance;
    }}
