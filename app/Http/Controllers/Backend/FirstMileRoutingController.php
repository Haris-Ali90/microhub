<?php

namespace App\Http\Controllers\Backend;

use App\Classes\Client;
use App\Classes\Fcm;
use App\Hub;
use App\HubStore;
use App\JoeyRoute;
use App\JoeyRouteLocations;
use App\RouteHistory;
use App\RoutingZones;
use App\SlotJob;
use App\Slots;
use App\Sprint;
use App\Task;
use App\UserDevice;
use App\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FirstMileRoutingController extends BackendController
{
    // create job id for first mile route by routific and ctc 2022-03-21
    public function storeFirstMileRoute(Request $request){

        date_default_timezone_set('America/Toronto');
        // pluck vendor ids for get hub stores
        $Date = date('Y-m-d H:i:s');

        $joeyRoutes = JoeyRoute::join('joey_route_locations','joey_route_locations.route_id' ,'=', 'joey_routes.id')
            ->whereNull('joey_route_locations.deleted_at')
            ->whereNull('joey_routes.deleted_at')
            ->where('route_completed', 0)
            ->where('mile_type',1)
            ->whereDate('joey_routes.date', 'LIKE', $Date.'%')
            ->groupBy('joey_route_locations.route_id')
            ->get();

        if(count($joeyRoutes) > 0){
            return response()->json( ['status'=>400,"output"=>'This orders has already in route']);
        }

        $hubStores = HubStore::with('stores')->whereNull('deleted_at')->where('hub_id', auth()->user()->hub_id)->pluck('vendor_id');

        $sprints = Sprint::with('Vendor')
            ->whereNotIn('status_id', [36])
            ->whereIn('status_id',[24,61,111])
            ->whereIn('creator_id', $hubStores)
//            ->whereDate('created_at', 'LIKE', $Date.'%')
            ->whereNull('deleted_at')
            ->groupBy('creator_id')
            ->get();


        if(count($sprints) < 1){
            return response()->json( ['status'=>400,"output"=>'This vendor has zero order count']);
        }

        $orders = array();

        foreach($sprints as $sprint){

            $address = $sprint->vendor->business_address;
            $latitude = $sprint->vendor->latitude/1000000;
            $longitude = $sprint->vendor->longitude/1000000;

            if($sprint->vendor->business_address == null){
                $location = \App\Locations::find($sprint->vendor->location_id);
                if($location){
                    $address = $location->address;
                    $latitude = $location->latitude/1000000;
                    $longitude = $location->longitude/1000000;
                }else{
                    $locationEnc = \App\LocationEnc::find($sprint->vendor->location_id);
                    $address = $locationEnc->setDecryptAddressAttribute($locationEnc->address, $sprint->vendor->location_id);
                }
            }

            $orders[$sprint->creator_id]= array(
                "location" => array(
                    "name" => $address,
                    "lat" => $latitude,
                    "lng" => $longitude
                ),
                "load" => 1,
                "duration" => 2
            );

        }

        $hubPick = Hub::where('id','=',$request->hub_id)->first();
        $zone = RoutingZones::where('hub_id','=',$request->hub_id)->first();
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

        //joey slots count
        $joeycounts=Slots::join('vehicles','slots.vehicle','=','vehicles.id')
            ->where('slots.hub_id','=',$request->hub_id)
            ->where('slots.mile_type', '=', 1)
            ->whereNull('slots.deleted_at')
            ->get(['vehicles.capacity','vehicles.min_visits','slots.start_time','slots.end_time','slots.hub_id','slots.joey_count','custom_capacity']);

        if(count($joeycounts)<1){
            return response()->json( ['status'=>400,"output"=>'No slot in this hub, kindly create slot']);
        }
        $j=0;
        foreach($joeycounts as $joe){
            if(!empty($joe->joey_count)){
                $joeycount= $joe->joey_count;
            }
            if(!isset($joeycount) || empty($joeycount)){
                return response()->json( ['status'=>400,"output"=>'Joey count should be greater than 1 in slot']);
            }


            for($i=1;$i<=$joeycount;$i++){
                if(empty($joe->custom_capacity)){
                    $capacity = $joe->capacity;
                }
                else{
                    $capacity = $joe->custom_capacity;
                }
                $shifts["joey_".$j] = array(
                    "start_location" => array(
                        "id" => $j,
                        "name" => $hubPick->address,
                        "lat" => $hubLat,
                        "lng" => $hubLong
                    ),
                    "end_location" => array(
                        "id" => $j,
                        "name" => $hubPick->address,
                        "lat" => $hubLat,
                        "lng" => $hubLong
                    ),
                    "shift_start" => date('H:i',strtotime($joe->start_time)),
                    "shift_end" => date('H:i',strtotime($joe->end_time)),
                    "capacity" => $capacity,
                    "min_visits_per_vehicle" => $joe->min_visits
                );
                $j++;
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

        $client = new Client( '/vrp' );
        $client->setData($payload);
        $apiResponse= $client->send();

        if(!empty($apiResponse->error)){
            return response()->json( ['status'=> 400, "output"=> 'Something went wrong, please contact your administrator' ]);
        }


        if(isset($apiResponse->solution)){
            $solution = $apiResponse->solution;
            if($apiResponse->num_unserved > 0){
                return response()->json([
                    "status" =>400,
                    "output"=>'Something went wrong, please contact your administrator'
                ]);
            }
            foreach ($solution as $key => $value){

                if(count($value)>1){
                    $Route = new JoeyRoute();
                    $Route->date = $Date;
                    $Route->hub = auth()->user()->hub_id;
                    $Route->zone = null;
                    if(isset($apiResponse->total_working_time)){
                        $Route->total_travel_time=$apiResponse->total_working_time;
                    }
                    else{
                        $Route->total_travel_time=0;
                    }
                    if(isset($apiResponse->total_distance))
                    {
                        $Route->total_distance=$apiResponse->total_distance;
                    }
                    else
                    {
                        $Route->total_distance=0;
                    }
                    $Route->mile_type = 1;
                    $Route->save();

                    $removeArray = array_slice($value, 1, -1);

                    for($i=0;$i<count($removeArray);$i++){

                        $routeLoc = new JoeyRouteLocations();
                        $routeLoc->route_id = $Route->id;
                        $routeLoc->ordinal = $i+1;
                        $routeLoc->task_id = $removeArray[$i]->location_id;

                        if(isset($removeArray[$i]->distance) && !empty($removeArray[$i]->distance)){
                            $routeLoc->distance = $removeArray[$i]->distance;
                        }

                        if(isset($removeArray[$i]->arrival_time) && !empty($removeArray[$i]->arrival_time)){
                            $routeLoc->arrival_time = $removeArray[$i]->arrival_time;
                            if(isset($removeArray[$i]->finish_time)){
                                $routeLoc->finish_time = $removeArray[$i]->finish_time;
                            }
                        }
                        $routeLoc->save();
                    }
                }
            }
            return response()->json([
                "status" => 200,
                "output"=> 'Route has been created successfully'
            ]);

        }else{
            return response()->json([
                "status" => 400,
                "output"=> 'Something went wrong, please contact your administrator'
            ]);
        }

    }

    public function firstMileDeleteRoute($routeId){

        $route= JoeyRoute::where('id',$routeId)->first();
        if ($route){
            if (isset($route->joey_id)) {
                $deviceIds = UserDevice::where('user_id', $route->joey_id)->where('is_deleted_at', 0)->pluck('device_token');
                $subject = 'Deleted Route ' . $routeId;
                $message = 'Your route has been deleted ';
                Fcm::sendPush($subject, $message, 'ecommerce', null, $deviceIds);
                $payload = ['notification' => ['title' => $subject, 'body' => $message, 'click_action' => 'ecommerce'],
                    'data' => ['data_title' => $subject, 'data_body' => $message, 'data_click_action' => 'ecommerce']];
                $createNotification = [
                    'user_id' => $route->joey_id,
                    'user_type' => 'Joey',
                    'notification' => $subject,
                    'notification_type' => 'ecommerce',
                    'notification_data' => json_encode(["body" => $message]),
                    'payload' => json_encode($payload),
                    'is_silent' => 0,
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                UserNotification::create($createNotification);
            }
        }

        JoeyRoute::where('id',$routeId)->update(['deleted_at'=>date('y-m-d H:i:s')]);
        return  "Route R-".$routeId." has been deleted Successfully";

    }

    public function getFirstMileRouteHistory($id)
    {
        $routeData = $this->getRouteHistory($id);
        return backend_view('first_mile.route.history',['routes'=>$routeData,'route_id'=>$id]);
    }

    public function getRouteHistory($id)
    {
        $routeData=RouteHistory::join('joeys','route_history.joey_id','=','joeys.id')
            ->leftjoin('merchantids','merchantids.task_id','=','route_history.task_id')
            ->leftjoin('dashboard_users','route_history.updated_by','=','dashboard_users.id')
            ->where('route_history.route_id','=',$id)
            ->whereNull('route_history.deleted_at')
            ->orderBy('route_history.created_at')
            ->get(['route_history.id','route_history.route_id','route_history.status','route_history.joey_id','route_history.route_location_id','route_history.created_at'
                ,'route_history.ordinal','joeys.first_name','joeys.last_name','merchantids.tracking_id','route_history.type','route_history.updated_by','dashboard_users.full_name']);

        return $routeData;
    }
}
