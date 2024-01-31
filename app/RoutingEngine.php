<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Support\Str;

class RoutingEngine extends Model
{
    //use SoftDeletes;
    protected $table = 'routing_engine';


    protected $guarded = [];

    public function Hub()
    {
        return $this->belongsTo(Hub::class,'hub_id');
    }

    public function getRoutingTypeAttribute($value)
    {
        if($value==1) return "Zones Routing";
        elseif($value==2) return "Custom Zones Routing";
        elseif($value==3) return "Custom  Routing";
        else return "";
    }
    public function getHubIdAttribute($value)
    {
        if($value==16) return "Montreal Hub (16)";
        elseif($value==17) return "Torronto Hub (17)";
        elseif($value==19) return "Ottawa  Hub (19)";
        else return "";
    }

    public static function CreateJobIdWithZonesId($sprints,$hub_id,$request,$is_custom_route)
    {
        $orders = array();
        
        foreach($sprints as $sprint){
            if($hub_id==19 && $is_custom_route==0)
            {
                $sprint=(object)$sprint;
            }
            
            $lat[0] = substr($sprint->latitude, 0, 2);
            $lat[1] = substr($sprint->latitude, 2);
            $latitude=$lat[0].".".$lat[1];

            $long[0] = substr($sprint->longitude, 0, 3);
            $long[1] = substr($sprint->longitude, 3);
            $longitude=$long[0].".".$long[1];
        
            if(empty($sprint->city_id) || $sprint->city_id==NULL){
                $dropoffAdd = Location::canadian_address($sprint->address.','.$sprint->postal_code.',canada');
                $latitude = $dropoffAdd['lat'];
                $longitude = $dropoffAdd['lng'];
            }

            $start = $sprint->start_time;    
            $end = $sprint->end_time;
                $orders[$sprint->id]= array(
                    "location" => array(
                        "name" => $sprint->address,
                        "lat" => $latitude,
                        "lng" => $longitude
                    ),
                    //"start" => $start,
                    //"end" => $end,
                    "load" => 1,
                    "duration" => 2
                );

                if($hub_id==17 && $is_custom_route==1)
                {
                    Sprint::where('id','=',$sprint->sprint_id)->update(['status_id'=>124,"in_hub_route"=>1]);
                    Task::where('sprint_id','=',$sprint->sprint_id)->update(['status_id'=>124]);
                    $checkforstatus=TaskHistory::where('sprint_id','=',$sprint->sprint_id)->where('status_id','=',125)->first();
                    // checking if order is Reattempt
                    $isReattempt=SprintReattempt::where('sprint_id','=',$sprint->sprint_id)->first();
                    
                    if(!$checkforstatus && $isReattempt==null)
                    {
                        $pickupstoretime_date=new \DateTime();
                        $pickupstoretime_date->modify('-2 minutes');
                        $taskhistory=new TaskHistory();
                        $taskhistory->sprint_id=$sprint->sprint_id;
                        $taskhistory->sprint__tasks_id=$sprint->id;
                        $taskhistory->status_id=125;
                        $taskhistory->created_at = $pickupstoretime_date->format('Y-m-d H:i:s');
                        $taskhistory->date = $pickupstoretime_date->format('Y-m-d H:i:s');
                        $taskhistory->save();
                    }
                    $taskhistory=new TaskHistory();
                    $taskhistory->sprint_id=$sprint->sprint_id;
                    $taskhistory->sprint__tasks_id=$sprint->id;
                    $taskhistory->status_id=124;
                    $taskhistory->created_at=date("Y-m-d H:i:s");
                    $taskhistory->date=date("Y-m-d H:i:s");
                    $taskhistory->save();
                }
        }

        $hubPick = Hub::where('id','=',$hub_id)->first();
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
        ->get(['vehicles.capacity','vehicles.min_visits','slots.start_time','slots.end_time','slots.hub_id','slots.joey_count']);

        if(count($joeycounts)<1){

            return ['statusCode'=>400,'message'=>"No Slot defined"];
        }
        $i=0;
        foreach($joeycounts as $joe){
            if(!empty($joe->joey_count)){
                $joeycount= $joe->joey_count;
            }
            if(!isset($joeycount) || empty($joeycount)){
                return ['statusCode'=>400,'message'=>"No Joey Count should be greater than 1 in slot"];
            }
            for($j=0;$j<$joeycount;$j++){

                $shifts["joey_".$i] = array(
                    "start_location" => array(
                        "id" => $i,
                        "name" => $hubPick->address,
                        "lat" => $hubLat,
                        "lng" => $hubLong 
                    ),
                    "shift_start" => date('H:i',strtotime($joe->start_time)),
                    "shift_end" => date('H:i',strtotime($joe->end_time)),
                    "capacity" => $joe->capacity,
                    "min_visits_per_vehicle" => $joe->min_visits
                );

                $i++;
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

         // dd(json_encode($payload));

          $client = new Client( '/vrp-long' );
          $client->setData($payload); 
          $apiResponse= $client->send();

          if(!empty($apiResponse->error)){      
            return ['statusCode'=>400,'message'=>$apiResponse->error];

          }

        
            $slotjob  = new  SlotJob();
            $slotjob->job_id=$apiResponse->job_id;
            $slotjob->hub_id=$hub_id;
            $slotjob->zone_id=$request['zone'];
            $slotjob->is_big_box=0;
            $slotjob->is_custom_route = $is_custom_route;
            $slotjob->unserved=null;
            $slotjob->engine=1;
            $slotjob->save();
        
    
        $job = "Request Submited Job_id ".$apiResponse->job_id;
        return ['statusCode'=>200,'message'=>$job];
    }
    public static function createJobIdBetaWithZonesId($sprints,$hub_id,$request,$is_custom_route)
    {
        $stops = array();
        
        foreach($sprints as $sprint){
            if($hub_id==19 && $is_custom_route==0)
            {
                $sprint=(object) $sprint;
            }
            $lat[0] = substr($sprint->latitude, 0, 2);
            $lat[1] = substr($sprint->latitude, 2);
            $latitude=$lat[0].".".$lat[1];

            $long[0] = substr($sprint->longitude, 0, 3);
            $long[1] = substr($sprint->longitude, 3);
            $longitude=$long[0].".".$long[1];
        
            if(empty($sprint->city_id) || $sprint->city_id==NULL){
                $dropoffAdd = Location::canadian_address($sprint->address.','.$sprint->postal_code.',canada');
                $latitude = $dropoffAdd['lat'];
                $longitude = $dropoffAdd['lng'];
            }

            $start = $sprint->start_time;    
            $end = $sprint->end_time;
          
                $stop['name']=(string)$sprint->id;
                $stop['locations']= array(array(
                    "address" => $sprint->address,
                    "latitude" => (float)$latitude,
                    "longitude" => (float)$longitude
                ));
                // $stop['email']=$sprint->email;
                // $stop['phone']=$sprint->phone;
                $stop['load']=1;
                // $sprint->load;
                $stop['duration']=2;
                // $sprint->duration;
                // $stop['isPriority']=$sprint->isPriority;
                // $stop['instructions']=$sprint->instructions;
                $stop['timeWindows']= array(array(
                    "startTime" => "09:00",
                     "endTime" => "21:00",
                )
                );
                    $stops[]=$stop;

                if($hub_id==17 && $is_custom_route==1)
                {
                    Sprint::where('id','=',$sprint->sprint_id)->update(['status_id'=>124,"in_hub_route"=>1]);
                    Task::where('sprint_id','=',$sprint->sprint_id)->update(['status_id'=>124]);
                    $checkforstatus=TaskHistory::where('sprint_id','=',$sprint->sprint_id)->where('status_id','=',125)->first();
                    // checking if order is Reattempt
                    $isReattempt=SprintReattempt::where('sprint_id','=',$sprint->sprint_id)->first();
                    
                    if(!$checkforstatus && $isReattempt==null)
                    {
                        $pickupstoretime_date=new \DateTime();
                        $pickupstoretime_date->modify('-2 minutes');
                        $taskhistory=new TaskHistory();
                        $taskhistory->sprint_id=$sprint->sprint_id;
                        $taskhistory->sprint__tasks_id=$sprint->id;
                        $taskhistory->status_id=125;
                        $taskhistory->created_at = $pickupstoretime_date->format('Y-m-d H:i:s');
                        $taskhistory->date = $pickupstoretime_date->format('Y-m-d H:i:s');
                        $taskhistory->save();
                    }
                    $taskhistory=new TaskHistory();
                    $taskhistory->sprint_id=$sprint->sprint_id;
                    $taskhistory->sprint__tasks_id=$sprint->id;
                    $taskhistory->status_id=124;
                    $taskhistory->created_at=date("Y-m-d H:i:s");
                    $taskhistory->date=date("Y-m-d H:i:s");
                    $taskhistory->save();
                }
           

        }
       
        $routes=[];
        $hubPick = Hub::where('id','=',$hub_id)->first();
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
        ->get(['vehicles.capacity','vehicles.min_visits','slots.start_time','slots.end_time','slots.hub_id','slots.joey_count']);

        if(count($joeycounts)<1){

            return ['statusCode'=>400,'message'=>"No Slot defined"];
        }
        $i=0;
        foreach($joeycounts as $joe){
            if(!empty($joe->joey_count)){
                $joeycount= $joe->joey_count;
            }
            if(!isset($joeycount) || empty($joeycount)){
                return ['statusCode'=>400,'message'=>"No Joey Count should be greater than 1 in slot"];
            }
            for($j=0;$j<$joeycount;$j++){

                $route['name']="joey_".$j;
                $route['constraints']['capacity']=$joe->capacity;
                $route['constraints']['startTime']="10:00";
                $route['constraints']['endTime']="18:00";
                $route['constraints']['startLocations']=[array(
                    "address" => $hubPick->address,
                    "latitude" => $hubLat,
                    "longitude" => $hubLong 
                )];
                $route['constraints']['endLocations']=[array(
                    "address" => $hubPick->address,
                    "latitude" => $hubLat,
                    "longitude" => $hubLong 
                )];
    
                $routes[]=$route;
            }
            
        }

        if(empty($routes)){
            return ['message'=>'Please set Joeys vehicle details to continue',"statusCode"=>400];
        }
        // beta routific
        $betapayload['routeScenario']=array(
           
            "stops"=>$stops,
            "routes"=>$routes
        );
      
        $routificBeta=new RoutificBeta('/optimize/create');
        $routificBeta->setData($betapayload);
        $response=$routificBeta->send();
         

        if(isset($response->type) && $response->type=="OPTIMIZE_CREATE_VALIDATION_ERROR")
        {
            return ['message'=>$response->type,"statusCode"=>400];
        }
        sleep(8);
        $job_id=$response->actionUuid;
        $RoutificBeta=new RoutificBeta('/optimize');
        $RoutificBeta->setJobID( $job_id);
        $response=$RoutificBeta->getJobResults();
        if($response->status=="finished")
        {
            $check_order_in_route=0;
           $scheduledRoutes=$response->routeSchedule->scheduledRoutes;
        
           foreach($scheduledRoutes as $scheduledRoute)
           {
               
                $routeTimeline=$scheduledRoute->activeRouteSolution->routeTimeline;
              
                if(count($routeTimeline)>2)
                {
                    $check_order_in_route=1;
                    break;
                }
           }
           if($check_order_in_route==0)
           {
            return ['message'=>"No Order in Routes and All order are going to be unserved",'statusCode'=>400];
           }
           else
           {
            $slotjob  = new  SlotJob();
            $slotjob->job_id = $job_id;
            $slotjob->hub_id =$hub_id;
            $slotjob->zone_id = $request['zone'];
            $slotjob->unserved = null;
            $slotjob->is_custom_route = $is_custom_route;
            $slotjob->engine=2;
            $slotjob->is_big_box=0;
            $slotjob->save();
            $job = "Request Submited Job_id ".$job_id;
          return ['message'=>$job,'statusCode'=>200];
           }
        }
        return ['message'=>"No Order in Routes and All order are going to be unserved",'statusCode'=>400];
        /*   
        $slotjob  = new  SlotJob();
        $slotjob->job_id = $response->actionUuid;
        $slotjob->hub_id =$hub_id;
        $slotjob->zone_id = $request['zone'];
        $slotjob->unserved = null;
        $slotjob->is_custom_route = $is_custom_route;
        $slotjob->engine=2;
        $slotjob->is_big_box=0;
        $slotjob->save();
        $job = "Request Submited Job_id ".$response->actionUuid;
        return ['statusCode'=>200,'message'=>$job];
        */

      
    
    }
    public static function createJobIdLogisticApiWithZones($sprints,$hub_id,$request,$is_custom_route)
    {

        $orders = array();
        
        foreach($sprints as $sprint){
            if($hub_id==19 && $is_custom_route==0)
            {
                $sprint=(object)$sprint;
            }
            $lat[0] = substr($sprint->latitude, 0, 2);
            $lat[1] = substr($sprint->latitude, 2);
            $latitude=$lat[0].".".$lat[1];

            $long[0] = substr($sprint->longitude, 0, 3);
            $long[1] = substr($sprint->longitude, 3);
            $longitude=$long[0].".".$long[1];
        
            if(empty($sprint->city_id) || $sprint->city_id==NULL){
                $dropoffAdd = Location::canadian_address($sprint->address.','.$sprint->postal_code.',canada');
                $latitude = $dropoffAdd['lat'];
                $longitude = $dropoffAdd['lng'];
            }

            $start = $sprint->start_time;    
            $end = $sprint->end_time;
    
                $orders[] = array(
                    "id" => "order_" . $sprint->id,
                    "geometry" => array(
                        "coordinates" => array(
                            "lat" => (float)$latitude,
                            "lon" => (float)$longitude
                        ),
                        "zipcode" => $sprint->postal_code
                    ),
                    "group_priority" => 0,
                    "order_priority" => 0,
                    "time_window" => array(
                        "start" => (int)'09:00' * 60,
                        "end" => (int)'18:00' * 60
                    ),
                "service" => array(
                    "dropoff_quantities" => 1,
                    "duration"=>2
                ),
                );
            
            if($hub_id==17 && $is_custom_route==1)
            {
                Sprint::where('id','=',$sprint->sprint_id)->update(['status_id'=>124,"in_hub_route"=>1]);
                Task::where('sprint_id','=',$sprint->sprint_id)->update(['status_id'=>124]);
                $checkforstatus=TaskHistory::where('sprint_id','=',$sprint->sprint_id)->where('status_id','=',125)->first();
                // checking if order is Reattempt
                $isReattempt=SprintReattempt::where('sprint_id','=',$sprint->sprint_id)->first();
                
                if(!$checkforstatus && $isReattempt==null)
                {
                    $pickupstoretime_date=new \DateTime();
                    $pickupstoretime_date->modify('-2 minutes');
                    $taskhistory=new TaskHistory();
                    $taskhistory->sprint_id=$sprint->sprint_id;
                    $taskhistory->sprint__tasks_id=$sprint->id;
                    $taskhistory->status_id=125;
                    $taskhistory->created_at = $pickupstoretime_date->format('Y-m-d H:i:s');
                    $taskhistory->date = $pickupstoretime_date->format('Y-m-d H:i:s');
                    $taskhistory->save();
                }
                $taskhistory=new TaskHistory();
                $taskhistory->sprint_id=$sprint->sprint_id;
                $taskhistory->sprint__tasks_id=$sprint->id;
                $taskhistory->status_id=124;
                $taskhistory->created_at=date("Y-m-d H:i:s");
                $taskhistory->date=date("Y-m-d H:i:s");
                $taskhistory->save();
            }
            

        }

        $hubPick = Hub::where('id','=',$hub_id)->first();
      //  dd($hubPick->address);
        $address = urlencode($hubPick->address);
        // google map geocode api url
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0";

        // get the json response
        $resp_json = file_get_contents($url);

        // decode the json
        $resp = json_decode($resp_json, true);

        // get hub postal code from address
        $postal = '';
        foreach($resp['results'] as $results) {
            foreach($results['address_components'] as $address_components) {
                if(isset($address_components['types']) && $address_components['types'][0] == 'postal_code') {
                    $postal = $address_components['long_name'];
                }
            }
        }

        // response status will be 'OK', if able to geocode given address
        if ($resp['status'] == 'OK') {
            $hubPostalCode = $postal;
            $hubLat = $resp['results'][0]['geometry']['location']['lat'];
            $hubLong = $resp['results'][0]['geometry']['location']['lng'];
        }
        $joeycounts=Slots::join('vehicles','slots.vehicle','=','vehicles.id')
        ->where('slots.zone_id','=',$request['zone'])
        ->whereNull('slots.deleted_at')
        ->get(['vehicles.capacity','vehicles.min_visits','slots.start_time','slots.end_time','slots.hub_id','slots.joey_count','vehicles.name']);

        if(count($joeycounts)<1){

            return ['statusCode'=>400,'message'=>"No Slot defined"];
        }
        $startDepots = array();
        $vehicle_types = array();
        $routing = array();
        $k = 1;
        foreach ($joeycounts as $key => $joey_route) {

            $car = Str::slug($joey_route->name);
            $key = $key + 1;
            $vehicle_types[] = array(
                "id" => "rider_" . $key,
                "profile" => "car",
                "count" => $joey_route->joey_count,
                "capacity" => $joey_route->capacity,
                "dispatch_after" => 0,
                "max_late_time" => 0,
                "max_distance" => 15000,
                "max_orders_per_route" => 120,
                "avoid_wait_time" => false,
                "use_all_vehicles" => false,
                "depots" => array(
                    "start_depot" => "any"
                )
            );

            $routing[] = array(
                "name" => "rider_" . $key,
                "base_profile" => "car",
                "average_speed" => 74+$k
            );

            for ($i = 1; $i <= $joey_route->joey_count; $i++) {
                $startDepots[] = array(
                    "id" => "rider_" . $k,
                    "geometry" => array(
                        "coordinates" => array(
                            "lat" => $hubLat,
                            "lon" => $hubLong
                        ),
                        "zipcode" => $hubPostalCode
                    ),
                    "service_duration" => 0
                );
                $k++;
            }


        }

      
        if (empty($startDepots)) {
            return ['message' => 'Please set Joeys vehicle details to continue', "statusCode" => 400];
        }

        $options = array(
            "distance" => "kilometer",
            "duration" => "minute"
        );

       
        $payload = array(
            "orders" => $orders,
            "start_depots" => $startDepots,
            "vehicle_types" => $vehicle_types,
            "units" => $options,
            "routing_profiles" => $routing,
        );

        //dd(json_encode($payload));

        $client = new Logistic('/vrp');

        $client->setData($payload);
       
        $apiResponse = $client->send();
        
        if (!empty($apiResponse->message)) {
            return ['message' => $apiResponse->message, "statusCode" => 400];
        }

        sleep(6);

        $url = "/vrp?job_id";
        $client = new Logistic($url);
        $client->setJobID($apiResponse->job_id);
        $apiResponseByJobId = $client->getJobResults();
       
        if ($apiResponseByJobId['status'] == 'SUCCEED') {
            $unassigned = $apiResponseByJobId['unassigned_stops']['unreachable'];
            $taskId = [];
            $trackingId = [];
            if (count($unassigned) > 0) {
                foreach ($unassigned as $key => $unsigned) {
                    $orderId = explode('_', $unsigned);
                    $taskId[] = $orderId[1];
                    $assignedOrder=MerchantIds::whereIn('task_id',$taskId)->first();
                    $trackingId[] = $assignedOrder->tracking_id;
//                    $task = implode(',', $taskId);
                }
                $tracking_id = implode(', ', $trackingId);
                $status = 'Tracking Id (' . $tracking_id . ') Un Served.';
              
                return ['message' => $status, "statusCode" => 400];
            }
            else{
                $slotjob = new  SlotJob();
                $slotjob->job_id = $apiResponse->job_id;
                $slotjob->hub_id = $hub_id;
                $slotjob->zone_id = $request['zone'];
                $slotjob->unserved = null;
                $slotjob->is_custom_route = $is_custom_route;
                $slotjob->engine=3;
                $slotjob->is_big_box=0;
                $slotjob->save();
             
                $job = "Request Submited Job_id ".$apiResponse->job_id;
                return ['message' => $job, 'statusCode' => 200];
            }
        }
    }

    public static function createJobId($orders,$hub_id,$joey_route_detail,$zone_id=null)
    {
        

        $hubPick = Hub::where('id','=',$hub_id)->first();
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
        $shifts= array();
        $k=1;
        foreach($joey_route_detail as $joey_route)
        {
            for($i=1;$i<=$joey_route->joeys_count;$i++){

        
                $shifts["joey_".$k] = array(
                    "start_location" => array(
                        "id" => $i,
                        "name" => $hubPick->address,
                        "lat" => $hubLat,
                        "lng" => $hubLong 
                    ),
                   //  "shift_start" =>"10:00" ,
                   //  "shift_end" =>"15:00",
                    "capacity" => $joey_route->capacity
                   //  ,
                   //  "min_visits_per_vehicle" => $joe->min_visits
                );
                $k++;
            }
        }

        if(empty($shifts)){
            return ['error'=>'Please set Joeys vehicle details to continue',"statusCode"=>400];
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
        return ['error'=>$apiResponse->error,"statusCode"=>400];
       
      }

      $slotjob  = new  SlotJob();
      $slotjob->job_id = $apiResponse->job_id;
      $slotjob->hub_id =$hub_id;
      $slotjob->zone_id = $zone_id;
      $slotjob->unserved = null;
 	  $slotjob->is_custom_route = ($zone_id==null)?1:0;
      $slotjob->is_big_box=0;
      $slotjob->engine=1;
      $slotjob->save();

      return ['Job_id'=>$apiResponse->job_id,'statusCode'=>200];

    }

    public static function createJobIdBeta($orders,$hub_id,$joey_route_detail,$stops=[],$zone_id=null)
    {
        
        $routes=[];
        $hubPick = Hub::where('id','=',$hub_id)->first();
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

        $shifts= array();
        $k=1;
      
        foreach($joey_route_detail as $joey_route)
        {
            for($i=1;$i<=$joey_route->joeys_count;$i++){

                $route['name']="joey_".$k;
                $route['constraints']['capacity']=$joey_route->capacity;
                $route['constraints']['startTime']="09:00";
                $route['constraints']['endTime']="18:00";
                $route['constraints']['startLocations']=[array(
                    "address" => $hubPick->address,
                    "latitude" => $hubLat,
                    "longitude" => $hubLong 
                )];
                $route['constraints']['endLocations']=[array(
                    "address" => $hubPick->address,
                    "latitude" => $hubLat,
                    "longitude" => $hubLong 
                )];
                $k++;
                $routes[]=$route;
            }
        }

        if(empty($routes)){
            return ['error'=>'Please set Joeys vehicle details to continue',"statusCode"=>400];
        }
        // beta routific
        $betapayload['routeScenario']=array(
           
            "stops"=>$stops,
            "routes"=>$routes
        );
       // dd(json_encode($betapayload));
        $routificBeta=new RoutificBeta('/optimize/create');
        $routificBeta->setData($betapayload);
        $response=$routificBeta->send();
        

        if(isset($response->type) && $response->type=="OPTIMIZE_CREATE_VALIDATION_ERROR")
        {
            return ['error'=>$response->type,"statusCode"=>400];
        }

        sleep(8);
       
        $job_id=$response->actionUuid;
        $RoutificBeta=new RoutificBeta('/optimize');
        $RoutificBeta->setJobID( $job_id);
        $response=$RoutificBeta->getJobResults();
        if($response->status=="finished")
        {
            $check_order_in_route=0;
           $scheduledRoutes=$response->routeSchedule->scheduledRoutes;
        
           foreach($scheduledRoutes as $scheduledRoute)
           {
               
                $routeTimeline=$scheduledRoute->activeRouteSolution->routeTimeline;
              
                if(count($routeTimeline)>2)
                {
                    $check_order_in_route=1;
                    break;
                }
           }
           if($check_order_in_route==0)
           {
            return ['error'=>"No Order in Routes and All order are going to be unserved",'statusCode'=>400];
           }
           else
           {
            $slotjob  = new  SlotJob();
            $slotjob->job_id = $job_id;
            $slotjob->hub_id =$hub_id;
            $slotjob->zone_id = $zone_id;
            $slotjob->unserved = null;
            $slotjob->is_custom_route = ($zone_id==null)?1:0;
            $slotjob->engine=2;
            $slotjob->is_big_box=0;
            $slotjob->save();
            $job = "Request Submited Job_id ".$job_id;
          return ['Job_id'=>$job_id,'statusCode'=>200];
           }
        }
        return ['error'=>"No Order in Routes and All order are going to be unserved",'statusCode'=>400];
       
     /* $slotjob  = new  SlotJob();
      $slotjob->job_id = $response->actionUuid;
      $slotjob->hub_id =$hub_id;
      $slotjob->zone_id = $zone_id;
      $slotjob->unserved = null;
 	  $slotjob->is_custom_route = ($zone_id==null)?1:0;
      $slotjob->engine=2;
      $slotjob->is_big_box=0;
      $slotjob->save();
      return ['Job_id'=>$response->actionUuid,'statusCode'=>200];
      */
    }

    public static function createJobIdLogisticApi($orders,$joey_route_detail,$hub_id,$zone_id=null)
    {
        $hubPick = Hub::where('id','=',$hub_id)->first();
      //  dd($hubPick->address);
        $address = urlencode($hubPick->address);
        // google map geocode api url
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0";

        // get the json response
        $resp_json = file_get_contents($url);

        // decode the json
        $resp = json_decode($resp_json, true);

        // get hub postal code from address
        $postal = '';
        foreach($resp['results'] as $results) {
            foreach($results['address_components'] as $address_components) {
                if(isset($address_components['types']) && $address_components['types'][0] == 'postal_code') {
                    $postal = $address_components['long_name'];
                }
            }
        }

        // response status will be 'OK', if able to geocode given address
        if ($resp['status'] == 'OK') {
            $hubPostalCode = $postal;
            $hubLat = $resp['results'][0]['geometry']['location']['lat'];
            $hubLong = $resp['results'][0]['geometry']['location']['lng'];
        }
        $startDepots = array();
        $vehicle_types = array();
        $routing = array();
        $k = 1;
        foreach ($joey_route_detail as $key => $joey_route) {

            $car = Str::slug($joey_route->name);
            $key = $key + 1;
            $vehicle_types[] = array(
                "id" => "rider_" . $key,
                "profile" => "car",
                "count" => $joey_route->joeys_count,
                "capacity" => $joey_route->capacity,
                "dispatch_after" => 0,
                "max_late_time" => 0,
                "max_distance" => 15000,
                "max_orders_per_route" => 120,
                "avoid_wait_time" => false,
                "use_all_vehicles" => false,
                "depots" => array(
                    "start_depot" => "any"
                )
            );

            $routing[] = array(
                "name" => "rider_" . $key,
                "base_profile" => "car",
                "average_speed" => 74+$k
            );

            for ($i = 1; $i <= $joey_route->joeys_count; $i++) {
                $startDepots[] = array(
                    "id" => "rider_" . $k,
                    "geometry" => array(
                        "coordinates" => array(
                            "lat" => $hubLat,
                            "lon" => $hubLong
                        ),
                        "zipcode" => $hubPostalCode
                    ),
                    "service_duration" => 0
                );
                $k++;
            }


        }

      
        if (empty($startDepots)) {
            return ['error' => 'Please set Joeys vehicle details to continue', "statusCode" => 400];
        }

        $options = array(
            "distance" => "kilometer",
            "duration" => "minute"
        );

       
        $payload = array(
            "orders" => $orders,
            "start_depots" => $startDepots,
            "vehicle_types" => $vehicle_types,
            "units" => $options,
            "routing_profiles" => $routing,
        );

        //dd(json_encode($payload));

        $client = new Logistic('/vrp');

        $client->setData($payload);
       
        $apiResponse = $client->send();
        
        if (!empty($apiResponse->message)) {
            return ['error' => $apiResponse->message, "statusCode" => 400];
        }

        sleep(6);

        $url = "/vrp?job_id";
        $client = new Logistic($url);
        $client->setJobID($apiResponse->job_id);
        $apiResponseByJobId = $client->getJobResults();
       
        if ($apiResponseByJobId['status'] == 'SUCCEED') {
            $unassigned = $apiResponseByJobId['unassigned_stops']['unreachable'];
            $taskId = [];
            $trackingId = [];
            if (count($unassigned) > 0) {
                foreach ($unassigned as $key => $unsigned) {
                    $orderId = explode('_', $unsigned);
                    $taskId[] = $orderId[1];
                    $assignedOrder=MerchantIds::whereIn('task_id',$taskId)->first();
                    $trackingId[] = $assignedOrder->tracking_id;
//                    $task = implode(',', $taskId);
                }
                $tracking_id = implode(', ', $trackingId);
                $status = 'Tracking Id (' . $tracking_id . ') Un Served.';
              
                return ['error' => $status, "statusCode" => 400];
            }
            else{
                $slotjob = new  SlotJob();
                $slotjob->job_id = $apiResponse->job_id;
                $slotjob->hub_id = $hub_id;
                $slotjob->zone_id = $zone_id;
                $slotjob->unserved = null;
                $slotjob->is_custom_route = ($zone_id==null)?1:0;
                $slotjob->engine=3;
                $slotjob->is_big_box=0;
                $slotjob->save();
             
                return ['Job_id' => $apiResponse->job_id, 'statusCode' => 200];
            }
        }
    }

}
