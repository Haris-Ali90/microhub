<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class SlotJob extends Model
{

    protected $table = 'slots_jobs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'id', 'name', 'created_at','updated_at','deleted_at',
    // ];

        public static function createRoutificRoute($job,$apiResponse)
        {
           
                $solution = $apiResponse['output']['solution'];
    
                if(!empty($solution)){
    
                    foreach ($solution as $key => $value){
    
                        if(count($value)>1){
    
                            $Route = new JoeyRoute();
    
                            //$Route->joey_id = $key;
                            $Route->date =date('Y-m-d H:i:s');
                            $Route->hub = $job->hub_id;
                            $Route->zone = $job->zone_id;
                            $Route->job_id = $job->job_id;
                            $Route->user_id = Auth::guard('web')->user()->id;
                            // $Route->total_travel_time=$apiResponse['output']['total_travel_time'];
                            if(isset($apiResponse['output']['total_working_time'])){
                                $Route->total_travel_time=$apiResponse['output']['total_working_time'];
                            }
                            else{
                                $Route->total_travel_time=0;
                            }
                            if(isset($apiResponse['output']['total_distance']))
                            {
                                $Route->total_distance=$apiResponse['output']['total_distance'];
                            }
                            else
                            {
                                $Route->total_distance=0;
                            }
    
                            $Route->save();
    
                            for($i=0;$i<count($value);$i++){
                                if($i>0){
    
    
                                   JoeyRouteLocations::where('task_id','=',$value[$i]['location_id'])->update(['deleted_at'=>date('Y-m-d H:i:s')]);
                                    $trackingId=MerchantIds::where('task_id','=',$value[$i]['location_id'])->first();
    
                                    CustomRoutingTrackingId::where('tracking_id',$trackingId->tracking_id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
    
                                    $routeLoc = new JoeyRouteLocations();
                                    $routeLoc->route_id = $Route->id;
                                    $routeLoc->ordinal = $i;
                                    $routeLoc->task_id = $value[$i]['location_id'];
    
                                    if(isset($value[$i]['distance']) && !empty($value[$i]['distance'])){
                                        $routeLoc->distance = $value[$i]['distance'];
                                    }
    
                                    if(isset($value[$i]['arrival_time']) && !empty($value[$i]['arrival_time'])){
                                        $routeLoc->arrival_time = $value[$i]['arrival_time'];
                                        $routeLoc->finish_time = $value[$i]['finish_time'];
                                    }
                                    $routeLoc->save();
    
                                    $sprint = Task::where('id','=',$value[$i]['location_id'])->first();
    
                                    $amazon_enteries = AmazonEntry::where('sprint_id','=',$sprint->sprint_id)->
                                    whereNUll('deleted_at')->
                                    first();
                                    if($amazon_enteries!=null)
                                    {
                                        $amazon_enteries->route_id=$Route->id;
                                        $amazon_enteries->ordinal=$i;
                                        $amazon_enteries->task_status_id=$sprint->status_id;
                                        $amazon_enteries->save();
                                    }
    
                                    $ctc_entries = CTCEntry::where('sprint_id','=',$sprint->sprint_id)->
                                    whereNUll('deleted_at')->
                                    first();
                                    if($ctc_entries!=null)
                                    {
                                        $ctc_entries->route_id=$Route->id;
                                        $ctc_entries->ordinal=$i;
                                        $ctc_entries->task_status_id=$sprint->status_id;
                                        $ctc_entries->save();
                                    }
    
                                    Sprint::where('id','=',$sprint->sprint_id)->update(['in_hub_route'=>1]);
    
                                }
                            }
                        }
                    }
    
                }
           
    
           
    
        }

        public static function createRoutificBetaRoute($job,$response)
        {
          
            $scheduledRoutes=$response->routeSchedule->scheduledRoutes;
            $stops=$response->routeScenario->stops;
            $orders=[];
            foreach($stops as $stop)
            {
                $orders[$stop->eventUuid]['task_id']=$stop->name;
            }
           
            foreach($scheduledRoutes as $scheduledRoute)
            {
                
                if($scheduledRoute->activeRouteSolution->stopsCount>0)
                {
                   
                    $activeRouteSolution=$scheduledRoute->activeRouteSolution;
                    $routeTimeline=$scheduledRoute->activeRouteSolution->routeTimeline;
    
                    $Route = new JoeyRoute();
                    $Route->date =date('Y-m-d H:i:s');
                    $Route->hub = $job->hub_id;
                    $Route->zone = $job->zone_id;
                    $Route->user_id=Auth::guard('web')->user()->id;
                    $Route->job_id = $job->job_id;
                    $Route->total_travel_time=$activeRouteSolution->workingTime;
                    $Route->total_distance=$activeRouteSolution->distance;
                    $Route->save();
                    $routeData=[];
                    foreach($routeTimeline as $routeTimelineRoute)
                    {
                        if($routeTimelineRoute->eventType!="leaving_start_location" && $routeTimelineRoute->eventType!="reaching_end_location")
                        {
                            $routeData[$routeTimelineRoute->eventUuid]['task_id']=$orders[$routeTimelineRoute->eventUuid]['task_id'];
                            $routeData[$routeTimelineRoute->eventUuid]['sequence']=$routeTimelineRoute->sequence;
                          
    
                            JoeyRouteLocations::where('task_id','=',$routeData[$routeTimelineRoute->eventUuid]['task_id'])->update(['deleted_at'=>date('Y-m-d H:i:s')]);
                            $trackingId=MerchantIds::where('task_id','=',$routeData[$routeTimelineRoute->eventUuid]['task_id'])->first();
    
                            CustomRoutingTrackingId::where('tracking_id',$trackingId->tracking_id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
                            RoutificJobTrackiingIds::where('task_id','=',$routeData[$routeTimelineRoute->eventUuid]['task_id'])->whereNull('deleted_at')->update(['deleted_at'=>date('Y-m-d H:i:s')]);
    
                            $sprint = Task::where('id','=',$routeData[$routeTimelineRoute->eventUuid]['task_id'])->first();
    
                            $routeLoc = new JoeyRouteLocations();
                            $routeLoc->route_id = $Route->id;
                            $routeLoc->ordinal = $routeData[$routeTimelineRoute->eventUuid]['sequence'];
                            $routeLoc->task_id =$routeData[$routeTimelineRoute->eventUuid]['task_id'];
                            $routeLoc->arrival_time=$routeTimelineRoute->arrivalTime;
                            $routeLoc->finish_time=$routeTimelineRoute->endTime;
                            $routeLoc->save();
    
                            $amazon_enteries = AmazonEntry::where('sprint_id','=',$sprint->sprint_id)->
                            whereNUll('deleted_at')->
                            first();
                            if($amazon_enteries!=null)
                            {
                                $amazon_enteries->route_id=$Route->id;
                                $amazon_enteries->ordinal=$routeLoc->ordinal;
                                $amazon_enteries->task_status_id=$sprint->status_id;
                                $amazon_enteries->save();
                            }
                            $ctc_enteries = CTCEntry::where('sprint_id','=',$sprint->sprint_id)->
                            whereNUll('deleted_at')->
                            first();
                            if($ctc_enteries!=null)
                            {
                                $ctc_enteries->route_id=$Route->id;
                                $ctc_enteries->ordinal=$routeLoc->ordinal;
                                $ctc_enteries->task_status_id=$sprint->status_id;
                                $ctc_enteries->save();
                            }
                            Sprint::where('id','=',$sprint->sprint_id)->update(['in_hub_route'=>1]);
    
                        }
                               
    
                    }
                   
                }
            }
        }

        public static function createLogisticRoute($job,$apiResponse)
        {
           
            if ($apiResponse['status'] == 'SUCCEED') {
    
                $routes = $apiResponse['routes'];
                $unassigned = $apiResponse['unassigned_stops']['unreachable'];
                $taskId = [];
                $status = '';
                if (count($unassigned) > 0) {
                    foreach ($unassigned as $key => $unsigned) {
                        $orderId = explode('_', $unsigned);
                        $taskId[] = $orderId[1];
                        $assignedOrder = CustomRoutingTrackingId::whereNotIn('task_id', $taskId)->whereNull('deleted_at')->get();
                        $task = implode(',', $taskId);
                        $status = 'But Task Order No ' . $task . ' Un Served';
                    }
                   
                    foreach ($assignedOrder as $asOrder) {
                        dd($asOrder);
                        CustomRoutingTrackingId::where('task_id',$asOrder->task_id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
                    }
    
                }
    
                foreach ($routes as $key => $value) {
    
                    $Route = new JoeyRoute();
                    $Route->date = date('Y-m-d H:i:s');
                    $Route->hub = $job->hub_id;
                    $Route->zone = $job->zone_id;
                    $Route->user_id = Auth::guard('web')->user()->id;
                    $Route->job_id = $job->job_id;
                    $Route->total_travel_time = (isset($value['summary']['travel_time'])) ? $value['summary']['travel_time'] : 0;
                    $Route->total_distance = (isset($value['summary']['distance'])) ? $value['summary']['distance'] : 0;
                    $Route->save();
    
                    for ($i = 0; $i < count($value['stops']); $i++) {
                        if ($i > 0) {
                            $taskId = explode("_", $value['stops'][$i]['id']);
                            JoeyRouteLocations::where('task_id', '=', $taskId[1])->update(['deleted_at' => date('Y-m-d H:i:s')]);
                            $trackingId = MerchantIds::where('task_id', '=', $taskId[1])->first();
    
                            $routeLoc = new JoeyRouteLocations();
                            $routeLoc->route_id = $Route->id;
                            $routeLoc->ordinal = $i;
                            $routeLoc->task_id = $taskId[1];
                            $routeLoc->distance = $value['stops'][$i]['distance_from_previous'];
    
                            if (isset($value['stops'][$i]) && !empty($value['stops'][$i])) {
                                $start = round($value['summary']['begin_time'] / 60, 0);
                                $end = round($value['summary']['end_time'] / 60, 0);
                                $timeStart = date("00:" . $start);
                                $timeEnd = date("00:" . $end);
                                $routeLoc->arrival_time = $timeStart;
                                $routeLoc->finish_time = $timeEnd;
                            }
                            $routeLoc->save();
                            $sprint = Task::where('id', '=', $taskId[1])->first();
                            CustomRoutingTrackingId::where('tracking_id',$trackingId->tracking_id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
                            Sprint::where('id', '=', $sprint->sprint_id)->update(['in_hub_route' => 1]);
    
                               $amazon_enteries = AmazonEntry::where('sprint_id','=',$sprint->sprint_id)->
                               whereNUll('deleted_at')->
                               first();
                               if($amazon_enteries!=null)
                               {
                                   $amazon_enteries->route_id=$Route->id;
                                   $amazon_enteries->ordinal=$i;
                                   $amazon_enteries->task_status_id=$sprint->status_id;
                                   $amazon_enteries->save();
                               }
                        }
                    }
                }
    
                if (count($unassigned) == 0) {
                    // LogisticCustomJoeyDetail::whereNull('deleted_at')->update(['deleted_at' => date('Y-m-d H:i:s')]);
                    // CustomRoutingTrackingId::whereNull('deleted_at')->update(['deleted_at' => date('Y-m-d H:i:s')]);
                }
    
            }
           
    
        }


}
