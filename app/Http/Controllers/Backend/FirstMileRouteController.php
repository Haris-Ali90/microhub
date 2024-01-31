<?php

namespace App\Http\Controllers\Backend;


use App\AmazonEntry;
use App\Classes\Client;
use App\Classes\Fcm;
use App\CTCEntry;
use App\Hub;
use App\HubStore;
use App\Joey;
use App\JoeyRoute;
use App\JoeyRouteLocations;
use App\JoeyStorePickup;
use App\LogRoutes;
use App\RouteHistory;
use App\Sprint;
use App\TaskHistory;
use App\UserDevice;
use App\UserNotification;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FirstMileRouteController extends BackendController
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
        "151" => "",
        "152" => "",
        '153' => 'Miss sorted to be reattempt',
        '154' => 'Joey unable to complete the route', '155' => 'To be re-attempted tomorrow'
    );
    //get first mile route list
    public function firstMileRoutesList(Request $request)
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
            ->where('mile_type',1)
//            ->where('route_completed', 0)
            ->where('date', 'LIKE', $date.'%')
            ->where('hub', $hubId)
            ->groupBy('joey_route_locations.route_id')
            ->get();

        return backend_view('first_mile.route.list',compact('routes'));
    }

    //first mile get route order details
    public function getRouteDetail(Request $request, $routeId)
    {
        $vendorId = JoeyRouteLocations::where('route_id',$routeId)->pluck('task_id');

        $routeDetails = Sprint::join('sprint__tasks', 'sprint__sprints.id', '=', 'sprint__tasks.sprint_id')
            ->join('sprint__contacts','sprint__tasks.contact_id','=','sprint__contacts.id')
            ->join('locations','sprint__tasks.location_id','=','locations.id')
            ->join('merchantids','merchantids.task_id','=','sprint__tasks.id')
//            ->where('sprint__tasks.type', '=', 'pickup')
            ->whereIn('sprint__sprints.creator_id',$vendorId)
            ->whereIn('sprint__sprints.status_id',[24,61,111])
            ->whereNotIn('sprint__sprints.status_id',[36])
            ->groupBy('sprint__tasks.sprint_id')
            ->get(['sprint__tasks.type','sprint__tasks.ordinal','sprint__tasks.sprint_id','sprint__contacts.name','sprint__contacts.phone','sprint__contacts.email','locations.address','locations.postal_code','locations.latitude','locations.longitude']);

        $hub = Hub::find(auth()->user()->hub_id);
        return json_encode(['routes'=>$routeDetails, 'hub' => $hub]);
    }

    //edit page of routes
    public function firstMileRouteEdit(Request $request, $routeId,$hubId){

        $routes = JoeyRouteLocations::where('route_id',$routeId)->get();
        return backend_view('first_mile.route.detail',['routes'=>$routes,'hub_id'=>$hubId,"route_id"=>$routeId]);

    }

    public function vendorOrderDetails(Request $request, $vendorIds, $routeId)
    {
        $routeDetails=[];
        $routeDetailsSecond=[];
        $vendorId = JoeyRoute::join('joey_route_locations','joey_route_locations.route_id' ,'=', 'joey_routes.id')
            ->whereNull('joey_route_locations.deleted_at')
            ->whereNull('joey_routes.deleted_at')
            ->where('joey_routes.mile_type',1)
            ->where('joey_routes.id', $routeId)
            ->where('joey_route_locations.task_id', $vendorIds)
            ->pluck('joey_route_locations.task_id');


        $pickupSprintId = JoeyStorePickup::where('route_id', $routeId)->whereNull('deleted_at')->pluck('sprint_id')->toArray();
        $deliveredSprintId = JoeyStorePickup::where('route_id', $routeId)->whereNotNull('deleted_at')->pluck('sprint_id')->toArray();
        $sprintId = array_merge($pickupSprintId, $deliveredSprintId);



        if(!empty($sprintId)){
            $routeDetailsSecond = Sprint::join('sprint__tasks', 'sprint__sprints.id', '=', 'sprint__tasks.sprint_id')
                ->join('sprint__contacts','sprint__tasks.contact_id','=','sprint__contacts.id')
                ->join('locations','sprint__tasks.location_id','=','locations.id')
                ->join('merchantids','merchantids.task_id','=','sprint__tasks.id')
                ->where('sprint__tasks.type', '=', 'dropoff')
                ->whereNotNull('merchantids.tracking_id')
                ->whereIn('sprint__sprints.creator_id',$vendorId)
                ->whereIn('sprint__sprints.id',$sprintId)
                ->whereNull('sprint__sprints.deleted_at')
                ->whereNotIn('sprint__sprints.status_id',[36])
                ->get()->toArray();
        }else{
            $routeDetails = Sprint::join('sprint__tasks', 'sprint__sprints.id', '=', 'sprint__tasks.sprint_id')
                ->join('sprint__contacts','sprint__tasks.contact_id','=','sprint__contacts.id')
                ->join('locations','sprint__tasks.location_id','=','locations.id')
                ->leftjoin('merchantids','merchantids.task_id','=','sprint__tasks.id')
                ->where('sprint__tasks.type', '=', 'dropoff')
                ->whereNotNull('merchantids.tracking_id')
                ->whereIn('sprint__sprints.creator_id',$vendorId)
                ->whereNull('sprint__sprints.deleted_at')
                ->whereIn('sprint__sprints.status_id',[24,61,111])
                ->whereNotIn('sprint__sprints.status_id',[36])
                ->get(['sprint__sprints.creator_id', 'merchantids.tracking_id', 'merchantids.merchant_order_num', 'locations.address', 'sprint__sprints.status_id'])->toArray();
        }


        $data = array_merge($routeDetails, $routeDetailsSecond);
        $hub = Hub::find(auth()->user()->hub_id);
        return json_encode(['routes'=>$data, 'hub' => $hub, 'statuses' => $this->test]);

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

    // get data for map route
    public function RouteMap(Request $request, $route_id){

        $vendorId = JoeyRouteLocations::where('route_id',$route_id)->pluck('task_id');
        $vendors = Vendor::with('location')->whereIn('id',$vendorId)->get();

        $i=0;
        $data=[];

        foreach($vendors as $vendor){
            $data[] = [];
            $address = $vendor->business_address;
            if($vendor->business_address == null){
                $address = $vendor->location->address;
            }

            $lat[0] = substr($vendor->location->latitude, 0, 2);
            $lat[1] = substr($vendor->location->latitude, 2);
            $data[$i]['latitude'] = floatval($lat[0].".".$lat[1]);

            $long[0] = substr($vendor->location->longitude, 0, 3);
            $long[1] = substr($vendor->location->longitude, 3);
            $data[$i]['longitude'] = floatval($long[0].".".$long[1]);
            $data[$i]['address'] = $address;
            $data[$i]['id'] = $vendor->id;
            $data[$i]['name'] = $vendor->name;
            $data[$i]['type'] = 'vendor';
            $i++;

        }

        $hub = Hub::find(auth()->user()->hub_id);

        $hub = [[
            "latitude" => (float)$hub->hub_latitude,
            "longitude" => (float)$hub->hub_longitude,
            "address" => $hub->address,
            "id" => $hub->id,
            "name" => $hub->title,
            "type" => "hub",
        ]];

        $mapDetail = array_merge($data, $hub);
        return json_encode($mapDetail);
    }

}