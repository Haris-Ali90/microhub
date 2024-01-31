<?php

namespace App\Http\Controllers\Backend;

use App\City;
use App\Classes\CurlRequestSend;
use App\CtcVendor;
use App\CustomerSupportReturnNotes;
use App\Http\Requests\Backend\MultipleReattemptOrderRequest;
use App\Http\Requests\Backend\ReattemptOrderHistoryRequest;
use App\Http\Requests\Backend\ReattemptOrderRequest;
use App\Http\Requests\Backend\ReattemptScanOrderRequest;
use App\JoeyRouteLocations;
use App\LocationUnencrypted;
use App\ReturnAndReattemptProcessHistory;
use App\ReturnReattemptProcess;
use App\Sprint;
use App\SprintContact;
use App\Task;
use App\MerchantIds;
use App\TaskHistory;
use App\Vendor;
use App\BoradlessDashboard;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ReattemptOrdersController extends BackendController
{

    const status_ids = [
        "136" => "Client requested to cancel the order",
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
        "145" => "Returned To Merchant",
        "146" => "Delivery Missorted, Incorrect Address",
        "147" => "Scanned at hub",
        "148" => "Scanned at Hub and labelled",
        "149" => "Bundle Pick From Hub",
        "150" => "Bundle Drop To Hub",
        '153' => 'Miss sorted to be reattempt',
        '154' => 'Joey unable to complete the route', '155' => 'To be re-attempted tomorrow'
    ];

    protected function getStatusWithKeys($arrg_status_ids)
    {
        $return_data = [];
        foreach(self::status_ids as $key => $status_label)
        {
            if(in_array( $key, $arrg_status_ids))
            {
                $return_data[$key] =  $status_label;
            }

        }

        return $return_data;
    }

    /*
     * Getting List Of Reattempt Orders Function
     * */
    public function getIndex(ReattemptScanOrderRequest $request)
    {
        //Getting request Data
        // user id data
        $user_data = auth()->user();
        //Set array in variable for confirm return status
        $confirm_return_status_ids = [143, 105, 111];
        //Set array in variable for transfer status
        $transfer_status_ids = [108, 109, 146];
        //Set query for reattempt data
        $return_reattempt_history = ReturnReattemptProcess::where('created_by', $user_data->id)
            ->where('is_processed', 0)
            ->where('is_expired_updated', 0)
            ->whereNull('deleted_at')
            ->orderBy('id','DESC')
            ->get();

        $approved_order_list_count = ReturnAndReattemptProcessHistory::where('verified_by', '!=' , null)
            ->whereNull('deleted_at')
            ->where('is_processed', 0)
            ->where('created_by', auth()->user()->id)
            ->count();

        return backend_view('reattempt-order.index', compact('approved_order_list_count', 'return_reattempt_history', 'confirm_return_status_ids', 'transfer_status_ids'/*,'old_request_data'*/));
    }

    public function approvedCustomerOrderCount()
    {
        $approved_order_list_count = ReturnAndReattemptProcessHistory::where('verified_by', '!=' , null)
            ->whereNull('deleted_at')
            ->where('is_processed', 0)
            ->where('created_by', auth()->user()->id)
            ->count();
        return array("count" =>$approved_order_list_count);
    }

    /*
     * Getting List Of All Reattempt Orders
     * */
    public function reattemptOrderList(ReattemptOrderHistoryRequest $request)
    {
        //Getting request Data
        $request_data = $request->all();
        //Getting request data after filer to set old data
        $old_request_data = $request_data;
        //Get current date
        $current_date = date('Y-m-d');
        //Get start date from request
        $start_date = (isset($request_data['start_date']) ? $request_data['start_date'] . ' 00:00:00' : $current_date . ' 00:00:00');
        //Get end date from request
        $end_date = (isset($request_data['end_date']) ? $request_data['end_date'] . ' 23:59:59' : $current_date . ' 23:59:59');
        //Get status from request
        $status_id = (isset($request_data['status']))? intval ($request_data['status'])  : 0 ;
        //Get CTC vendors
        $ctc_vendor = CtcVendor::pluck('vendor_id')->toArray();
        //merging array for ctc montreal and ottawa vendors
        $vendor_ids = array_merge($ctc_vendor,[477282,477260]);
        //Set query for reattempt history data
        $query = ReturnReattemptProcess::join('sprint__sprints', 'return_and_reattempt_process_history.sprint_id', '=', 'sprint__sprints.id')
            ->where('is_processed', 1)
            ->whereIn('sprint__sprints.creator_id',$vendor_ids)
            ->whereNull('return_and_reattempt_process_history.deleted_at')
            ->distinct('return_and_reattempt_process_history.id')
            ->select('return_and_reattempt_process_history.*');

        //Check condition for date and status filter
        if($start_date != 0 && $end_date != 0 && $status_id > 0)
        {
            $query->whereBetween(\DB::raw("CONVERT_TZ(return_and_reattempt_process_history.created_at,'UTC','America/Toronto')"), [$start_date, $end_date])
                ->where('return_and_reattempt_process_history.status_id',$status_id);

        }
        elseif ($status_id > 0)
        {
            $query->where('return_and_reattempt_process_history.status_id',$status_id);
        }
        else
        {
            $query->whereBetween(\DB::raw("CONVERT_TZ(return_and_reattempt_process_history.created_at,'UTC','America/Toronto')"), [$start_date, $end_date]);
        }
        //Set data in variable for view
        $return_reattempt_history = $query->get();



        return backend_view('reattempt-order.reattempt-order-list', compact('return_reattempt_history', 'old_request_data'));
    }

    /*
     * Scanning Tracking Id Function
     * */
    public function searchTrackingId(ReattemptOrderRequest $reattemtRequest)
    {
        //Variable for transfer button
        $transfer_btn = '';
        //Variable for reattempt button
        $reattempt_btn = '';
        //Variable for multiple reattempt button
        $multipleReattempt = '';
        //Variable for remove button
        $remove_btn = '';
        //Set array in variable for confirm return status
        $confirm_return_status_ids = [143, 105, 111];
        //Set array in variable for transfer status
        $transfer_status_ids = [108, 109, 146];
        //Scan allowed status
        $scan_allowed_status_ids = [136, 106, 110, 102, 112, 137, 140, 107, 131, 135,142];
        //Scan for modal
        $scan_modal_status_id = [124, 133, 121, 61];
        $scan_model_select_list = [
            "124"=>$this->getStatusWithKeys([140]),
            "133"=>$this->getStatusWithKeys([140]),
            "121"=>$this->getStatusWithKeys([136,135,108,106,107,109,143,105,112,131,137,140,146,154]),
            "61" =>$this->getStatusWithKeys([112]),
        ];

        //All status id array merge
        $all_scan_id_array = array_merge($confirm_return_status_ids,$transfer_status_ids,$scan_allowed_status_ids,$scan_modal_status_id);

        $routeData = JoeyRouteLocations::join('merchantids','merchantids.task_id','=','joey_route_locations.task_id')
            ->where('tracking_id','=',$reattemtRequest->tracking_id)
            ->whereNull('joey_route_locations.deleted_at')
            ->first(['joey_route_locations.id','joey_route_locations.created_at','joey_route_locations.task_id','joey_route_locations.route_id','ordinal','merchant_order_num','joey_route_locations.is_transfered']);

        $routeDetails=null;

        if($routeData){

            $taskIds = JoeyRouteLocations::where('route_id',$routeData->route_id)->whereNull('deleted_at')->pluck('task_id');
            $sprint = Task::whereIn('id',$taskIds)->whereNull('deleted_at')->pluck('sprint_id');
            $reattemptCount = Sprint::whereNull('deleted_at')->where('is_reattempt', 1)->whereIn('id',$sprint)->count();
            $pickupCount = Task::whereIn('id', $taskIds)->whereNull('deleted_at')->where('status_id', 121)->count();
            $orderCount = JoeyRouteLocations::where('route_id',$routeData->route_id)->whereNull('deleted_at')->count();
            $deliveredCount = Task::whereIn('id', $taskIds)->whereNull('deleted_at')->whereIn('status_id', [17, 113, 114, 116, 117, 118, 132, 138, 139, 144])->count();
            $returnCount = Task::whereIn('id', $taskIds)->whereNull('deleted_at')->whereIn('status_id', [101, 104, 105, 106, 107, 108, 109, 110, 111, 112, 131, 135, 136,143,146])->count();

            $routeDetails = [
                'route_id' => $routeData->route_id,
                'order_count' => $orderCount,
                'pickup_count' => $pickupCount,
                'reattempt_count' => $reattemptCount,
                'delivered_count' => $deliveredCount,
                'return_count' => $returnCount
            ];
        }

        //Getting Data For Reattempt And Return Order History
        $order_data = DB::table('merchantids')
            ->join('sprint__tasks', 'merchantids.task_id', '=', 'sprint__tasks.id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->join('sprint__contacts', 'sprint__contacts.id', '=', 'sprint__tasks.contact_id')
            ->join('sprint__sprints', 'sprint__tasks.sprint_id', '=', 'sprint__sprints.id')
            ->leftJoin('sprint_reattempts', 'sprint__sprints.id', '=', 'sprint_reattempts.sprint_id')
            ->where('merchantids.tracking_id', $reattemtRequest->tracking_id)
            ->whereNull('merchantids.deleted_at')
            ->orderBy('merchantids.id','desc')
            ->whereIn('sprint__sprints.status_id', $all_scan_id_array)
            ->select(['merchantids.*', 'sprint__sprints.creator_id as merchant_id', 'sprint__sprints.id as sprint_id', 'sprint__tasks.id as task_id', 'sprint__sprints.status_id'/*, 'joey_route_locations.route_id'*/, 'locations.address', 'locations.id as location_id', 'locations.postal_code as postal_code', 'sprint__contacts.phone', 'sprint__contacts.id as s_contact_id', 'sprint_reattempts.reattempts_left'])
            ->distinct('merchantids.tracking_id')
            ->first();
        //Checking Condition Data Not Empty
        if (empty($order_data)) {
            return response()->json(['status' => false, 'message' => 'This tracking id does not exist try to re-scan', 'routeCounts' => $routeDetails,'reattemptSuccess' => 'false']);
        }
        $is_route_exist = JoeyRouteLocations::where('task_id',$order_data->task_id)->pluck('route_id');

        if($is_route_exist->isEmpty())
        {
            return response()->json(['status' => false, 'message' => 'This order is not routed, please scan it in Inbound Scanning', 'routeCounts' => $routeDetails,'reattemptSuccess' => 'false']);
        }
        //Getting Data From return_and_reattempt_process_history For Checking Condition
        $tracking_id = ReturnReattemptProcess::where('merchantids_id', '=', $order_data->id)
            ->where('is_processed', '=', 0)
            ->NotDeleted()
            ->first();

        //Checking Condition If Tracking Id Is Already In Process or Waiting For Approval or Already Scan Tracking Id

        if ($tracking_id != null && $tracking_id->process_type == 'customer_support' && $tracking_id->is_expired_updated == 0)
        {
            return response()->json(['status' => false, 'message' => 'This tracking id already transfer to customer support and waiting for address approval', 'routeCounts' => $routeDetails,'reattemptSuccess' => 'false']);
        }
        elseif ($tracking_id != null && $tracking_id->process_type == 'customer_support' && $tracking_id->is_expired_updated == 1)
        {
            return response()->json(['status' => false, 'message' => 'This tracking id already exist in return to merchant bucket']);
        }
        elseif ($tracking_id != null && $tracking_id->process_type == 'return')
        {
            return response()->json(['status' => false, 'message' => 'This tracking id already exist in returned to merchant bucket']);
        }
        elseif ($tracking_id != null && $tracking_id->created_by == Auth::user()->id)
        {
            return response()->json(['status' => false, 'message' => 'You already scan this tracking id', 'routeCounts' => $routeDetails,'reattemptSuccess' => 'false']);
        }
        elseif ($tracking_id != null)
        {
            return response()->json(['status' => false, 'message' => 'This tracking id already in process', 'routeCounts' => $routeDetails,'reattemptSuccess' => 'false']);
        }

        //Vendor Reattempt checking
        $vendorIsReattempt = Vendor::where('id',$order_data->merchant_id)->first();

        if ($vendorIsReattempt->reattempts == 0)
        {
            return response()->json(['status' => false, 'message' => 'This merchant is not allowed for reattempt!']);
        }

        if(in_array( $order_data->status_id, $scan_modal_status_id))
        {
            return response()->json([
                'status' => true,
                'message' => 'This Tracking id current status is "'.self::status_ids[$order_data->status_id].'" kindly update status from select box',
                'selection_data'=> $scan_model_select_list[$order_data->status_id],
                'type'=>'modal',
                'data'=>[
                    "sprint_id"=>$order_data->sprint_id,
                    "task_id"=>$order_data->task_id,
                    "tracking_id"=>$reattemtRequest->tracking_id
                ],
                'routeCounts' => $routeDetails,
                'reattemptSuccess' => 'false'
            ]);
        }

        //checking condition for reattempt left because reattempt set on 1 and now subtract 1 on reattempt to show 1 less value
        $reattempt_left = 3;
        if(is_null($order_data->reattempts_left))
        {
            $reattempt_left = Sprint::find($order_data->sprint_id)->Vendor->reattempts - 1;
        }
        else
        {
            $reattempt_left = $order_data->reattempts_left - 1;
        }

        //Create History For Return And Reattempt Order History
        $history = ReturnReattemptProcess::create([
            'created_by' => Auth::user()->id,
            'sprint_id' => $order_data->sprint_id,
            'task_id' => $order_data->task_id,
            'merchantids_id' => $order_data->id,
            //'route_id' => $order_data->route_id,
            'tracking_id' => $order_data->tracking_id,
            'location_id' => $order_data->location_id,
            'sprint_contact_id' => $order_data->s_contact_id,
            'customer_address' => $order_data->address,
            'postal_code' => $order_data->postal_code,
            'customer_phone' => $order_data->phone,
            'status_id' => $order_data->status_id,
            'reattempt_left' => $reattempt_left,
            'varify_note' => '',
            'is_action_applied' => 0
        ]);


        //Checking Condition For Show Button Of Transfer or Reattempt
        //        $return_btn = '  <button type="submit" class="col-md-12 btn btn-warning btn-sm return-order" data-id="'.$history->id.'" data-sprint-id="' . $history->sprint_id . '">Return to merchant</button>';
        $addres_td = $order_data->address . ' ' . $order_data->postal_code;
        $phone_td = $order_data->phone;

        // checking reattempt left is available
        //$reattempt_status = ($order_data->reattempts_left > 0 || $order_data->reattempts_left == null) ? true : false;
        $reattempt_status = ($order_data->reattempts_left > 1 || is_null($order_data->reattempts_left)) ? true : false;

        //checking condition for returned order
        if ($reattempt_status == false)
        {
            $history->is_expired_updated = 1;
            $history->process_type =  'customer_support';
            $history->save();

            return response()->json(['status' => false, 'message' => 'This tracking id '.$order_data->tracking_id.' has no reattempt left moved to return bucket on dashboard', 'routeCounts' => $routeDetails,'reattemptSuccess' => 'false']);
        }
        elseif (in_array( $order_data->status_id, $confirm_return_status_ids))
        {
            $history->is_expired_updated = 1;
            $history->process_type =  'customer_support';
            $history->save();

            return response()->json(['status' => false, 'message' => 'This tracking id '.$order_data->tracking_id.' has status of " '.self::status_ids[$order_data->status_id].' " moved to return bucket on dashboard', 'routeCounts' => $routeDetails,'reattemptSuccess' => 'false']);
        }
        if (in_array($order_data->status_id, $transfer_status_ids) && $reattempt_status == true) {
            // $transfer_btn = '<button type="submit" class="col-md-12 btn btn-primary btn-sm transfer-order" data-id="' . $history->id . '">Transfer to customer support</button>';
            $this->transferOrder($history->id);
            return response()->json(['status' => false, 'message' => 'This order is transfered to customer support!','reattemptSuccess' => 'true']);
            /*this below code is com*/
            /*//return editable input for address in responce
            $addres_td =  '<div class="table-input-with-btn">
            <input type="text" data-event-status="false"
                               data-type="customer_address"
                               data-match="'.$history->location_id.'"
                               data-id="'.$history->id.'"
                               data-lat=""
                               data-lng=""
                               data-city=""
                               data-postal-code=""
                               data-old-val="'.$history->customer_address.' '.$history->postal_code.'"
                               class="form-control update-address-on-change google-address"
                               value="'.$history->customer_address.', '.$history->postal_code.'"/>
                                <button class="datatable-input-update-btn fa fa-pencil" style="display: none"></button>
                            </div>';
            //return editable input for phone in responce
            $phone_td = '<div class="table-input-with-btn">
                            <input type="text" data-event-status="false" data-type="customer_phone"
                           data-match="'.$history->sprint_contact_id.'"
                           data-id="'.$history->id.'"
                           data-old-val="'.$history->customer_phone.'"
                           class="form-control update-address-on-change"
                           value="'.$history->customer_phone.'" name="phone"/>
                            <button class="datatable-input-update-btn fa fa-pencil" style="display: none"></button>
                         </div>';*/
        } elseif (!in_array($order_data->status_id, $confirm_return_status_ids) && $reattempt_status == true || $history->verified_by > 0) {
            $objetoRequest = new \Illuminate\Http\Request();
            $objetoRequest->setMethod('POST');
            $objetoRequest->request->add([
                'ids'=>$history->id
            ]);
            $this->reattemptOrder($history->tracking_id,$objetoRequest);
            return response()->json(['status' => false, 'message' => 'This tracking id '.$history->tracking_id.' reattempt successfully','reattemptSuccess' => 'true']);
            //$multipleReattempt = '<Input class="reattemptCheckbox" type="checkbox" value="'.$history->tracking_id.'" data-reattempt="'.$history->id .'" name="scan-reattempt">';
            //  $reattempt_btn = '  <button type="submit" class="col-md-12 btn btn-success btn-sm scan-for-reattempt-order scan-for-reattempt-order-' . $history->tracking_id . '" data-id="' . $history->id . '" data-tracking_id="' . $history->tracking_id . '">Scan for reattempt</button>';
            //$reattempt_btn = '  <button type="submit" class="col-md-12 btn btn-success btn-sm scan-for-reattempt-order reattempt-order scan-for-reattempt-order-' . $history->tracking_id . '" data-id="' . $history->id . '" data-tracking_id="' . $history->tracking_id . '">Scan for reattempt </button>';
        }
        if ($history->is_action_applied < 1) {
            $remove_btn = '  <button type="submit" class="col-md-12 btn btn-danger btn-sm remove-reattempt-order" data-id="' . $history->id . '">Remove </button>';
        }


        //Show Data In Table After Scanning Tracking Id
        $responce_body = [
            'checkbox' => $multipleReattempt,
            'order_id' => $order_data->sprint_id,
            'tracking_id' => $order_data->tracking_id,
            //'route_no' => $order_data->route_id,
            'customer_address' => $addres_td,
            'customer_phone' => $phone_td,
            'status' => self::status_ids[$order_data->status_id],
            'count_of_reattempt_left' => $reattempt_left,
            'customer_support_note' => '',
            'sub_note' => '',
            'action' => /*$return_btn . " " .*/
                $transfer_btn . " " . $reattempt_btn . " " . $remove_btn,
        ];


        return response()->json(['status' => true, 'body' => $responce_body, 'routeCounts' => $routeDetails,'reattemptSuccess' => 'false']);

    }

    /*
     * Transfer Order To Customer Support Function
     * */
    public function transferOrder($id)
    {
        $update = ReturnReattemptProcess::where('id', $id)->update(['process_type' => 'customer_support', 'is_action_applied' => 1]);
        if ($update > 0) {
            return response()->json(['status' => true, 'message' => 'Transfer to customer support successfully']);
        }

        return response()->json(['status' => false, 'message' => 'Something went wrong please try again in a bit later ']);


    }

    /*
     * Reattempt Order Function
     * */
    public function reattemptOrder($tracking_id, Request $request)
    {
        // $host = config('app.joeyco_api_link.production');
        $curl = new CurlRequestSend();
        $curl->setHost('https://api.joeyco.com');
        $curl->setMethod('post');
        $curl->setUri('/order/hub/return');
        $curl->setHeader('Authorization', 'Authorization: Basic ' . base64_encode('api:api1243'));
        $curl->setData(['tracking_id' => $tracking_id]);
        $curl->send();
        $responce = $curl->arrayResponce();
        $responce_jason = end($responce['responce']);

        // checking the respoce
        if ($responce['error'] != null) {
            return response()->json(['status' => false, 'message' => 'Something went wrong please try again in a bit later', 'error' => $responce['error']]);
        }
        else {
            $server_responce = json_decode($responce_jason, true);
            //checking  other server resonce
            if ($server_responce['http']['code'] != 200) {
                return response()->json(['status' => false, 'message' => $server_responce['response'], 'error' => $server_responce]);
            }
            elseif($server_responce['response']['status'] == false)
            {
                return response()->json(['status' => false, 'message' => $server_responce['response']['reattempOrderCreationResponse'],"error"=>'']);
            }
            else
            {
                DB::beginTransaction();
                try {
                    // getting data of retrun process
                    $reattempt_update = ReturnReattemptProcess::where('id', $request->ids)->latest()
                        ->first();


                    // getting new merchant id data
                    $merchantids = MerchantIds::where('tracking_id', $reattempt_update->tracking_id)->latest()
                        ->first();

                    $boradless_dashboard = BoradlessDashboard::where('tracking_id', $reattempt_update->tracking_id)->latest()
                        ->first();

                    $location = (isset($merchantids->dropoffTask->location)) ? $merchantids->dropoffTask->location : null;

                    //$location_enc = (isset($merchantids->dropoffTask->location_enc)) ? $merchantids->dropoffTask->location_enc : null;

                    $sprint_contact = (isset($merchantids->dropoffTask->sprint_contact)) ? $merchantids->dropoffTask->sprint_contact : null;

                    $contact_enc = (isset($merchantids->dropoffTask->contact_enc)) ? $merchantids->dropoffTask->contact_enc : null;

                    // checking all the models are loaded
                    if ($reattempt_update == null || $merchantids == null || $location == null || $sprint_contact == null || $contact_enc == null) {
                        return response()->json([
                            'status' => false,
                            'message' => "Something went wrong",
                            'error' => [
                                'reattempt_update' => $reattempt_update,
                                'merchantids' => $merchantids,
                                'location' => $location,
                                'sprint_contact' => $sprint_contact,
                            ]
                        ]);
                    }


                    // checking the address is updated
                    if($reattempt_update->is_address_updated == 1)
                    {

                        // $merchantids  updating data
                        $merchantids->address_line2 = $reattempt_update->customer_address;
                        $merchantids_updated = $merchantids->save();

                        $plan_address = explode(',',$reattempt_update->customer_address)[0];
                        // $plan_address = $reattempt_update->customer_address;
                        // update location
                        $location->address = $plan_address;
                        $location->latitude = $reattempt_update->latitude;
                        $location->longitude = $reattempt_update->longitude;
                        $location->postal_code = $reattempt_update->postal_code;
                        $location->city_id = $reattempt_update->city_id;
                        $location->state_id = $reattempt_update->state_id;
                        $location->country_id = $reattempt_update->country_id;
                        $location_updated = $location->save();

                        $boradless_dashboard->address_line_1 = explode(',',$reattempt_update->customer_address)[0];
                        $boradless_updated = $boradless_dashboard->save();

                        // update location enc
                        //$location_enc->address = $plan_address;
                        //$location_enc->latitude = $reattempt_update->latitude;
                        //$location_enc->longitude = $reattempt_update->longitude;
                        //$location_enc->postal_code = $reattempt_update->postal_code;
                        //$location_enc->city_id = $reattempt_update->city_id;
                        //$location_enc->state_id = $reattempt_update->state_id;
                        //$location_enc->country_id = $reattempt_update->country_id;
                        //$location_updated_enc = $location_enc->save();
                    }
                    else{
                        $reattempt_update = ReturnReattemptProcess::where('tracking_id', $merchantids->tracking_id)
                            ->where('is_address_updated', 1)
                            ->first();
                        if(!empty($reattempt_update)){
                            $plan_address = explode(',',$reattempt_update->customer_address)[0];
                            $latest_task = Task::where('id',$merchantids->task_id)->latest()
                                ->first();
                            LocationUnencrypted::where('id',$latest_task->location_id)
                                ->update(
                                    [
                                        'updated_at'=>date('Y-m-d H:i:s'),
                                        'address' => $plan_address,
                                        'latitude' => $reattempt_update->latitude,
                                        'longitude' => $reattempt_update->longitude,
                                        'postal_code' => $reattempt_update->postal_code,
                                        'city_id' => $reattempt_update->city_id,
                                        'state_id' => $reattempt_update->state_id,
                                        'country_id' => $reattempt_update->country_id,
                                    ]
                                );
                        }
                        else{
                            $reattempt_update = ReturnReattemptProcess::where('tracking_id', $merchantids->tracking_id)
                                ->orderBy('id','DESC')
                                ->first();
                        }
                    }

                    //update  $sprint contact
                    $sprint_contact->phone = $reattempt_update->customer_phone;
                    $sprint_contact_updated = $sprint_contact->save();

                    //update contact enc
                    $contact_enc->phone = $reattempt_update->customer_phone;
                    $contact_end_updated = $contact_enc->save();

                    // ReturnReattemptProcess
                    $reattempt_update->process_type = 'reattempt';
                    $reattempt_update->is_processed = 1;
                    $reattempt_update->proceed_at = date("Y-m-d H:i:s");
                    $reattempt_update = $reattempt_update->save();
                    // dd($reattempt_update,$sprint_contact_updated,$contact_end_updated);

                    DB::commit();

                    return response()->json(['status' => true, 'message' => 'Order reattempt successfully', 'error' => '']);
                }
                catch (\Exception $e) {

                    DB::rollback();
                    return response()->json(['status' => false, 'message' => 'Something went wrong',"error"=>$e]);
                }
            }

        }
    }

    /*
     * Transfer Order To Customer Support Function
     * */
    public function reattemptOrderColumnUpdate(Request $request)
    {
        //dd($request->all());
        // update sprint address
        if ($request->type == 'customer_address') {
            //Getting Data From Ajax Request
            $update_address = $request->val;
            $latitude = str_replace(".", "", $request->lat);
            $latitudes = (strlen($latitude) > 10) ? (int)substr($latitude, 0, 9) : (int)$latitude;
            $longitude = str_replace(".", "", $request->lng);
            $longitudes = (strlen($longitude) > 10) ? (int)substr($longitude, 0, 9) : (int)$longitude;
            $postal_code = $request->postalcode;
            $city = $request->city_val;


            $cities_data = City::where('name', $city)->first();

            if (empty($cities_data) || $city == '' || $city == null) {
                return response()->json(['status' => false, 'message' => 'Wrong address please enter correct address']);
            }


            $sprint_address = LocationUnencrypted::where('id', $request->ids)
                ->update([
                        'address' => $update_address,
                        'latitude' => $latitudes,
                        'longitude' => $longitudes,
                        'postal_code' => $postal_code,
                        'city_id' => $cities_data->id,
                        'state_id' => $cities_data->state_id,
                        'country_id' => $cities_data->country_id]
                );

            $sprint_addr = ReturnReattemptProcess::where('id', $request->id)->update(['customer_address' => $update_address, 'postal_code' => $postal_code, 'is_action_applied' => 1]);

            if ($sprint_address == 1) // creating record update successfully responce
            {
                return response()->json(['status' => true, 'message' => 'Address updated successfully']);
            }

            return response()->json(['status' => false, 'message' => 'Something went wrong please try again in a bit later ']);


        }
        // update sprint contact
        if ($request->type == 'customer_phone') {
            $update_address = $request->val;

            $sprint_contact = SprintContact::where('id', $request->ids)->update(['phone' => $update_address]);
            $sprint_cont = ReturnReattemptProcess::where('id', $request->id)->update(['customer_phone' => $update_address, 'is_action_applied' => 1]);

            if ($sprint_contact == 1) // creating record update successfully responce
            {
                return response()->json(['status' => true, 'message' => 'Phone updated successfully']);
            }
            return response()->json(['status' => false, 'message' => 'Something went wrong please try again in a bit later ']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again in a bit later ']);
    }

    /*
     * Return Order Function
     * */
    public function returnOrder($id, Request $request)
    {
        // update sprint contact
        if (!empty($id)) {

            $current_date = date("Y-m-d H:i:s");

            //Getting Data From Sprint And Vendor for return order
            $sprint_update = Sprint::find($id);
            $vendor_data = $sprint_update->Vendor;

            //Update Sprint For Return Order
            $sprint_update->status_id = 13;
            $sprint_update->created_at = $current_date;
            $sprint_update->save();


            //update task for return order
            $task_update = Task::where('sprint_id', $id)
                ->update([
                    'status_id' => 13,
                    'created_at' => $current_date,
                    'location_id' => $vendor_data->location_id,
                    'contact_id' => $vendor_data->contact_id
                ]);

            //update Reattempt order for return order request
            $reattempt_update = ReturnReattemptProcess::where('id', $request->return_process_id)
                ->update([
                    'process_type' => 'return',
                    'is_processed' => 1,
                    'proceed_at' => $current_date
                ]);

            if ($sprint_update == true && $task_update > 0) {
                return response()->json(['status' => true, 'message' => 'Order Return successfully']);
            }
            return response()->json(['status' => false, 'message' => 'Something went wrong ']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong please try again in a bit later ']);
    }

    /*
     * Remove Data From Reattempt And Return Order
     * */
    public function deleteReattempt($id)
    {
        $current_date = date("Y-m-d H:i:s");
        $remove = ReturnReattemptProcess::where('id', $id)
            ->where('is_action_applied', 0)
            ->first();

        // checking this record is already in process
        if (!isset($remove)) {
            return response()->json(['status' => false, 'message' => 'Sorry can not deleted this order some action already applied']);
        }

        // updating the record
        $remove->deleted_at = $current_date;
        $remove->deleted_by = 'routing_support';
        $remove->save();

        return response()->json(['status' => true, 'message' => 'Remove successfully from your bucket']);
    }

    /**
     *Customer Support Approved Order List
     *
     */
    public function approvedOrderList()
    {
        $approved_order_list = ReturnReattemptProcess::where('verified_by', '!=' , null)
            ->whereNull('deleted_at')
            ->where('is_processed', 0)
            ->where('created_by', auth()->user()->id)
            ->get();

        return backend_view('reattempt-order.approved-order-list', compact('approved_order_list'));
    }

    /**
     * Notes Show Function
     *
     */
    public function showNotes(Request $request, $id)
    {
        $notes = CustomerSupportReturnNotes::where('rarph_ref_id', $id)->where('deleted_at', null)->get();
        return backend_view('reattempt-order.customer_support_notes', compact('notes'));
    }

    /*
     * Multiple Reattempt Order Function
     * */
    public function multipleReattemptOrder(MultipleReattemptOrderRequest $request)
    {
        $request_data = $request->data;

        $responce_data = ['errors_data'=>[],'success_data'=>[]];
        $date = date("Y-m-d H:i:s");
        foreach($request_data as $key => $single_request) {

            $curl = new CurlRequestSend();
            $curl->setHost('https://api.joeyco.com');
            $curl->setMethod('post');
            $curl->setUri('/order/hub/return');
            $curl->setHeader('Authorization', 'Authorization: Basic ' . base64_encode('api:api1243'));
            $curl->setData(['tracking_id' => $single_request['tracking_id']]);
            $curl->send();
            $responce = $curl->arrayResponce();
            $responce_jason = end($responce['responce']);

            // checking the respoce
            if ($responce['error'] != null) {
                $responce_data['errors_data'][$key] =['status' => false, 'message' => 'This tracking id '.$single_request['tracking_id'].' could not reattempt by bulk reattempt action please try again in a bit later by individual reattempt action.', 'error' => $responce['error'],'request_data'=>$single_request];
            }
            else
            {
                $server_responce = json_decode($responce_jason, true);
                //checking  other server resonce
                if ($server_responce['http']['code'] != 200) {
                    $responce_data['errors_data'][$key] =['status' => false, 'message' => 'This tracking id '.$single_request['tracking_id'].' could not reattempt by bulk reattempt action please try again in a bit later by individual reattempt action.', 'error' => $server_responce,'request_data'=>$single_request];
                } else
                {
                    DB::beginTransaction();
                    try {
                        // getting data of retrun process
                        $reattempt_update = ReturnReattemptProcess::where('id', $single_request['reattempt_id'])->latest()
                            ->first();


                        // getting new merchant id data
                        $merchantids = MerchantIds::where('tracking_id', $reattempt_update->tracking_id)->latest()
                            ->first();

                        $location = (isset($merchantids->dropoffTask->location)) ? $merchantids->dropoffTask->location : null;

                        //$location_enc = (isset($merchantids->dropoffTask->location_enc)) ? $merchantids->dropoffTask->location_enc : null;

                        $sprint_contact = (isset($merchantids->dropoffTask->sprint_contact)) ? $merchantids->dropoffTask->sprint_contact : null;

                        $contact_enc = (isset($merchantids->dropoffTask->contact_enc)) ? $merchantids->dropoffTask->contact_enc : null;

                        // checking all the models are loaded
                        if ($reattempt_update == null || $merchantids == null || $location == null || $sprint_contact == null || $contact_enc == null) {
                            return response()->json([
                                'status' => false,
                                'message' => "Something went wrong",
                                'error' => [
                                    'reattempt_update' => $reattempt_update,
                                    'merchantids' => $merchantids,
                                    'location' => $location,
                                    'sprint_contact' => $sprint_contact,
                                ]
                            ]);
                        }




                        // checking the address is updated
                        if($reattempt_update->is_address_updated == 1)
                        {

                            // $merchantids  updating data
                            $merchantids->address_line2 = $reattempt_update->customer_address;
                            $merchantids_updated = $merchantids->save();

                            $plan_address = explode(',',$reattempt_update->customer_address)[0];
                            // update location
                            $location->address = $plan_address;
                            $location->latitude = $reattempt_update->latitude;
                            $location->longitude = $reattempt_update->longitude;
                            $location->postal_code = $reattempt_update->postal_code;
                            $location->city_id = $reattempt_update->city_id;
                            $location->state_id = $reattempt_update->state_id;
                            $location->country_id = $reattempt_update->country_id;
                            $location_updated = $location->save();


                            // update location enc
                            //$location_enc->address = $plan_address;
                            //$location_enc->latitude = $reattempt_update->latitude;
                            //$location_enc->longitude = $reattempt_update->longitude;
                            //$location_enc->postal_code = $reattempt_update->postal_code;
                            //$location_enc->city_id = $reattempt_update->city_id;
                            //$location_enc->state_id = $reattempt_update->state_id;
                            //$location_enc->country_id = $reattempt_update->country_id;
                            //$location_updated_enc = $location_enc->save();
                        }

                        //update  $sprint contact
                        $sprint_contact->phone = $reattempt_update->customer_phone;
                        $sprint_contact_updated = $sprint_contact->save();

                        //update contact enc
                        $contact_enc->phone = $reattempt_update->customer_phone;
                        $contact_end_updated = $contact_enc->save();

                        // ReturnAndReattemptProcessHistory
                        $reattempt_update->process_type = 'reattempt';
                        $reattempt_update->is_processed = 1;
                        $reattempt_update->proceed_at = date("Y-m-d H:i:s");
                        $reattempt_update = $reattempt_update->save();

                        DB::commit();

                        $responce_data['success_data'][$key] =['status' => true, 'message' => 'This tracking id '.$single_request['tracking_id'].' reattempt successfully.', 'error' => '','request_data'=>$single_request];
                    }
                    catch (\Exception $e) {

                        DB::rollback();
                        $responce_data['errors_data'][$key] =['status' => false, 'message' => 'This tracking id '.$single_request['tracking_id'].' could not reattempt by bulk reattempt action please try again in a bit later by individual reattempt action...', 'error' => $e,'request_data'=>$single_request];
                    }
                }

            }

        }

        return response()->json($responce_data);

    }

    /**
     * update status of scanned order for reattempt
     * */
    public function updateStatusOfScannedOrder(Request $request)
    {
        $date_time = date('Y-m-d H:i:s');
        DB::beginTransaction();
        try {

            // updating sprint status
            $update_sprint = Sprint::where('id',$request->sp_id)->update(['status_id'=>$request->scan_modal_status_change]);

            //update sprint task
            $update_task = Task::where('id',$request->task_id)->update(['status_id'=>$request->scan_modal_status_change]);

            //update Borderless
            $update_borderless = BoradlessDashboard::where('task_id',$request->task_id)->update(['task_status_id'=>$request->scan_modal_status_change]);

            // add new status update history
            $add_sprint_task_history = TaskHistory::create([
                "sprint__tasks_id" => $request->task_id,
                "sprint_id" => $request->sp_id,
                "status_id"=> $request->scan_modal_status_change,
                "date" => $date_time,
                "created_at" => $date_time,
            ]);

            DB::commit();
        }
        catch (\Exception $e) {

            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Something went wrong, Please try again in bit later',"error"=>$e]);
        }

        return response()->json(['status' => true, 'message' => 'Status updated successfully of tracking id " '.$request->tracking_id.'" ','tracking_id'=>$request->tracking_id]);


    }

}
