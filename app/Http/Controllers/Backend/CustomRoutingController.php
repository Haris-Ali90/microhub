<?php

namespace App\Http\Controllers\Backend;

use Config;

use Illuminate\Http\Request;

// use App\Http\Requests;
use App\Http\Requests\Backend\CategoryRequest;
use Illuminate\Support\Facades\Request as FacadeRequest;
use App\Http\Controllers\Backend\BackendController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use App\Sprint;
use App\Client;
use App\SlotJob;
use App\Hub;
use App\CustomRoutingFile;
use App\CustomRoutingTrackingId;
use App\Task;
use App\Vendor;
use App\MerchantIds;
use App\TaskHistory;
use App\ContactUncrypted;
use App\LocationUnencrypted;
use App\JoeyCapacityDetail;
use App\EnableRouteFile;
use App\EnableForRoutesTrackingId;
use App\AmazonEntry;
use App\SprintReattempt;
use App\StatusMap;
Use App\ReturnAndReattemptProcessHistory;
use App\Classes\ReturnOrderCreation;
use App\RoutingEngine;
use App\BoradlessDashboard;

//use Validator;

use App\Zone;
use App\UserEntities;

class CustomRoutingController extends BackendController
{
    public function markReturnedMerchant(Request $request)
    {
        $user= Auth::user();
        $id= CustomRoutingTrackingId::where('user_id','=',$user->id)->where('is_big_box','=',0)->where('tracking_id','=',$request->Tracking_id)->whereNull('deleted_at')->first();
      
        $id->deleted_at=date('Y-m-d H:i:s');
        $id->save();
        $sprint=MerchantIds::join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
        ->where('tracking_id',$request->Tracking_id)
        ->first(['sprint__tasks.sprint_id','merchantids.task_id']);
       
               Sprint::where('id','=',$sprint->sprint_id)->update(['status_id'=>111,"in_hub_route"=>0]);
               Task::where('id','=',$sprint->task_id)->update(['status_id'=>111]);
               $taskhistory=new TaskHistory();
               $taskhistory->sprint_id=$sprint->sprint_id;
               $taskhistory->sprint__tasks_id=$sprint->task_id;
               $taskhistory->status_id=111;
               $taskhistory->created_at=date("Y-m-d H:i:s");
               $taskhistory->date=date("Y-m-d H:i:s");
               $taskhistory->save();

        return  response()->json(['valid'=>$id->valid_id]);
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
    public function getIndex($id,Request $request)
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
        else if($id==129){
            $vendor=Vendor::
            whereIn('id',[477607,477609,477613])->get(['first_name','last_name','id']);
        }
        else
        {
            $vendor=Vendor::
            whereIn('id',[477255,477254,477283,477284,477286,477287,477288,477289,477307,477308,477309,477310,477311,477312,477313,477314,477292,477294,477315,477317,477316,477295,
            477302,477303,477304,477305,477306,477296,477290,477297,477298,477299,477300,477320,477301,477318,477171,477559,477587,477589])->get(['first_name','last_name','id']);
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
        return backend_view( 'customrouting.index', compact('date','returnStatus','ottawa_dash','id','tracking_id_data','total_count','valid_id','vendor','joey_route_detail','joey_route_detail_count') );
    }
    public function addJoeyCount(Request $request)
    { 
       
        $user= Auth::user();
        $data=$request->all();
       
        $joey_capacity_detail=new JoeyCapacityDetail();
        $joey_capacity_detail->vehicle_id=$data['vehicle_id'];
        $joey_capacity_detail->user_id=$user->id;
        $joey_capacity_detail->hub_id=$data['hub_id'];
        $joey_capacity_detail->joeys_count=$data['joeys'];
        $joey_capacity_detail->save();
       
        return response()->json(['status_code'=>200,'vehicle_id'=>$joey_capacity_detail->vehicle_id,'joeys_count'=>$joey_capacity_detail->joeys_count,'id'=>$joey_capacity_detail->id]);
    }
    public function deleteJoeyCount(Request $request)
    {
        JoeyCapacityDetail::where('id',$request->id)->update(['deleted_at'=>date("Y-m-d H:i:s")]);
        return response()->json(['status_code'=>200]);
    }
    public function getJoeyCountDetail(Request $request)
    {
        $joey_capacity_detail=JoeyCapacityDetail::where('id','=',$request->id)->first();

        return response()->json(['status_code'=>200,'vehicle_id'=>$joey_capacity_detail->vehicle_id,'joeys_count'=>$joey_capacity_detail->joeys_count,'id'=>$joey_capacity_detail->id]);
    }
    public function updateJoeyCountDetail(Request $request)
    {
        $joey_capacity_detail=JoeyCapacityDetail::where('id','=',$request->id)->first();
        $data=$request->all();
        $joey_capacity_detail->vehicle_id=$data['vehicle_id'];
        $joey_capacity_detail->joeys_count=$data['joeys'];
        $joey_capacity_detail->save();
        
        return response()->json(['status_code'=>200,'vehicle_id'=>$data['vehicle_id'],'joeys_count'=>$data['joeys'],'id'=>$joey_capacity_detail->id]);
    }
    public function removeTrackingid(Request $request)
    {
        $user= Auth::user();
        $id= CustomRoutingTrackingId::where('user_id','=',$user->id)->where('is_big_box','=',0)->where('tracking_id','=',$request->Tracking_id)->whereNull('deleted_at')->first();
      
       $id->deleted_at=date('Y-m-d H:i:s');
        $id->save();

        return  response()->json(['valid'=>$id->valid_id]);
    }
	 public function multipleRemoveTrackingid(Request $request)
    {
        $user= Auth::user();
         CustomRoutingTrackingId::where('user_id','=',$user->id)->where('is_big_box','=',0)->whereIn('tracking_id',$request->deleteId)->whereNull('deleted_at')->update(['deleted_at'=>date('Y-m-d H:i:s')]);
        return  response()->json();
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

        $sprints= MerchantIds::join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
        ->join("sprint__sprints",'sprint__sprints.id','=','sprint__tasks.sprint_id')
        ->join('locations','location_id','=','locations.id')
        ->where('sprint__tasks.type','=','dropoff')
        ->whereIn('merchantids.tracking_id',$tracking_ids)
        ->get(['start_time','end_time','sprint__sprints.creator_id','sprint__tasks.id','sprint__tasks.sprint_id','due_time','address','locations.latitude','locations.longitude','locations.postal_code',
        'locations.city_id']);

        $routingEngine=RoutingEngine::where('hub_id',$hub_id)->where('routing_type',3)->first();

        $stops=[];
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
         
 
        //  $lat[0] = substr($sprint->latitude, 0, 2);
        //  $lat[1] = substr($sprint->latitude, 2);
         $latitude= $sprint->latitude/1000000;
 
        //  $long[0] = substr($sprint->longitude, 0, 3);
        //  $long[1] = substr($sprint->longitude, 3);
         $longitude= $sprint->longitude/1000000;
     
         if(empty($sprint->city_id) || $sprint->city_id==NULL){
             $dropoffAdd = $this->canadian_address($sprint->address.','.$sprint->postal_code.',canada');
            if(!empty($dropoffAdd)){
                $latitude = $dropoffAdd['lat'];
                $longitude = $dropoffAdd['lng'];
            }
             
         }
 
         $start = $sprint->start_time;    
         $end = $sprint->end_time;
         
        
        if($routingEngine->engine==1)
        {
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
        else if($routingEngine->engine==2)
        {
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
                "startTime" => date('H:i',strtotime($start)),
                 "endTime" => date('H:i',strtotime($end)),
            )
            );
                $stops[]=$stop;
        }
        else
        {
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
        }
        
        }

        if($routingEngine->engine==1)
        {
            // dd("engine {$routingEngine->engine}");
            $job_id= RoutingEngine::createJobId($orders,$hub_id,$joey_route_detail);
        }
        else if($routingEngine->engine==2)
        {
            // dd("engine {$routingEngine->engine}");
            
            $job_id= RoutingEngine::createJobIdBeta($orders,$hub_id,$joey_route_detail,$stops);
        }
        else 
        {
           // dd("engine {$routingEngine->engine}");
            $job_id= RoutingEngine::createJobIdLogisticApi($orders,$joey_route_detail,$hub_id);
        }

      //  $job_id= $this->createJobId($orders,$hub_id,$joey_route_detail);
        if($job_id['statusCode']==200){
        //  CustomRoutingTrackingId::
        //  where('user_id','=',$user->id)->where('hub_id','=',$hub_id)->whereNull('deleted_at')->whereIn('tracking_id',$tracking_ids)->update(['deleted_at'=>date("Y-m-d H:i:s")]);
         return response()->json(['status_code'=>200,'Job_id'=>$job_id['Job_id']]);
        }
        else
        {
         return response()->json(['status_code'=>400,'Job_id'=>Null,"error"=>$job_id['error']]);
        }
      
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

    public function postCreateOrder(Request $request)
    {

        
        
        $data['name'] =trim(str_replace(array("\n", "\r"), '', $request->name));
        $data['phone'] =trim(str_replace(array("\n", "\r"), '', $request->phone));
        $data['address'] =trim(str_replace(array("\n", "\r"), '', $request->address));
         $data['postal_code'] =trim(str_replace(array("\n", "\r"), '', $request->postal_code));
        $data['vendor_id'] =trim(str_replace(array("\n", "\r"), '', $request->vendor_id));
        $data['tracking_id'] =trim(str_replace(array("\n", "\r"), '', $request->tracking_Id)); 
      
        $vendor = Vendor::find($data['vendor_id']);
         
          $startTime = empty($vendor->attributes['order_start_time']) ? time() :
           date('H:i',strtotime($vendor->attributes['order_start_time']));
           $startTime="10:00";
          $due = strtotime( date("Y-m-d $startTime" ) );
          $dueTime = new \DateTime();
          $dueTime->setTimestamp($due);
          $dueTime->modify("+1 day");
          
          $end_time= date('H:i',strtotime("21:00:00") );
          $d = new \stdClass();
          $sprint = new \stdClass();
          $sprint->creator_id =$data['vendor_id'];
        //   $sprint->merchant_order_num= $data->merchant_order_num;
          $sprint->tracking_id= $data['tracking_id'];
          $sprint->end_time=$end_time;
          $sprint->start_time=$startTime;
          $sprint->due_time=strtotime($dueTime->format('y-m-d H:i:s'));
          $d->sprint = $sprint;
          $contact = new \stdClass();
          $contact->name = $data['name'];
          $contact->phone = $data['phone'];
          $d->contact = $contact;

          $location = new \stdClass();
          

          $dropoffAdd = $data['address']." ".$data['postal_code'];
         
          $googleAddress=$this->google_address($data['address'],$data['postal_code']);
        
          if($googleAddress==0)
          {
            return   response()->json(['status_code'=>400,"error"=>"Invalid Address"]);
          }
         
         
          if(!isset($googleAddress['postal_code']) || !isset($googleAddress['address']))
          {
            return   response()->json(['status_code'=>400,"error"=>"Invalid Address"]);
          }

          $location->address=$dropoffAdd;
       
          $d->location=$location;
          
          $notification_method = new \stdClass();
          $d->notification_method='none';
          $d->admin='1'; 
          $HTTP_RAW_POST_DATA='$HTTP_RAW_POST_DATA';
          $amazon_enteries=null;
          if(in_array($data['vendor_id'],[477260,477282,476592]))
          {
            $amazon_enteries=new AmazonEntry();
            $amazon_enteries->creator_id=$data['vendor_id'];
            $amazon_enteries->tracking_id=$data['tracking_id'];
            $amazon_enteries->address_line_1=$data['address'];
            $amazon_enteries->address_line_2=$data['address'];
            $amazon_enteries->address_line_3=$data['address'];
          }
          $response = $this->OrderRequest($d,'create_order_custom_route',"POST");

           
           $response=json_decode($response,true);
           if($response==null)
           {
            return   response()->json(['status_code'=>400,"error"=>json_encode($response)]);
           }
          if($response['http']['code']==400)
            {
                  
              
                    return    response()->json(['status_code'=>400,"error"=>json_encode($response['response'])]);
            
            }
            if($response['http']['code']==201 || $response['http']['code']==200)
            {
               
               if(isset($response['response']['id']) )
               {
                   
              $tracking_id_data=  CustomRoutingTrackingId::where('tracking_id','=', $data['tracking_id'] )->where('is_big_box','=',0)->whereNull('deleted_at')->first();
           
              $tracking_id= MerchantIds::
              join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
            ->join('sprint__sprints',"sprint__sprints.id",'=','sprint__tasks.sprint_id')
            ->join('locations','locations.id','=',"location_id")
            ->join('sprint__contacts','sprint__contacts.id','=','contact_id')
            ->where('sprint__tasks.type','=','dropoff')
            ->where('tracking_id','=',$data['tracking_id'])->first(['sprint__sprints.creator_id','locations.address','locations.postal_code','sprint__tasks.id','sprint__tasks.status_id'
            ,'sprint__contacts.name','sprint__contacts.phone','merchantids.tracking_id','merchantids.task_id','sprint__tasks.sprint_id']);
     
                 $tracking_id_data->valid_id=1;
                 $tracking_id_data->vendor_id=$tracking_id->creator_id;
                 $tracking_id_data->name=$tracking_id->name;
                 $tracking_id_data->contact_no=$tracking_id->phone;
                 $tracking_id_data->address=$tracking_id->address;
                 $tracking_id_data->postal_code=$tracking_id->postal_code;
                 $tracking_id_data->reason='Order created';
                 $tracking_id_data->save();

                if(in_array($tracking_id->creator_id,[477340,477341,477342,477343,477344,477345,477346,477255,477254,477283,477284,477286,477287,477288,477289,477307,477308,477309,477310,
                477311,477312,477313,477314,477292,477294,477315,477317,477316,477295,477302,477303,477304,477305,477306,477296,477290,477297,
                477298,477299,477300,477320,477301,477318,477328,476294,477334,477335,477336,477337,477338,477339,477171,477559,477607,477609,477613,477587,477589]))
                 {
                   
                    
                    
                     $pickupstoretime_date=new \DateTime();               
                     $pickupstoretime_date->modify('+1 minutes');
                      $taskhistory=new TaskHistory();
                      $taskhistory->sprint_id=$tracking_id->sprint_id;
                      $taskhistory->sprint__tasks_id=$tracking_id->id;
                      $taskhistory->status_id=125;
                      $taskhistory->created_at = $pickupstoretime_date->format('Y-m-d H:i:s');
                      $taskhistory->date = $pickupstoretime_date->format('Y-m-d H:i:s');
                      $taskhistory->save();
                     
                      $pickupstoretime_date=new \DateTime();
                      $pickupstoretime_date->modify('+2 minutes');
                      $taskhistory=new TaskHistory();
                      $taskhistory->sprint_id=$tracking_id->sprint_id;
                      $taskhistory->sprint__tasks_id=$tracking_id->id;
                      $taskhistory->status_id=124;
                      $taskhistory->created_at= $pickupstoretime_date->format('Y-m-d H:i:s');
                      $taskhistory->date= $pickupstoretime_date->format('Y-m-d H:i:s');
                      $taskhistory->save();

                     $date=$tracking_id_data->route_enable_date." 17:00:00";
                     Sprint::where('id','=',$tracking_id->sprint_id)->update(['status_id'=>124,"in_hub_route"=>0]);
                     Task::where('id','=',$tracking_id->task_id)->update(['status_id'=>124]);

                     
                 } 
                 else
                 {
                 
                     $date=$tracking_id_data->route_enable_date." 17:00:00";
                     $date = date('Y-m-d H:i:s', strtotime($date . ' -1 days'));
                     Sprint::where('id','=',$tracking_id->sprint_id)->update(['status_id'=>61,"in_hub_route"=>0,"created_at"=>$date]);
                     Task::where('id','=',$tracking_id->task_id)->update(['status_id'=>61,'created_at'=>$date]);
     
                 }

                 if($tracking_id!=null && $amazon_enteries!=null)
                 {
                     $amazon_enteries->sprint_id=$response['response']['id'];
                     $amazon_enteries->task_id=$tracking_id->id;
                     $amazon_enteries->task_status_id=$tracking_id->status_id;
                     $amazon_enteries->address_line_2=$amazon_enteries->address_line_1;
                     $amazon_enteries->address_line_1=$tracking_id->address;
					 $amazon_enteries->is_custom_route=1;
                     $amazon_enteries->save();
                 }
              
                 return response()->json(['status_code'=>200,"message"=>"Order created",
                 "data"=>["tracking_id"=>$tracking_id_data->tracking_id,"vendor_id"=>$tracking_id_data->vendor_id,"name"=>$tracking_id_data->name,
                 "phone"=>$tracking_id_data->contact_no,"address"=>$tracking_id_data->address,"postal_code"=>$tracking_id_data->postal_code,
                 "route_enable_date"=>$tracking_id_data->route_enable_date,
                 "valid"=>$tracking_id_data->valid_id,'reason'=>$tracking_id_data->reason]]);
          
               }
               else
               {
                return  response()->json(['status_code'=>400,"error"=>json_encode($response['response'])]);
               }

               
            }

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

    public function getTrackingIdDetail(Request $request)
    {
        $user= Auth::user();
        $exist=1;
        $tracking_id_data=CustomRoutingTrackingId::where('user_id','=',$user->id)->where('is_big_box','=',0)->where('tracking_id','=',trim($request->tracking_id))->
        where('hub_id','=',trim($request->hub_id))->whereNull('deleted_at')->first();
        if($tracking_id_data==null)
        {
         $tracking_id_data=new CustomRoutingTrackingId();
         $tracking_id_data->user_id= $user->id;
         $tracking_id_data->tracking_id= trim($request->tracking_id);
         $tracking_id_data->hub_id= trim($request->hub_id);
         $exist=0;
        } 
        
       
        $tracking_id= MerchantIds::
          join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
        ->join('sprint__sprints',"sprint__sprints.id",'=','sprint__tasks.sprint_id')
        ->join('locations','locations.id','=',"location_id")
        ->join('sprint__contacts','sprint__contacts.id','=','contact_id')
        ->where('sprint__tasks.type','=','dropoff')
        ->where('tracking_id','=',trim($request->tracking_id))->first(['sprint__sprints.creator_id','locations.address','locations.postal_code'
        ,'sprint__contacts.name','sprint__contacts.phone','merchantids.tracking_id','sprint__sprints.id','merchantids.task_id','sprint__sprints.status_id']);
 
         if($tracking_id !=null)
         {

            if($request->hub_id==16)
            {
                if($tracking_id->creator_id!=477260)
                {
                    return response()->json( ['status_code'=>404,"error"=>"Tracking Id does not belong to this city."]);
                }
            }
            elseif($request->hub_id==19)
            {
                if(!in_array($tracking_id->creator_id,[477340,477341,477342,477343,477344,477345,477346,477282,476592]))
                {
                    return response()->json( ['status_code'=>404,"error"=>"Tracking Id does not belong to this city."]);
                }
            }
            elseif($request->hub_id==17)
            {
                if(!in_array($tracking_id->creator_id,[477542,477255,477254,477283,477284,477286,477287,477288,477289,477307,477308,477309,477310,
                477311,477312,477313,477314,477292,477294,477315,477317,477316,477295,477302,477303,477304,477305,477306,477296,477290,477297,
                477298,477299,477300,477320,477301,477318,477328,476294,477334,477335,477336,477337,477338,477339,477171,477559,477587,477589]))
                {
                    return response()->json( ['status_code'=>404,"error"=>"Tracking Id does not belong to this city."]);
                }
            }

            elseif($request->hub_id==129)
            {
                if(!in_array($tracking_id->creator_id,[477607,477609,477613]))
                {
                    return response()->json( ['status_code'=>404,"error"=>"Tracking Id does not belong to this city."]);
                }
            }

            
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
             477298,477299,477300,477320,477301,477318,477328,476294,477334,477335,477336,477337,477338,477339,477171,477559,477607,477609,477613,477587,477589]))
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
                 BoradlessDashboard::where('sprint_id', '=', $tracking_id->id)->update(['task_status_id'=>124]); 
              
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
    public function editOrder(Request $request)
    {
        $user= Auth::user();
        $data=$request->all();
        $orderData=MerchantIds::join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
        ->where('merchantids.tracking_id',$data['tracking_id'])
        ->where('sprint__tasks.type','=','dropoff')
        ->first(['contact_id','location_id','sprint__tasks.sprint_id']);
        $contactData=ContactUncrypted::where('id',$orderData->contact_id)->first();
        $contactData->name=$data['name'];
        $contactData->phone=$data['phone'];
        $contactData->save();
       
        $locationData=LocationUnencrypted::where('id',$orderData->location_id)->first();
        $pattern = "/^[A-Za-z]{1}+$/";
        if(preg_match($pattern,$data['address']))
        {
            return response()->json( ['status_code'=>400,"error"=>"Invalid Address.","data"=>["tracking_id"=>null,"vendor_id"=>null,
            "name"=>null,'phone'=>null,'address'=>null,'postal_code'=>null,'valid'=>1,'vendor'=>[],"exist"=>1]]);
         
        }

        $pattern = "/^[0-9 ]+$/";
        if(preg_match($pattern,$data['address']))
        {
            
            return response()->json( ['status_code'=>400,"error"=>"Invalid Address.","data"=>["tracking_id"=>null,"vendor_id"=>null,
            "name"=>null,'phone'=>null,'address'=>null,'postal_code'=>null,'valid'=>1,'vendor'=>[],"exist"=>1]]);
        }
        $pattern = "/^[A-Za-z ]+$/";
        if(!preg_match($pattern,$data['name']))
        {
            return response()->json( ['status_code'=>400,"error"=>"Invalid Customer Name.","data"=>["tracking_id"=>null,"vendor_id"=>null,
            "name"=>null,'phone'=>null,'address'=>null,'postal_code'=>null,'valid'=>1,'vendor'=>[],"exist"=>1]]);
       
        }
      
        $pattern = "/^[0-9]+$/";
        if(!preg_match($pattern,$data['phone']))
        {
            return response()->json( ['status_code'=>400,"error"=>"Invalid Customer Phone No.","data"=>["tracking_id"=>null,"vendor_id"=>null,
            "name"=>null,'phone'=>null,'address'=>null,'postal_code'=>null,'valid'=>1,'vendor'=>[],"exist"=>1]]);
       
        }
        
        $googleAddress=$this->google_address($data['address'],$data['postal_code']);
     
        if($googleAddress==0)
        {
            return response()->json( ['status_code'=>400,"data"=>["tracking_id"=>null,"vendor_id"=>null,
            "name"=>null,'phone'=>null,'address'=>null,'postal_code'=>null,'valid'=>1,'vendor'=>[],"exist"=>1]]);
        }
        
        if(!isset($googleAddress['postal_code']) || !isset($googleAddress['address']))
        {
            return response()->json( ['status_code'=>400,"data"=>["tracking_id"=>null,"vendor_id"=>null,
            "name"=>null,'phone'=>null,'address'=>null,'postal_code'=>null,'valid'=>1,'vendor'=>[],"exist"=>1]]);
        }

        $locationData->address=$googleAddress['address'];
        $locationData->postal_code=$googleAddress['postal_code'];
        
        // $locationData->latitude =$googleAddress['lat'];
        // $locationData->longitude =$googleAddress['lat'];
        $locationData->latitude =(int)str_replace(".","",$googleAddress['lat']);
        $locationData->longitude =(int)str_replace(".","",$googleAddress['lng']);
        $locationData->save();

        // $locationData->city_id  =
        // $locationData->state_id =
        // $locationData->country_id =
        // dd($data);

        $tracking_id_data=CustomRoutingTrackingId::where('user_id','=',$user->id)->where('tracking_id','=',$data['tracking_id'])->whereNull('deleted_at')->first();
        $tracking_id_data->name=$data['name'];
        $tracking_id_data->contact_no=$data['phone'];
        $tracking_id_data->address=$googleAddress['address'];
        $tracking_id_data->postal_code=$googleAddress['postal_code'];
        $tracking_id_data->save();
        // amazon entry address updated
        $amazon_enteries =AmazonEntry::where('sprint_id','=',$orderData->sprint_id)->whereNull('deleted_at')->first();
        if($amazon_enteries!=null)
        {
            $amazon_enteries->address_line_3=$amazon_enteries->address_line_2;
            $amazon_enteries->address_line_2=$amazon_enteries->address_line_1;
            $amazon_enteries->address_line_1=$googleAddress['address'];
            $amazon_enteries->save();
        }

        return response()->json( ['status_code'=>200,"data"=>["tracking_id"=>$data['tracking_id'],"vendor_id"=>$tracking_id_data->vendor_id,"route_enable_date"=>$tracking_id_data->route_enable_date,
        "name"=>$data['name'],'phone'=>$data['phone'],'address'=>$googleAddress['address'],'postal_code'=>$googleAddress['postal_code'],'valid'=>1,'vendor'=>[],"exist"=>1,'reason'=>$tracking_id_data->reason]]);

    }
    public function routeEnable(Request $request)
    {
        return backend_view( 'customrouting.enable-routes');
    }
    public function enableTrackingId(Request $request)
    {
            $data=$request->all();
            $data['tracking_id']=explode("\n",$data['tracking_id']);
            // unset($data['tracking_id'][count($data['tracking_id'])-1]);
           
            if($request->get('vendor_type')=="CTC")
            {
                $hub_id=17;
            }
            elseif($request->get('vendor_type')=="Amazon")
            {
                $hub_id=19;
            }
            else
            {
                $hub_id=16;
            }
            
            $i=0;
                foreach($data['tracking_id'] as $value)
           {
                
            $value=trim($value);
               
               $sprint= MerchantIds::join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
               ->join('sprint__sprints','sprint__sprints.id','=','sprint__tasks.sprint_id')
               ->whereNull('sprint__sprints.deleted_at')
                 ->where('type','=',"dropoff")  
                 ->whereNotNull('tracking_id')
                 ->orderby('sprint__sprints.id','DESC')                
                ->where('tracking_id','=',$value)->first(['sprint__tasks.id','sprint__tasks.sprint_id','sprint__sprints.creator_id']);

                if(!empty($sprint))
                {
                   
                    if($hub_id==17)
                    {
                        $i=1;
                        Sprint::where('id','=',$sprint->sprint_id)->update(['status_id'=>124,"in_hub_route"=>0]);
                        Task::where('id','=',$sprint->id)->update(['status_id'=>124]);
                        
                    } 
                    elseif($hub_id==19 || $hub_id==16 )
                    {
                        $i=1;
                        $date = date("Y-m-d H:i:s");
                        $date = date('Y-m-d H:i:s', strtotime($date . ' -1 days'));
                        Sprint::where('id','=',$sprint->sprint_id)->update(['status_id'=>61,"in_hub_route"=>0,"created_at"=>$date]);
                        Task::where('id','=',$sprint->id)->update(['status_id'=>61]);
                       
                    }
                    
                    
                }
            
           }
           if($i==0)
           {
               return response()->json( ['status_code'=>400,'error'=>'Invalid Trackings!']);
           }

           
           return response()->json( ['status_code'=>200,'success'=>'Order Enable for Routing Successfully']);
    }

    public function updateStatuscreateReattempt(Request $request)
    {           
        $user= Auth::user();
        $tracking_id=trim($request->tracking_id);
        $tracking_id_data=CustomRoutingTrackingId::where('user_id','=',$user->id)->where('is_big_box','=',0)->where('tracking_id','=',trim($request->tracking_id))->
        where('hub_id','=',trim($request->hub_id))->whereNull('deleted_at')->first();
        $merchantIds= MerchantIds::
        join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
        ->where('sprint__tasks.type','=','dropoff')
        ->where('tracking_id','=',$tracking_id)->first(['merchantids.tracking_id','sprint__tasks.sprint_id','merchantids.task_id']);
        Sprint::where('id','=',$merchantIds->sprint_id)->update(['status_id'=>$request->status_id,"in_hub_route"=>0]);
        Task::where('id','=',$merchantIds->task_id)->update(['status_id'=>$request->status_id]);
           
                
                
            $pickupstoretime_date=\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
           
         
            $taskhistory = new TaskHistory();
            $taskhistory->sprint_id = $merchantIds->sprint_id;
            $taskhistory->sprint__tasks_id = $merchantIds->task_id;
            $taskhistory->status_id = $request->status_id;
            $taskhistory->created_at = $pickupstoretime_date->format('Y-m-d H:i:s');
            $taskhistory->date = $pickupstoretime_date->format('Y-m-d H:i:s');
            $taskhistory->save();

                $ReturnOrderCreation=new ReturnOrderCreation($tracking_id);
                if(!$ReturnOrderCreation->isInvalidQRCode())
                {
                    $response=$ReturnOrderCreation->getResponse();
                    return response()->json( ['status_code'=>404,"error"=>"Alert :".$response['response']]);
                }
                $ReturnOrderCreation->createReturnResponse();
                $ReturnOrderCreation->createReturn();
                $response=$ReturnOrderCreation->getResponse();
                if($response['status_code']==400)
                {
                    $tracking_id_data->reason=$response['response'];
                    $tracking_id_data->valid_id=4;
                    $tracking_id_data->save();
                    // return response()->json( ['status_code'=>404,"error"=>"Alert :".$response['response']]);
                    return response()->json( ['status_code'=>405,"data"=>["tracking_id"=>trim($request->tracking_id),"route_enable_date"=>$tracking_id_data->route_enable_date,"vendor_id"=>$tracking_id_data->creator_id,
                "name"=>$tracking_id_data->name,'phone'=>$tracking_id_data->phone,'address'=>$tracking_id_data->address,'postal_code'=>$tracking_id_data->postal_code,'valid'=>$tracking_id_data->valid_id,'vendor'=>[],"exist"=>1,'reason'=>$tracking_id_data->reason]]);

                }
                $current_date = date("Y-m-d H:i:s");
                $remove = ReturnAndReattemptProcessHistory::where('tracking_id', $tracking_id)
                    ->where('is_action_applied', 0)
                    ->first();

                // checking this record is already in process
                if (isset($remove)) {
                    // updating the record
                    $remove->deleted_at = $current_date;
                    $remove->deleted_by = 'custom_routing';
                    $remove->save();
                }

                $merchantIds= MerchantIds::
                join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
                ->join('sprint__sprints',"sprint__sprints.id",'=','sprint__tasks.sprint_id')
                ->join('locations','locations.id','=',"location_id")
                ->join('sprint__contacts','sprint__contacts.id','=','contact_id')
                ->where('sprint__tasks.type','=','dropoff')
                ->where('tracking_id','=',$tracking_id)->first(['sprint__sprints.creator_id','locations.address','locations.postal_code'
                ,'sprint__contacts.name','sprint__contacts.phone','merchantids.tracking_id','sprint__sprints.id','merchantids.task_id','sprint__sprints.status_id']);


                $tracking_id_data->valid_id=1;
                $tracking_id_data->vendor_id=$merchantIds->creator_id;
                $tracking_id_data->name=$merchantIds->name;
                $tracking_id_data->contact_no=$merchantIds->phone;
                $tracking_id_data->address=$merchantIds->address;
                $tracking_id_data->postal_code=$merchantIds->postal_code;
                $tracking_id_data->reason="Order reattempted.";
                $tracking_id_data->save();

                return response()->json( ['status_code'=>200,"data"=>["tracking_id"=>trim($request->tracking_id),"route_enable_date"=>$tracking_id_data->route_enable_date,"vendor_id"=>$merchantIds->creator_id,
                "name"=>$merchantIds->name,'phone'=>$merchantIds->phone,'address'=>$merchantIds->address,'postal_code'=>$merchantIds->postal_code,'valid'=>1,'vendor'=>[],"exist"=>1,'reason'=>$tracking_id_data->reason]]);
               
    }
    public function createReattempt(Request $request)
    {
            $user= Auth::user();
        
            $tracking_id_data=CustomRoutingTrackingId::where('user_id','=',$user->id)->where('is_big_box','=',0)->where('tracking_id','=',trim($request->tracking_id))->
            where('hub_id','=',trim($request->hub_id))->whereNull('deleted_at')->first();
            $tracking_id=trim($request->tracking_id);
            $ReturnOrderCreation=new ReturnOrderCreation($tracking_id);
            if(!$ReturnOrderCreation->isInvalidQRCode())
            {
                $response=$ReturnOrderCreation->getResponse();
                return response()->json( ['status_code'=>404,"error"=>"Alert :".$response['response']]);
            }
            $ReturnOrderCreation->createReturnResponse();
            $ReturnOrderCreation->createReturn();
            $response=$ReturnOrderCreation->getResponse();
            if($response['status_code']==400)
            {
                $tracking_id_data->reason=$response['response'];
                $tracking_id_data->valid_id=4;
                $tracking_id_data->save();
                // return response()->json( ['status_code'=>404,"error"=>"Alert :".$response['response']]);
                return response()->json( ['status_code'=>405,"data"=>["tracking_id"=>trim($request->tracking_id),"route_enable_date"=>$tracking_id_data->route_enable_date,"vendor_id"=>$tracking_id_data->creator_id,
            "name"=>$tracking_id_data->name,'phone'=>$tracking_id_data->phone,'address'=>$tracking_id_data->address,'postal_code'=>$tracking_id_data->postal_code,'valid'=>$tracking_id_data->valid_id,'vendor'=>[],"exist"=>1,'reason'=>$tracking_id_data->reason]]);

            }
            $current_date = date("Y-m-d H:i:s");
            $remove = ReturnAndReattemptProcessHistory::where('tracking_id', $tracking_id)
                ->where('is_action_applied', 0)
                ->first();

                // checking this record is already in process
            if (isset($remove)) {
                    // updating the record
                $remove->deleted_at = $current_date;
                $remove->deleted_by = 'custom_routing';
                $remove->save();
            }

            $merchantIds= MerchantIds::
            join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
            ->join('sprint__sprints',"sprint__sprints.id",'=','sprint__tasks.sprint_id')
            ->join('locations','locations.id','=',"location_id")
            ->join('sprint__contacts','sprint__contacts.id','=','contact_id')
            ->where('sprint__tasks.type','=','dropoff')
            ->where('tracking_id','=',$tracking_id)->first(['sprint__sprints.creator_id','locations.address','locations.postal_code'
            ,'sprint__contacts.name','sprint__contacts.phone','merchantids.tracking_id','sprint__sprints.id','merchantids.task_id','sprint__sprints.status_id']);


            $tracking_id_data->valid_id=1;
            $tracking_id_data->vendor_id=$merchantIds->creator_id;
            $tracking_id_data->name=$merchantIds->name;
            $tracking_id_data->contact_no=$merchantIds->phone;
            $tracking_id_data->address=$merchantIds->address;
            $tracking_id_data->postal_code=$merchantIds->postal_code;
            $tracking_id_data->reason='Reattempt created.';
            $tracking_id_data->save();


            return response()->json( ['status_code'=>200,"data"=>["tracking_id"=>trim($request->tracking_id),"route_enable_date"=>$tracking_id_data->route_enable_date,"vendor_id"=>$merchantIds->creator_id,
            "name"=>$merchantIds->name,'phone'=>$merchantIds->phone,'address'=>$merchantIds->address,'postal_code'=>$merchantIds->postal_code,'valid'=>1,'vendor'=>[],"exist"=>1,'reason'=>$tracking_id_data->reason]]);
           
    }

    public function postFileRead(Request $request)
    {
        $file = $request->excelFile;
        $path_org = "public/";
        $k=0;
        // upload main image org size
        $name=$file->getClientOriginalName();

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $allow_file = array("csv");
        if(!in_array($extension,$allow_file) ){
            return back()->with('error','Only CSV file allow to process');
            // return response()->json( ['status_code'=>400,'error'=>'Only CSV file allow to process']);
                       
        
        }

        $rand_num = uniqid();
        $name=$rand_num.'-opimg-'.$name;
        $file->move($path_org,$name);
        $filedata=file_get_contents("public/".$name);
        $enablefiledata=new EnableRouteFile();
        $enablefiledata->file_name=$name;
        $enablefiledata->vendor_type=$request->vendor_type;
      
       
       

         $filedata=explode("\n",$filedata);
         $i=0;
         $hub_id=null;
         $ids=[];
        if(count($filedata)-2 <= 0)
        {
            return back()->with('error','No data found in file');
      
           
        }
            // if($request->get('vendor_type')=="CTC")
            // {
            //     $hub_id=17;
            // }
            // elseif($request->get('vendor_type')=="Amazon")
            // {
            //     $hub_id=19;
            // }
            // else
            // {
            //     $hub_id=16;
            // }
                foreach($filedata as $value)
           {
                
             if($i==0)
             {
                    $value=(String)trim($value);
                    $value = preg_replace('/[^A-Za-z0-9\_]/', '', $value);
                    if(strcmp($value,"tracking_id")!=0)
                    {
                        return back()->with('error','File should have column name tracking_id');
                        // return response()->json( ['status_code'=>400,'error'=>'File should have column name tracking_id']);
                       
                    }
                    $enablefiledata->save();
                  
             }
             elseif($i==count($filedata)-1)
             {

             }
             else
             {
                
                $value = preg_replace('/[^A-Za-z0-9\_]/', '', $value);
                $ids[$i]=trim($value);
                
               $sprint= MerchantIds::join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
               ->join('sprint__sprints','sprint__sprints.id','=','sprint__tasks.sprint_id')
               ->whereNull('sprint__sprints.deleted_at')
               ->whereNotNull('merchantids.tracking_id')
               ->orderby('sprint__sprints.id','DESC')
                 ->where('type','=',"dropoff")                         
                ->where('tracking_id','=',$ids[$i])->first(['sprint__tasks.id','sprint__tasks.sprint_id','sprint__sprints.creator_id']);
               
                if(!empty($sprint))
                {
                    $k=1;
                    $trackingIddata=new EnableForRoutesTrackingId();
                    $trackingIddata->file_id=$enablefiledata->id;
                    $trackingIddata->tracking_id=$ids[$i];
                    $trackingIddata->task_id=$sprint->id;
                    $trackingIddata->sprint_id=$sprint->sprint_id;
                    $trackingIddata->is_enable=0;
                    $task=Task::where('id','=',$sprint->id)->first(['location_id']);
                    $trackingIddata->location_id=$task->location_id;
                    $trackingIddata->save();
                    
                }
             }
             $i++;
           }

           unlink("public/".$name);

           if($k==0)
           {
            return redirect('backend/enable/route')->with(['error'=>'No Order for Enable for Routing']);
           }
        //    $orders=EnableRouteFile::join('enable_for_routes_tracking_id','enable_for_routes_tracking_id.file_id','=','enable_route_file.id')
        //    ->join('locations','locations.id','=','enable_for_routes_tracking_id.location_id')
        //    ->where('enable_route_file.id','=',$enablefiledata->id)
        //    ->whereNull('enable_route_file.deleted_at')
        //    ->whereNull('enable_for_routes_tracking_id.deleted_at')
        //    ->get(['enable_for_routes_tracking_id.tracking_id','enable_for_routes_tracking_id.id','locations.address','locations.postal_code'])->toArray();

           return redirect('backend/enable/route/'.$enablefiledata->id.'/file');
        //    $data=[];
        //    foreach($orders as $order)
        //    {
        //     $data[]=array_values($order);
        //    }
          
        // return response()->json( ['status_code'=>200,'success'=>'Order Enable for Routing Successfully'file_id'=>$enablefiledata->id]);
      
    }
    public function routeEnableOrders($id)
    {
        $orders=EnableRouteFile::join('enable_for_routes_tracking_id','enable_for_routes_tracking_id.file_id','=','enable_route_file.id')
           ->join('locations','locations.id','=','enable_for_routes_tracking_id.location_id')
           ->where('enable_route_file.id','=',$id)
           ->whereNull('enable_route_file.deleted_at')
           ->whereNull('enable_for_routes_tracking_id.deleted_at')
           ->get(['enable_for_routes_tracking_id.tracking_id','enable_for_routes_tracking_id.id','locations.address','locations.postal_code']);
           if(count($orders)==0)
           {
            return redirect('backend/enable/route')->with(['success'=>'All Order Enable for Routing Successfully']);
           }
           return backend_view( 'customrouting.enable-route-file', compact('orders','id') );

    }
    public function enableOrderForRoute(Request $request)
    {
        $file_id=$request->file_id;
        $tracking_id=$request->ids;
        $orders =EnableRouteFile::
        join('enable_for_routes_tracking_id','enable_for_routes_tracking_id.file_id','=','enable_route_file.id')
        ->where('enable_for_routes_tracking_id.file_id','=',$file_id)
        ->whereIn('enable_for_routes_tracking_id.tracking_id',$tracking_id)
        ->get(['enable_for_routes_tracking_id.sprint_id','enable_for_routes_tracking_id.task_id'
        ,'enable_route_file.vendor_type','enable_for_routes_tracking_id.tracking_id']);
        if(count($orders)==0)
        {
            return response()->json( ['status_code'=>400,'error'=>'No Order for Enable for Routing','file_id'=>$file_id]);
        }
        foreach($orders as $sprint)
        {
           
                    if($sprint->vendor_type=="CTC")
                    {
                        EnableForRoutesTrackingId::where('tracking_id','=',$sprint->tracking_id)->where('file_id','=',$file_id)->update(['is_enable'=>1]);
                        Sprint::where('id','=',$sprint->sprint_id)->update(['status_id'=>124,"in_hub_route"=>0]);
                        Task::where('id','=',$sprint->task_id)->update(['status_id'=>124]);
                        
                    } 
                    else
                    {
                        EnableForRoutesTrackingId::where('tracking_id','=',$sprint->tracking_id)->where('file_id','=',$file_id)->update(['is_enable'=>1]);
                        $date = date("Y-m-d H:i:s");
                        $date = date('Y-m-d H:i:s', strtotime($date . ' -1 days'));
                        Sprint::where('id','=',$sprint->sprint_id)->update(['status_id'=>61,"in_hub_route"=>0,"created_at"=>$date]);
                        Task::where('id','=',$sprint->task_id)->update(['status_id'=>61]);
  
                    }
        }
        EnableForRoutesTrackingId::whereIn('tracking_id',$tracking_id)->where('file_id','=',$file_id)->update(['deleted_at'=>date("Y-m-d H:i:s")]);
        return response()->json( ['status_code'=>200,'success'=>'Order Enable for Routing Successfully','file_id'=>$file_id]);
    }

    public function getCTCVendors(Request $request)
    {
       $vendor=[];
        if($request->get('is_true')=='true')
        {
            $vendor = Vendor::whereIn('id',[477282,476592])->get(['first_name', 'last_name', 'id']);
        }
        else
        {
            if($request->is_true_ctc=='true')
            {
                $vendor = Vendor::whereIn('id',[477340,477341,477342,477343,477344,477345,477346])->get(['first_name', 'last_name', 'id']);
            }
        }
       
        return response()
        ->json(['status_code' => 200, 'vendors'=>$vendor]);
    }
}
