<?php

namespace App\Http\Controllers\Backend;

use App\BoradlessDashboard;
use App\Classes\Fcm;
use App\Client;
use App\CTCEntry;
use App\Hub;
use App\HubStore;
use App\Joey;
use App\JoeyRoute;
use App\JoeyRouteLocations;
use App\Locations;
use App\LogRoutes;
use App\MicroHubPostalCodes;
use App\RouteHistory;
use App\RoutingZones;
use App\SlotJob;
use App\Slots;
use App\SlotsPostalCode;
use App\Sprint;
use App\Task;
use App\UserDevice;
use App\UserNotification;
use App\ZonesTypes;
use App\MicroHubZones;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LastMileRouteController extends Controller
{

    // get route list of last mile
    public function lastMileRoutesList(Request $request){

        date_default_timezone_set("America/Toronto");

        if(empty($request->input('date'))){
            $date = date('Y-m-d');
        }
        else{
            $date = $request->input('date');
        }

        $countQry = "SELECT route_id,joey_routes.joey_id,joey_routes.`date`,joeys.first_name,joeys.last_name,
        CONCAT(zones_routing.title,'(',joey_routes.zone,')') AS zone,
        COUNT(joey_route_locations.id) AS counts,
        SUM(CASE WHEN sprint__sprints.status_id in(17,113,114,116,117,118,132,138,139,144,104,105,106,107,108,109,110,111,112,131,135,136) THEN 0 ELSE 1 END) AS d_counts,
        SUM(joey_route_locations.distance) AS distance,
        SUM(CASE WHEN sprint__sprints.status_id in(17,113,114,116,117,118,132,138,139,144,104,105,106,107,108,109,110,111,112,131,135,136) THEN 0 ELSE joey_route_locations.distance END) AS d_distance,
        SEC_TO_TIME(SUM(TIME_TO_SEC(finish_time)-TIME_TO_SEC(arrival_time))) AS duration,
        SEC_TO_TIME(SUM(CASE WHEN sprint__sprints.status_id in(17,113,114,116,117,118,132,138,139,144,104,105,106,107,108,109,110,111,112,131,135,136) THEN 0 ELSE TIME_TO_SEC(finish_time)-TIME_TO_SEC(arrival_time) END)) AS d_duration
        FROM joey_route_locations 
        JOIN sprint__tasks ON(task_id=sprint__tasks.id) 
        JOIN sprint__sprints ON(sprint_id=sprint__sprints.id) 
        JOIN joey_routes ON(route_id=joey_routes.id) 
        JOIN locations ON(location_id=locations.id)
        LEFT JOIN joeys ON (joeys.id=joey_routes.joey_id)
        LEFT JOIN  zones_routing ON (zones_routing.id=joey_routes.zone AND zones_routing.`deleted_at` IS NULL)
        WHERE joey_routes.date LIKE '".$date."%'
        AND joey_routes.`mile_type` = 3
        AND joey_routes.`hub` = '".auth()->user()->hub_id."'
        AND sprint__tasks.`deleted_at` IS NULL
        AND joey_route_locations.`deleted_at` IS NULL 
        #AND zones_routing.`deleted_at` IS NULL
        AND joey_routes.deleted_at IS NULL GROUP BY route_id";

        $counts = DB::select($countQry);

        return backend_view('last_mile.route.list',compact('counts'));
    }

    public function lastMileRoutesOnlyList(Request $request){

        date_default_timezone_set("America/Toronto");

        if(empty($request->input('date'))){
            $date = date('Y-m-d');
        }
        else{
            $date = $request->input('date');
        }


        $zones = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();

        if(empty($zones)){
            $counts = [];
            return backend_view('last_mile.route.list-only',compact('counts'));
        }
        if(count($zones) > 1 ){
            $zonesString = implode(",", $zones);
        }
        else{
            $zonesString = implode("", $zones);
        }

        $countQry = "SELECT route_id,joey_routes.joey_id,joey_routes.`date`,
        CONCAT(zones_routing.title,'(',joey_routes.zone,')') AS zone,
        COUNT(joey_route_locations.id) AS counts,
        SUM(CASE WHEN sprint__sprints.status_id in(17,113,114,116,117,118,132,138,139,144,104,105,106,107,108,109,110,111,112,131,135,136) THEN 0 ELSE 1 END) AS d_counts,
        SUM(joey_route_locations.distance) AS distance,
        SUM(CASE WHEN sprint__sprints.status_id in(17,113,114,116,117,118,132,138,139,144,104,105,106,107,108,109,110,111,112,131,135,136) THEN 0 ELSE joey_route_locations.distance END) AS d_distance,
        SEC_TO_TIME(SUM(TIME_TO_SEC(finish_time)-TIME_TO_SEC(arrival_time))) AS duration,
        SEC_TO_TIME(SUM(CASE WHEN sprint__sprints.status_id in(17,113,114,116,117,118,132,138,139,144,104,105,106,107,108,109,110,111,112,131,135,136) THEN 0 ELSE TIME_TO_SEC(finish_time)-TIME_TO_SEC(arrival_time) END)) AS d_duration
        FROM joey_route_locations 
        JOIN sprint__tasks ON(task_id=sprint__tasks.id) 
        JOIN sprint__sprints ON(sprint_id=sprint__sprints.id) 
        JOIN joey_routes ON(route_id=joey_routes.id) 
        JOIN locations ON(location_id=locations.id)
        LEFT JOIN  zones_routing ON (zones_routing.id=joey_routes.zone AND zones_routing.`deleted_at` IS NULL)
        WHERE joey_routes.date LIKE '".$date."%'
        #AND joey_routes.`mile_type` = 3
        AND zone in(".$zonesString.")
        AND sprint__tasks.`deleted_at` IS NULL
        AND joey_route_locations.`deleted_at` IS NULL 
        #AND zones_routing.`deleted_at` IS NULL
        AND joey_routes.deleted_at IS NULL GROUP BY route_id";

        $counts = DB::select($countQry);

        return backend_view('last_mile.route.list-only',compact('counts'));
    }

    //get route detail in popup
    public function getRouteDetail($routeId)
    {
        $routes = JoeyRouteLocations::join('sprint__tasks','task_id','=','sprint__tasks.id')
            ->join('merchantids','merchantids.task_id','=','sprint__tasks.id')
            ->join('sprint__contacts','contact_id','=','sprint__contacts.id')
            ->join('locations','location_id','=','locations.id')
            ->whereNull('sprint__tasks.deleted_at')
            ->where('route_id','=',$routeId)
            ->whereNull('joey_route_locations.deleted_at')
            ->orderBy('joey_route_locations.ordinal')
            ->get(['type','route_id','joey_route_locations.ordinal','merchant_order_num','sprint_id','name','phone','email','address','postal_code','latitude','longitude','distance']);


        return json_encode($routes);
    }

    // get route edit list of last mile
    public function lastMileRouteEdit($routeId,$hubId){

        $route = $this->hubRouteEdit($routeId);
        return backend_view('last_mile.route.edit',['route'=>$route,'hub_id'=>$hubId,"route_id"=>$routeId]);

    }

    //edit list query
    public function hubRouteEdit($routeId){

        $route = JoeyRouteLocations::join('sprint__tasks','joey_route_locations.task_id','=','sprint__tasks.id')
            ->Join('merchantids','merchantids.task_id','=','sprint__tasks.id')
            ->join('locations','location_id','=','locations.id')
            ->join('sprint__sprints','sprint_id','=','sprint__sprints.id')
            ->where('route_id','=',$routeId)
            ->whereNull('joey_route_locations.deleted_at')
            ->whereNull('sprint__sprints.deleted_at')
            ->whereNotNull('merchantids.tracking_id')
            ->orderBy('joey_route_locations.ordinal','asc')
            ->get([
                'joey_route_locations.id',
                'merchantids.merchant_order_num',
                'joey_route_locations.task_id',
                'merchantids.tracking_id',
                'sprint_id',
                'type',
                'due_time',
                'etc_time',
                'address',
                'postal_code',
                'joey_route_locations.arrival_time',
                'joey_route_locations.finish_time',
                'joey_route_locations.distance',
                'sprint__tasks.status_id',
                'joey_route_locations.is_transfered',
                'joey_route_locations.ordinal'
            ]);

        return $route;

    }

    // get route map detail of last mile
    public function RouteMap($route_id){

        $routes = JoeyRouteLocations::join('sprint__tasks','task_id','=','sprint__tasks.id')
            ->join('locations','location_id','=','locations.id')
            ->where('route_id','=',$route_id)
            ->whereNull('joey_route_locations.deleted_at')
            ->orderBy('joey_route_locations.ordinal')
            ->get(['type','route_id','joey_route_locations.ordinal','sprint_id','address','postal_code','latitude','longitude']);

        $i=0;
        $data=[];

        foreach($routes as $route){

            $data[] = $route;

            $lat[0] = substr($route->latitude, 0, 2);
            $lat[1] = substr($route->latitude, 2);
            $data[$i]['latitude'] = floatval($lat[0].".".$lat[1]);

            $long[0] = substr($route->longitude, 0, 3);
            $long[1] = substr($route->longitude, 3);
            $data[$i]['longitude'] = floatval($long[0].".".$long[1]);
            $i++;

        }
        return json_encode($data);
    }

    // get remaining route detail of last mile
    public function remainingRouteMap($route_id){

        $routes = JoeyRouteLocations::join('sprint__tasks','task_id','=','sprint__tasks.id')
            ->join('locations','location_id','=','locations.id')
            ->where('route_id','=',$route_id)
            ->whereNull('joey_route_locations.deleted_at')
            ->whereNotIn('status_id',[17,112,113,114,116,117,118,132,138,139,144,105,106,107,108,109,110,111,131,135])
            ->orderBy('joey_route_locations.ordinal')
            ->get(['type','route_id','joey_route_locations.ordinal','sprint_id','address','postal_code','latitude','longitude']);

        $i=0;
        foreach($routes as $route){

            $data[$i]['type'] = $route->type;
            $data[$i]['route_id'] = $route->route_id;
            $data[$i]['ordinal'] = $route->ordinal;
            $data[$i]['sprint_id'] = $route->sprint_id;
            $data[$i]['address'] = $route->address;
            $data[$i]['postal_code'] = $route->postal_code;

            $lat[0] = substr($route->latitude, 0, 2);
            $lat[1] = substr($route->latitude, 2);
            $data[$i]['latitude'] = floatval($lat[0].".".$lat[1]);

            $long[0] = substr($route->longitude, 0, 3);
            $long[1] = substr($route->longitude, 3);
            $data[$i]['longitude'] = floatval($long[0].".".$long[1]);
            $i++;

        }
        return json_encode($data);
    }

    // delete route of last mile
    public function lastMileDeleteRoute($routeId)
    {
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

    //re route of last mile
    public function reRoute($hubId,$routeId){

        $route = JoeyRouteLocations::join('sprint__tasks','joey_route_locations.task_id','=','sprint__tasks.id')
            ->join('locations','location_id','=','locations.id')
            ->whereNull('joey_route_locations.deleted_at')
            ->where('route_id','=',$routeId)
            ->whereNotIn('status_id',[17,36])
            ->get(['joey_route_locations.task_id','sprint_id','address','latitude','longitude','due_time','etc_time']);


        if($route->count()<1){
            return "No order to route";
        }

        foreach($route as $routeLoc){

            $lat[0] = substr($routeLoc->latitude, 0, 2);
            $lat[1] = substr($routeLoc->latitude, 2);
            $latitude=$lat[0].".".$lat[1];

            $long[0] = substr($routeLoc->longitude, 0, 3);
            $long[1] = substr($routeLoc->longitude, 3);
            $longitude=$long[0].".".$long[1];

            $orders[$routeLoc->task_id]= array(
                "location" => array(
                    "name" => $routeLoc->address,
                    "lat" => $latitude,
                    "lng" => $longitude
                ),
                "start" => "09:00",
                "end" => "21:00",
                "load" => 1,
                "duration" => 2
            );
        }

        $hubPick = Hub::find($hubId);
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
        else{
            $error = new LogRoutes();
            $error->error = 'hub address or format is incorrect';
            $error->save();


            echo '<script>alert("hub address or format is incorrect")</script>';
            echo "<script> window.history.back(); </script>";

        }

        $joey['joey'] = array(
            "start_location" => array(
                "id" => 1,
                "name" => $hubPick->address,
                "lat" => $hubLat,
                "lng" => $hubLong
            ),
            "shift_start" => '09:00',
            "shift_end" => '21:00'
        );

        $options = array(
            "shortest_distance" => true,
            "polylines" => true
        );

        $payload = array(
            "visits" => $orders,
            "fleet" => $joey,
            "options" => $options
        );

        $client = new \App\Classes\Client( '/vrp' );
        $client->setData($payload);
        $apiResponse= $client->send();

        if(!empty($apiResponse->solution)){
            foreach($apiResponse->solution as $solution){
                for($i=1;$i<count($solution);$i++){
                    JoeyRouteLocations::where('task_id','=',$solution[$i]->location_id)->update(['deleted_at' => date('Y-m-d H:i:s')]);

                    $routeLoc = new JoeyRouteLocations();
                    $routeLoc->route_id = $routeId;
                    $routeLoc->ordinal = $i;
                    $routeLoc->task_id = $solution[$i]->location_id;
                    $routeLoc->arrival_time = $solution[$i]->arrival_time;
                    $routeLoc->finish_time = $solution[$i]->finish_time;
                    $routeLoc->distance = $solution[$i]->distance;
                    $routeLoc->save();
                }
            }
            return "Route R-".$routeId." has been re-routed successfully";
        }

        return "Reroute FAILED";
    }

    //route history of last mile
    public function getLastMileRouteHistory($id)
    {
        $routeData = $this->getRouteHistory($id);
        return backend_view('last_mile.route.history',['routes'=>$routeData,'route_id'=>$id]);
    }

    // route history list
    public function getRouteHistory($id)
    {
        $routeData=RouteHistory::join('joeys','route_history.joey_id','=','joeys.id')
            ->leftjoin('merchantids','merchantids.task_id','=','route_history.task_id')
            ->leftjoin('dashboard_users','route_history.updated_by','=','dashboard_users.id')
            ->where('route_history.route_id','=',$id)
            ->whereNull('route_history.deleted_at')
            ->orderBy('route_history.created_at')->
            get(['route_history.id','route_history.route_id','route_history.status','route_history.joey_id','route_history.route_location_id',\DB::raw("CONVERT_TZ(route_history.created_at,'UTC','America/Toronto') as created_at")
                ,'route_history.ordinal','joeys.first_name','joeys.last_name','merchantids.tracking_id','route_history.type','route_history.updated_by','dashboard_users.full_name']);

        return $routeData;
    }

    //transfer route to joey
    public function routeTransfer(Request $request)
    {
        $routedata= JoeyRoute::where('id',$request->input('route_id'))->first();

        $joey_id=$routedata->joey_id;
        $routedata->joey_id=$request->input('joey_id');
        $routedata->save();

        // amazon entry data updated for joey tranfer in route
        $joey_data=Joey::where('id','=',$request->input('joey_id'))->first();
        // AmazonEntry::where('route_id','=',$request->get('route_id'))->
        //              whereNUll('deleted_at')->whereNull('delivered_at')->whereNotIn('task_status_id',[104,105,106,107,108,109,110,111,112,131,135,136,101,102,103])->
        //              update(['joey_id'=>$request->input('joey_id'),'joey_name'=>$joey_data->first_name." ".$joey_data->last_name]);

        $task_ids=JoeyRouteLocations::where('route_id','=',$request->get('route_id'))->whereNull('deleted_at')->pluck('task_id');

        $ctcEntriesSprintId=CTCEntry::whereIn('task_id',$task_ids)
            ->whereNUll('deleted_at')
            ->whereNotIn('task_status_id',[104,105,106,107,108,109,110,111,112,131,135,136,101,102,103])
            ->pluck('sprint_id');
        if ($ctcEntriesSprintId) {
            CTCEntry::whereIn('sprint_id',$ctcEntriesSprintId)->
            update(['joey_id'=>$request->input('joey_id'),'joey_name'=>$joey_data->first_name." ".$joey_data->last_name]);
        }

        $borderLessSprintId=BoradlessDashboard::whereIn('task_id',$task_ids)
            ->whereNUll('deleted_at')
            ->whereNotIn('task_status_id',[104,105,106,107,108,109,110,111,112,131,135,136,101,102,103])
            ->pluck('sprint_id');
        if ($borderLessSprintId) {
            BoradlessDashboard::whereIn('sprint_id',$borderLessSprintId)->
            update(['joey_id'=>$request->input('joey_id'),'joey_name'=>$joey_data->first_name." ".$joey_data->last_name]);
        }

        if($joey_id==null)
        {
            $route_history =new  RouteHistory();
            $route_history->route_id=$request->input('route_id');
            $route_history->joey_id=$request->input('joey_id');
            $route_history->route_location_id=NULL;
            $route_history->status=0;
            $route_history->save();
        }
        else
        {
            $route_history =new  RouteHistory();
            $route_history->route_id=$request->input('route_id');
            $route_history->joey_id=$request->input('joey_id');
            $route_history->route_location_id=NULL;
            $route_history->status=1;
            $route_history->save();
        }

        $deviceIds = UserDevice::where('user_id',$request->input('joey_id'))->where('is_deleted_at', 0)->pluck('device_token');
        $subject = 'New Route '.$request->input('route_id');
        $message = 'You have assigned new route';
        Fcm::sendPush($subject, $message,'ecommerce',null, $deviceIds);
        $payload =['notification'=> ['title'=> $subject,'body'=> $message,'click_action'=> 'ecommerce'],
            'data'=> ['data_title'=> $subject,'data_body'=> $message,'data_click_action'=> 'ecommerce']];
        $createNotification= [
            'user_id' => $request->input('joey_id'),
            'user_type'  => 'Joey',
            'notification'  => $subject,
            'notification_type'  => 'ecommerce',
            'notification_data'  => json_encode(["body"=> $message]),
            'payload'            => json_encode($payload),
            'is_silent'          => 0,
            'is_read'            => 0,
            'created_at'         => date('Y-m-d H:i:s')
        ];
        UserNotification::create($createNotification);

        if($joey_id != null)
        {
            if ($joey_id != $request->input('joey_id'))
            {
                $deviceIds = UserDevice::where('user_id',$joey_id)->where('is_deleted_at', 0)->pluck('device_token');
                $subject = 'Route Transferred '.$request->input('route_id');
                $message = 'Your route has been transferred to another joey';
                Fcm::sendPush($subject, $message,'ecommerce',null, $deviceIds);
                $payload =['notification'=> ['title'=> $subject,'body'=> $message,'click_action'=> 'ecommerce'],
                    'data'=> ['data_title'=> $subject,'data_body'=> $message,'data_click_action'=> 'ecommerce']];
                $createNotification= [
                    'user_id' => $joey_id,
                    'user_type'  => 'Joey',
                    'notification'  => $subject,
                    'notification_type'  => 'ecommerce',
                    'notification_data'  => json_encode(["body"=> $message]),
                    'payload'            => json_encode($payload),
                    'is_silent'          => 0,
                    'is_read'            => 0,
                    'created_at'         => date('Y-m-d H:i:s')
                ];
                UserNotification::create($createNotification);
            }
        }

        return response()->json(['status' => '1', 'body' => ['route_id'=>$request->route_id,'joey_id'=>$request->joey_id]]);

    }
    public function getLocationMap($id,Request $request)
    {


        $date=$request->input('date');

        if($id === 17)
        {
            $hub=$id;
            $ids=[477542,477255,477254,477283,477284,477286,477287,477288,477289,477307,477308,477309,477310,477311,477312,477313,477314,477292,477294,477315,477317,477316,477295
                ,477302,477303,477304,477305,477306,477296,477290,477297,477298,477299,477300,477320,477301,477318,477334,477335,477336,477337,477338,477339,477171,477559,477625,477587,477621,477627,477635,477633,477661];

        }
        elseif($id === 16)
        {
            $hub=$id;
            $ids=477260;

        }
        elseif($id === 19)
        {

            $ids = [477282,477340,477341,477342,477343, 477344, 477345,477346, 476592, 477631,477629];
            $hub=$id;

        }
        else{
            $hub = auth()->user()->hub_id;

        }

        $datas=JoeyRoute::
        where('hub','=',$hub)
            ->where('joey_routes.date','like',$date."%")
            ->where('joey_routes.mile_type','=',3)
            ->whereNull('deleted_at')
            ->OrderBy('date','asc')
            ->get(['joey_routes.id as route_id','date']);

        $value=[];
        $i=0;
        $key=[];
        foreach($datas as $data)
        {

            $location= JoeyRouteLocations::join('sprint__tasks','sprint__tasks.id','=','joey_route_locations.task_id')
                ->join('locations','locations.id','=','sprint__tasks.location_id')
                ->join('sprint__sprints','sprint__tasks.sprint_id','=','sprint__sprints.id')
                ->whereNull('joey_route_locations.deleted_at')->whereNull('sprint__tasks.deleted_at')
                ->whereNull('locations.deleted_at')
                ->where('joey_route_locations.route_id','=',$data->route_id)
                ->GroupBy('joey_route_locations.route_id')
                ->whereIn('sprint__sprints.status_id',[17,112,113,114,116,117,118,132,138,139,144,105,106,107,108,109,110,111,131,135])
                ->get(['locations.longitude','locations.latitude','sprint__tasks.sprint_id','locations.address','joey_route_locations.ordinal']);

            if(!$location->IsEmpty())
            {
                $key[]=$data->route_id;
            }

            $j=0;
            foreach($location as $loc)
            {
                $lat[0] = substr($loc->latitude, 0, 2);
                $lat[1] = substr($loc->latitude, 2);
                $value['data'][$i][$j]['latitude'] = floatval($lat[0].".".$lat[1]);

                $long[0] = substr($loc->longitude, 0, 3);
                $long[1] = substr($loc->longitude, 3);
                $value['data'][$i][$j]['longitude'] = floatval($long[0].".".$long[1]);


                $value['data'][$i][$j]['sprint_id']=$loc->sprint_id;
                $value['data'][$i][$j]['address']=$loc->address;
                $value['data'][$i][$j]['route_id']=$data->route_id.'-'.$loc->ordinal;
                $j++;
            }


            $i++;
        }

        $value['key']=$key;

        return json_encode($value);
    }
    public function getRouteMapLocation(Request $request)
    {
        $value=[];
        $ids=$request->input('ids');

        $i=0;
        foreach($ids as $id)
        {

            $location= JoeyRouteLocations::join('sprint__tasks','sprint__tasks.id','=','joey_route_locations.task_id')
                ->join('sprint__sprints','sprint__sprints.id','=','sprint__tasks.sprint_id')
                ->join('locations','locations.id','=','sprint__tasks.location_id')
                ->where('type','=','dropoff')
                ->where('joey_route_locations.route_id','=',$id)
                ->get(['locations.longitude','locations.latitude','sprint__tasks.sprint_id','locations.address','joey_route_locations.ordinal']);

            $j=0;

            foreach($location as $loc)
            {
                // $lat[0] = substr($loc->latitude, 0, 2);
                // $lat[1] = substr($loc->latitude, 2);
                // $value['data'][$i][$j]['latitude'] = floatval($lat[0].".".$lat[1]);
                $value['data'][$i][$j]['latitude'] = $loc->latitude/1000000;

                // $long[0] = substr($loc->longitude, 0, 3);
                // $long[1] = substr($loc->longitude, 3);
                // $value['data'][$i][$j]['longitude'] = floatval($long[0].".".$long[1]);
                $value['data'][$i][$j]['longitude'] = $loc->longitude/1000000;

                $value['data'][$i][$j]['sprint_id']=$loc->sprint_id;
                $value['data'][$i][$j]['address']=$loc->address;
                $value['data'][$i][$j]['route_id']=$id."-".$loc->ordinal;
                $value['data'][$i][$j]['ordinal']=$loc->ordinal;
                $j++;
            }
            $i++;
        }

        return json_encode($value);

    }


}
