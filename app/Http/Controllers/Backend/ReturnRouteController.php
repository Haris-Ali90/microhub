<?php


namespace App\Http\Controllers\Backend;
use App\AmazonEntry;
use App\BoradlessDashboard;
use App\Classes\Fcm;
use App\Client;
use App\CTCEntry;
use App\CustomRoutingTrackingId;
use App\Hub;
use App\Joey;
use App\JoeyCapacityDetail;
use App\JoeyHubRoute;
use App\JoeyRoute;
use App\JoeyRouteLocations;
use App\LocationUnencrypted;
use App\LogRoutes;
use App\RouteHistory;
use App\RouteTransferLocation;
use App\SlotJob;
use App\Slots;
use App\SlotsPostalCode;
use App\SprintReattempt;
use App\SprintTaskHistory;
use App\StatusMap;
use App\TaskHistory;
use App\UserDevice;
use App\UserNotification;
use App\Vendor;
use Illuminate\Http\Request;
use App\Sprint;
use App\Task;
use App\MerchantIds;
use App\JobRoutes;
use DB;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class ReturnRouteController extends BackendController {



    public function RouteTransfer(Request $request){

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

        $amazonEntriesSprintId=AmazonEntry::whereIn('task_id',$task_ids)
            ->whereNUll('deleted_at')
            ->whereNotIn('task_status_id',[104,105,106,107,108,109,110,111,112,131,135,136,101,102,103])
            ->pluck('sprint_id');

        if($amazonEntriesSprintId)
        {
            AmazonEntry::whereIn('sprint_id',$amazonEntriesSprintId)->
            update(['joey_id'=>$request->input('joey_id'),'joey_name'=>$joey_data->first_name." ".$joey_data->last_name]);
        }

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

    public function hubRouteEdit($routeId,$hubId){
        $route = JoeyRouteLocations::join('sprint__tasks','joey_route_locations.task_id','=','sprint__tasks.id')
            ->Join('merchantids','merchantids.task_id','=','sprint__tasks.id')
            ->join('locations','location_id','=','locations.id')
            ->join('sprint__sprints','sprint_id','=','sprint__sprints.id')
            // ->whereNotIn('sprint__sprints.status_id',[36,17])
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
                'start_time',
                'end_time',
                'address',
                'postal_code',
                'joey_route_locations.arrival_time',
                'joey_route_locations.finish_time',
                'joey_route_locations.distance',
                'sprint__sprints.status_id',
                'joey_route_locations.is_transfered',
                'joey_route_locations.ordinal'
            ]);

        return backend_view('returnroute.edit-hub-route',['route'=>$route,'hub_id'=>$hubId,"route_id"=>$routeId]);

    }

    public function ctcRoutificControls(Request $request){

        date_default_timezone_set("America/Toronto");

        if(empty($request->input('date'))){
            $date = date('Y-m-d');
        }
        else{
            $date = $request->input('date');
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
        -- JOIN slots_postal_code ON(slots_postal_code.postal_code= SUBSTRING(locations.`postal_code`,1,3))
        -- JOIN zones_routing ON(zone_id=zones_routing.id)
        WHERE creator_id IN(477542,477171,477255,477254,477283,477284,477286,477287,477288,477289,477307,477308,477309,477310,477311,477312,477313,
        477314,477292,477294,477315,477317,477316,477295,477302,477303,477304,477305,477306,477296,477290,477297,477298,477299,477300,
        477320,477301,477318,477328,476294,477334,477335,477336,477337,477338,477339) 
        AND joey_routes.date LIKE '".$date."%' 
        AND sprint__tasks.`deleted_at` IS NULL
        AND joey_route_locations.`deleted_at` IS NULL 
        #AND zones_routing.`deleted_at` IS NULL
        AND joey_routes.deleted_at IS NULL GROUP BY route_id";

        $counts = DB::select($countQry);


        return backend_view('returnroute.ctc-new',compact('counts'));

    }

    public function routeDetails($routeId){

        $routes = JoeyRouteLocations::join('sprint__tasks','task_id','=','sprint__tasks.id')
            ->join('merchantids','merchantids.task_id','=','sprint__tasks.id')
            ->join('sprint__contacts','contact_id','=','sprint__contacts.id')
            ->join('locations','location_id','=','locations.id')
            ->where('route_id','=',$routeId)
            ->whereNull('joey_route_locations.deleted_at')
            ->whereNotNull('merchantids.tracking_id')
            ->where('merchantids.tracking_id','!=','')
            ->orderBy('joey_route_locations.ordinal')
            ->get(['type','route_id','joey_route_locations.ordinal','sprint_id','merchant_order_num','tracking_id','name','phone','email','address','postal_code','latitude','longitude','distance']);


        return json_encode($routes);

    }

    public function deleteRoute($routeId){

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

    public function droutificjob(Request $request){

        SlotJob::where('id','=',$request->get('delete_id'))->update(['status'=>'finished','deleted_at'=>date('Y-m-d h:i:s')]);
        return redirect()->back();
    }

    public function createCustomRoute($id){

        $url= "/jobs";

        $client = new Client($url);
        $client->setJobID($id);
        $apiResponse = $client->getJobResults();

        $job=SlotJob::where('job_id','=',$id)->first();


        SlotJob::where('job_id','=',$job->job_id)->update(['status'=>$apiResponse['status']]);

        if($apiResponse['status']=='finished'){

            $solution = $apiResponse['output']['solution'];

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

        else{

            $error = new LogRoutes();
            $error->error = $job->job_id." is in ".$apiResponse['status'];
            $error->save();

            return back()->with('error','Routes creation is in process');
        }
        return back()->with('success','Route Created Successfully!');

    }

    public function getdeleteRouteview()
    {
        return backend_view('returnroute.route-routific-deleted');
    }

    public function getCustomRouteIndex($id,Request $request)
    {
        $date=$request->get('date');

        if(empty($date)){
            $date=date('Y-m-d');
        }
        if($id==16)
        {
            $vendor=Vendor::
            where('id',477260)->get(['first_name','last_name','id']);
        }
        else if($id==19)
        {
            $vendor=Vendor::
            whereIn('id',[477282,476592,477340,477341,477342,477343,477344,477345,477346])->get(['first_name','last_name','id']);
        }
        else if($id==20)
        {
            $vendor=Vendor::
            where('id',476674)->get(['first_name','last_name','id']);
        }
        else
        {
            $vendor=Vendor::
            whereIn('id',[477255,477254,477283,477284,477286,477287,477288,477289,477307,477308,477309,477310,477311,477312,477313,477314,477292,477294,477315,477317,477316,477295,
                477302,477303,477304,477305,477306,477296,477290,477297,477298,477299,477300,477320,477301,477318,477171])->get(['first_name','last_name','id']);
        }

        $user= Auth::user();
        $tracking_id_data=CustomRoutingTrackingId::where('user_id','=',$user->id)
            ->where('hub_id','=',$id)
            ->whereNull('deleted_at')
            ->whereNotNull('tracking_id')
            ->where('is_big_box','=',0)
            ->where('tracking_id','!=','')
            ->get();
        $joey_route_detail=JoeyCapacityDetail::where('user_id','=',$user->id)->where('is_big_box','=',0)->where('hub_id','=',$id)->whereNull('deleted_at')
            ->get();
        $total_count=count($tracking_id_data);
        $valid_id= CustomRoutingTrackingId::where('user_id','=',$user->id)
            ->where('hub_id','=',$id)->where('valid_id','=',1)
            ->whereNull('deleted_at')
            ->whereNotNull('tracking_id')
            ->where('is_big_box','=',0)
            ->where('tracking_id','!=','')
            ->count();
        $ottawa_dash =[];
        $joey_route_detail_count=count($joey_route_detail);
        $returnStatus=StatusMap::getReturnStatus();

        return backend_view( 'returnroute.index', compact('date','returnStatus','ottawa_dash','id','tracking_id_data','total_count','valid_id','vendor','joey_route_detail','joey_route_detail_count') );
    }

    public function getTrackingIdDetail(Request $request)
    {
        $user= Auth::user();
        $input = $request->all();
        $exist=1;
        $tracking_id_data=CustomRoutingTrackingId::where('user_id','=',$user->id)->where('is_big_box','=',0)->where('tracking_id','=',trim($request->tracking_id))->
        where('hub_id','=',trim($request->hub_id))->whereNull('deleted_at')->first();
        if($tracking_id_data==null)
        {
            $tracking_id_data=new CustomRoutingTrackingId();
            $tracking_id_data->user_id= $user->id;
            $tracking_id_data->tracking_id= trim($request->tracking_id);
            $tracking_id_data->hub_id= trim($request->hub_id);
            $tracking_id_data->is_big_box=0;

            $exist=0;
        }


        $merchant_data=MerchantIds::where('tracking_id','=',$input['tracking_id'])->pluck('task_id')->first();
        $sprint_task_data=Task::where('id','=',$merchant_data)->pluck('sprint_id')->first();
        $tracking_id = Sprint::where('id','=',$sprint_task_data)->get()->first();

//        $tracking_id= MerchantIds::join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
//            ->join('sprint__sprints',"sprint__sprints.id",'=','sprint__tasks.sprint_id')
//            ->join('locations','locations.id','=',"location_id")
//            ->join('sprint__contacts','sprint__contacts.id','=','contact_id')
//            ->where('sprint__tasks.type','=','dropoff')
//            ->where('tracking_id','=',trim($request->tracking_id))->first(['sprint__sprints.creator_id','locations.address','locations.postal_code'
//                ,'sprint__contacts.name','sprint__contacts.phone','merchantids.tracking_id','sprint__sprints.id','merchantids.task_id','sprint__sprints.status_id']);
        if($tracking_id !=null)
        {
//            if($request->hub_id==16)
//            {
//                if($tracking_id->creator_id!=477260)
//                {
//                    return response()->json( ['status_code'=>404,"error"=>"Tracking Id does not belong to this city."]);
//                }
//            }
//            elseif($request->hub_id==19)
//            {
//                if(!in_array($tracking_id->creator_id,[477340,477341,477342,477343,477344,477345,477346,477282,476592]))
//                {
//                    return response()->json( ['status_code'=>404,"error"=>"Tracking Id does not belong to this city."]);
//                }
//            }
//            elseif($request->hub_id==17)
//            {
            if($tracking_id['status_id'] != 111){
                return response()->json( ['status_code'=>800,"error"=>"This order is not for return policy!"]);
            }else{
                if(!in_array($tracking_id->creator_id,[477542,477255,477254,477283,477284,477286,477287,477288,477289,477307,477308,477309,477310,
                    477311,477312,477313,477314,477292,477294,477315,477317,477316,477295,477302,477303,477304,477305,477306,477296,477290,477297,
                    477298,477299,477300,477320,477301,477318,477328,476294,477334,477335,477336,477337,477338,477339,477171]))
                {
                    return response()->json( ['status_code'=>404,"error"=>"Tracking Id does not belong to this city."]);
                }
            }

//            }


            $tracking_id_data->vendor_id=$tracking_id->creator_id;
            $tracking_id_data->name=$tracking_id->name;
            $tracking_id_data->contact_no=$tracking_id->phone;
            $tracking_id_data->address=$tracking_id->address;
            $tracking_id_data->postal_code=$tracking_id->postal_code;
            // checking return and Delivered status
            $checkReturnDeliveredStatus=TaskHistory::where('sprint_id','=',$tracking_id->id)->
            whereIn('status_id',[136,106,110,102,112,137,107,131,135,142,17,133,121,113,114,116,117,118,132,138,139,144,143,105,111,108,109,146])->OrderBy('id','DESC')->
            first();
            //143,105,111,108,109,146
            if($checkReturnDeliveredStatus!=null)
            {
                $checkSprintReattempt = SprintReattempt::where('sprint_id','=',$tracking_id->id)->first();
                if($checkSprintReattempt!=null)
                {
                    if($checkSprintReattempt->reattempts_left<=1)
                    {
                        $checkReturnStatus=TaskHistory::where('sprint_id','=',$tracking_id->id)->
                        where('status_id',111)->OrderBy('id','DESC')->
                        first();
                        if($checkReturnStatus==null)
                        {
                            $tzUTC = new \DateTimeZone('UTC');
                            $pickupstoretime_date=\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
                            $pickupstoretime_date->setTimezone($tzUTC);

                            $taskhistory = new TaskHistory();
                            $taskhistory->sprint_id = $tracking_id->id;
                            $taskhistory->sprint__tasks_id = $tracking_id->task_id;
                            $taskhistory->status_id = 111;
                            $taskhistory->created_at = $pickupstoretime_date->format('Y-m-d H:i:s');
                            $taskhistory->date = $pickupstoretime_date->format('Y-m-d H:i:s');
                            $taskhistory->save();

                        }
                        $tracking_id_data->valid_id=4;
                        $tracking_id_data->route_enable_date=$request->date;

                        $tracking_id_data->reason='Reattempt limit exceeded. Please return to merchant.';
                        $tracking_id_data->save();
                        return response()->json( ['status_code'=>200,"data"=>["tracking_id"=>trim($request->tracking_id),"vendor_id"=>$tracking_id->creator_id,
                            "name"=>$tracking_id->name,'phone'=>$tracking_id->phone,'address'=>$tracking_id->address,'postal_code'=>$tracking_id->postal_code,"route_enable_date"=>$tracking_id_data->route_enable_date,'valid'=>4,'vendor'=>[],"exist"=>$exist,'reason'=>$tracking_id_data->reason]]);

                    }
                }

                if(in_array($checkReturnDeliveredStatus->status_id,  [136,106,110,102,112,137,107,131,135,142]))
                {
                    $tracking_id_data->valid_id=2;
                    $tracking_id_data->route_enable_date=$request->date;
                    $tracking_id_data->reason='Tracking Id has a return status.';
                    $tracking_id_data->save();
                    return response()->json( ['status_code'=>200,"data"=>["tracking_id"=>trim($request->tracking_id),"vendor_id"=>$tracking_id->creator_id,
                        "name"=>$tracking_id->name,'phone'=>$tracking_id->phone,'address'=>$tracking_id->address,'postal_code'=>$tracking_id->postal_code,"route_enable_date"=>$tracking_id_data->route_enable_date,'valid'=>2,'vendor'=>[],"exist"=>$exist,'reason'=>$tracking_id_data->reason]]);


                }

                if(in_array($checkReturnDeliveredStatus->status_id, [17,113,114,116,117,118,132,138,139,144]))
                {
                    $tracking_id_data->valid_id=3;
                    $tracking_id_data->route_enable_date=$request->date;
                    $tracking_id_data->reason='This order has already delivered and order status is '.StatusMap::getDescription($checkReturnDeliveredStatus->status_id).'. 
                     Please update the return status to create a reattempt.';
                    $tracking_id_data->save();
                    return response()->json( ['status_code'=>200,"data"=>["tracking_id"=>trim($request->tracking_id),"vendor_id"=>$tracking_id->creator_id,
                        "name"=>$tracking_id->name,'phone'=>$tracking_id->phone,'address'=>$tracking_id->address,'postal_code'=>$tracking_id->postal_code,"route_enable_date"=>$tracking_id_data->route_enable_date,'valid'=>3,'vendor'=>[],"exist"=>$exist,'reason'=>$tracking_id_data->reason]]);

                }
                if(in_array($checkReturnDeliveredStatus->status_id, [133,121]))
                {
                    $tracking_id_data->valid_id=3;
                    $tracking_id_data->route_enable_date=$request->date;
                    $tracking_id_data->reason='Order status is '.StatusMap::getDescription($checkReturnDeliveredStatus->status_id).'. 
                     Please update the return status to create a reattempt.';
                    $tracking_id_data->save();
                    return response()->json( ['status_code'=>200,"data"=>["tracking_id"=>trim($request->tracking_id),"vendor_id"=>$tracking_id->creator_id,
                        "name"=>$tracking_id->name,'phone'=>$tracking_id->phone,'address'=>$tracking_id->address,'postal_code'=>$tracking_id->postal_code,"route_enable_date"=>$tracking_id_data->route_enable_date,'valid'=>3,'vendor'=>[],"exist"=>$exist,'reason'=>$tracking_id_data->reason]]);

                }
                if(in_array($checkReturnDeliveredStatus->status_id, [143,105,111]))
                {

                    $tracking_id_data->valid_id=5;
                    $tracking_id_data->route_enable_date=$request->date;
                    $tracking_id_data->reason='This order has been damaged and will return to the merchant. Please return to merchant.';
                    $tracking_id_data->save();
                    return response()->json( ['status_code'=>200,"data"=>["tracking_id"=>trim($request->tracking_id),"vendor_id"=>$tracking_id->creator_id,
                        "name"=>$tracking_id->name,'phone'=>$tracking_id->phone,'address'=>$tracking_id->address,'postal_code'=>$tracking_id->postal_code,"route_enable_date"=>$tracking_id_data->route_enable_date,'valid'=>5,'vendor'=>[],"exist"=>$exist,'reason'=>$tracking_id_data->reason]]);
                }
                if(in_array($checkReturnDeliveredStatus->status_id, [108,109,146]))
                {
                    $tracking_id_data->valid_id=4;
                    $tracking_id_data->route_enable_date=$request->date;
                    $tracking_id_data->reason='This order has transferred to customer support to update the address. Please place package in the customer service review bin..';
                    $tracking_id_data->save();
                    return response()->json( ['status_code'=>200,"data"=>["tracking_id"=>trim($request->tracking_id),"vendor_id"=>$tracking_id->creator_id,
                        "name"=>$tracking_id->name,'phone'=>$tracking_id->phone,'address'=>$tracking_id->address,'postal_code'=>$tracking_id->postal_code,"route_enable_date"=>$tracking_id_data->route_enable_date,'valid'=>4,'vendor'=>[],"exist"=>$exist,'reason'=>$tracking_id_data->reason]]);
                }
            }



            Sprint::where('id','=',$tracking_id->id)->update(['in_hub_route'=>0]);
            $tracking_id_data->valid_id=1;
            $tracking_id_data->vendor_id=$tracking_id->creator_id;
            $tracking_id_data->name=$tracking_id->name;
            $tracking_id_data->contact_no=$tracking_id->phone;
            $tracking_id_data->address=$tracking_id->address;
            $tracking_id_data->postal_code=$tracking_id->postal_code;
            $tracking_id_data->route_enable_date=$request->date;
            $tracking_id_data->save();
            if(in_array($tracking_id->creator_id,[477542,477340,477341,477342,477343,477344,477345,477346,477255,477254,477283,477284,477286,477287,477288,477289,477307,477308,477309,477310,
                477311,477312,477313,477314,477292,477294,477315,477317,477316,477295,477302,477303,477304,477305,477306,477296,477290,477297,
                477298,477299,477300,477320,477301,477318,477328,476294,477334,477335,477336,477337,477338,477339,477171]))
            {

                $checkforstatus = TaskHistory::where('sprint_id', '=', $tracking_id->id)
                    ->where('status_id', '=', 125)
                    ->first();

                // checking if order is Reattempt
                $isReattempt=SprintReattempt::where('sprint_id','=',$tracking_id->id)->first();


                $pickupstoretime_date=\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
                $pickupstoretime_date->modify('+1 minutes');
                if (!$checkforstatus && $isReattempt==null)
                {
                    $taskhistory = new TaskHistory();
                    $taskhistory->sprint_id = $tracking_id->id;
                    $taskhistory->sprint__tasks_id = $tracking_id->task_id;
                    $taskhistory->status_id = 125;
                    $taskhistory->created_at = $pickupstoretime_date->format('Y-m-d H:i:s');
                    $taskhistory->date = $pickupstoretime_date->format('Y-m-d H:i:s');
                    $taskhistory->save();
                }
                $pickupstoretime_date=\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
                $pickupstoretime_date->modify('+2 minutes');
                $taskhistory=new TaskHistory();
                $taskhistory->sprint_id=$tracking_id->id;
                $taskhistory->sprint__tasks_id=$tracking_id->task_id;
                $taskhistory->status_id=124;
                $taskhistory->created_at = $pickupstoretime_date->format('Y-m-d H:i:s');
                $taskhistory->date = $pickupstoretime_date->format('Y-m-d H:i:s');
                $taskhistory->save();


                Sprint::where('id','=',$tracking_id->id)->update(['status_id'=>124,"in_hub_route"=>0]);
                Task::where('id','=',$tracking_id->task_id)->update(['status_id'=>124]);

            }
            else
            {
                $date = date('Y-m-d ', strtotime($request->date . ' -1 days'))."17:00:00";
                Sprint::where('id','=',$tracking_id->id)->update(['status_id'=>61,"in_hub_route"=>0,"created_at"=>$date]);
                Task::where('id','=',$tracking_id->task_id)->update(['status_id'=>61,"created_at"=>$date]);

                AmazonEntry::where('sprint_id', '=', $tracking_id->id)->update([ "created_at" => $date,'task_status_id'=>61]);



            }
            return response()->json( ['status_code'=>200,"data"=>["tracking_id"=>trim($request->tracking_id),"route_enable_date"=>$tracking_id_data->route_enable_date,"vendor_id"=>$tracking_id->creator_id,
                "name"=>$tracking_id->name,'phone'=>$tracking_id->phone,'address'=>$tracking_id->address,'postal_code'=>$tracking_id->postal_code,'valid'=>1,'vendor'=>[],"exist"=>$exist,'reason'=>$tracking_id_data->reason]]);
        }
        else
        {
            $tracking_id_data->valid_id=0;
            $tracking_id_data->reason='Order does not exist in the system.';
            $tracking_id_data->route_enable_date=$request->date;
            $tracking_id_data->save();
            return response()->json( ['status_code'=>200,"data"=>["tracking_id"=>trim($request->tracking_id),"vendor_id"=>"","route_enable_date"=>$tracking_id_data->route_enable_date,
                "name"=>"",'phone'=>"",'address'=>"",'postal_code'=>"",'valid'=>0,"vendor"=>[],"exist"=>$exist,'reason'=>$tracking_id_data->reason]]);
        }


    }

    public function postCreateRoute(Request $request)
    {
        $orders=[];
        $hub_id=$request->hub_id;
        $user= Auth::user();
        $joey_route_detail=JoeyCapacityDetail::join('vehicles','vehicles.id','=','custom_joey_detail.vehicle_id')
            ->where('user_id','=',$user->id)->where('hub_id','=',$hub_id)->whereNull('deleted_at')->where('is_big_box','=',0)
            ->get(['vehicles.id','vehicles.capacity','custom_joey_detail.joeys_count']);

        $tracking_ids=CustomRoutingTrackingId::where('user_id','=',$user->id)
            ->where('hub_id','=',$hub_id)
            ->whereNull('deleted_at')
            ->where('valid_id',1)
            ->whereNotNull('tracking_id')
            ->where('is_big_box','=',0)
            ->where('tracking_id','!=','')
            ->pluck('tracking_id');

        $tracking_ids_array  = array();
        foreach ($tracking_ids as $data){
            array_push($tracking_ids_array,$data);
        }


        $sprints= MerchantIds::join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
            ->join("sprint__sprints",'sprint__sprints.id','=','sprint__tasks.sprint_id')
            ->join('locations','location_id','=','locations.id')
            ->where('sprint__tasks.type','=','pickup')
            ->whereIn('merchantids.tracking_id',$tracking_ids_array)
            ->get(['start_time','end_time','sprint__sprints.creator_id','sprint__tasks.id','sprint__tasks.sprint_id','due_time','address','locations.latitude','locations.longitude','locations.postal_code',
                'locations.city_id']);



        foreach($sprints as $sprint)
        {
            if(in_array($sprint->creator_id,["477282","477260",'476592']))
            {
                $date = date("Y-m-d")." 17:00:00";
                $date = date('Y-m-d H:i:s', strtotime($date . ' -1 days'));
                Sprint::where('id','=',$sprint->sprint_id)->update(['status_id'=>61,"in_hub_route"=>0,"created_at"=>$date]);
                Task::where('id','=',$sprint->id)->update(['status_id'=>61]);

            }
            else
            {

                Sprint::where('id','=',$sprint->sprint_id)->update(['status_id'=>124,"in_hub_route"=>0]);
                Task::where('id','=',$sprint->id)->update(['status_id'=>124]);
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


            $lat[0] = substr($sprint->latitude, 0, 2);
            $lat[1] = substr($sprint->latitude, 2);
            $latitude=$lat[0].".".$lat[1];

            $long[0] = substr($sprint->longitude, 0, 3);
            $long[1] = substr($sprint->longitude, 3);
            $longitude=$long[0].".".$long[1];

            if(empty($sprint->city_id) || $sprint->city_id==NULL){
                $dropoffAdd = $this->canadian_address($sprint->address.','.$sprint->postal_code.',canada');
                if(!empty($dropoffAdd)){
                    $latitude = $dropoffAdd['lat'];
                    $longitude = $dropoffAdd['lng'];
                }

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

        }
        $job_id= $this->createJobId($orders,$hub_id,$joey_route_detail);
//        dd($job_id);
        if($job_id['status_code']==200){
            //  CustomRoutingTrackingId::
            //  where('user_id','=',$user->id)->where('hub_id','=',$hub_id)->whereNull('deleted_at')->whereIn('tracking_id',$tracking_ids)->update(['deleted_at'=>date("Y-m-d H:i:s")]);
            return response()->json(['status_code'=>200,'Job_id'=>$job_id['Job_id']]);
        }
        else
        {
            return response()->json(['status_code'=>400,'Job_id'=>Null,"error"=>$job_id['error']]);
        }

    }

    public function getroutificjob(Request $request,$id){
        $date=$request->get('date');
        $hub_id=$request->get('id');
        if(empty($date)){
            $date=date('Y-m-d');
        }

        $datas = SlotJob::leftJoin('zones_routing','zone_id','=','zones_routing.id')
            ->whereNull('slots_jobs.deleted_at')
            ->where('slots_jobs.created_at','like',$date.'%')
            ->where('slots_jobs.hub_id','=',$id)
            ->where('is_big_box','=',0)
            ->get(['job_id','title','status','slots_jobs.id','is_custom_route']);

        return backend_view('returnroute.routific_job',compact('datas','id'));
    }

    public function multipleRemoveTrackingid(Request $request)
    {
        $user= Auth::user();
        CustomRoutingTrackingId::where('user_id','=',$user->id)->where('is_big_box','=',0)->whereIn('tracking_id',$request->deleteId)->whereNull('deleted_at')->update(['deleted_at'=>date('Y-m-d H:i:s')]);
        return  response()->json();
    }

    public function removeTrackingid(Request $request)
    {
        $user= Auth::user();
        $id= CustomRoutingTrackingId::where('user_id','=',$user->id)->where('is_big_box','=',0)->where('tracking_id','=',$request->Tracking_id)->whereNull('deleted_at')->first();

        $id->deleted_at=date('Y-m-d H:i:s');
        $id->save();

        return  response()->json(['valid'=>$id->valid_id]);
    }

    public function addJoeyCount(Request $request)
    {

        $user= Auth::user();
        $data=$request->all();

        $joey_capacity_detail=new JoeyCapacityDetail();
        $joey_capacity_detail->vehicle_id=$data['vehicle_id'];
        $joey_capacity_detail->user_id=$user->id;
        $joey_capacity_detail->hub_id=$user->hub_id;
        $joey_capacity_detail->joeys_count=$data['joeys'];
        $joey_capacity_detail->is_big_box=0;
        $joey_capacity_detail->save();

//        return response()->json(['status_code'=>200,'vehicle_id'=>$joey_capacity_detail->vehicle_id,'joeys_count'=>$joey_capacity_detail->joeys_count,'id'=>$joey_capacity_detail->id]);
        return back()->with('success','Vehicle Added Successfully!');
    }

    public function getJoeyCountDetail(Request $request)
    {
        $joey_capacity_detail=JoeyCapacityDetail::where('id','=',$request->id)->first();

        return response()->json(['status_code'=>200,'vehicle_id'=>$joey_capacity_detail->vehicle_id,'joeys_count'=>$joey_capacity_detail->joeys_count,'id'=>$joey_capacity_detail->id]);
    }

    public function updateJoeyCountDetail(Request $request)
    {
        $data=$request->all();

        $joey_capacity_detail = JoeyCapacityDetail::where('id','=',$data['id'])->update(['vehicle_id'=>$data['vehicle_id'],"joeys_count"=>$data['joeys']]);
        return back()->with('success','Joey Count Updated Successfully!');
    }

    public function deleteJoeyCount(Request $request)
    {
        JoeyCapacityDetail::where('id',$request->id)->update(['deleted_at'=>date("Y-m-d H:i:s")]);
        return response()->json(['status_code'=>200]);
    }

    public function removeOrderInRoute(Request $request)
    {
        $orders=MerchantIds::join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
            ->join('sprint__sprints','sprint__sprints.id','=','sprint__tasks.sprint_id')
            ->whereIn('tracking_id',json_decode($request->tracking_ids))
            ->where('sprint__sprints.in_hub_route','=',1)
            ->get(['merchantids.tracking_id']);

        if(count($orders)==0)
        {
            return response()->json(['status_code'=>400]);
        }
        foreach($orders as $order)
        {
            CustomRoutingTrackingId::
            where('tracking_id','=',$order->tracking_id)
                ->where('is_big_box','=',0)
                ->update(['deleted_at'=>date('Y-m-d H:i:s')]);

        }
        return response()->json(['status_code'=>200]);

    }



    public function getstatusdesc($id){

        $status = array("136" => "Client requested to cancel the order",
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
            "121" => "Out for delivery",
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
            "255" =>"Order delay");
        return $status[$id];
    }

    public function canadian_address($address){

        if(substr($address,-1)==' '){
            $postal_code = substr($address,-8,-1);
        }
        else {
            $postal_code = substr($address,-7);
        }

        if(substr($postal_code, 0, 1)==' '|| substr($postal_code, 0, 1)==','){
            $postal_code = substr($postal_code,-6);
        }

        if(substr($postal_code,-1)==' '){
            $postal_code = substr($postal_code,0,6);
        }

        $address1 =  substr($address,0,-7);

        //parsing address for suite-Component
        $address = explode(' ',trim($address));
        $address[0] = str_replace('-',' ', $address[0]);
        $address = implode(" ",$address);
        // url encode the address

        $address = urlencode($address);
        // google map geocode api url
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0";


        // get the json response
        $resp_json = file_get_contents($url);

        // decode the json
        $resp = json_decode($resp_json, true);

        // response status will be 'OK', if able to geocode given address
        if($resp['status']=='OK'){

            $completeAddress = [];
            $addressComponent = $resp['results'][0]['address_components'];

            // get the important data

            for ($i=0; $i < sizeof($addressComponent); $i++) {
                if ($addressComponent[$i]['types'][0] == 'administrative_area_level_1')
                {
                    $completeAddress['division'] = $addressComponent[$i]['short_name'];
                }
                elseif ($addressComponent[$i]['types'][0] == 'locality') {
                    $completeAddress['city'] = $addressComponent[$i]['short_name'];
                }
                else {
                    $completeAddress[$addressComponent[$i]['types'][0]] = $addressComponent[$i]['short_name'];
                }
                if($addressComponent[$i]['types'][0] == 'postal_code' && $addressComponent[$i]['short_name']!=$postal_code){
                    $completeAddress['postal_code'] =$postal_code;
                }
            }

            if (array_key_exists('subpremise', $completeAddress)) {
                $completeAddress['suite'] = $completeAddress['subpremise'];
                unset($completeAddress['subpremise']);
            }
            else {
                $completeAddress['suite'] = '';
            }

            if($resp['results'][0]['formatted_address'] == $address1){
                $completeAddress['address'] = $resp['results'][0]['formatted_address'];
            }
            else{
                $completeAddress['address'] = $address1;
            }



            $completeAddress['lat'] = $resp['results'][0]['geometry']['location']['lat'];
            $completeAddress['lng'] = $resp['results'][0]['geometry']['location']['lng'];

            unset($completeAddress['administrative_area_level_2']);
            unset($completeAddress['street_number']);


            return $completeAddress;

        }
        else{
            //  throw new GenericException($resp['status'],403);
        }


    }

    public function data(DataTables $datatables, Request $request)
    {


        date_default_timezone_set("America/Toronto");

        if(empty($request->input('date'))){
            $date = date('Y-m-d');
        }
        else{
            $date = $request->input('date');
        }
        $routes = JoeyRoute::join('joey_route_locations','route_id','=','joey_routes.id')
            ->join('sprint__tasks','sprint__tasks.id','=','joey_route_locations.task_id')
            ->join('sprint__sprints','sprint_id','=','sprint__sprints.id')
            ->join('locations','sprint__tasks.location_id','=','locations.id')
            ->join('slots_postal_code','slots_postal_code.postal_code','=',\DB::raw("SUBSTRING(locations.postal_code,1,3)"))
            ->join('zones_routing','slots_postal_code.zone_id','=','zones_routing.id')
            ->leftJoin('joeys','joey_routes.joey_id','=','joeys.id')
            ->whereNotIn('sprint__sprints.status_id',[36])
            ->whereNull('joey_routes.deleted_at')
            ->whereNull('joey_route_locations.deleted_at')
            ->whereNull('slots_postal_code.deleted_at')
            ->whereNull('zones_routing.deleted_at')
            ->where('joey_routes.date','like',$date."%")
            ->where('creator_id','=',477260)
            ->distinct()
            ->select([DB::raw('COUNT(joey_route_locations.id) AS counts'),
                DB::raw('SUM(CASE WHEN sprint__sprints.status_id in(113,114,17) THEN 0 ELSE 1 END) AS d_counts'),
                DB::raw('SUM(joey_route_locations.distance) AS distance'),
                DB::raw('SUM(CASE WHEN sprint__sprints.status_id in(113,114,17) THEN 0 ELSE joey_route_locations.distance END) AS d_distance'),
                DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(finish_time)-TIME_TO_SEC(arrival_time))) AS duration'),
                DB::raw('SEC_TO_TIME(SUM(CASE WHEN sprint__sprints.status_id in(113,114,17) THEN 0 ELSE TIME_TO_SEC(finish_time)-TIME_TO_SEC(arrival_time) END)) AS d_duration'),
                DB::raw('CONCAT(title,"(",zone_id,")") AS zone'),
                'joey_routes.joey_id as joey_id','date','joey_routes.id as route_id','first_name','last_name','joey_routes.total_travel_time',
                'joey_routes.total_distance'])
            ->groupBy('route_id')
//->get()
        ;



        return $datatables->eloquent($routes)
            ->addIndexColumn()

            ->editColumn('route_id', static function ($routes) {

                return 'R-'.$routes->route_id;
            })
            ->editColumn('first_name', static function ($routes) {
                return $routes->first_name.' '.$routes->last_name;
            })

            ->editColumn('zone', static function ($routes) {
                return $routes->zone;
            })
            ->editColumn('duration', static function ($routes) {
                $duration = (!empty($routes->duration) || $routes->duration != NULL) ?$routes->duration : 0 ;
                /* $duration = 0;
                if (!empty($routes->duration) || $routes->duration != NULL) {
                $duration = $routes->duration;
                }*/

                return $duration;
            })

            ->editColumn('distance', static function ($routes) {
                $distance = (!empty($routes->distance) || $routes->distance != NULL ) ? round($routes->distance / 1000, 2) : 0 ;
                $d_distance = (!empty($routes->d_distance) || $routes->d_distance != NULL ) ? round($routes->d_distance/1000,2) : 0 ;
                /*if (!empty($routes->distance) || $routes->distance != NULL) {
                return $distance = round($routes->distance / 1000, 2);
                } else {
                return $distance = 0;
                }
                if(!empty($routes->d_distance) || $routes->d_distance!=NULL ){
                return $d_distance = round($routes->d_distance/1000,2);
                }else*/
                return $d_distance."km/".$distance."km";
            })

            ->editColumn('order', static function ($routes) {
                return $routes->d_counts."/".$routes->counts;
            })

            ->addColumn('action', static function ($routes) {
                return backend_view('returnroute.montreal-action',compact('routes'));
            })
            ->make(true);
    }



    public function createJobId($orders,$hub_id,$joey_route_detail)
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
            return ['error'=>'Please set Joeys vehicle details to continue',"status_code"=>400];
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
            return ['error'=>$apiResponse->error,"status_code"=>400];

        }

        $slotjob  = new  SlotJob();
        $slotjob->job_id = $apiResponse->job_id;
        $slotjob->hub_id =$hub_id;
        $slotjob->zone_id = null;
        $slotjob->unserved = null;
        $slotjob->is_custom_route = 1;
        $slotjob->save();

        return ['Job_id'=>$apiResponse->job_id,'status_code'=>200];

    }


    public function OrderRequest($data,$url,$request)
    {

        $json_data = json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.joeyco.com/'.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $request,
            CURLOPT_POSTFIELDS =>$json_data,
            CURLOPT_HTTPHEADER =>  array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function google_address($address,$postal_code)
    {

        $address = urlencode($address);
        $postal_code = urlencode($postal_code);

        // google map geocode api url
        $url ="https://maps.googleapis.com/maps/api/geocode/json?address={$address}components=country:canada|postal_code:$postal_code&key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0";
        // "https://maps.googleapis.com/maps/api/geocode/json?address={$address}components=country:canada&key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0";

        // get the json response
        $resp_json = file_get_contents($url);

        // decode the json
        $resp = json_decode($resp_json, true);

        // response status will be 'OK', if able to geocode given address
        if($resp['status']=='OK'){

            $completeAddress = [];
            $addressComponent = $resp['results'][0]['address_components'];

            // get the important data

            for ($i=0; $i < sizeof($addressComponent); $i++) {
                if ($addressComponent[$i]['types'][0] == 'administrative_area_level_1')
                {
                    $completeAddress['division'] = $addressComponent[$i]['short_name'];
                }
                elseif ($addressComponent[$i]['types'][0] == 'locality') {
                    $completeAddress['city'] = $addressComponent[$i]['short_name'];
                }
                else {
                    $completeAddress[$addressComponent[$i]['types'][0]] = $addressComponent[$i]['short_name'];
                }
                if($addressComponent[$i]['types'][0] == 'postal_code'){
                    $completeAddress['postal_code'] = $addressComponent[$i]['short_name'];
                }
            }

            if (array_key_exists('subpremise', $completeAddress)) {
                $completeAddress['suite'] = $completeAddress['subpremise'];
                unset($completeAddress['subpremise']);
            }
            else {
                $completeAddress['suite'] = '';
            }


            $completeAddress['address'] = $resp['results'][0]['formatted_address'];

            $completeAddress['lat'] = $resp['results'][0]['geometry']['location']['lat'];
            $completeAddress['lng'] = $resp['results'][0]['geometry']['location']['lng'];

            unset($completeAddress['administrative_area_level_2']);

            return $completeAddress;

        }
        else{
            //  throw new GenericException($resp['status'],403);
            return 0;
        }


    }



}