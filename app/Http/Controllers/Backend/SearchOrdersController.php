<?php

namespace App\Http\Controllers\Backend;


use App\BoradlessDashboard;
use App\Classes\Fcm;
use App\Classes\JoyFlagLoginValidationsHandler;
use App\CTCEntry;
use App\CtcVendor;
use App\CustomerFlagCategories;
use App\FinanceVendorCity;
use App\FinanceVendorCityDetail;
use App\FlagCategoryMetaData;
use App\CustomerFlagCategoryValues;
use App\FlagHistory;
use App\CustomerIncidents;
use App\HubZones;
use DateTimeZone;
use DateTime;
use App\Http\Requests\Backend\UploadImageRequest;
use App\Joey;
use App\JoeyPerformanceHistory;
use App\JoeyRouteLocations;
use App\Reason;
use App\RouteHistory;
use App\UserDevice;
use App\UserNotification;
use Illuminate\Http\Request;
use App\AmazonEnteries;
use App\Claim;
use App\TaskHistory;
use App\SprintReattempt;
use App\MerchantIds;
use App\Sprint;
use App\Task;
use DB;
use Illuminate\Support\Facades\Auth;
use App\TrackingImageHistory;
use App\JoeyRoutes;
use App\JoeyLocation;
use App\SprintConfirmation;
use App\SprintTaskHistory;
use App\StatusMap;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Twilio\Rest\Client;


class SearchOrdersController extends BackendController
{

    public static $status = array("136" => "Client requested to cancel the order",
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
        "255" => 'Order Delay',
        '147' => 'Scanned at Hub',
        '148' => 'Scanned at Hub and labelled',
        '149' => 'pick from hub',
        '150' => 'drop to other hub',
        '153' => 'Miss sorted to be reattempt',
        '154' => 'Joey unable to complete the route', '155' => 'To be re-attempted tomorrow'
    );

    private $status_codes = [
        'completed'=>
            [
                "JCO_ORDER_DELIVERY_SUCCESS"=>17,
                "JCO_HAND_DELIEVERY" => 113,
                "JCO_DOOR_DELIVERY" => 114,
                "JCO_NEIGHBOUR_DELIVERY" => 116,
                "JCO_CONCIERGE_DELIVERY" => 117,
                "JCO_BACK_DOOR_DELIVERY" => 118,
                "JCO_OFFICE_CLOSED_DELIVERY" => 132,
                "JCO_DELIVER_GERRAGE" => 138,
                "JCO_DELIVER_FRONT_PORCH" => 139,
                "JCO_DEILVER_MAILROOM" => 144
            ],
        'return'=>
            [
                "JCO_ITEM_DAMAGED_INCOMPLETE" => 104,
                "JCO_ITEM_DAMAGED_RETURN" => 105,
                "JCO_CUSTOMER_UNAVAILABLE_DELIEVERY_RETURNED" => 106,
                "JCO_CUSTOMER_UNAVAILABLE_LEFT_VOICE" => 107,
                "JCO_CUSTOMER_UNAVAILABLE_ADDRESS" => 108,
                "JCO_CUSTOMER_UNAVAILABLE_PHONE" => 109,
                "JCO_HUB_DELIEVER_REDELIEVERY" => 110,
                "JCO_HUB_DELIEVER_RETURN" => 111,
                "JCO_ORDER_REDELIVER" => 112,
                "JCO_ORDER_RETURN_TO_HUB" => 131,
                "JCO_CUSTOMER_REFUSED_DELIVERY" => 135,
                "CLIENT_REQUEST_CANCEL_ORDER" => 136,
                "JCO_ON_WAY_PICKUP" => 101,
            ],

        'pickup'=>
            [
                "JCO_HUB_PICKUP"=>121
            ],

    ];

    public function statusmap($id)
    {
        $statusid = array("136" => "Client requested to cancel the order",
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
            "32"  => "Order accepted by Joey",
            "14"  => "Merchant accepted",
            "36"  => "Cancelled by JoeyCo",
            "124" => "At hub - processing",
            "38"  => "Draft",
            "18"  => "Delivery failed",
            "56"  => "Partially delivered",
            "17"  => "Delivery success",
            "68"  => "Joey is at dropoff location",
            "67"  => "Joey is at pickup location",
            "13"  => "At hub - processing",
            "16"  => "Joey failed to pickup order",
            "57"  => "Not all orders were picked up",
            "15"  => "Order is with Joey",
            "112" => "To be re-attempted",
            "131" => "Office closed - returned to hub",
            "125" => "Pickup at store - confirmed",
            "61"  => "Scheduled order",
            "37"  => "Customer cancelled the order",
            "34"  => "Customer is editting the order",
            "35"  => "Merchant cancelled the order",
            "42"  => "Merchant completed the order",
            "54"  => "Merchant declined the order",
            "33"  => "Merchant is editting the order",
            "29"  => "Merchant is unavailable",
            "24"  => "Looking for a Joey",
            "23"  => "Waiting for merchant(s) to accept",
            "28"  => "Order is with Joey",
            "133" => "Packages sorted",
            "55"  => "ONLINE PAYMENT EXPIRED",
            "12"  => "ONLINE PAYMENT FAILED",
            "53"  => "Waiting for customer to pay",
            "141" => "Lost package",
            "60"  => "Task failure",
            "255" => 'Order Delay',
            "145" => 'Returned To Merchant',
            "146" => "Delivery Missorted, Incorrect Address",
            '147' => 'Scanned at Hub',
            '148' => 'Scanned at Hub and labelled',
            '149' => 'pick from hub',
            '150' => 'drop to other hub',
            '153' => 'Miss sorted to be reattempt',
            '154' => 'Joey unable to complete the route', '155' => 'To be re-attempted tomorrow'
        );
        return $statusid[$id];
    }


  public function get_trackingid(Request $request)
  {
          $user=[];

        if(!empty($request->input('tracking_id')))
        {

            $id=$request->input('tracking_id');
            // dd(id)
            $user= MerchantIds::join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')->
            join('sprint__sprints','sprint__tasks.sprint_id','=','sprint__sprints.id') 
             ->where('sprint__tasks.type','=','dropoff');

            $user=$user->whereNull('sprint__sprints.deleted_at')
               ->where('merchantids.tracking_id','=',$id)->orderBy('merchantids.id','DESC')->take(1)
               ->get(array("sprint__sprints.*",'merchantids.tracking_id'));
               
                if(empty($user))
                {
                    $user=[];
                }

        }


          return backend_view('searchorder',['data'=>$user]);
  }

  public function get_trackingorderdetails($sprintId, Request $request)
  {

      $data = Auth::user();


      $statistics_id = implode(',',FinanceVendorCity::pluck('id')->toArray());

      $hubIds = HubZones::whereIn('zone_id', function ($query) use ($statistics_id) {
          $query->select(
              DB::raw('zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id in (' . $statistics_id . ')) ')
          );
      })
          ->pluck('hub_id')
          ->toArray();

      $show_message = $request->message;
      if(!is_null($show_message))
      {
          $current_url  = $request->url();
          $query_string = http_build_query( $request->except(['message'] ) );
          return redirect($current_url.'?'.$query_string)
              ->with('alert-success', $show_message);
      }

      $result= Sprint::join('sprint__tasks','sprint_id','=','sprint__sprints.id')
          ->leftJoin('merchantids','merchantids.task_id','=','sprint__tasks.id')
          ->leftJoin('joey_route_locations','joey_route_locations.task_id','=','sprint__tasks.id')
          ->leftJoin('joey_routes','joey_routes.id','=','joey_route_locations.route_id')
          ->leftJoin('joeys','joeys.id','=','joey_routes.joey_id')
          ->join('locations','sprint__tasks.location_id','=','locations.id')
          ->join('sprint__contacts','contact_id','=','sprint__contacts.id')
          ->leftJoin('vendors','creator_id','=','vendors.id')
          ->where('sprint__tasks.sprint_id','=',$sprintId)
          ->whereNull('joey_route_locations.deleted_at')
          ->orderBy('ordinal','DESC')->take(1)
          ->get(array('sprint__tasks.*','joey_routes.hub','joey_routes.id as route_id',\DB::raw("CONVERT_TZ(joey_routes.date,'UTC','America/Toronto') as route_date"),'locations.address','locations.suite','locations.postal_code','sprint__contacts.name','sprint__contacts.phone','sprint__contacts.email',
              'joeys.first_name as joey_firstname','joeys.id as joey_id',
              'joeys.last_name as joey_lastname','vendors.id as merchant_id','vendors.first_name as merchant_firstname','vendors.last_name as merchant_lastname','merchantids.scheduled_duetime'
          ,'joeys.id as joey_id','merchantids.tracking_id','joeys.phone as joey_contact','joey_route_locations.ordinal as stop_number','merchantids.merchant_order_num','merchantids.address_line2','sprint__sprints.creator_id','sprint__sprints.is_hub'));

      $i=0;

      $data = [];
      $sprint_id = 0;
      $order_type = ($result[0]->is_hub > 0)?'ecommerce':'grocery';
      foreach($result as $tasks){
          $sprint_id = $tasks->sprint_id;
          $status2 = array();
          $status = array();
          $status1 = array();
          $data[$i] =  $tasks;
          $taskHistory= TaskHistory::where('sprint_id','=',$tasks->sprint_id)->WhereNotIn('status_id',[17,38,0])->orderBy('date')
              //->where('active','=',1)
              ->get(['status_id',\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto') as created_at")]);

          $returnTOHubDate = SprintReattempt::
          where('sprint_reattempts.sprint_id','=' ,$tasks->sprint_id)->orderBy('created_at')
              ->first();

          if(!empty($returnTOHubDate))
          {
              $taskHistoryre= TaskHistory::where('sprint_id','=', $returnTOHubDate->reattempt_of)->WhereNotIn('status_id',[17,38,0])->orderBy('date')
                  ->get(['status_id',\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto') as created_at")]);

              foreach ($taskHistoryre as $history){

                  $status[$history->status_id]['id'] = $history->status_id;
                  if($history->status_id==13)
                  {
                      $status[$history->status_id]['description'] ='At hub - processing';
                  }
                  else
                  {
                      $status[$history->status_id]['description'] =$this->statusmap($history->status_id);
                  }
                  $status[$history->status_id]['created_at'] = $history->created_at;

              }

          }
          if(!empty($returnTOHubDate))
          {
              $returnTO2 = SprintReattempt::
              where('sprint_reattempts.sprint_id','=' , $returnTOHubDate->reattempt_of)->orderBy('created_at')
                  ->first();

              if(!empty($returnTO2))
              {
                  $taskHistoryre= TaskHistory::where('sprint_id','=',$returnTO2->reattempt_of)->WhereNotIn('status_id',[17,38,0])->orderBy('date')
                      ->get(['status_id',\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto') as created_at")]);

                  foreach ($taskHistoryre as $history){

                      $status2[$history->status_id]['id'] = $history->status_id;
                      if($history->status_id==13)
                      {
                          $status2[$history->status_id]['description'] ='At hub - processing';
                      }
                      else
                      {
                          $status2[$history->status_id]['description'] = $this->statusmap($history->status_id);
                      }
                      $status2[$history->status_id]['created_at'] = $history->created_at;

                  }

              }
          }


          foreach ($taskHistory as $history){


              if (in_array($history->status_id, [61,13]) or in_array($history->status_id, [124,125])) {
                  $status1[$history->status_id]['id'] = $history->status_id;

                  if ($history->status_id == 13) {
                      $status1[$history->status_id]['description'] = 'At hub - processing';
                  } else {
                      $status1[$history->status_id]['description'] = $this->statusmap($history->status_id);
                  }
                  $status1[$history->status_id]['created_at'] = $history->created_at;
              }
              else{
                  if ($history->created_at >= $tasks->route_date){
                      $status1[$history->status_id]['id'] = $history->status_id;

                      if ($history->status_id == 13) {
                          $status1[$history->status_id]['description'] = 'At hub - processing';
                      } else {
                          $status1[$history->status_id]['description'] = $this->statusmap($history->status_id);
                      }
                      $status1[$history->status_id]['created_at'] = $history->created_at;
                  }

              }

          }

          if($status!=null)
          {
              $sort_key = array_column($status, 'created_at');
              array_multisort($sort_key, SORT_ASC, $status);
          }
          if($status1!=null)
          {
              $sort_key = array_column($status1, 'created_at');
              array_multisort($sort_key, SORT_ASC, $status1);
          }
          if($status2!=null)
          {
              $sort_key = array_column($status2, 'created_at');
              array_multisort($sort_key, SORT_ASC, $status2);
          }


          $data[$i]['status']= $status;
          $data[$i]['status1']= $status1;
          $data[$i]['status2']=$status2;
          $i++;
      }

        $reasons = Reason::all();

      //getting flag categories
      $flagCategories =  CustomerFlagCategories::where('parent_id', 0)
          ->where('is_enable', 1)
          ->whereNull('deleted_at')
          ->get();

      //getting joey performance flag
      $joey_flags_history = FlagHistory::where('sprint_id',$sprint_id)
          ->orderBy('id', 'DESC')
          ->whereNull('deleted_at')
          ->where('unflaged_by','=',0)
          ->get();
        $manualHistory=[];    
        if(isset($result[0])){
            $manualHistory=$this->getManualStatusData($result[0]->tracking_id);
        }

        return backend_view('orderdetailswtracknigid',
            [
                'data'=>$data,
                'sprintId' => $sprintId,
                'reasons' => $reasons,
                'flagCategories' => $flagCategories,
                'joey_flags_history' => $joey_flags_history,
                'order_type' => $order_type,
                'manualHistory' => $manualHistory,
                'hubIds' => $hubIds,
            ]
        );
  }
  public function getManualStatusData($tracking_id)
    {

        $query = TrackingImageHistory::where('tracking_id', $tracking_id)->orderBy('created_at','desc')->get();

        if(count($query)){
            foreach ($query as $key => $value) {

                $current_status = $value->status_id;
                if ($current_status == 13) {
                    $query[$key]->status_id= "At hub Processing";
                }else {
                    $query[$key]->status_id= self::$status[$current_status];
                }
                if (isset($value->attachment_path)) {
                    // $query[$key]->attachment_path= '<img onClick="ShowLightBox(this);" style = "width:50px;height:50px" src = "' . $value->attachment_path . '" />';
                    $query[$key]->attachment_path=$value->attachment_path;

                } else {
                    $query[$key]->attachment_path= '';
                }
                if (isset($value->reason)) {
                    $query[$key]->reason_id= $value->reason->title;
                } else {
                    $query[$key]->reason_id= '';
                }
                if ($value->created_at) {
                    $created_at = new \DateTime($value->created_at, new \DateTimeZone('UTC'));
                    $created_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    $query[$key]->created_at= $created_at->format('Y-m-d H:i:s');
                } else {
                    $query[$key]->created_at= '';
                }
                if (isset($value->user)) {
                    $query[$key]->user_id= $value->user->full_name;
                } else {
                    $query[$key]->user_id= '';
                }

            }
        }
        return $query;
    }

  //Create Flag
    public function createFlag($flag_cat_id, Request $request)
    {
        DB::beginTransaction();
        try {
            // getting incident count by joey id and category id
            $incident_count = JoeyPerformanceHistory::where('joey_id',$request->joey_id)
                    ->where('flag_cat_id',$flag_cat_id)
                    ->where('unflaged_by','=',0)
                    ->count() + 1;

            // getting category data
            $flag_category = CustomerFlagCategories::where('id',$flag_cat_id)->first();

            // flag cat incident value should applied
            $flag_incident_values = CustomerFlagCategoryValues::where('category_ref_id',$flag_cat_id)->first()->toArray();

            // geting incident label
            $incident_label = '';
            $incident_label_finance = '';
            $rating_label = '';
            $incident_id = 1;

            // checking the incident is on conclusion or not
            if($incident_count < 4) // for incident value
            {

                $incident_id = $flag_incident_values['incident_'.$incident_count.'_ref_id'];

                $finance_incident_value = $flag_incident_values['finance_incident_'.$incident_count];
                $finance_incident_operator = $flag_incident_values['finance_incident_'.$incident_count.'_operator'];
                $incident_label = CustomerIncidents::where('id',$incident_id)->pluck('label')->first();
                $incident_label_finance = '{"value":"'.$finance_incident_value.'","operator":"'.$finance_incident_operator.'"}';

                $rating_value = $flag_incident_values['rating_'.$incident_count];
                $rating_operator = $flag_incident_values['rating_'.$incident_count.'_operator'];
                $rating_label = '{"value":"'.$rating_value.'","operator":"'.$rating_operator.'"}';

            }
            elseif($incident_count == 4) // for conclusion
            {
                $incident_id = $flag_incident_values['conclusion_ref_id'];
                $finance_incident_value = $flag_incident_values['finance_conclusion'];
                $finance_incident_operator = $flag_incident_values['finance_conclusion_operator'];
                $incident_label = CustomerIncidents::where('id',$incident_id)->pluck('label')->first();
                $incident_label_finance = '{"value":"'.$finance_incident_value.'","operator":"'.$finance_incident_operator.'"}';

                $rating_value = $flag_incident_values['rating_'.$incident_count];
                $rating_operator = $flag_incident_values['rating_'.$incident_count.'_operator'];
                $rating_label = '{"value":"'.$rating_value.'","operator":"'.$rating_operator.'"}';
            }
            else // for termination
            {
                $incident_id = 4; // this id for termination label
                $finance_incident_value = $flag_incident_values['finance_conclusion'];
                $finance_incident_operator = $flag_incident_values['finance_conclusion_operator'];
                $incident_label = CustomerIncidents::where('id',$incident_id)->pluck('label')->first();
                $incident_label_finance = '{"value":"'.$finance_incident_value.'","operator":"'.$finance_incident_operator.'"}';

                $rating_value = $flag_incident_values['rating_'.$incident_id];
                $rating_operator = $flag_incident_values['rating_'.$incident_id.'_operator'];
                $rating_label = '{"value":"'.$rating_value.'","operator":"'.$rating_operator.'"}';
                //dd([$incident_label_finance,$rating_label]);
            }

            //Mark Flag Against Joey
            $Joey_performance_history_data = JoeyPerformanceHistory::create([
                'joey_id' => $request->joey_id,
                'tracking_id' => $request->tracking_id,
                'sprint_id' => $request->sprint,
                'flag_cat_id' => $flag_category->id,
                'flag_cat_name' => $flag_category->category_name,
                'flaged_by' => Auth::user()->id,
                'portal_type' => 'dashboard',
                'incident_value_applied' => $incident_label,
                'finance_incident_value_applied' => $incident_label_finance,
                'rating_value' =>$rating_label
            ]);

            //Getting joeys details to send notification
            $joey_data = Joey::where('id','=',$request->joey_id)
                ->first();
				
			if ($joey_data == null)
			{
				return response()->json(['status' => false, 'message' => 'This order has no joey for flag']);
			}

            //base64 convert
            $email = base64_encode ($joey_data->email);

            //getting flag details
            $joey_flag = ["sprint_no"=> $request->sprint,"flag_name"=> $flag_category->category_name];
			


            // set login validation
            $login_validation = new JoyFlagLoginValidationsHandler();
            $login_validation->setValues($request->joey_id,$incident_id);
            $login_validation->applyAction();

            DB::commit();
            return response()->json(['status' => true, 'message' => 'This order Flaged successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Something went wrong ']);
        }
    }

    //un-flag order
    public function unFlag($unFlag_id)
    {
        //getting data for un-flag order
        $unflag = JoeyPerformanceHistory::find($unFlag_id);

        //Getting joeys details to send notification
        $joey_data = Joey::where('id','=',$unflag->joey_id)
            ->first();

        //checking condition data exist or not
        if (is_null($unflag))
        {
            return redirect()->back()
                ->with('alert-danger', 'The id does`nt exist');
        }

        //Update Sprint For Return Order
        $unflag->unflaged_by = Auth::user()->id;
        $unflag->unflaged_date = date('Y-m-d H:i:s');
        $unflag->save();

        //base64 convert email
        $email = base64_encode ($joey_data->email);

        //getting flag details
        $joey_flag = ["sprint_no"=> $unflag->sprint_id,"flag_name"=> $unflag->flag_cat_name];

             return redirect()->back()
            ->with('alert-success', 'This order is un-flag successfully');

    }

  public function updatestatus(Request $request){

    $sprint_id=$request->get('sprint_id');
      $statusId=$request->get('statusId');
      $task=MerchantIds::join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')->
      join('sprint__sprints','sprint__sprints.id','=','sprint__tasks.sprint_id')
      ->where('sprint__sprints.id','=',$sprint_id)->
      where('sprint__tasks.type','=','dropoff')->
          whereNull('sprint__tasks.deleted_at')->
          whereNull('sprint__sprints.deleted_at')
          ->orderby('sprint__sprints.id','DESC')->first(['merchantids.tracking_id','merchantids.task_id','creator_id','sprint__tasks.sprint_id']);

      $statistics_id = FinanceVendorCity::pluck('id')->toArray();

      $gettingVendorId = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $statistics_id)
          ->pluck('vendors_id')
          ->toArray();

      if (!in_array($task->creator_id, $gettingVendorId)) {
          return redirect()->back()->with('error', 'You don`t have permission to update the status of this order');
      }

      //entry into route_history
      $status = '';

      if(in_array($statusId,$this->status_codes['completed']))
      {
          $status = 2;

      }elseif (in_array($statusId,$this->status_codes['return'])){
          $status = 4;
      }
      elseif (in_array($statusId,$this->status_codes['pickup'])){
          $status = 3;
      }



      $route =JoeyRouteLocations::join('joey_routes','joey_routes.id','=','joey_route_locations.route_id')
          ->where('joey_route_locations.task_id','=',$task->task_id)
          ->first(['joey_route_locations.id','joey_route_locations.route_id','joey_routes.joey_id','joey_route_locations.ordinal','joey_route_locations.task_id']);


      if(!empty($status)) {
          if(!empty($route)){
              $routehistory=new RouteHistory();
              $routehistory->route_id=$route->route_id;
              $routehistory->joey_id=$route->joey_id;
              $routehistory->status=$status;
              $routehistory->route_location_id=$route->id;
              $routehistory->task_id=$route->task_id;
              $routehistory->ordinal=$route->ordinal;
              $routehistory->type='Manual';
              $routehistory->updated_by=Auth::guard('web')->user()->id;
              $routehistory->save();

              if (isset($route->joey_id)) {
                  $deviceIds = UserDevice::where('user_id', $route->joey_id)->pluck('device_token');
                  $subject = 'R-' . $route->route_id . '-' . $route->ordinal;
                  $message = 'Your order status has been changed to ' . $this->statusmap($request->get('statusId'));
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
      }

      if(!empty($task))
      {
          $requestData['order_id'] = $task->sprint_id;
          $ctc_vendor_id= CtcVendor::where('vendor_id','=',$task->creator_id)->first();
      $taskhistory=TaskHistory::where('sprint_id','=',$requestData['order_id'])->where('status_id','=',125)->first();
          if($taskhistory) {
              if ($taskhistory->status_id == $statusId) {
                  return back()->with('success', 'Status Updated Successfully!');
              }
          }
          if($statusId==124 && !empty($ctc_vendor_id))
          {
              $taskhistory=TaskHistory::where('sprint_id','=',$requestData['order_id'])->where('status_id','=',125)->first();
              if($taskhistory==null)
              {
                  $pickupstoretime_date=new \DateTime();
                  $pickupstoretime_date->modify('-2 minutes');

                  $taskhistory=new TaskHistory();
                  $taskhistory->sprint_id=$requestData['order_id'];
                  $taskhistory->sprint__tasks_id=$task->task_id;
                  $taskhistory->status_id=125;
                  $taskhistory->date = $pickupstoretime_date->format('Y-m-d H:i:s');
                  $taskhistory->created_at = $pickupstoretime_date->format('Y-m-d H:i:s');
                  $taskhistory->save();
              }

          }

          $delivery_status = [17,113,114,116,117,118,132,138,139,144,104,105,106,107,108,109,110,111,112,131,135,136];
          //[17,118,117,107,108,111,113,114,116];
          if (in_array($statusId, $delivery_status))
          {

              $taskhistory=TaskHistory::where('sprint_id','=',$requestData['order_id'])->where('status_id','=',121)->first();
              if($taskhistory==null)
              {
                  $pickuptime_date=new \DateTime();
                  $pickuptime_date->modify('-2 minutes');

                  $taskhistory=new TaskHistory();
                  $taskhistory->sprint_id=$requestData['order_id'];
                  $taskhistory->sprint__tasks_id=$task->task_id;
                  $taskhistory->status_id=121;
                  $taskhistory->date=$pickuptime_date->format('Y-m-d H:i:s');
                  $taskhistory->created_at=$pickuptime_date->format('Y-m-d H:i:s');
                  $taskhistory->save();

                  if(!empty($route)){

                      $routehistory=new RouteHistory();
                      $routehistory->route_id=$route->route_id;
                      $routehistory->joey_id=$route->joey_id;
                      $routehistory->status=3;
                      $routehistory->route_location_id=$route->id;
                      $routehistory->task_id=$route->task_id;
                      $routehistory->ordinal=$route->ordinal;
                      $routehistory->created_at=$pickuptime_date->format('Y-m-d H:i:s');
                      $routehistory->updated_at=$pickuptime_date->format('Y-m-d H:i:s');
                      $routehistory->type='Manual';
                      $routehistory->updated_by=Auth::guard('web')->user()->id;

                      $routehistory->save();

                  }

                  $this->updateAmazonEntry(121,$requestData['order_id']);
                  $this->updateBorderLessDashboard(121,$requestData['order_id']);
                  $this->updateCTCEntry(121,$requestData['order_id']);
                  $this->updateClaims(121,$requestData['order_id']);
              }

          }
          Sprint::where('id','=',$requestData['order_id'])->update(['status_id'=>$statusId]);
          Task::where('id','=',$task->task_id)->update(['status_id'=>$statusId]);

          $taskhistory=new TaskHistory();
          $taskhistory->sprint_id=$requestData['order_id'];
          $taskhistory->sprint__tasks_id=$task->task_id;
          $taskhistory->status_id=$statusId;
          $taskhistory->date=date('Y-m-d H:i:s');
          $taskhistory->created_at=date('Y-m-d H:i:s');
          $taskhistory->save();
             // calling amazon update entry function 
             $this->updateAmazonEntry($statusId,$requestData['order_id']);
          $this->updateBorderLessDashboard($statusId,$requestData['order_id']);
          $this->updateCTCEntry($statusId,$requestData['order_id']);
          $this->updateClaims($statusId,$requestData['order_id']);

          $createData = [
              'tracking_id' =>$task->tracking_id,
              'status_id' => $request->get('statusId'),
              'user_id' => auth()->user()->id,
              'domain' => 'dashboard'
          ];
          TrackingImageHistory::create($createData);


      }
        return back()->with('success','Status Updated Successfully!');
  }


  public function updateAmazonEntry($status_id,$order_id,$imageUrl=null)
  {
              if($status_id==133)
              {
                    // Get amazon enteries data from tracking id and check if the data exist in database and if exist update the sort date of the tracking id and status of that tracking id.  
                    $amazon_enteries =AmazonEnteries::where('sprint_id','=',$order_id)->whereNull('deleted_at')->first();
                    if($amazon_enteries!=null)
                    {
                        
                        $amazon_enteries->sorted_at=date('Y-m-d H:i:s');
                        $amazon_enteries->task_status_id=133;
                        $amazon_enteries->order_image=$imageUrl;
                        $amazon_enteries->save();

                    }
              }
              elseif($status_id==121)
              {
                $amazon_enteries =AmazonEnteries::where('sprint_id','=',$order_id)->whereNull('deleted_at')->first();
                if($amazon_enteries!=null)
                {
                    $amazon_enteries->picked_up_at=date('Y-m-d H:i:s');
                    $amazon_enteries->task_status_id=121;
                    $amazon_enteries->order_image=$imageUrl;
                    $amazon_enteries->save();
    
                }
              }
              elseif(in_array($status_id,[17,113,114,116,117,118,132,138,139,144]))
              {
                $amazon_enteries =AmazonEnteries::where('sprint_id','=',$order_id)->whereNull('deleted_at')->first();
                if($amazon_enteries!=null)
                {
                    $amazon_enteries->delivered_at=date('Y-m-d H:i:s');
                    $amazon_enteries->task_status_id=$status_id;
                    $amazon_enteries->order_image=$imageUrl;
                    $amazon_enteries->save();
    
                }
              }
              elseif(in_array($status_id,[104,105,106,107,108,109,110,111,112,131,135,136,101,102,103,140]))
              {
                $amazon_enteries =AmazonEnteries::where('sprint_id','=',$order_id)->whereNull('deleted_at')->first();
                if($amazon_enteries!=null)
                {
                    $amazon_enteries->returned_at=date('Y-m-d H:i:s');
                    $amazon_enteries->task_status_id=$status_id;
                    $amazon_enteries->order_image=$imageUrl;
                    $amazon_enteries->save();
    
                }
              }
      
  }

    public function updateBorderLessDashboard($status_id,$order_id,$imageUrl=null)
    {
        if ($status_id == 133) {
            // Get amazon enteries data from tracking id and check if the data exist in database and if exist update the sort date of the tracking id and status of that tracking id.
            $borderless_dashboard = BoradlessDashboard::where('sprint_id', '=', $order_id)->whereNull('deleted_at')->first();
            if ($borderless_dashboard != null) {

                $borderless_dashboard->sorted_at = date('Y-m-d H:i:s');
                $borderless_dashboard->task_status_id = 133;
                $borderless_dashboard->order_image = $imageUrl;
                $borderless_dashboard->save();

            }
        } elseif ($status_id == 121) {
            $borderless_dashboard = BoradlessDashboard::where('sprint_id', '=', $order_id)->whereNull('deleted_at')->first();
            if ($borderless_dashboard != null) {
                $borderless_dashboard->picked_up_at = date('Y-m-d H:i:s');
                $borderless_dashboard->task_status_id = 121;
                $borderless_dashboard->order_image = $imageUrl;
                $borderless_dashboard->save();

            }
        } elseif (in_array($status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144])) {
			
            $borderless_dashboard = BoradlessDashboard::where('sprint_id', '=', $order_id)->whereNull('deleted_at')->first();
            if ($borderless_dashboard != null) {
                $borderless_dashboard->delivered_at = date('Y-m-d H:i:s');
                $borderless_dashboard->task_status_id = $status_id;
                $borderless_dashboard->order_image = $imageUrl;
                $borderless_dashboard->save();

            }
        } elseif (in_array($status_id, [104, 105, 106, 107, 108, 109, 110, 111, 112, 131, 135, 136, 101, 102, 103, 140])) {
			
            $borderless_dashboard = BoradlessDashboard::where('sprint_id', '=', $order_id)->whereNull('deleted_at')->first();
            if ($borderless_dashboard != null) {
                $borderless_dashboard->returned_at = date('Y-m-d H:i:s');
                $borderless_dashboard->task_status_id = $status_id;
                $borderless_dashboard->order_image = $imageUrl;
                $borderless_dashboard->save();

            }
        }
    }
    public function get_multipletrackingid(Request $request)
    {


        $tracking_ids=trim($request->input('tracking_id'));
        $merchant_order_no=trim($request->input('merchant_order_no'));
        $phone_no=trim($request->input('phone_no'));
        $orders=[];


        if(!empty($tracking_ids) || !empty($merchant_order_no) || !empty($phone_no) )
        {
            $user= MerchantIds::join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
                ->join('sprint__sprints','sprint__tasks.sprint_id','=','sprint__sprints.id')
                ->join('locations','sprint__tasks.location_id','=','locations.id')
                ->join('sprint__contacts','contact_id','=','sprint__contacts.id')
                ->where('sprint__tasks.type','=','dropoff')
                ->whereNull('sprint__sprints.deleted_at')
                ->whereNotNull('merchantids.tracking_id');



            if(!empty($tracking_ids))
            {

                if (strpos($tracking_ids,',') !== false) {

                    $id=explode(",",$tracking_ids);
                }
                else
                {
                    $id=explode("\n",$tracking_ids);

                }

                $i=0;
                $ids=[];
                foreach($id as $trackingid)
                {

                    if(!empty(trim($trackingid)))
                    {

                        $pattern = "/^[a-zA-Z0-9@#$&*_-]*/i";
                        preg_match($pattern,trim($trackingid),$matche);
                        $ids[$i]= $matche[0];
                        $i++;
                    }

                }
                if(!empty($ids))
                {

                    $user=$user->whereIn('merchantids.tracking_id',$ids);

                }
            }


            if(!empty($merchant_order_no))
            {
                if(!empty($merchant_order_no))
                {
                    if (strpos($merchant_order_no,',') !== false) {

                        $merchant_order_no=explode(",",$merchant_order_no);
                    }
                    else
                    {
                        $merchant_order_no=explode("\n",$merchant_order_no);

                    }
                    $i=0;
                    $ids=[];
                    foreach($merchant_order_no as $id)
                    {
                        if(!empty(trim($id)))
                        {
                            $merchant_orders_no[$i]=trim($id);
                            $i++;
                        }

                    }

                    if(!empty($merchant_orders_no))
                    {
                        $user=$user->whereIn('merchantids.merchant_order_num',$merchant_orders_no);
                    }
                }

            }
            if(!empty($phone_no))
            {
                if(!empty($phone_no))
                {
                    if (strpos($phone_no,',') !== false) {

                        $phone_no=explode(",",$phone_no);
                    }
                    else
                    {
                        $phone_no=explode("\n",$phone_no);

                    }
                    $i=0;
                    $customers_phone_no=[];
                    foreach($phone_no as $id)
                    {
                        if(!empty(trim($id)))
                        {

                            $customers_phone_no[$i]=(str_contains(trim($id), '+') )? trim($id) : "+".trim($id);

                            $i++;
                        }

                    }

                    if(!empty($customers_phone_no))
                    {
                        $user=$user->whereIn('sprint__contacts.phone',$customers_phone_no);
                    }
                }

            }

            $orders=$user->orderBy('merchantids.id','DESC')
                ->get(array("sprint__sprints.id",'sprint__sprints.creator_id','sprint__sprints.status_id',\DB::raw("CONVERT_TZ(sprint__sprints.created_at,'UTC','America/Toronto') as created_at"),'merchantids.tracking_id','merchantids.merchant_order_num','sprint__contacts.phone','locations.address','merchantids.address_line2'));

            $i=0;


            foreach($orders as $order)
            {

                if($orders[$i]->status_id==17 && $orders[$i]->creator_id!=477260 && $orders[$i]->creator_id!=477282 )
                {

                    $status_history=TaskHistory::where('sprint_id','=',$orders[$i]->id)->
                    //  where('status_id','!=',17)->
                    whereIn('status_id',[114,116,117,118,132,138,139,144,113,147,148,149,150])->
                    orderby('id','DESC')->
                    first();

                    if(!empty($status_history))
                    {
                        $orders[$i]->status_id=$status_history->status_id;
                    }


                }
                $i++;
            }


            if(empty($orders))
            {
                $orders=[];
            }

        }

        return backend_view('multiplesearchorder',['data'=>$orders]);
    }

    public function get_multiOrderUpdates(Request $request){
      return backend_view('multipleupdateorder',['data'=>[]]);
  }

  public function post_multiOrderUpdates(Request $request){

      $k=0;
      $trackingIdValidator = [];
      $user=[];
      $id = $request->input('tracking_id');
      if (strpos($id,',') !== false) {
          $id=explode(",",$id);

      }
      else
      {
          $id=explode("\n",$id);

      }

      $requestData['status_id']=$request->input('status_id');

      foreach($id as $trackingid){
          $pattern = "/^[a-zA-Z0-9@#$&*_-]*/i";
          preg_match($pattern,trim($trackingid),$match);
          $trackingid=$match[0];
          $task=MerchantIds::join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')->
          join('sprint__sprints','sprint__sprints.id','=','sprint__tasks.sprint_id')
              ->where('merchantids.tracking_id','=',$trackingid)->
              //->whereNull('deleted_at')->
              whereNull('sprint__tasks.deleted_at')->
              whereNull('sprint__sprints.deleted_at')
              ->orderby('sprint__sprints.id','DESC')->first(['merchantids.task_id','creator_id','sprint__tasks.sprint_id']);

          $statistics_id = FinanceVendorCity::pluck('id')->toArray();


          $gettingVendorId = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $statistics_id)
              ->pluck('vendors_id')
              ->toArray();
          if($task !=null){
              if (!in_array($task->creator_id, $gettingVendorId)) {
                  //dd($trackingIdValidator,$trackingid);
                  array_push($trackingIdValidator,$trackingid);
                  continue;
              }

          }

          if(empty($task)){
              continue;
          }

          //route history entry work

          $status = '';

          if(in_array($requestData['status_id'],$this->status_codes['completed']))
          {
              $status = 2;

          }elseif (in_array($requestData['status_id'],$this->status_codes['return'])){
              $status = 4;
          }
          elseif (in_array($requestData['status_id'],$this->status_codes['pickup'])){
              $status = 3;
          }


          $route =JoeyRouteLocations::join('joey_routes','joey_routes.id','=','joey_route_locations.route_id')
              ->where('joey_route_locations.task_id','=',$task->task_id)
              ->first(['joey_route_locations.id','joey_route_locations.route_id','joey_routes.joey_id','joey_route_locations.ordinal','joey_route_locations.task_id']);

          if(!empty($route)){

              $routehistory=new RouteHistory();
              $routehistory->route_id=$route->route_id;
              $routehistory->joey_id=$route->joey_id;
              $routehistory->status=$status;
              $routehistory->route_location_id=$route->id;
              $routehistory->task_id=$route->task_id;
              $routehistory->ordinal=$route->ordinal;
              $routehistory->type='Manual';
              $routehistory->updated_by=Auth::guard('web')->user()->id;

              $routehistory->save();

              if (isset($route->joey_id)) {
                  $deviceIds = UserDevice::where('user_id', $route->joey_id)->pluck('device_token');
                  $subject = 'R-' . $route->route_id . '-' . $route->ordinal;
                  $message = 'Your order status has been changed to ' . $this->statusmap($request->input('status_id'));
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



          if(!empty($task->task_id)){
              $requestData['order_id'] = $task->sprint_id;
              $k=1;
               $taskhistory=TaskHistory::where('sprint_id','=',$requestData['order_id'])->where('status_id','=',125)->first();
              if($taskhistory) {
                  if ($taskhistory->status_id == $request->input('status_id')) {

                      continue;
                  }
              }
              $ctc_vendor_id= CtcVendor::where('vendor_id','=',$task->creator_id)->first();
              if($requestData['status_id']==124 && !empty($ctc_vendor_id))
              {
                  $taskhistory=TaskHistory::where('sprint_id','=',$requestData['order_id'])->where('status_id','=',125)->first();
                  if($taskhistory==null)
                  {

                      $pickupstoretime_date=new \DateTime();
                      $pickupstoretime_date->modify('-2 minutes');

                      $taskhistory=new TaskHistory();
                      $taskhistory->sprint_id=$requestData['order_id'];
                      $taskhistory->sprint__tasks_id=$task->task_id;
                      $taskhistory->status_id=125;
                      $taskhistory->date = $pickupstoretime_date->format('Y-m-d H:i:s');
                      $taskhistory->created_at = $pickupstoretime_date->format('Y-m-d H:i:s');
                      $taskhistory->save();
                  }

              }

              $delivery_status = [17,113,114,116,117,118,132,138,139,144,104,105,106,107,108,109,110,111,112,131,135,136];

              if (in_array($requestData['status_id'], $delivery_status)) {

                  $taskhistory=TaskHistory::where('sprint_id','=',$requestData['order_id'])->where('status_id','=',121)->first();
                  if($taskhistory==null)
                  {

                      $pickuptime_date=new \DateTime();
                      $pickuptime_date->modify('-2 minutes');

                      $taskhistory=new TaskHistory();
                      $taskhistory->sprint_id=$requestData['order_id'];
                      $taskhistory->sprint__tasks_id=$task->task_id;
                      $taskhistory->status_id=121;
                      $taskhistory->date=$pickuptime_date->format('Y-m-d H:i:s');
                      $taskhistory->created_at=$pickuptime_date->format('Y-m-d H:i:s');
                      $taskhistory->save();

                      if(!empty($route)){

                          $routehistory=new RouteHistory();
                          $routehistory->route_id=$route->route_id;
                          $routehistory->joey_id=$route->joey_id;
                          $routehistory->status=3;
                          $routehistory->route_location_id=$route->id;
                          $routehistory->task_id=$route->task_id;
                          $routehistory->ordinal=$route->ordinal;
                          $routehistory->created_at=$pickuptime_date->format('Y-m-d H:i:s');
                          $routehistory->updated_at=$pickuptime_date->format('Y-m-d H:i:s');
                          $routehistory->updated_by=Auth::guard('web')->user()->id;
                          $routehistory->type='Manual';
                          $routehistory->save();

                      }

                      $this->updateAmazonEntry(121,$requestData['order_id']);
                      $this->updateBorderLessDashboard(121,$requestData['order_id']);
                      $this->updateCTCEntry(121,$requestData['order_id']);
                      $this->updateClaims(121,$requestData['order_id']);

                  }

              }

              Sprint::where('id','=',$requestData['order_id'])->update(['status_id'=>$requestData['status_id']]);


              Task::where('sprint_id','=',$requestData['order_id'])->update(['status_id'=>$requestData['status_id']]);

              $taskhistory=new TaskHistory();
              $taskhistory->sprint_id=$requestData['order_id'];
              $taskhistory->sprint__tasks_id=$task->task_id;
              $taskhistory->status_id=$requestData['status_id'];
              $taskhistory->date=date('Y-m-d H:i:s');
              $taskhistory->created_at=date('Y-m-d H:i:s');
              $taskhistory->save();
                // calling amazon update entry function 
              $this->updateAmazonEntry($requestData['status_id'],$requestData['order_id']);
              $this->updateBorderLessDashboard($requestData['status_id'],$requestData['order_id']);
              $this->updateCTCEntry($requestData['status_id'],$requestData['order_id']);
              $this->updateClaims($requestData['status_id'],$requestData['order_id']);


              $createData = [
                  'tracking_id' => $trackingid,
                  'status_id' => $requestData['status_id'],
                  'user_id' => auth()->user()->id,
                  'domain' => 'dashboard'
              ];
              TrackingImageHistory::create($createData);


          }
      }
      if($k==0)
      {
          return back()->with('error','Invalid Tracking Id!');
      }

      if(count($trackingIdValidator) > 0)
      {
          $error_message = implode(", " , $trackingIdValidator);
          $returnMessage =  back()->with('error', "Some of the tracking ids status can't be updated due to permissoins issue kindly check this tracking ids '".$error_message." '  belongs to your hub ");
      }
      else
      {
          $returnMessage = back()->with('success', 'Status Updated Successfully!');
      }
      return $returnMessage;
  }

	public function sprintImageUpload(UploadImageRequest $request)
    {
        $postData = $request->all();
        
		$image_base64 =  base64_encode(file_get_contents($_FILES['sprint_image']['tmp_name']));

        $task=MerchantIds::join('sprint__tasks','sprint__tasks.id','=','merchantids.task_id')
            ->join('sprint__sprints','sprint__sprints.id','=','sprint__tasks.sprint_id')
            ->where('sprint__sprints.id','=',$postData['sprint_id'])
			->where('sprint__tasks.type','=','dropoff')
            ->first(['sprint__tasks.id','sprint__tasks.sprint_id','sprint__tasks.ordinal','sprint__sprints.creator_id','merchantids.tracking_id']);

        $route_data=JoeyRoutes::join('joey_route_locations','joey_route_locations.route_id','=','joey_routes.id')
            ->where('joey_route_locations.task_id','=',$task->id)
            ->whereNull('joey_route_locations.deleted_at')
            ->first(['joey_route_locations.id','joey_routes.joey_id','joey_route_locations.route_id','joey_route_locations.ordinal']);

        if(empty($route_data)) {
            session()->flash('alert-warning', 'Joey not assigned yet. Image cannot be uploaded.!');
            return Redirect::to('search/orders/trackingid/'.$task->sprint_id.'/details');
        }
    	$taskhistory=TaskHistory::where('sprint_id','=',$postData['sprint_id'])->where('status_id','=',125)->first();
        if($taskhistory) {
            if ($taskhistory->status_id == $postData['status_id']) {

                session()->flash('alert-success', 'Image Uploaded');
                return Redirect::to('search/orders/trackingid/' . $task->sprint_id . '/details');
            }
        }
        $data = ['image' =>  $image_base64];//$base64Data];
        $response =  $this->sendData('POST', '/',  $data );
        // checking responce
        if(!isset($response->url))
        {
            session()->flash('alert-warning', 'File cannot be uploaded due to server error!');
            return Redirect::to('search/orders/trackingid/'.$task->sprint_id.'/details');
        }

        $attachment_path =   $response ->url;


        




        $status = '';

        if(in_array($postData['status_id'],$this->status_codes['completed']))
        {
            $status = 2;

        }elseif (in_array($postData['status_id'],$this->status_codes['return'])){
            $status = 4;
        }
        elseif (in_array($postData['status_id'],$this->status_codes['pickup'])){
            $status = 3;
        }


        $route_data=JoeyRoutes::join('joey_route_locations','joey_route_locations.route_id','=','joey_routes.id')
            ->where('joey_route_locations.task_id','=',$task->id)
            ->whereNull('joey_route_locations.deleted_at')
            ->first(['joey_route_locations.id','joey_routes.joey_id','joey_route_locations.route_id','joey_route_locations.ordinal','joey_route_locations.task_id']);
        
        if(!empty($route_data))
        {
            $routeHistoryRecord = [
                'route_id' =>$route_data->route_id,
                'route_location_id' => $route_data->id,
                'ordinal' => $route_data->ordinal,
                'joey_id'=>  $route_data->joey_id,
                'task_id'=>$task->id,
                'status'=> $status,
                'type'=>'Manual',
                'updated_by'=>auth()->user()->id,
            ];
            RouteHistory::create($routeHistoryRecord);
        }
        $statusDescription= StatusMap::getDescription($postData['status_id']);
        $updateData = [
            'ordinal' => $task->ordinal,
            'task_id' => $task->id,
            'joey_id' =>$route_data->joey_id,
            'name' => $statusDescription,
            'title' => $statusDescription,
            'confirmed' => 1,
            'input_type' => 'image/jpeg',
            'attachment_path' => $attachment_path
        ];
        SprintConfirmation::create($updateData);


        if(!empty($task->id)) {
            $order_id = $task->sprint_id;
            $ctc_vendor_id = CtcVendor::where('vendor_id', '=', $task->creator_id)->first();
            if ($postData['status_id']== 124 && !empty($ctc_vendor_id)) {
                $taskhistory = TaskHistory::where('sprint_id', '=', $order_id)->where('status_id', '=', 125)->first();
                if ($taskhistory == null) {

                    $pickupstoretime_date=new \DateTime();
                    $pickupstoretime_date->modify('-2 minutes');

                    $taskhistory = new TaskHistory();
                    $taskhistory->sprint_id = $order_id;
                    $taskhistory->sprint__tasks_id = $task->id;

                    $taskhistory->status_id = 125;
                    $taskhistory->date = $pickupstoretime_date->format('Y-m-d H:i:s');
                    $taskhistory->created_at = $pickupstoretime_date->format('Y-m-d H:i:s');
                    $taskhistory->save();
                }

            }

            $delivery_status = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 104, 105, 106, 107, 108, 109, 110, 111, 112, 131, 135, 136];

            if (in_array($postData['status_id'], $delivery_status)) {

                $taskhistory = TaskHistory::where('sprint_id', '=', $order_id)->where('status_id', '=', 121)->first();
                if ($taskhistory == null) {

                    $pickuptime_date=new \DateTime();
                    $pickuptime_date->modify('-2 minutes');

                    $taskhistory = new TaskHistory();
                    $taskhistory->sprint_id = $order_id;
                    $taskhistory->sprint__tasks_id = $task->id;
                    $taskhistory->status_id = 121;
                    $taskhistory->date=$pickuptime_date->format('Y-m-d H:i:s');
                    $taskhistory->created_at=$pickuptime_date->format('Y-m-d H:i:s');
                    $taskhistory->save();

                    if(!empty($route_data)){

                        $routehistory=new RouteHistory();
                        $routehistory->route_id=$route_data->route_id;
                        $routehistory->joey_id=$route_data->joey_id;
                        $routehistory->status=3;
                        $routehistory->route_location_id=$route_data->id;
                        $routehistory->task_id=$route_data->task_id;
                        $routehistory->ordinal=$route_data->ordinal;
                        $routehistory->created_at=$pickuptime_date->format('Y-m-d H:i:s');
                        $routehistory->updated_at=$pickuptime_date->format('Y-m-d H:i:s');
                        $routehistory->type='Manual';
                        $routehistory->updated_by=Auth::guard('web')->user()->id;

                        $routehistory->save();

                    }
                    $this->updateAmazonEntry(121,$order_id);
                    $this->updateBorderLessDashboard(121,$order_id);
                    $this->updateCTCEntry(121,$order_id);
                    $this->updateClaims(121,$order_id);


                }

            }
        }

        Task::where('id','=',$task->id)->update(['status_id'=>$postData['status_id']]);
        Sprint::where('id','=',$task->sprint_id)->whereNull('deleted_at')->update(['status_id'=>$postData['status_id']]);

        $this->updateAmazonEntry($postData['status_id'],$task->sprint_id,$attachment_path);
        $this->updateBorderLessDashboard($postData['status_id'],$task->sprint_id,$attachment_path);
        $this->updateCTCEntry($postData['status_id'],$task->sprint_id,$attachment_path);
        $this->updateClaims($postData['status_id'],$task->sprint_id,$attachment_path);


        $taskHistoryRecord = [
            'sprint__tasks_id' =>$task->id,
            'sprint_id' => $task->sprint_id,
            'status_id' => $postData['status_id'],
            'date' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),

        ];
        SprintTaskHistory::create( $taskHistoryRecord );

        $createData = [
            'tracking_id' => $task->tracking_id,
            'status_id' => $postData['status_id'],
            'user_id' => auth()->user()->id,
            'attachment_path' => $attachment_path,
            'reason_id' => $postData['reason_id'],
            'domain' => 'dashboard'
        ];
        TrackingImageHistory::create($createData);

        if (isset($route_data->joey_id)) {
            $deviceIds = UserDevice::where('user_id', $route_data->joey_id)->pluck('device_token');
            $subject = 'R-' . $route_data->route_id . '-' . $route_data->ordinal;
            $message = 'Your order status has been changed to ' . $this->statusmap($postData['status_id']);
            Fcm::sendPush($subject, $message, 'ecommerce', null, $deviceIds);
            $payload = ['notification' => ['title' => $subject, 'body' => $message, 'click_action' => 'ecommerce'],
                'data' => ['data_title' => $subject, 'data_body' => $message, 'data_click_action' => 'ecommerce']];
            $createNotification = [
                'user_id' => $route_data->joey_id,
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

        session()->flash('alert-success', 'Image Uploaded');
        return Redirect::to('search/orders/trackingid/'.$task->sprint_id.'/details');

    }

    public function sendData($method, $uri, $data=[] ) {
        $host ='assets.joeyco.com';

        $json_data = json_encode($data);
        $headers = [
            'Accept-Encoding: utf-8',
            'Accept: application/json; charset=UTF-8',
            'Content-Type: application/json; charset=UTF-8',
            'User-Agent: JoeyCo',
            'Host: ' . $host,
        ];

        if (!empty($json_data) ) {

            $headers[] = 'Content-Length: ' . strlen($json_data);

        }


        $url = 'https://' . $host . $uri;


        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (strlen($json_data) > 2) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        }

        if (env('APP_ENV') === 'local') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        set_time_limit(0);

        $this->originalResponse = curl_exec($ch);

        $error = curl_error($ch);


        curl_close($ch);

        if (empty($error)) {


            $this->response = explode("\n", $this->originalResponse);

            $code = explode(' ', $this->response[0]);
            $code = $code[1];

            $this->response = $this->response[count($this->response) - 1];
            $this->response = json_decode($this->response);

            if (json_last_error() != JSON_ERROR_NONE) {

                $this->response = (object) [
                    'copyright' => 'Copyright © ' . date('Y') . ' JoeyCo Inc. All rights reserved.',
                    'http' => (object) [
                        'code' => 500,
                        'message' => json_last_error_msg(),
                    ],
                    'response' => new \stdClass()
                ];
            }
        }
        else{
                dd(['error'=> $error,'responce'=>$this->originalResponse]);
        }

        return $this->response;
    }

    public function  updateCTCEntry($status_id,$order_id,$imageUrl=null)
    {
        if($status_id==133)
        {
            // Get amazon enteries data from tracking id and check if the data exist in database and if exist update the sort date of the tracking id and status of that tracking id.
            $ctc_entries =CTCEntry::where('sprint_id','=',$order_id)->whereNull('deleted_at')->first();
            if($ctc_entries!=null)
            {

                $ctc_entries->sorted_at=date('Y-m-d H:i:s');
                $ctc_entries->task_status_id=133;
                $ctc_entries->order_image=$imageUrl;
                $ctc_entries->save();

            }
        }
        elseif($status_id==121)
        {
            $ctc_entries =CTCEntry::where('sprint_id','=',$order_id)->whereNull('deleted_at')->first();
            if($ctc_entries!=null)
            {
                $ctc_entries->picked_up_at=date('Y-m-d H:i:s');
                $ctc_entries->task_status_id=121;
                $ctc_entries->order_image=$imageUrl;
                $ctc_entries->save();

            }
        }
        elseif(in_array($status_id,[17,113,114,116,117,118,132,138,139,144]))
        {
            $ctc_entries =CTCEntry::where('sprint_id','=',$order_id)->whereNull('deleted_at')->first();
            if($ctc_entries!=null)
            {
                $ctc_entries->delivered_at=date('Y-m-d H:i:s');
                $ctc_entries->task_status_id=$status_id;
                $ctc_entries->order_image=$imageUrl;
                $ctc_entries->save();

            }
        }
        elseif(in_array($status_id,[104,105,106,107,108,109,110,111,112,131,135,136,101,102,103,140,143]))
        {
            $ctc_entries =CTCEntry::where('sprint_id','=',$order_id)->whereNull('deleted_at')->first();
            if($ctc_entries!=null)
            {
                $ctc_entries->returned_at=date('Y-m-d H:i:s');
                $ctc_entries->task_status_id=$status_id;
                $ctc_entries->order_image=$imageUrl;
                $ctc_entries->save();

            }
        }

    }

    public function SearchTracking(Request $request)
    {
        // date_default_timezone_set("America/Toronto");

        $tracking_ids=trim($request->input('tracking_id'));
        $return=[];
        $return['is_pickedup']=0;
        $return['is_delivered_return']=0;
        $return['is_delivered']=0;
        $return['is_returned']=0;

        if(!empty($tracking_ids))
        {
            $return_status = [101,102,103,104,105,106,107,108,109,110,111,112,131,135,136,137,140];
            $delivered_status = [17,113,114,116,117,118,132,138,139,144];
           $delivered_and_return_status = [17,113,114,116,117,118,132,138,139,144,101,103,104,105,106,107,108,109,110,111,112,131,135,136,140];
           $merchantid=MerchantIds::where('tracking_id',$tracking_ids)->first();
            if(!empty($merchantid)){
                $return=$this->SearchTrackingDetails($merchantid->task->sprint_id,$merchantid,$request);
            
                $return['is_pickedup']=0;
                $return['is_delivered_return']=0;
                $return['is_delivered']=0;
                $return['is_returned']=0;

                $task_histories=$merchantid->Task->sprintTaskHistoryDetail;
                foreach ($task_histories as $task_history) {
                    if($task_history->status_id==121){ //picked up
                        $return['is_pickedup']=1;
                    }
                    if(in_array($task_history->status_id,$delivered_and_return_status)){ //delivered or return
                        $return['is_delivered_return']=1;
                        $return['msg_deliver_return']=$this->statusmap($task_history->status_id)." at ".ConvertTimeZone($task_history->created_at,$CurrentTimeZone = 'UTC' ,$ConvertTimeZone = 'America/Toronto',$format = 'd M Y h:i a')??"";
                        // date('d M Y h:i a', strtotime($task_history->created_at))??"";
                        if(in_array($task_history->status_id,$delivered_status)){
                            $return['is_delivered']=1;
                        }
                        elseif(in_array($task_history->status_id,$return_status)){
                            $return['is_returned']=1;
                        }
                    }
                }
            }
        }
        return backend_view('search-trackin-details', $return);
    }

    public function SearchTrackingDetails($sprintId,$merchantid,$request)
    {

        $show_message = $request->message;
        if(!is_null($show_message))
        {
            $current_url  = $request->url();
            $query_string = http_build_query( $request->except(['message'] ) );
            return redirect($current_url.'?'.$query_string)
                ->with('alert-success', $show_message);
        }
  
        $result= Sprint::join('sprint__tasks','sprint_id','=','sprint__sprints.id')
            ->leftJoin('merchantids','merchantids.task_id','=','sprint__tasks.id')
            ->leftJoin('joey_route_locations','joey_route_locations.task_id','=','sprint__tasks.id')
            ->leftJoin('joey_routes','joey_routes.id','=','joey_route_locations.route_id')
            ->leftJoin('joeys','joeys.id','=','joey_routes.joey_id')
            ->join('locations','sprint__tasks.location_id','=','locations.id')
            ->join('sprint__contacts','contact_id','=','sprint__contacts.id')
            ->leftJoin('vendors','creator_id','=','vendors.id')
            ->where('sprint__tasks.sprint_id','=',$sprintId)
            ->whereNull('joey_route_locations.deleted_at')
            ->orderBy('ordinal','DESC')->take(1)
            ->get(array('sprint__tasks.*','joey_routes.id as route_id','locations.address','locations.suite','locations.postal_code','sprint__contacts.name','sprint__contacts.phone','sprint__contacts.email',
                'joeys.first_name as joey_firstname','joeys.id as joey_id',
                'joeys.last_name as joey_lastname','vendors.id as merchant_id','vendors.first_name as merchant_firstname','vendors.last_name as merchant_lastname','merchantids.scheduled_duetime'
            ,'joeys.id as joey_id','merchantids.tracking_id','joeys.phone as joey_contact','joey_route_locations.ordinal as stop_number','merchantids.merchant_order_num','merchantids.address_line2','sprint__sprints.creator_id','sprint__sprints.is_hub'));
  
        $i=0;
  
        $data = [];
        $sprint_id = 0;
        foreach($result as $tasks){
            $sprint_id = $tasks->sprint_id;
            $status2 = array();
            $status = array();
            $status1 = array();
           
            $tasks->joey_address='';
            $tasks->expected_datetime='';
            $tasks->joey_lat=0;
            $tasks->joey_lng=0;
            $tasks->duration=0;

            // Joey address
            $joeyLocation=JoeyLocation::where('joey_id',$tasks->joey_id)->orderBy('id',"DESC")->first();
            if(!empty($joeyLocation)){
                $tasks->joey_lat=(float)(((int)(substr($joeyLocation->latitude,0,8)))/1000000);
                $tasks->joey_lng=(float)(((int)(substr($joeyLocation->longitude,0,9)))/1000000);
                // $tasks->joey_address=$this->getAddressByLatLng($tasks->joey_lat,$tasks->joey_lng);
                $getAddressByLatLng=$this->getAddressByLatLng($tasks->joey_lat,$tasks->joey_lng);
                if( $getAddressByLatLng['status']==200){
                    $tasks->joey_address=$getAddressByLatLng['address'];
                }else{
                    // $tasks->joey_address='Invalid joey location';
                    $tasks->joey_address=0;
                }
                // echo $tasks->joey_address;die;
            }
            // Joey address

            // Expected Arrival

            $expected_date='';
            $response['is_amazon']=0;
            $vendor_check=$merchantid->Task->sprint->creator_id;
            if($vendor_check==477260 || $vendor_check==477282  || $vendor_check==476592){

                $amazon_sprint_task_histories=$merchantid->Task->sprintTaskHistoryDetail;
                if(!empty($amazon_sprint_task_histories)){
                    foreach ($amazon_sprint_task_histories as $amazon_sprint_task_history) {
                        if($amazon_sprint_task_history->status_id==13){
                            $expected_date=date('Y-m-d H:i:s', strtotime(ConvertTimeZone($amazon_sprint_task_history->created_at->toDateTimeString(),"UTC",'America/Toronto'). ' +1 day'));
                        }
                    }
                    if($expected_date==''){
                        foreach ($amazon_sprint_task_histories as $amazon_sprint_task_history) {
                            if($amazon_sprint_task_history->status_id==61){
                                $expected_date=date('Y-m-d H:i:s', strtotime(ConvertTimeZone($amazon_sprint_task_history->created_at->toDateTimeString(),"UTC",'America/Toronto'). ' +1 day'));

                            }
                        }
                    }
                }


            }else{
                // echo 2;die;

                $other_sprint_task_histories=$merchantid->Task->sprintTaskHistoryDetail;
                if(!empty($other_sprint_task_histories)){
                    foreach ($other_sprint_task_histories as $other_sprint_task_history) {
                        if($other_sprint_task_history->status_id==125){
                            $expected_date=date('Y-m-d H:i:s', strtotime(ConvertTimeZone($other_sprint_task_history->created_at->toDateTimeString(),"UTC",'America/Toronto'). ' +1 day'));
                        }
                    }
                    if($expected_date==''){
                        foreach ($other_sprint_task_histories as $other_sprint_task_history) {
                            if($other_sprint_task_history->status_id==133){
                                $expected_date=date('Y-m-d H:i:s', strtotime(ConvertTimeZone($other_sprint_task_history->created_at->toDateTimeString(),"UTC",'America/Toronto'). ' +1 day'));

                            }
                        }
                    }

                }


            }

            if($expected_date!='' || $expected_date!=null){
                $tasks->expected_datetime=date("Y-m-d",strtotime($expected_date))." 21:00:00";
            }

            // Expected Arrival


                $from['name']=$tasks->joey_address;
                $from['lat']=$tasks->joey_lat;
                $from['lng']=$tasks->joey_lng;
                

                $to['name']=$merchantid->Task->task_Location->address;
                $to['lat']=(float)(((int)(substr($merchantid->Task->task_Location->latitude,0,8)))/1000000);
                $to['lng']=(float)(((int)(substr($merchantid->Task->task_Location->longitude,0,9)))/1000000);

                $tasks->cust_lat=$to['lat'];
                $tasks->cust_lng=$to['lng'];

                $duration=$this->gettimedifference($from,$to);
                if(!isset($duration['status'])){
                    $tasks->duration=$duration;
                }


            // Duration

            $data[$i] =  $tasks;
            $taskHistory= TaskHistory::where('sprint_id','=',$tasks->sprint_id)->WhereNotIn('status_id',[17,38,0])->orderBy('date')
                ->get(['status_id',\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto') as created_at")]);
  
            $returnTOHubDate = SprintReattempt::
            where('sprint_reattempts.sprint_id','=' ,$tasks->sprint_id)->orderBy('created_at')
                ->first();
  
            if(!empty($returnTOHubDate))
            {
                $taskHistoryre= TaskHistory::where('sprint_id','=', $returnTOHubDate->reattempt_of)->WhereNotIn('status_id',[17,38,0])->orderBy('date')
                    ->get(['status_id',\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto') as created_at")]);
  
                foreach ($taskHistoryre as $history){
  
                    $status[$history->status_id]['id'] = $history->status_id;
                    if($history->status_id==13)
                    {
                        $status[$history->status_id]['description'] ='At hub - processing';
                    }
                    else
                    {
                        $status[$history->status_id]['description'] =$this->statusmap($history->status_id);
                    }
                    $status[$history->status_id]['created_at'] = $history->created_at;
  
                }
  
            }
            if(!empty($returnTOHubDate))
            {
                $returnTO2 = SprintReattempt::
                where('sprint_reattempts.sprint_id','=' , $returnTOHubDate->reattempt_of)->orderBy('created_at')
                    ->first();
  
                if(!empty($returnTO2))
                {
                    $taskHistoryre= TaskHistory::where('sprint_id','=',$returnTO2->reattempt_of)->WhereNotIn('status_id',[17,38,0])->orderBy('date')
                        ->get(['status_id',\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto') as created_at")]);
  
                    foreach ($taskHistoryre as $history){
  
                        $status2[$history->status_id]['id'] = $history->status_id;
                        if($history->status_id==13)
                        {
                            $status2[$history->status_id]['description'] ='At hub - processing';
                        }
                        else
                        {
                            $status2[$history->status_id]['description'] = $this->statusmap($history->status_id);
                        }
                        $status2[$history->status_id]['created_at'] = $history->created_at;
  
                    }
  
                }
            }
  

            foreach ($taskHistory as $history){
  
                $status1[$history->status_id]['id'] = $history->status_id;
  
                if($history->status_id==13)
                {
                    $status1[$history->status_id]['description'] ='At hub - processing';
                }
                else
                {
                    $status1[$history->status_id]['description'] = $this->statusmap($history->status_id);
                }
                $status1[$history->status_id]['created_at'] = $history->created_at;
  
            }
  
            if($status!=null)
            {
                $sort_key = array_column($status, 'created_at');
                array_multisort($sort_key, SORT_ASC, $status);
            }
            if($status1!=null)
            {
                $sort_key = array_column($status1, 'created_at');
                array_multisort($sort_key, SORT_ASC, $status1);
            }
            if($status2!=null)
            {
                $sort_key = array_column($status2, 'created_at');
                array_multisort($sort_key, SORT_ASC, $status2);
            }
  
  
            $data[$i]['status']= $status;
            $data[$i]['status1']= $status1;
            $data[$i]['status2']=$status2;
            $i++;
        }

       
        $return=[
            'data'=>$data,
            'sprintId' => $sprintId,

        ];
        return $return;
    }
    public function gettimedifference($from=[],$to=[])
    {
        $ch = curl_init();

          $data=array(
            "visits"=>[
                "order_1"=>[
                   "location"=>[
                       "name"=>$to['name'],
                       "lat"=>$to['lat'],
                       "lng"=>$to['lng']
                   ]
                ]
            ],
            "fleet"=>[
                "vehicle_1"=>[
                   "start_location"=>[
                       "id" => "depot",
                       "name"=>$from['name'],
                       "lat"=>$from['lat'],
                       "lng"=>$from['lng']
                   ]
                ]
            ],
         );

          $data = json_encode($data);

        curl_setopt($ch, CURLOPT_URL,"https://api.routific.com/v1/vrp");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJfaWQiOiI1Njk5ZDJjODUzNWFkMTBkMWQ0YmFlMTgiLCJpYXQiOjE0NTgxNjgzNjR9.RXZHpu7tVE3dersb5TZrtJMM8u4BehM0PriS9Dj1YAc'
        ));
        // routific_api_key
        // Receive server response ...
        $server_output = curl_exec($ch);
        curl_close ($ch);
        $res =json_decode($server_output,true);
        if(isset($res['total_travel_time'])){
            // return $res['total_travel_time'];
            $return_data=$res['total_travel_time'];
        }
        else{
            $return_data['status']=400;
            $return_data['error']=$res['error'];
            $return_data['error_type']=$res['error_type'];
            // return $return_data;
        }
        return $return_data;

    }
    public function getAddressByLatLng($lat,$lng)
    {
        $latlng=$lat.','.$lng;
        $return=[];

        // google map geocode api url
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latlng&key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0";
        if (($resp_json = @file_get_contents($url)) === false) {
            $return['status']=400;
        }
        
        // decode the json
        else{
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
                $completeAddress['status']=200;
                unset($completeAddress['administrative_area_level_2']);

                $return['status']=200;
                $return['address']= $completeAddress['address'];
            }

        }
        return $return;


    }
    public function updateClaims($sprint_status_id,$sprint_id,$imageUrl=null)
    {
        $updateData = [
            'sprint_status_id'=>$sprint_status_id,
            ];
        if ($imageUrl != null)
        {
            $updateData['image'] = $imageUrl;
        }
        Claim::where('sprint_id',$sprint_id)->update($updateData);
    }
}
