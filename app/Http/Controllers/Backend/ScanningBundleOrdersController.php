<?php

namespace App\Http\Controllers\Backend;

use App\BoradlessDashboard;
use App\City;
use App\Classes\CurlRequestSend;
use App\Classes\Google\GoogleAuthenticator;
use App\Classes\RestAPI;
use App\CTCEntry;
use App\CtcVendor;
use App\CustomerSupportReturnNotes;
use App\Http\Requests\Backend\MultipleReattemptOrderRequest;
use App\Http\Requests\Backend\ReattemptOrderHistoryRequest;
use App\Http\Requests\Backend\ReattemptOrderRequest;
use App\Http\Requests\Backend\ReattemptScanOrderRequest;
use App\Http\Requests\Backend\ScannedBundleRequest;
use App\Hub;
use App\Locations;
use App\LocationUnencrypted;
use App\MicroHubBundles;
use App\MicroHubOrder;
use App\MicroHubPostalCodes;
use App\CurrentHubOrder;
use App\ReturnReattemptProcess;
use App\Sprint;
use App\SprintContact;
use App\SprintTaskHistory;
use App\Task;
use App\MerchantIds;
use App\TaskHistory;
use App\User;
use Carbon\Carbon;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ScanningBundleOrdersController extends BackendController
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
     * Getting List Of Orders Function
     * */
    public function getIndex(Request $request)
    {
        $to_date = !empty($request->get('to_date_picker')) ? $request->get('to_date_picker').' 00:00:00' : date("Y-m-d").' 00:00:00';
        $from_date = !empty($request->get('from_date_picker')) ? $request->get('from_date_picker').' 23:59:59' : date("Y-m-d").' 23:59:59';

        $users = User::where('userType', 'admin')->where('hub_id', auth()->user()->hub_id)->pluck('id');

        //Set query for reattempt data
        $MicroHubScannedOrder = MicroHubOrder::join('sprint__tasks', 'sprint__tasks.sprint_id', '=', 'orders_actual_hub.sprint_id')
            ->join('merchantids', 'merchantids.task_id', '=', 'sprint__tasks.id')
            ->join('sprint__sprints', 'sprint__tasks.sprint_id', '=', 'sprint__sprints.id')
            ->whereNull('sprint__tasks.deleted_at')
            ->whereNull('merchantids.deleted_at')
            ->whereNull('orders_actual_hub.deleted_at')
            ->whereNotNull('merchantids.tracking_id')
            ->whereBetween('orders_actual_hub.created_at', [$to_date, $from_date])
            ->whereIn('orders_actual_hub.scanned_by',$users)
            ->groupBy('orders_actual_hub.bundle_id')
            ->get([
                'merchantids.tracking_id',
                'orders_actual_hub.bundle_id',
                'orders_actual_hub.hub_id',
                'orders_actual_hub.is_my_hub',
                'orders_actual_hub.scanned_by',
                'orders_actual_hub.created_at',
                'sprint__sprints.status_id',
                'sprint__sprints.deleted_at',
                'orders_actual_hub.sprint_id',
            ]);


        return backend_view('scanning-orders-mile.index', compact('MicroHubScannedOrder','to_date','from_date'));
    }

    /*
     * Scanning Tracking Id Function
     * */
    public function searchTrackingId(ReattemptOrderRequest $reattemtRequest)
    {
        $reattemtRequest->validate([
            'tracking_id' => 'required',
        ]);

        $data = $reattemtRequest->all();

        DB::beginTransaction();
        try {
            $hub = User::find(auth()->user()->id);

            $hubId = $hub->hub_id;

            $merchantIds = MerchantIds::with('taskids')->whereNull('deleted_at')->where('tracking_id', $data['tracking_id'])->first();

            if(empty($merchantIds)){
                return RestAPI::response('Tracking Id Is In valid', false);
            }

            $sprintTask = Task::whereNull('deleted_at')->find($merchantIds->task_id);

            if(empty($sprintTask)){
                return RestAPI::response('Task Not Available', false);
            }

            $location = Locations::whereNull('deleted_at')->find($sprintTask->location_id);

            if(empty($location)){
                return RestAPI::response('Location Not Found Of This Tracking Id', false);
            }

            $sprint = Sprint::find($sprintTask->sprint_id);
            $postalCode = substr($location->postal_code, 0, 3);

            $microHubPostalCode = MicroHubPostalCodes::where('postal_code', $postalCode)->whereNull('deleted_at')->first();

            if(empty($microHubPostalCode)){
                return RestAPI::response('Postal code not found', false);
            }

            $status = 0;
            $isMyHub = 0;

            if($microHubPostalCode->hub_id != $hubId){

                $hub_name = Hub::where('id',$microHubPostalCode->hub_id)->pluck('title')->toArray();
                $hub_data = $hub_name;
                $hub_id = $microHubPostalCode->hub_id;
                $status = 148;
                $isMyHub = 0;

            }
            if($microHubPostalCode->hub_id == $hubId){

                $hub_name = Hub::where('id',$microHubPostalCode->hub_id)->pluck('title')->toArray();
                $hub_data = $hub_name;
                $hub_id = $hubId;
                $status = 147;
                $isMyHub = 1;
            }
            $microHubOrder = MicroHubOrder::where('hub_id', $hubId)->where('sprint_id', $sprint->id)->first();

            if(!empty($microHubOrder)){
                CurrentHubOrder::create([
                    'hub_id' => $hub_id,
                    'sprint_id' => $sprint->id,
                ]);

                return response()->json(['status' => false, 'message' => 'No Order Found']);
            }else{

                MicroHubOrder::create([
                    'hub_id' => $hub_id,
                    'sprint_id' => $sprint->id,
                    'is_my_hub' => $isMyHub,
                    'bundle_id' => 'MMB-'.$hub_id.'-'.strtotime(date('Y-m-d')).'-'.$hubId,
                    'scanned_by' => auth()->user()->id
                ]);
                CurrentHubOrder::create([
                    'hub_id' => $hub_id,
                    'sprint_id' => $sprint->id,
                ]);
            }

            $sprint->update(['status_id'=>$status]);
            $sprintTask->update(['status_id'=>$status]);
            $sprintTaskHistory = [
                'sprint__tasks_id' => $sprintTask->id,
                'sprint_id' => $sprint->id,
                'status_id' => $status,
            ];

            SprintTaskHistory::create($sprintTaskHistory);
            $ctcEntries = CTCEntry::where('tracking_id', $data['tracking_id'])->update(['task_status_id' => $status]);

            $hub = Hub::whereNull('deleted_at')->find($microHubPostalCode->hub_id);

            if ($isMyHub == 1)
            {
                $ownHub = 'Yes';
            }
            else
            {
                $ownHub = 'No';
            }

            $hubData = Hub::where('id',$hub_id)->first();

            //Show Data In Table After Scanning Tracking Id
            $responce_body = [
                'bundle_id' => 'MMB-'.$hub_id.'-'.strtotime(date('Y-m-d')).'-'.$hubId,
                'hub_id' => $hub_data,
                'own_hub' => $ownHub,
                'created_at' => date('Y-m-d h:i:s'),
                'hub_name' => $hubData->title,
                'tracking_id' => $data['tracking_id'],
                'action' => '',
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return RestAPI::response($e->getMessage(), false, 'error_exception');
        }
        return response()->json(['status' => true, 'body' => $responce_body]);

    }

    public function scanningOrder($id)
    {
        $hub = User::find(auth()->user()->id);
        $hubId = $hub->hub_id;
        $ownHubDetail = Hub::where('id',$hubId)->first();
        $hub_bundle = MicroHubOrder::where('bundle_id',$id)->pluck('hub_id')->toArray();
        $otherHubDetail = Hub::whereIn('id',$hub_bundle)->first();


        $authenticator = new GoogleAuthenticator();

        $bundleID =  $authenticator->getQRCodeUrl($id);

        return backend_view('scanning-orders-mile.print-lable.index', compact('otherHubDetail','ownHubDetail','bundleID','id'));
    }

    public function scanningOrderDetail($id)
    {
        $users = User::where('hub_id', auth()->user()->hub_id)->pluck('id');

        $hub_bundle =  MicroHubOrder::join('sprint__tasks', 'sprint__tasks.sprint_id', '=', 'orders_actual_hub.sprint_id')
            ->join('merchantids', 'merchantids.task_id', '=', 'sprint__tasks.id')
            ->where('orders_actual_hub.bundle_id',$id)
            ->whereNull('orders_actual_hub.deleted_at')
            ->whereNotNull('merchantids.tracking_id')
            ->whereIn('orders_actual_hub.scanned_by',$users)
            ->get([
                'merchantids.tracking_id',
                'orders_actual_hub.bundle_id',
                'orders_actual_hub.hub_id',
                'orders_actual_hub.is_my_hub',
                'orders_actual_hub.scanned_by',
                'orders_actual_hub.created_at'
            ]);
//            MicroHubOrder::where('bundle_id',$id)->where('scanned_by',auth()->user()->id)->get();
        return backend_view('scanning-orders-mile.show', compact('hub_bundle'));
    }

    /**
     * Bundle Scanning
     **/
    public function getScannedBundles(Request $request)
    {
        $to_date = !empty($request->get('to_date_picker')) ? $request->get('to_date_picker').' 00:00:00' : date("Y-m-d").' 00:00:00';
        $from_date = !empty($request->get('from_date_picker')) ? $request->get('from_date_picker').' 23:59:59' : date("Y-m-d").' 23:59:59';

        $users = User::where('userType', 'admin')->where('hub_id', auth()->user()->hub_id)->pluck('id');

        //Set query for reattempt data
        $MicroHubBundleScanned = MicroHubBundles::whereNull('deleted_at')
            ->whereBetween('created_at', [$to_date, $from_date])
            ->whereIn('scanned_by',$users)
            ->groupBy('hub_id')
            ->get();

//dd($MicroHubBundleScanned,$users);
        return backend_view('scanning-orders-mile.scanned-bundles', compact('MicroHubBundleScanned','to_date','from_date'));
    }

    /*
     * Scanning Tracking Id Function
     * */
    public function searchBundlesId(ScannedBundleRequest $scannedBundleId)
    {
        $scannedBundleId->validate([
            'bundle_id' => 'required',
        ]);

        $data = $scannedBundleId->all();

        DB::beginTransaction();
        try {
            $hub = User::find(auth()->user()->id);

            $orderScanned = MicroHubOrder::where('bundle_id',$data['bundle_id'])->first();



            if(empty($orderScanned)){
                return RestAPI::response('Bundle Id Is Invalid', false);
            }
            $bundleScanned = MicroHubBundles::where('bundle_id',$orderScanned->bundle_id)->first();
            if(!empty($bundleScanned) || $bundleScanned != null){
                return RestAPI::response('Bundle Id Already Scanned', false);
            }
            if ($orderScanned->is_my_hub == 1)
            {
                $status = 147;
            }
            else
            {
                $status = 150;
            }
            $BundleData = MicroHubOrder::where('bundle_id',$data['bundle_id'])->pluck('sprint_id')->toArray();

            $sprintData = Sprint::whereIn('id',$BundleData)->get();
            foreach ($sprintData as $sprint)
            {
                $sprint->update(['status_id'=>$status]);
                $sprintTasks = Task::join('merchantids','sprint__tasks.id','=','merchantids.task_id')
                    ->whereNull('sprint__tasks.deleted_at')
                    ->whereNotNull('merchantids.tracking_id')
                    ->whereIn('sprint__tasks.ordinal',[2,4])
                    ->where('sprint__tasks.sprint_id',$sprint->id)
                    ->get(['sprint__tasks.id','merchantids.tracking_id']);
                foreach ($sprintTasks as $sprintTask)
                {
                    $sprintTask->update(['sprint__tasks.status_id'=>$status]);
                    $sprintTaskHistory = [
                        'sprint__tasks_id' => $sprintTask->id,
                        'sprint_id' => $sprint->id,
                        'status_id' => $status,
                    ];
                    SprintTaskHistory::create($sprintTaskHistory);
                    CTCEntry::where('tracking_id', $sprintTask->tracking_id)->update(['task_status_id' => $status]);
                    BoradlessDashboard::where('tracking_id', $sprintTask->tracking_id)->update(['task_status_id' => $status]);
                }
            }


                MicroHubBundles::create([
                    'hub_id' => $orderScanned->hub_id,
                    'is_my_hub' => $orderScanned->is_my_hub,
                    'bundle_id' => $orderScanned->bundle_id,
                    'scanned_by' => auth()->user()->id
                ]);

            //Show Data In Table After Scanning Tracking Id
            $responce_body = [
                'bundle_id' => $orderScanned->bundle_id,
                'hub_id' => $orderScanned->hub_id,
                'own_hub' => $orderScanned->own_hub,
                'created_at' => date('Y-m-d h:i:s'),
               // 'tracking_id' => $data['tracking_id'],
                'action' => '',
            ];
            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            return RestAPI::response($e->getMessage(), false, 'error_exception');
        }
        return response()->json(['status' => true, 'body' => $responce_body]);


    }


}
