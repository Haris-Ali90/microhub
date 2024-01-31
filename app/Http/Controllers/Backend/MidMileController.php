<?php

namespace App\Http\Controllers\Backend;

use App\AmazonEntry;
use App\AssignMiJob;
use App\Classes\Client;
use App\Classes\Fcm;
use App\Classes\MidMileClient;
use App\CTCEntry;
use App\CurrentHubOrder;
use App\Hub;
use App\HubStore;
use App\Joey;
use App\JoeyRoute;
use App\JoeyRouteLocations;
use App\LogRoutes;
use App\MicroHubOrder;
use App\MiJob;
use App\MiJobDetail;
use App\MiJobRoute;
use App\RouteHistory;
use App\RoutingZones;
use App\SlotJob;
use App\Slots;
use App\Sprint;
use App\Task;
use App\User;
use App\UserDevice;
use App\UserNotification;
use App\Vendor;
use Illuminate\Http\Request;

class MidMileController extends BackendController
{
    public $test = array("136" => "Client requested to cancel the order",
        "137" => "Delay in delivery due to weather or natural disaster",
        "118" => "left at back door",
        "117" => "left with concierge",
        "135" => "Customer refused delivery",
        "108" => "Customer unavailable-Incorrect address",
        "106" => "Customer unavailable - delivery returned",
        "107" => "Customer unavailable - Left voice mail - order returned",
        "109" => "Customer unavailable - Incorrect phone number",
        "142" => "Damaged at hub (before going OFD)",
        "143" => "Damaged on road - undeliverable",
        "144" => "Delivery to mailroom",
        "103" => "Delay at pickup",
        "139" => "Delivery left on front porch",
        "138" => "Delivery left in the garage",
        "114" => "Successful delivery at door",
        "113" => "Successfully hand delivered",
        "120" => "Delivery at Hub",
        "110" => "Delivery to hub for re-delivery",
        "111" => "Delivery to hub for return to merchant",
        "121" => "Pickup from Hub",
        "102" => "Joey Incident",
        "104" => "Damaged on road - delivery will be attempted",
        "105" => "Item damaged - returned to merchant",
        "129" => "Joey at hub",
        "128" => "Package on the way to hub",
        "140" => "Delivery missorted, may cause delay",
        "116" => "Successful delivery to neighbour",
        "132" => "Office closed - safe dropped",
        "101" => "Joey on the way to pickup",
        "32" => "Order accepted by Joey",
        "14" => "Merchant accepted",
        "36" => "Cancelled by JoeyCo",
        "124" => "At hub - processing",
        "38" => "Draft",
        "18" => "Delivery failed",
        "56" => "Partially delivered",
        "17" => "Delivery success",
        "68" => "Joey is at dropoff location",
        "67" => "Joey is at pickup location",
        "13" => "At hub - processing",
        "16" => "Joey failed to pickup order",
        "57" => "Not all orders were picked up",
        "15" => "Order is with Joey",
        "112" => "To be re-attempted",
        "131" => "Office closed - returned to hub",
        "125" => "Pickup at store - confirmed",
        "61" => "Scheduled order",
        "37" => "Customer cancelled the order",
        "34" => "Customer is editting the order",
        "35" => "Merchant cancelled the order",
        "42" => "Merchant completed the order",
        "54" => "Merchant declined the order",
        "33" => "Merchant is editting the order",
        "29" => "Merchant is unavailable",
        "24" => "Looking for a Joey",
        "23" => "Waiting for merchant(s) to accept",
        "28" => "Order is with Joey",
        "133" => "Packages sorted",
        "55" => "ONLINE PAYMENT EXPIRED",
        "12" => "ONLINE PAYMENT FAILED",
        "53" => "Waiting for customer to pay",
        "141" => "Lost package",
        "60" => "Task failure",
        "255" =>"Order delay",
        "145"=>"Returned To Merchant",
        "146" => "Delivery Missorted, Incorrect Address",
        "147" => "Scanned at hub",
        "148" => "Scanned at Hub and labelled",
        "149" => "pick from hub",
        "150" => "drop to other hub",
        '153' => 'Miss sorted to be reattempt',
        '154' => 'Joey unable to complete the route', '155' => 'To be re-attempted tomorrow',
        "151" => "",
        "152" => "",
    );

    private $client;

    /**
     * Create a new controller instance.
     *
     * @param MidMileClient $client
     */
    public function __construct(MidMileClient $client)
    {
        $this->client = $client;
    }

    // mid mile assigned job list
    public function miJob(Request $request)
    {
        $date = $request->get('date');
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $hubId = auth()->user()->hub_id;

        $miJobId = AssignMiJob::where('hub_id', $hubId)->pluck('mi_job_id');

        $miJobs = MiJob::whereIn('id',$miJobId)
//            ->where('created_at', 'like', $date . '%')
            ->where('type', 'micro_hub_mid_mile')
            ->get();

        return backend_view('mid_mile.mi_job.list', compact('miJobs'));
    }

    // mid mile job detail
    public function detail(MiJob $mi_job)
    {
        $miJobDetail = MiJobDetail::where('mi_job_id', $mi_job->id)->whereNull('deleted_at')->get();
        return backend_view('mid_mile.mi_job.detail', compact('miJobDetail', 'mi_job'));
    }

    // create route for mid mile
    public function createRouteForMidMile(Request $request){

        $jobId = $request->get('job_id');
        $date = $request->get('create_date');
        $authHubId = auth()->user()->hub_id;

        $miJobs = MiJob::join('mi_job_details', 'mi_job_details.mi_job_id', '=', 'mi_jobs.id')
            ->where('mi_jobs.id', $jobId)
            ->whereNull('mi_job_details.deleted_at')
            ->get(['mi_jobs.*', 'mi_jobs.type as mid_mile_type', 'mi_job_details.*']);

        $hubId = 0;
        $payload = [];
        $visits=[];
        $fleets=[];
        $mileType='';
        $hubOrderCount=0;
        $sprintOrderCount=0;
        foreach ($miJobs as $key => $miJob) {
            if ($miJob->type == 'pickup') {
                if ($miJob->location_type == 'store') {
                    $mileType=$miJob->location_type;
                    $vendor = Vendor::find($miJob->locationid);

                    $sprint = Sprint::where('creator_id', $miJob->locationid)
                        ->whereIn('status_id', [61,24])
                        ->whereNull('deleted_at')
                        ->count();

                    $sprintOrderCount += $sprint;

                    if($sprintOrderCount > 0){
                        if(isset($vendor)){
                            if($vendor->location){
                                $lat[0] = substr($vendor->location->latitude, 0, 2);
                                $lat[1] = substr($vendor->location->latitude, 2);
                                $latitude = $lat[0] . "." . $lat[1];

                                $long[0] = substr($vendor->location->longitude, 0, 3);
                                $long[1] = substr($vendor->location->longitude, 3);
                                $longitude = $long[0] . "." . $long[1];

                                $visits[$miJob->locationid] = [
                                    "location" => [
                                        "name" => $vendor->business_address,
                                        "lat" => $latitude,
                                        "lng" => $longitude,
                                    ],
                                    "duration" => 10,
                                ];
                                if($miJob->start_time != null){
                                    $visits[$miJob->locationid]['start'] = date('H:i',strtotime($miJob->start_time));
                                }
                                if($miJob->end_time != null){
                                    $visits[$miJob->locationid]['end'] = date('H:i',strtotime($miJob->end_time));
                                }
                            }
                        }
                    }
                }

                if ($miJob->location_type == 'hub') {
                    $mileType=$miJob->location_type;
                    $user = User::where('hub_id', $miJob->locationid)->pluck('id');

                    $hubids = MicroHubOrder::getHubIds($miJob->locationid,$user);

                    $microHubOrder = MicroHubOrder::join('sprint__sprints','sprint__sprints.id','=','orders_actual_hub.sprint_id')
                        ->whereIn('sprint__sprints.status_id', [148])
                        ->whereNotIn('sprint__sprints.status_id', [36])
                        ->where('orders_actual_hub.is_my_hub', 0)
                        ->whereIn('orders_actual_hub.scanned_by',$user)
                        ->whereNull('orders_actual_hub.deleted_at')
                        ->count();
                    $sprintIds = CurrentHubOrder::where('hub_id', $miJob->locationid)->where('is_actual_hub', 0)->pluck('sprint_id');

                    $hubBundleOther = MicroHubOrder::join('sprint__sprints','sprint__sprints.id','=','orders_actual_hub.sprint_id')
                        ->where('sprint__sprints.status_id', 150)
                        ->whereNotIn('sprint__sprints.status_id', [36])
                        ->whereIn('orders_actual_hub.sprint_id',$sprintIds)
                        ->count();

                    $hubOrderCount += $microHubOrder + $hubBundleOther;

                    if($hubOrderCount > 0){

                        $hub = Hub::find($miJob->locationid);
                        if(isset($hub)){
                            if(in_array($miJob->locationid, $hubids)){
                                $visits[$miJob->locationid] = [
                                    "location" => [
                                        "name" => $hub->address,
                                        "lat" => $hub->hub_latitude,
                                        "lng" => $hub->hub_longitude,
                                    ],
                                    "duration" => 10,
                                ];

                                if($miJob->start_time != null){
                                    $visits[$miJob->locationid]['start'] = date('H:i',strtotime($miJob->start_time));
                                }
                                if($miJob->end_time != null){
                                    $visits[$miJob->locationid]['end'] = date('H:i',strtotime($miJob->end_time));
                                }
                            }
                        }
                    }

                }
            }
            if ($miJob->type == 'dropoff') {
                $hub = Hub::find($miJob->locationid);
                $fleets[$miJob->locationid] = array(
                    "start_location" => array(
                        "name" => $miJob->start_address,
                        "lat" => $miJob->start_latitude,
                        "lng" => $miJob->start_longitude
                    ),
                    "end_location" => array(
                        "name" => $hub->address,
                        "lat" => $hub->hub_latitude,
                        "lng" => $hub->hub_longitude
                    ),
                    "shift_start" => date('H:i',strtotime($miJob->start_time)),
                    "shift_end" => date('H:i',strtotime($miJob->end_time)),
                );
                $hubId = $hub->id;
            }
        }

        if($sprintOrderCount == 0 && $hubOrderCount == 0){
            return json_encode([
                "status" => "Route Creation Error",
                "output"=> 'This job has zero order count'
            ]);
        }

        $payload = array(
            "visits" => $visits,
            "fleet" => $fleets,
        );


        $res = json_encode($payload);
        $result = $this->client->getJobId($res);

        if(isset($result->solution)){
            $solution = $result->solution;
            if($result->num_unserved > 0){
                return json_encode([
                    "status" => "Route Creation Error",
                    "output"=> 'Something went wrong, please contact your administrator'
                ]);
            }
            if(!empty($solution)){
                foreach ($solution as $key => $value){
                    if(count($value)>1){

                        $miJobDetail = MiJobDetail::where('location_type', 'store')->where('mi_job_id', $jobId)->first();
                        $routeType = 2;
                        if(isset($miJobDetail)){
                            if($miJobDetail->location_type == 'store'){
                                $routeType = 4;
                            }
                        }

                        $Route = new JoeyRoute();
                        $Route->date =date('Y-m-d H:i:s');
                        $Route->hub = $authHubId;
                        if(isset($result->total_working_time)){
                            $Route->total_travel_time=$result->total_working_time;
                        }
                        else{
                            $Route->total_travel_time=0;
                        }
                        if(isset($result->total_distance))
                        {
                            $Route->total_distance=$result->total_distance;
                        }
                        else
                        {
                            $Route->total_distance=0;
                        }
                        $Route->mile_type = $routeType;
                        $Route->save();

                        MiJobRoute::create([
                            'route_id' => $Route->id,
                            'mi_job_id' => $jobId,
                        ]);

                        for($i=0;$i<count($value);$i++){
                            if($i>0){

                                $routeLoc = new JoeyRouteLocations();
                                $routeLoc->route_id = $Route->id;
                                $routeLoc->ordinal = $i;
                                $routeLoc->task_id = $value[$i]->location_id;

                                if(isset($value[$i]->distance) && !empty($value[$i]->distance)){
                                    $routeLoc->distance = $value[$i]->distance;
                                }

                                if(isset($value[$i]->arrival_time) && !empty($value[$i]->arrival_time)){
                                    $routeLoc->arrival_time = $value[$i]->arrival_time;
                                    if(isset($value[$i]->finish_time)){
                                        $routeLoc->finish_time = $value[$i]->finish_time;
                                    }
                                }
                                $routeLoc->save();

                            }
                        }

                    }
                }
                return response()->json(['status' => 200, "output" => 'Route has been created successfully']);
            }
        }else{
            return json_encode([
                "status" => "Route Creation Error",
                "output"=> "Something went wrong, please contact your administrator"
            ]);
        }
    }

    //get mid mile route list
    public function midMileRoutesList(Request $request)
    {
        date_default_timezone_set("America/Toronto");

        if(empty($request->input('date'))){
            $date = date('Y-m-d');
        }
        else{
            $date = $request->input('date');
        }
        $hubId = auth()->user()->hub_id;

        $routes = JoeyRoute::join('joey_route_locations','joey_route_locations.route_id' ,'=', 'joey_routes.id')
            ->Leftjoin('joeys', 'joeys.id', '=', 'joey_routes.joey_id')
            ->whereNull('joey_route_locations.deleted_at')
            ->whereNull('joey_routes.deleted_at')
            ->where('mile_type',2)
            ->where('date', 'LIKE', $date.'%')
            ->where('hub', $hubId)
            ->groupBy('joey_route_locations.route_id')
            ->get(['joey_routes.id', 'joey_routes.joey_id', 'joeys.first_name', 'joeys.last_name', 'joey_routes.date', 'joey_route_locations.route_id', 'joey_route_locations.task_id']);

        return backend_view('mid_mile.route.list',compact('routes'));
    }

    //mid mile Detail route list po up
    public function getRouteDetail(Request $request, $routeId)
    {

        if(empty($request->input('date'))){
            $date = date('Y-m-d');
        }
        else{
            $date = $request->input('date');
        }
        // get all tasks ids in joey locations table
        $taskIds = JoeyRouteLocations::where('route_id',$routeId)->pluck('task_id')->toArray();
        // get last drop off task id
        $lastLocationId = JoeyRouteLocations::where('route_id',$routeId)->pluck('task_id')->last();

        $miJobRoute = MiJobRoute::where('route_id', $routeId)->first();

        $miJobLocationId = MiJobDetail::where('type', 'pickup')->where('mi_job_id', $miJobRoute->mi_job_id)->whereIn('locationid', $taskIds)->pluck('locationid')->toArray();

        $startHub = MiJob::find($miJobRoute->mi_job_id);

        $scannedBy = User::whereIn('hub_id', $miJobLocationId)->pluck('id')->toArray();
        //get sprint ids against ids variable

        $hubBundles = MicroHubOrder::join('sprint__sprints','sprint__sprints.id','=','orders_actual_hub.sprint_id')
            ->where('sprint__sprints.status_id', 148)
            ->whereNotIn('sprint__sprints.status_id', [36])
            ->whereNull('sprint__sprints.deleted_at')
            ->where('orders_actual_hub.is_my_hub', 0)
            ->whereIn('orders_actual_hub.scanned_by', $scannedBy)
            ->whereNull('orders_actual_hub.deleted_at')
//            ->where('created_at', 'LIKE', $date.'%')
            ->groupBy('orders_actual_hub.hub_id')
            ->pluck('orders_actual_hub.sprint_id')
            ->toArray();

        $sprintIds = CurrentHubOrder::whereIn('hub_id', $miJobLocationId)->where('is_actual_hub', 0)->pluck('sprint_id');

        $otherHubBundles = MicroHubOrder::join('sprint__sprints','sprint__sprints.id','=','orders_actual_hub.sprint_id')
            ->whereIn('sprint__sprints.status_id', [150])
            ->whereNotIn('sprint__sprints.status_id', [36])
            ->whereNull('sprint__sprints.deleted_at')
            ->whereIn('orders_actual_hub.sprint_id',$sprintIds)
//            ->whereDate('created_at', 'LIKE', $date.'%')
            ->whereNull('orders_actual_hub.deleted_at')
            ->groupBy('orders_actual_hub.bundle_id')
            ->pluck('orders_actual_hub.sprint_id')
            ->toArray();



        $microhubBundle = array_merge($hubBundles, $otherHubBundles);
        $hubBundlesData=[];
        $uniqueSprintId = array_unique($microhubBundle);

        foreach ($uniqueSprintId as $hubBundle){
            $orderActualHub = MicroHubOrder::where('sprint_id', $hubBundle)->first();

            $hub = Hub::find($orderActualHub['hub_id']);
            $microHubOrderCount = MicroHubOrder::where('is_my_hub', 0)->where('bundle_id', $orderActualHub['bundle_id'])->count();
            $hubBundlesData[] =[
                'id' => 'MMB-'.$orderActualHub['hub_id'],
                'bundle_id' => $orderActualHub['bundle_id'],
                'reference_no' => 'MR-'.$miJobRoute->mi_job_id,
                'hub_name' => $hub->title,
                'address' => $hub->address,
                'latitude' => $hub->hub_latitude,
                'longitude' => $hub->hub_longitude,
                'no_of_order' => $microHubOrderCount
            ];
        }
        //get last dropOff hub
        $hub = Hub::find($lastLocationId);

        return json_encode(['routes'=>$hubBundlesData, 'hub' => $hub, 'pickUpHub' => $startHub, 'reference_id' => $miJobRoute->mi_job_id]);

    }

    // get route edit list of mid mile
    public function midMileRouteEdit(Request $request, $routeId){

        $route = $this->hubRouteEdit($routeId, $request);
        return backend_view('mid_mile.route.edit',['route'=>$route,"route_id"=>$routeId]);

    }

    //edit list query
    public function hubRouteEdit($routeId, $request){
        if(empty($request->input('date'))){
            $date = date('Y-m-d');
        }
        else{
            $date = $request->input('date');
        }

        // get all tasks ids in joey locations table
        $taskIds = JoeyRouteLocations::where('route_id',$routeId)->pluck('task_id')->toArray();
        // get last drop off task id
//        $lastLocationId = JoeyRouteLocations::where('route_id',$routeId)->pluck('task_id')->last();
        $miJobRoute = MiJobRoute::where('route_id', $routeId)->first();
        $miJobLocationId = MiJobDetail::where('mi_job_id', $miJobRoute->mi_job_id)->whereIn('locationid', $taskIds)->pluck('locationid')->toArray();

//        $startHub = MiJob::find($miJobRoute->mi_job_id);
        $scannedBy = User::whereIn('hub_id', $miJobLocationId)->pluck('id')->toArray();
        //get sprint ids against ids variable
        $hubBundles = MicroHubOrder::join('sprint__sprints','sprint__sprints.id','=','orders_actual_hub.sprint_id')
            ->where('sprint__sprints.status_id', 148)
            ->whereNotIn('sprint__sprints.status_id', [36])
            ->whereNull('sprint__sprints.deleted_at')
            ->where('orders_actual_hub.is_my_hub', 0)
            ->whereIn('orders_actual_hub.scanned_by', $scannedBy)
            ->whereNull('orders_actual_hub.deleted_at')
//            ->where('created_at', 'LIKE', $date.'%')
            ->groupBy('orders_actual_hub.hub_id')
            ->pluck('orders_actual_hub.sprint_id')
            ->toArray();

        $sprintIds = CurrentHubOrder::whereIn('hub_id', $miJobLocationId)->where('is_actual_hub', 0)->pluck('sprint_id');

        $otherHubBundles = MicroHubOrder::join('sprint__sprints','sprint__sprints.id','=','orders_actual_hub.sprint_id')
            ->whereIn('sprint__sprints.status_id', [150])
            ->whereNotIn('sprint__sprints.status_id', [36])
            ->whereNull('sprint__sprints.deleted_at')
            ->whereIn('orders_actual_hub.sprint_id',$sprintIds)
//            ->whereDate('created_at', 'LIKE', $date.'%')
            ->whereNull('orders_actual_hub.deleted_at')
            ->groupBy('orders_actual_hub.bundle_id')
            ->pluck('orders_actual_hub.sprint_id')
            ->toArray();

        $microhubBundle = array_merge($hubBundles, $otherHubBundles);


        $hubBundlesData=[];

        foreach ($microhubBundle as $hubBundle){
            $orderActualHub = MicroHubOrder::where('sprint_id', $hubBundle)->first();

            $hub = Hub::find($orderActualHub['hub_id']);
            $microHubOrderCount = MicroHubOrder::where('is_my_hub', 0)->where('bundle_id', $orderActualHub['bundle_id'])->count();
            $hubBundlesData[] =[
                'id' => 'MMB-'.$orderActualHub['hub_id'],
                'bundle_id' => $orderActualHub['bundle_id'],
                'reference_no' => 'MR-'.$miJobRoute->mi_job_id,
                'hub_id' => $orderActualHub['hub_id'],
                'hub_name' => $hub->title,
                'address' => $hub->address,
                'latitude' => $hub->hub_latitude,
                'longitude' => $hub->hub_longitude,
                'no_of_order' => $microHubOrderCount
            ];
        }

        return $hubBundlesData;

    }

    // bundle order details
    public function bundleOrderDetails(Request $request, $hubId, $bundleId){
        $date = date('Y-m-d');

        $microHubBundle = MicroHubOrder::where('is_my_hub', 0)->where('bundle_id',$bundleId)->whereNull('deleted_at')->get();

        $data=[];
        foreach($microHubBundle as $hubOrder){
            $sprint = Sprint::with('sprint_Tasks', 'sprint_Tasks.merchantIds')
                ->whereHas('sprint_Tasks', function ($query){
                    $query->where('type', 'dropoff');
                })->whereNull('deleted_at')->find($hubOrder->sprint_id);

            $hub = Hub::whereNull('deleted_at')->find($hubOrder->hub_id);

            $trackingId='';
            $merchantOrderNo='';
            if (isset($sprint->sprint_Tasks)) {
                foreach($sprint->sprint_Tasks as $task){
                    if($task->type == 'dropoff'){
                        if(isset($task->merchantIds)){
                            $trackingId = ($task->merchantIds->tracking_id) ? $task->merchantIds->tracking_id : '';
                            $merchantOrderNo = ($task->merchantIds->merchant_order_num) ? $task->merchantIds->merchant_order_num : '';
                        }
                    }
                }
            }

            $data[] = [
                'id' => $hubOrder->id,
                'bundle_id' => 'MMB-'.$hub->id,
                'hub_name' => $hub->title,
                'hub_address' => $hub->address,
                'sprint_id' => 'CR-'.$hubOrder->sprint_id,
                'tracking_id' => $trackingId,
                'merchant_order_no' => $merchantOrderNo,
                'status_id' => $sprint->status_id,
            ];
        }

        return json_encode(['details'=>$data, 'statuses' => $this->test]);
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

//        $task_ids=JoeyRouteLocations::where('route_id','=',$request->get('route_id'))->whereNull('deleted_at')->pluck('task_id');
//
//        $amazonEntriesSprintId=AmazonEntry::whereIn('task_id',$task_ids)
//            ->whereNUll('deleted_at')
//            ->whereNotIn('task_status_id',[104,105,106,107,108,109,110,111,112,131,135,136,101,102,103])
//            ->pluck('sprint_id');
//
//        if($amazonEntriesSprintId)
//        {
//            AmazonEntry::whereIn('sprint_id',$amazonEntriesSprintId)->
//            update(['joey_id'=>$request->input('joey_id'),'joey_name'=>$joey_data->first_name." ".$joey_data->last_name]);
//        }
//
//        $ctcEntriesSprintId=CTCEntry::whereIn('task_id',$task_ids)
//            ->whereNUll('deleted_at')
//            ->whereNotIn('task_status_id',[104,105,106,107,108,109,110,111,112,131,135,136,101,102,103])
//            ->pluck('sprint_id');
//        if ($ctcEntriesSprintId) {
//            CTCEntry::whereIn('sprint_id',$ctcEntriesSprintId)->
//            update(['joey_id'=>$request->input('joey_id'),'joey_name'=>$joey_data->first_name." ".$joey_data->last_name]);
//        }
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

    // route map
    public function RouteMap(Request $request, $route_id){

        if(empty($request->input('date'))){
            $date = date('Y-m-d');
        }
        else{
            $date = $request->input('date');
        }
        // get all tasks ids in joey locations table
        $miJobId = MiJobRoute::where('route_id', $route_id)->pluck('mi_job_id');
        $miJobDetails = MiJobDetail::whereIn('mi_job_id', $miJobId)->get();
        $i=0;
        $data=[];
        foreach($miJobDetails as $miJobDetail){
            if($miJobDetail->type == 'pickup'){
                if($miJobDetail->location_type == 'store'){
                    $vendor = Vendor::whereNull('deleted_at')->find($miJobDetail->locationid);

                    $name = $vendor->name;
                    $address = $vendor->business_address;

                    $lat[0] = substr($vendor->latitude, 0, 2);
                    $lat[1] = substr($vendor->latitude, 2);
                    $dataLatitude = floatval($lat[0].".".$lat[1]);

                    $long[0] = substr($vendor->longitude, 0, 3);
                    $long[1] = substr($vendor->longitude, 3);
                    $dataLongitude = floatval($long[0].".".$long[1]);

                    if($vendor->business_address == null){
                        $address = $vendor->location->address;

                        $lat[0] = substr($vendor->location->latitude, 0, 2);
                        $lat[1] = substr($vendor->location->latitude, 2);
                        $dataLatitude = floatval($lat[0].".".$lat[1]);

                        $long[0] = substr($vendor->location->longitude, 0, 3);
                        $long[1] = substr($vendor->location->longitude, 3);
                        $dataLongitude = floatval($long[0].".".$long[1]);
                    }
                }
                if($miJobDetail->location_type == 'hub'){
                    $hub = Hub::whereNull('deleted_at')->find($miJobDetail->locationid);
                    $name = $hub->title;
                    $address = $hub->address;
                    $dataLatitude = $hub->hub_latitude;
                    $dataLongitude = $hub->hub_longitude;
                }
            }
            if($miJobDetail->type == 'dropoff'){
                if($miJobDetail->location_type == 'hub'){
                    $hub = Hub::whereNull('deleted_at')->find($miJobDetail->locationid);
                    $name = $hub->title;
                    $address = $hub->address;
                    $dataLatitude = $hub->hub_latitude;
                    $dataLongitude = $hub->hub_longitude;
                }
            }


            $data[$i]['latitude'] = $dataLatitude;
            $data[$i]['longitude'] = $dataLongitude;
            $data[$i]['address'] = $address;
            $data[$i]['id'] = $miJobDetail->locationid;
            $data[$i]['name'] = $name;
            $data[$i]['type'] = $miJobDetail->type;
            $i++;
        }

        return json_encode($data);
    }

    // delete route of mid mile
    public function midMileDeleteRoute($routeId)
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
        return  "Route R-".$routeId." deleted Successfully";
    }





    // get mid mile hub list with orders Count
//    public function index(Request $request)
//    {
//        $hubId = auth()->user()->hub_id;
//        $Date =$request->get('create_date');
//
//        $hubStores = HubStore::with('stores')->whereNull('deleted_at')->where('hub_id', auth()->user()->hub_id)->pluck('vendor_id');
//        $sprints = Sprint::with('Vendor')
//            ->whereIn('status_id',[128])
////            ->whereDate('created_at', '=', date('Y-m-d'))
//            ->whereIn('creator_id', $hubStores)
//            ->groupBy('creator_id')
//            ->get();
//
//        $hubIds = [];
//        foreach($sprints as $sprint){
//            $hubIds[] = MicroHubOrder::where('sprint_id',$sprint->id)->pluck('hub_id');
//        }
//
//        $hubs = Hub::with('sprint')->whereNull('deleted_at')->find($hubIds);
//        return backend_view('mid_mile.index', ['data'=> $hubs, 'id' => $hubId]);
//    }
//    //get mid mile order count
//    public function getMidMileOrderCount($hub_id, $date)
//    {
//        $vendorIds = HubStore::where('hub_id', $hub_id)->WhereNull('deleted_at')->pluck('vendor_id');
//        $sprints = Sprint::with('Vendor')
//            ->whereIn('status_id',[128])
//            ->whereIn('creator_id', $vendorIds)
//            ->groupBy('creator_id')
//            ->get();
//        $hubIds = [];
//        $orderCount = 0;
//        foreach($sprints as $sprint){
//            $hubIds[] = MicroHubOrder::where('sprint_id',$sprint->id)->pluck('hub_id');
//        }
//        $hubs = Hub::with('sprint')->whereNull('deleted_at')->find($hubIds);
//
//        foreach ($hubs as $hub){
//            $orderCount+=$hub->sprint->count();
//        }
//
//        $joeyCount = Slots::where('hub_id', '=',  $hub_id)
//            ->WhereNull('slots.deleted_at')
//            ->where('mile_type',2)
//            ->sum('joey_count');
//
//        $vehicleTyp = Slots::where('hub_id', '=',  $hub_id)
//            ->join('vehicles', 'vehicles.id', '=', 'slots.vehicle')
//            ->WhereNull('slots.deleted_at')
//            ->where('mile_type',2)
//            ->get(['vehicles.name', 'slots.joey_count']);
//
//        if($joeyCount==null){
//            $joeyCount=0;
//        }
//
//        if($vehicleTyp->isEmpty()){
//            $vehicleTyp[0]=['name'=>'','joey_count'=>''];
//        }
//
//        $response = ['orders' => $orderCount, 'joeys_count' => $joeyCount, 'slots_detail' => $vehicleTyp];
//
//        return json_encode($response);
//
//
//    }
//    public function createJobIdForMidMile(Request $request)
//    {
//        date_default_timezone_set('America/Toronto');
//        // pluck vendor ids for get hub stores
//        $Date =$request->get('create_date');
//        $hubId = auth()->user()->hub_id;
//        $vendorIds = HubStore::with('stores')->whereNull('deleted_at')->where('hub_id', auth()->user()->hub_id)->pluck('vendor_id');
//        $sprints = Sprint::with('Vendor')
//            ->whereIn('status_id',[128])
//            ->whereIn('creator_id', $vendorIds)
//            ->groupBy('creator_id')
//            ->get();
//
//        $hubIds = [];
//        $orderCount = 0;
//        foreach($sprints as $sprint){
//            $hubIds[] = MicroHubOrder::where('sprint_id',$sprint->id)->pluck('hub_id');
//        }
//        $otherHubs = Hub::with('sprint')->whereNull('deleted_at')->find($hubIds);
//
//
//        $mineHub = Hub::find(auth()->user()->hub_id);
//        $consolidatedHub = Hub::where('is_consolidated',1)->where('city__id', $mineHub->city__id)->first();
//
//
//
//        $orders = array();
//        foreach ($otherHubs as $hub){
//            if(count($hub->sprint) < 1){
//                return response()->json( ['status_code'=>400,"error"=>'No Order in this hub']);
//            }
//            foreach($hub->sprint as $sprint){
//
//                $hubLatitude = (float)substr($hub->hub_latitude, 0, 8) / 1000000;
//                $hubLongitude = (float)substr($hub->hub_longitude, 0, 9) / 1000000;
//                $orders[$hub->id]= array(
//                    "location" => array(
//                        "name" => $hub->address,
//                        "lat" => $hubLatitude,
//                        "lng" => $hubLongitude
//                    ),
//                    "load" => $hub->sprint->count(),
//                    "duration" => 2
//                );
//
//            }
//        }
//
//        $consolidatedHubLatitude = (float)substr($consolidatedHub->hub_latitude, 0, 8) / 1000000;
//        $consolidatedHubLongitude = (float)substr($consolidatedHub->hub_longitude, 0, 9) / 1000000;
//
//        $orders[$consolidatedHub->id] = array(
//            "location" => array(
//                "name" => $consolidatedHub->address,
//                "lat" => $consolidatedHubLatitude,
//                "lng" => $consolidatedHubLongitude
//            ),
//            "load" => $hub->sprint->count(),
//            "duration" => 2
//        );
//
//
//        $hubPick = Hub::where('id','=',$request->hub_id)->first();
//        $zone = RoutingZones::where('hub_id','=',$request->hub_id)->first();
//        $address = urlencode($hubPick->address);
//        // google map geocode api url
//        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0";
//
//        // get the json response
//        $resp_json = file_get_contents($url);
//
//        // decode the json
//        $resp = json_decode($resp_json, true);
//
//        // response status will be 'OK', if able to geocode given address
//        if($resp['status']=='OK'){
//            $hubLat = $resp['results'][0]['geometry']['location']['lat'];
//            $hubLong = $resp['results'][0]['geometry']['location']['lng'];
//        }
//
//        //joey slots count
//        $joeycounts=Slots::join('vehicles','slots.vehicle','=','vehicles.id')
//            ->where('slots.hub_id','=',$request->hub_id)
//            ->where('slots.mile_type', '=', 2)
//            ->whereNull('slots.deleted_at')
//            ->get(['vehicles.capacity','vehicles.min_visits','slots.start_time','slots.end_time','slots.hub_id','slots.joey_count','custom_capacity']);
//
//        if(count($joeycounts)<1){
//            return response()->json( ['status_code'=>400,"error"=>'No slot in this hub']);
//        }
//        $j=0;
//        foreach($joeycounts as $joe){
//            if(!empty($joe->joey_count)){
//                $joeycount= $joe->joey_count;
//            }
//            if(!isset($joeycount) || empty($joeycount)){
//                return response()->json( ['status_code'=>400,"error"=>'Joey count should be greater than 1 in slot']);
//            }
//
//
//            for($i=1;$i<=$joeycount;$i++){
//                if(empty($joe->custom_capacity)){
//                    $capacity = $joe->capacity;
//                }
//                else{
//                    $capacity = $joe->custom_capacity;
//                }
//                $shifts["joey_".$j] = array(
//                    "start_location" => array(
//                        "id" => $j,
//                        "name" => $hubPick->address,
//                        "lat" => $hubLat,
//                        "lng" => $hubLong
//                    ),
//                    "end_location" => array(
//                        "id" => $j,
//                        "name" => $hubPick->address,
//                        "lat" => $hubLat,
//                        "lng" => $hubLong
//                    ),
//                    "shift_start" => date('H:i',strtotime($joe->start_time)),
//                    "shift_end" => date('H:i',strtotime($joe->end_time)),
//                    "capacity" => $capacity,
//                    "min_visits_per_vehicle" => $joe->min_visits
//                );
//                $j++;
//            }
//        }
//
//        $options = array(
//            "shortest_distance" => true,
//            "polylines" => true
//        );
//
//        $payload = array(
//            "visits" => $orders,
//            "fleet" => $shifts,
//            "options" => $options
//        );
//
//        $client = new Client( '/vrp-long' );
//        $client->setData($payload);
//        $apiResponse= $client->send();
//
//        if(!empty($apiResponse->error)){
//            return response()->json( ['status_code'=>400,"error"=>$apiResponse->error]);
//        }
//
//
//        $slotjob  = new  SlotJob();
//        $slotjob->job_id=$apiResponse->job_id;
//        $slotjob->hub_id=$request->hub_id;
//        $slotjob->engine = 2;
//        $slotjob->mile_type = 2;
//        $slotjob->unserved=null;
//        $slotjob->save();
//
//        $job = "Request Submited Job_id ".$apiResponse->job_id;
//
//        return response()->json( ['status_code'=>200,"success"=> $job]);
//    }
//    // mid mile job list
//    public function getMidMileJobList(Request $request){
//
//        $hubId = auth()->user()->hub_id;
//        $assignJobs = AssignMiJob::where('hub_id', $hubId)->pluck('mi_job_id');
//        $jobs = MiJob::with('jobDetails')->whereIn('id',$assignJobs)->get();
//
//        return backend_view('mid_mile.job.list',compact('jobs','hubId'));
//    }
//    public function getRoutificJob($date,$id){
//
//        $datas = SlotJob::whereNull('slots_jobs.deleted_at')
//            ->where('slots_jobs.created_at','like',$date.'%')
//            ->where('slots_jobs.hub_id','=',$id)
//            ->where('slots_jobs.mile_type','=',2)
//            ->get(['job_id','status','slots_jobs.id','engine']);
//
//        return $datas;
//    }
//    public function getMidMileRouteHistory($id)
//    {
//        $routeData = $this->getRouteHistory($id);
//            return backend_view('mid_mile.route.history',['routes'=>$routeData,'route_id'=>$id]);
//    }
//    // route history list
//    public function getRouteHistory($id)
//    {
//        $routeData=RouteHistory::join('joeys','route_history.joey_id','=','joeys.id')
//            ->leftjoin('merchantids','merchantids.task_id','=','route_history.task_id')
//            ->leftjoin('dashboard_users','route_history.updated_by','=','dashboard_users.id')
//            ->where('route_history.route_id','=',$id)
//            ->whereNull('route_history.deleted_at')
//            ->orderBy('route_history.created_at')->
//            get(['route_history.id','route_history.route_id','route_history.status','route_history.joey_id','route_history.route_location_id',\DB::raw("CONVERT_TZ(route_history.created_at,'UTC','America/Toronto') as created_at")
//                ,'route_history.ordinal','joeys.first_name','joeys.last_name','merchantids.tracking_id','route_history.type','route_history.updated_by','dashboard_users.full_name']);
//
//        return $routeData;
//    }
//    // delete job of mid mile
//    public function deleteMidMileJob(Request $request){
//
//        SlotJob::where('id','=',$request->get('delete_id'))->update(['status'=>'finished','deleted_at'=>date('Y-m-d h:i:s')]);
//        return redirect()->back();
//    }
//    // get data for map route
//    public function createJob(Request $request)
//    {
//        $jobId = $request->get('job_id');
//        $date = $request->get('create_date');
//
//
//        $miJobs = MiJob::join('mi_job_details', 'mi_job_details.mi_job_id', '=', 'mi_jobs.id')
//            ->where('mi_jobs.id', $jobId)
//            ->whereNull('mi_job_details.deleted_at')
////            ->where('mi_jobs.created_at', 'like', $date . '%')
//            ->get(['mi_jobs.*', 'mi_jobs.type as mid_mile_type', 'mi_job_details.*']);
//
//        $hubId = 0;
//        $payload = [];
//        $visits=[];
//        $fleets=[];
//        $mileType='';
//        $hubOrderCount=0;
//        $sprintOrderCount=0;
//        foreach ($miJobs as $key => $miJob) {
//            if ($miJob->type == 'pickup') {
//                if ($miJob->location_type == 'store') {
//                    $mileType=$miJob->location_type;
//                    $vendor = Vendor::find($miJob->locationid);
//
//                    $sprint = Sprint::where('creator_id', $miJob->locationid)
//                        ->whereIn('status_id', [61])
//                        ->whereNull('deleted_at')
//                        ->count();
//
//                    $sprintOrderCount += $sprint;
//                    if($sprintOrderCount == 0){
//                        return json_encode([
//                            "status" => "Route Creation Error",
//                            "output"=> 'This store has zero order count'
//                        ]);
//                    }
//                    if($sprintOrderCount > 0){
//                        if(isset($vendor)){
//                            if($vendor->location){
//                                $lat[0] = substr($vendor->location->latitude, 0, 2);
//                                $lat[1] = substr($vendor->location->latitude, 2);
//                                $latitude = $lat[0] . "." . $lat[1];
//
//                                $long[0] = substr($vendor->location->longitude, 0, 3);
//                                $long[1] = substr($vendor->location->longitude, 3);
//                                $longitude = $long[0] . "." . $long[1];
//
//                                $visits[$miJob->locationid] = [
//                                    "location" => [
//                                        "name" => $vendor->business_address,
//                                        "lat" => $latitude,
//                                        "lng" => $longitude,
//                                    ],
//                                    "duration" => 10,
//                                ];
//                                if($miJob->start_time != null){
//                                    $visits[$miJob->locationid]['start'] = date('H:i',strtotime($miJob->start_time));
//                                }
//                                if($miJob->end_time != null){
//                                    $visits[$miJob->locationid]['end'] = date('H:i',strtotime($miJob->end_time));
//                                }
//                            }
//                        }
//                    }
//                }
//
//                if ($miJob->location_type == 'hub') {
//                    $mileType=$miJob->location_type;
//                    $user = User::where('hub_id', $miJob->locationid)->pluck('id');
//
//                    $microHubOrder = MicroHubOrder::whereHas('sprint', function($query){
//                        $query->whereIn('status_id', [148])->whereNotIn('status_id', [36]);
//                    })->where('is_my_hub', 0)
//                        ->whereIn('scanned_by',$user)
//                        ->whereNull('deleted_at')
//                        ->count();
//
//                    $sprintIds = CurrentHubOrder::where('hub_id', $miJob->locationid)->where('is_actual_hub', 0)->pluck('sprint_id');
//                    $hubBundleOther = MicroHubOrder::whereHas('sprint', function($query) {
//                        $query->where('status_id', 150)->whereNotIn('status_id', [36]);
//                    })->whereIn('sprint_id',$sprintIds)->count();
//
//                    $hubOrderCount += $microHubOrder + $hubBundleOther;
//
//                    if($hubOrderCount > 0){
//
//                        $hub = Hub::find($miJob->locationid);
//                        if(isset($hub)){
//                            $visits[$miJob->locationid] = [
//                                "location" => [
//                                    "name" => $hub->address,
//                                    "lat" => $hub->hub_latitude,
//                                    "lng" => $hub->hub_longitude,
//                                ],
//                                "duration" => 10,
//                            ];
//
//                            if($miJob->start_time != null){
//                                $visits[$miJob->locationid]['start'] = date('H:i',strtotime($miJob->start_time));
//                            }
//                            if($miJob->end_time != null){
//                                $visits[$miJob->locationid]['end'] = date('H:i',strtotime($miJob->end_time));
//                            }
//                        }
//                    }
//
//                }
//            }
//            if ($miJob->type == 'dropoff') {
//                $hub = Hub::find($miJob->locationid);
//                $fleets[$miJob->locationid] = array(
//                    "start_location" => array(
//                        "name" => $miJob->start_address,
//                        "lat" => $miJob->start_latitude,
//                        "lng" => $miJob->start_longitude
//                    ),
//                    "end_location" => array(
//                        "name" => $hub->address,
//                        "lat" => $hub->hub_latitude,
//                        "lng" => $hub->hub_longitude
//                    ),
//                    "shift_start" => date('H:i',strtotime($miJob->start_time)),
//                    "shift_end" => date('H:i',strtotime($miJob->end_time)),
//                );
//                $hubId = $hub->id;
//            }
//        }
//
//        if($hubOrderCount == 0){
//            return json_encode([
//                "status" => "Route Creation Error",
//                "output"=> 'This hub has zero order count'
//            ]);
//        }
//
//        $payload = array(
//            "visits" => $visits,
//            "fleet" => $fleets,
//        );
//
//        $res = json_encode($payload);
//        $result = $this->client->getJobId($res);
//
//        if(isset($result->solution)){
//            $solution = $result->solution;
//            if($result->num_unserved > 0){
//                return json_encode([
//                    "status" => "Route Creation Error",
//                    "output"=> 'Something went wrong, please contact your administrator'
//                ]);
//            }
//            if(!empty($solution)){
//                foreach ($solution as $key => $value){
//                    if(count($value)>1){
//
//                        $miJobDetail = MiJobDetail::where('location_type', 'store')->where('mi_job_id', $jobId)->first();
//                        $routeType = 2;
//                        if(isset($miJobDetail)){
//                            if($miJobDetail->location_type == 'store'){
//                                $routeType = 4;
//                            }
//                        }
//
//                        $Route = new JoeyRoute();
//                        $Route->date =date('Y-m-d H:i:s');
//                        $Route->hub = $hubId;
//                        if(isset($result->total_working_time)){
//                            $Route->total_travel_time=$result->total_working_time;
//                        }
//                        else{
//                            $Route->total_travel_time=0;
//                        }
//                        if(isset($result->total_distance))
//                        {
//                            $Route->total_distance=$result->total_distance;
//                        }
//                        else
//                        {
//                            $Route->total_distance=0;
//                        }
//                        $Route->mile_type = $routeType;
//                        $Route->save();
//
//                        MiJobRoute::create([
//                            'route_id' => $Route->id,
//                            'mi_job_id' => $jobId,
//                        ]);
//
//                        for($i=0;$i<count($value);$i++){
//                            if($i>0){
//
//                                $routeLoc = new JoeyRouteLocations();
//                                $routeLoc->route_id = $Route->id;
//                                $routeLoc->ordinal = $i;
//                                $routeLoc->task_id = $value[$i]->location_id;
//
//                                if(isset($value[$i]->distance) && !empty($value[$i]->distance)){
//                                    $routeLoc->distance = $value[$i]->distance;
//                                }
//
//                                if(isset($value[$i]->arrival_time) && !empty($value[$i]->arrival_time)){
//                                    $routeLoc->arrival_time = $value[$i]->arrival_time;
//                                    if(isset($value[$i]->finish_time)){
//                                        $routeLoc->finish_time = $value[$i]->finish_time;
//                                    }
//                                }
//                                $routeLoc->save();
//
//                            }
//                        }
//
//                    }
//                }
//                return response()->json(['status_code' => 200, "success" => 'Route has been created successfully']);
//            }
//        }else{
//            return json_encode([
//                "status" => "Route Creation Error",
//                "output"=> "Something went wrong, please contact your administrator"
//            ]);
//        }
//
//
//    }


}
