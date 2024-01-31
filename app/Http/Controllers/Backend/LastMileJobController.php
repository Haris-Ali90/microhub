<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Hub;
use App\HubStore;
use App\JoeyRoute;
use App\JoeyRouteLocations;
use App\LogRoutes;
use App\MicroHubPostalCodes;
use App\RoutingZones;
use App\SlotJob;
use App\Slots;
use App\SlotsPostalCode;
use App\Sprint;
use App\Task;
use App\ZonesTypes;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LastMileJobController extends Controller
{

    public function getLastMileJobList(Request $request){

        $hubId = auth()->user()->hub_id;
        $date=$request->get('date');
        $hub_id=$request->get('id');
        if(empty($date)){
            $date=date('Y-m-d');
        }

        $lastMileJobs = $this->getRoutificJob($date,$hubId);
        return backend_view('last_mile.job.list',compact('lastMileJobs','hubId'));
    }

    public function getRoutificJob($date,$id){

        $datas = SlotJob::leftJoin('zones_routing','zone_id','=','zones_routing.id')
            ->whereNull('slots_jobs.deleted_at')
            ->where('slots_jobs.created_at','like',$date.'%')
            ->where('slots_jobs.hub_id','=',$id)
            ->where('slots_jobs.is_big_box','=',0)
            ->where('slots_jobs.mile_type','=',3)
            ->get(['job_id','title','status','slots_jobs.id','is_custom_route','engine']);

        return $datas;
    }

    public function createLastMileJobId(Request $request){

        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');
        if($request->get('end_time') == '00:00'){
            $endTime = '12:00';
        }
        date_default_timezone_set('America/Toronto');

        $postals= SlotsPostalCode::where('zone_id','=',$request['zone'])->pluck('postal_code')->toArray();

        $hubId = auth()->user()->hub_id;


//        $vendorIds = HubStore::where('hub_id', $hubId)->WhereNull('deleted_at')->pluck('vendor_id');

        $sprints = Task::join('sprint__sprints','sprint__tasks.sprint_id','=','sprint__sprints.id')
            ->rightjoin('locations','location_id','=','locations.id')
            ->join('merchantids','task_id','=','sprint__tasks.id')
//            ->whereIn('sprint__sprints.creator_id',$vendorIds)
            ->where('sprint__tasks.type','=','dropoff')
            ->whereIn(\DB::raw('SUBSTR(locations.postal_code,1,3)'),$postals)
            ->where('sprint__sprints.in_hub_route',0)
            ->whereIn('sprint__sprints.status_id',[13,147])
            ->whereNull('sprint__sprints.deleted_at')
            ->whereNotIn('sprint__sprints.status_id',[36])
            ->whereNotNull('merchantids.tracking_id')
            ->orderBy('locations.postal_code')
            ->take(200)
            ->get(['start_time','end_time','sprint__tasks.id','sprint__tasks.sprint_id','due_time','address','locations.latitude','locations.longitude','locations.postal_code','locations.city_id']);

        if(count($sprints)<1){
            return response()->json( ['status_code'=>400,"error"=>'This zone has zero order count']);
        }

        $orders = array();

        foreach($sprints as $sprint){

            $lat[0] = substr($sprint->latitude, 0, 2);
            $lat[1] = substr($sprint->latitude, 2);
            $latitude=$lat[0].".".$lat[1];

            $long[0] = substr($sprint->longitude, 0, 3);
            $long[1] = substr($sprint->longitude, 3);
            $longitude=$long[0].".".$long[1];

            $start = $sprint->start_time;
            $end = $sprint->end_time;

            $orders[$sprint->id]= array(
                "location" => array(
                    "name" => $sprint->address,
                    "lat" => $latitude,
                    "lng" => $longitude
                ),
                "start" => $startTime,
                "end" => $endTime,
                "load" => 1,
                "duration" => 2
            );

        }


        $hubPick = Hub::where('id','=',$hubId)->first();
        $address = urlencode($hubPick->address);
        // google map geocode api url
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0";

        // get the json response
        $resp_json = file_get_contents($url);

        // decode the json
        $resp = json_decode($resp_json, true);

        // response status will be 'OK', if able to geocode given address
        if($resp['status']=='OK'){
            $hubLat = $resp['results'][0]['geometry']['location']['lat'];
            $hubLong = $resp['results'][0]['geometry']['location']['lng'];
        }

        $joeycounts=Slots::join('vehicles','slots.vehicle','=','vehicles.id')
            ->where('slots.zone_id','=',$request['zone'])
            ->whereNull('slots.deleted_at')
            ->get(['vehicles.capacity','vehicles.min_visits','slots.start_time','slots.end_time','slots.hub_id','slots.joey_count','custom_capacity']);

        if(count($joeycounts)<1){
            return response()->json( ['status_code'=>400,"error"=>'No slot in this zone']);
        }

        foreach($joeycounts as $joe){
            if(!empty($joe->joey_count)){
                $joeycount= $joe->joey_count;
            }

            if(!isset($joeycount) || empty($joeycount)){
                return response()->json( ['status_code'=>400,"error"=>'Joey count should be greater than 1 in slot']);
            }

            for($i=1;$i<=$joeycount;$i++){

                if(empty($joe->custom_capacity)){
                    $capacity = $joe->capacity;
                }
                else{
                    $capacity = $joe->custom_capacity;
                }
                $shifts["joey_".$i] = array(
                    "start_location" => array(
                        "id" => $i,
                        "name" => $hubPick->address,
                        "lat" => $hubLat,
                        "lng" => $hubLong
                    ),
                    "shift_start" => date('H:i',strtotime($joe->start_time)),
                    "shift_end" => date('H:i',strtotime($joe->end_time)),
                    "capacity" => $capacity,
                    "min_visits_per_vehicle" => $joe->min_visits
                );
            }
        }

        $options = array(
            "shortest_distance" => true,
            "polylines" => true
        );

        $payload = array(
            "visits" => $orders,
            "fleet" => $shifts,
            "options" => $options
        );

        $client = new Client( '/vrp-long' );
        $client->setData($payload);
        $apiResponse= $client->send();

        if(!empty($apiResponse->error)){
            return back()->with('error',$apiResponse->error);
        }

        $slotjob  = new  SlotJob();
        $slotjob->job_id=$apiResponse->job_id;
        $slotjob->hub_id=auth()->user()->hub_id;
        $slotjob->zone_id=$request['zone'];
        $slotjob->engine = 1;
        $slotjob->is_custom_route = 1;
        $slotjob->mile_type = 3;
        $slotjob->unserved=null;
        $slotjob->save();

        $job = "Job has been created successfully and job id ".$apiResponse->job_id;

        return response()->json( ['status_code'=>200,"success"=> $job]);

    }

    public function createRouteForLastMile($id){

        $url= "/jobs";

        $client = new \App\Classes\Client($url);
        $client->setJobID($id);
        $apiResponse = $client->getJobResults();

        $job=SlotJob::where('job_id','=',$id)->first();

        SlotJob::where('job_id','=',$job->job_id)->update(['status'=>$apiResponse['status']]);

        if($apiResponse['status']=='finished'){

            $solution = $apiResponse['output']['solution'];
            if($apiResponse['output']['num_unserved'] > 0){
                return json_encode([
                    "status" => "Route Creation Error",
                    "output"=> 'Something went wrong, please contact your administrator'
                ]);
            }
            if(!empty($solution)){

                foreach ($solution as $key => $value){

                    if(count($value)>1){

                        $Route = new JoeyRoute();

                        //$Route->joey_id = $key;
                        $Route->date =date('Y-m-d H:i:s');
                        $Route->hub = $job->hub_id;
                        $Route->zone = $job->zone_id;
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
                        $Route->mile_type = 3;
                        $Route->save();

                        for($i=0;$i<count($value);$i++){
                            if($i>0){


                                JoeyRouteLocations::where('task_id','=',$value[$i]['location_id'])->update(['deleted_at'=>date('Y-m-d H:i:s')]);

                                $routeLoc = new JoeyRouteLocations();
                                $routeLoc->route_id = $Route->id;
                                $routeLoc->ordinal = $i;
                                $routeLoc->task_id = $value[$i]['location_id'];

                                if(isset($value[$i]['distance']) && !empty($value[$i]['distance'])){
                                    $routeLoc->distance = $value[$i]['distance'];
                                }

                                if(isset($value[$i]['arrival_time']) && !empty($value[$i]['arrival_time'])){
                                    $routeLoc->arrival_time = $value[$i]['arrival_time'];
                                    if(isset($value[$i]['finish_time'])){
                                        $routeLoc->finish_time = $value[$i]['finish_time'];
                                    }
                                }
                                $routeLoc->save();

                                $sprint = Task::where('id','=',$value[$i]['location_id'])->first();

                                Sprint::where('id','=',$sprint->sprint_id)->update(['in_hub_route'=>1]);

                            }
                        }
                    }
                }

                return  response()->json( ['status'=>200,"output"=> 'Route has been created successfully!']);
            }
        }

        else{

            $error = new LogRoutes();
            $error->error = $job->job_id." is in ".$apiResponse['status'];
            $error->save();

            return back()->with('error','Route creation is in process');
        }
    }

    // delete job of last mile
    public function deleteLastMileJob(Request $request){

        SlotJob::where('id','=',$request->get('delete_id'))->update(['status'=>'finished','deleted_at'=>date('Y-m-d h:i:s')]);
        return redirect()->back()->with('success', 'Job has been deleted successfully');
    }
}
