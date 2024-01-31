<?php

namespace App\Http\Controllers\Backend;

use App\BoradlessDashboard;
use App\Classes\Fcm;
use App\CtcEntries;
use App\CTCEntry;
use App\CustomerFlagCategories;
use App\CustomerRoutingTrackingId;
use App\FlagCategoryMetaData;
use App\FlagHistory;
use App\Http\Traits\BasicModelFunctions;
use App\HubStore;
use App\JoeyRouteLocations;
use App\JoeyRoutes;
use App\Locations;
use App\Reason;
use App\SlotPostalCode;
use App\Sprint;
use App\MerchantIds;
use App\SprintReattempt;
use App\SprintTaskHistory;
use App\TaskHistory;
use App\TrackingDelay;
use App\TrackingNote;
use App\UserDevice;
use App\UserNotification;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use App\Ctc;
use App\Task;
use App\Notes;
use App\Ctc_count;
use App\CtcVendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
//use Illuminate\Database\Eloquent\Builder;

class FirstMileReportingController extends BackendController
{

    use BasicModelFunctions;
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
        "145" => 'Returned To Merchant',
        "146" => "Delivery Missorted, Incorrect Address",
        '147' => 'Scanned at Hub',
        '148' => 'Scanned at Hub and labelled',
        '149' => 'pick from hub',
        '150' => 'drop to other hub');

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
            "145" => 'Returned To Merchant',
            "146" => "Delivery Missorted, Incorrect Address",
            '147' => 'Scanned at Hub',
            '148' => 'Scanned at Hub and labelled',
            '149' => 'pick from hub',
            '150' => 'drop to other hub');
        return $statusid[$id];
    }

    /**
     * Get First Mile Reporting
     */
    public function getFirstMileReporting(Request $request)
    {
        $selectDateFilter = isset($request['days'])?$request->get('days'):'';
        $ctc_range_count = [];
        $ctc_count=[
            "total" => 0,
            "picked-up" => 0,
            "at-hub" => 0,
            "at-store" => 0,
            "sorted-order" => 0,
            "out-for-delivery" => 0,
            "delivered-order" => 0,
            "returned" => 0,
            "returned-to-merchant" => 0
        ];
        $city = !empty($request->get('city')) ? $request->get('city') : 'all';
        $start = !empty($request->get('start')) ? $request->get('start') : date("Y-m-d");
        $end = !empty($request->get('end')) ? $request->get('end') : date("Y-m-d");

        $ctcVendorIds = HubStore::where('hub_id', auth()->user()->hub_id)->pluck('vendor_id')->toArray();
        $first_mile_data = BoradlessDashboard::whereNull('deleted_at')->whereIn('creator_id', $ctcVendorIds)
            ->whereNotIn('task_status_id', [38, 36]);
        if (!empty($request))
        {
            if ($request['days'] == 'lastweek') {

                $startOfCurrentWeek = Carbon::now()->startOfWeek();
                $startOfLastWeek = $startOfCurrentWeek->copy()->subDays(5)->format('Y-m-d');
                $currentDate = Carbon::now()->format('Y-m-d');
                $start_dt = new DateTime($startOfLastWeek." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($currentDate." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $sprint_ids = $first_mile_data->where('created_at','>',$start)
                    ->where('created_at','<',$end)->distinct('store_name');

                //$storeName = array_values($sprint_ids);
                //$sprintsIds = array_keys($sprint_ids);

                $sprintsIds = $sprint_ids->pluck('sprint_id');
                $storeName = $sprint_ids->pluck('store_name');

                $sprint = new BoradlessDashboard();

                $ctc_count = $sprint->getFirstMileSprintCounts($sprintsIds);

                foreach ($storeName as $range_date) {


                    $startOfCurrentWeek = Carbon::now()->startOfWeek();
                    $startOfLastWeek = $startOfCurrentWeek->copy()->subDays(5)->format('Y-m-d');
                    $currentDate = Carbon::now()->format('Y-m-d');

                    $start_dt = new DateTime($startOfLastWeek." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($currentDate." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');

                    $sprintsIds = $sprint_ids->where('store_name',$range_date)->pluck('sprint_id');

                    $sprint = new BoradlessDashboard();
                    $ctc_range_count[$range_date] = $sprint->getFirstMileSprintCounts($sprintsIds);


                }
            }
            elseif ($request['days'] == '1days')
            {

                $currentDate = Carbon::now()->format('Y-m-d');

                $start_dt = new DateTime($currentDate." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($currentDate." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $sprint_ids = $first_mile_data->where('created_at','>',$start)
                    ->where('created_at','<',$end)->distinct('store_name');

                $sprintsIds = $sprint_ids->pluck('sprint_id');
                $storeName = $sprint_ids->pluck('store_name');

                $sprint = new BoradlessDashboard();

                $ctc_count = $sprint->getFirstMileSprintCounts($sprintsIds);

                foreach ($storeName as $range_date) {


                    $sprintsIds = $sprint_ids->where('store_name',$range_date)->pluck('sprint_id');
                    $sprint = new BoradlessDashboard();
                    $ctc_range_count[$range_date] = $sprint->getFirstMileSprintCounts($sprintsIds);

                }
            }
        }
        else
        {

            $currentDate = Carbon::now()->format('Y-m-d');

            $start_dt = new DateTime($currentDate." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($currentDate." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            $sprint_ids = $first_mile_data->where('created_at','>',$start)
                ->where('created_at','<',$end)->distinct('store_name');

            $sprintsIds = $sprint_ids->pluck('sprint_id');
            $storeName = $sprint_ids->pluck('store_name');

            $sprint = new BoradlessDashboard();

            $ctc_count = $sprint->getFirstMileSprintCounts($sprintsIds);

            foreach ($storeName as $range_date) {

                $start_dt = new DateTime($currentDate." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($currentDate." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $sprintsIds = $sprint_ids->where('store_name',$range_date)->pluck('sprint_id');
                $sprint = new BoradlessDashboard();

                $ctc_range_count[$range_date] = $sprint->getFirstMileSprintCounts($sprintsIds);


            }
        }



        return backend_view('first_mile.first_mile_reporting', compact('selectDateFilter','ctc_count', 'ctc_range_count','city','start','end'));
    }

    /**
     * Get CTC Route Info
     */
    public function getFirstMileReportingData(Datatables $datatables, Request $request)
    {

        if ($request->ajax()) {

            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $data_for = $request->data_for;
            $sprint_id_data = $request->sprint_id_data;

            //$ctcVendorIds = CtcVendor::pluck('vendor_id');

            $ctcVendorIds = HubStore::where('hub_id', auth()->user()->hub_id)->pluck('vendor_id')->toArray();

            $sprint_id_data = str_replace('[','',$sprint_id_data);
            $sprint_id_data = str_replace(']','',$sprint_id_data);
            $sprint_id_data1= array_map('intval', explode(',', $sprint_id_data));

            if ($data_for == 'at-store')
            {
                $query = BoradlessDashboard::whereIn('creator_id', $ctcVendorIds)
                    ->whereIn('sprint_id', $sprint_id_data1)
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereNotIn('task_status_id', [38, 36])
                    ->where('task_status_id', 61)
                    ->groupBy('sprint_id')
                    ->select(['boradless_dashboard.*','task_status_id as tasks_history_status_id']);
            }
            elseif ($data_for == 'total_orders')
            {
                $query = BoradlessDashboard::whereIn('creator_id', $ctcVendorIds)
                    ->whereIn('sprint_id', $sprint_id_data1)
                    ->whereBetween('created_at', [$from_date, $to_date])
                    ->whereNotIn('task_status_id', [38, 36])
                    ->groupBy('sprint_id')
                    ->select(['boradless_dashboard.*','task_status_id as tasks_history_status_id']);

            }
            else
            {
                $query = BoradlessDashboard:: join('sprint__tasks_history', 'sprint__tasks_history.sprint_id', '=', 'boradless_dashboard.sprint_id')
                    ->whereIn('boradless_dashboard.creator_id', $ctcVendorIds)
                    ->whereIn('boradless_dashboard.sprint_id', $sprint_id_data1)
                    ->whereBetween('boradless_dashboard.created_at', [$from_date, $to_date])
                    ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
                    ->groupBy('sprint__tasks_history.sprint_id','boradless_dashboard.sprint_id')
                    ->select(['boradless_dashboard.*','sprint__tasks_history.status_id as tasks_history_status_id']);
            }


            $sprint = new BoradlessDashboard();
            // using fillters
            ($data_for == 'picked-up') ? $query->where('sprint__tasks_history.status_id', 125) : $query;
            ($data_for == 'at-hub') ? $query->whereIn('sprint__tasks_history.status_id', [124]) : $query;
            //($data_for == 'at-store') ? $query->where('sprint__tasks_history.status_id', 61) : $query;
            ($data_for == 'sorted-order') ? $query->where('sprint__tasks_history.status_id', 133) : $query;
            ($data_for == 'out-for-delivery') ? $query->where('sprint__tasks_history.status_id', 121) : $query;
            ($data_for == 'delivered-order') ? $query->whereIn('sprint__tasks_history.status_id', $sprint->getStatusCodes('competed')) : $query;
            ($data_for == 'returned') ? $query->whereIn('sprint__tasks_history.status_id', $sprint->getStatusCodes('return'))->where('sprint__tasks_history.status_id', '!=', 111) : $query;
            ($data_for == 'returned-to-merchant') ? $query->where('sprint__tasks_history.status_id', 111) : $query;

            return $datatables->eloquent($query)
                ->editColumn('status_id', static function ($record) {
                    return self::$status[$record->tasks_history_status_id];
                })
                ->addColumn('tracking_id', static function ($record) {
                    if ($record->tracking_id) {
                        return substr($record->tracking_id, strrpos($record->tracking_id, '_') + 0);
                    } else {
                        "";
                    }
                })
                ->addColumn('store_name', static function ($record) {
                    if ($record->store_name) {
                        return $record->store_name;
                    } else {
                        "";
                    }
                })
               ->addColumn('picked_up_at', static function ($record) {

                    return isset($record->JoeyNameRelation) ? $record->JoeyNameRelation->created_at : '';

                })
                ->addColumn('delivered_at', static function ($record) {

                    if (isset($record->JoeyNameRelation->deleted_at))
                    {
                        if (!is_null($record->JoeyNameRelation->deleted_at))
                        {
                            return $record->JoeyNameRelation->deleted_at;
                        }
                        else
                        {
                            return '';
                        }
                    }
                    else
                    {
                        return '';
                    }


                })
                ->addColumn('joey_name', static function ($record) {

                    return isset($record->JoeyNameRelation->JoeyName) ? $record->JoeyNameRelation->JoeyName->full_name : '';

                })
                ->editColumn('created_at', static function ($record) {
                    return (new \DateTime($record->created_at))->setTimezone(new \DateTimeZone('America/Toronto'))->format('Y-m-d H:i:s');
                })
                ->make(true);

        }
    }

    public function get_trackingorderdetails($sprintId)
    {
        $result = Sprint::join('sprint__tasks', 'sprint_id', '=', 'sprint__sprints.id')
            ->leftJoin('merchantids', 'merchantids.task_id', '=', 'sprint__tasks.id')
            ->leftJoin('joey_route_locations', 'joey_route_locations.task_id', '=', 'sprint__tasks.id')
            ->leftJoin('joey_routes', 'joey_routes.id', '=', 'joey_route_locations.route_id')
            ->leftJoin('joeys', 'joeys.id', '=', 'joey_routes.joey_id')
            ->join('locations', 'sprint__tasks.location_id', '=', 'locations.id')
            ->join('sprint__contacts', 'contact_id', '=', 'sprint__contacts.id')
            ->leftJoin('vendors', 'creator_id', '=', 'vendors.id')
            ->where('sprint__tasks.sprint_id', '=', $sprintId)
            ->whereNull('joey_route_locations.deleted_at')
            ->orderBy('ordinal', 'DESC')->take(1)
            ->get(array('sprint__tasks.*', 'joey_routes.id as route_id',\DB::raw("CONVERT_TZ(joey_routes.date,'UTC','America/Toronto') as route_date"), 'locations.address', 'locations.suite', 'locations.postal_code', 'sprint__contacts.name', 'sprint__contacts.phone', 'sprint__contacts.email',
                'joeys.first_name as joey_firstname', 'joeys.id as joey_id',
                'joeys.last_name as joey_lastname', 'vendors.first_name as merchant_firstname', 'vendors.last_name as merchant_lastname', 'merchantids.scheduled_duetime'
            , 'joeys.id as joey_id', 'merchantids.tracking_id', 'joeys.phone as joey_contact', 'joey_route_locations.ordinal as stop_number', 'merchantids.merchant_order_num', 'merchantids.address_line2', 'sprint__sprints.creator_id'));

        $i = 0;

        $data = [];

        foreach ($result as $tasks) {
            $status2 = array();
            $status = array();
            $status1 = array();
            $data[$i] = $tasks;
            $taskHistory = TaskHistory::where('sprint_id', '=', $tasks->sprint_id)->WhereNotIn('status_id', [17, 38])->orderBy('date')
                //->where('active','=',1)
                ->get(['status_id', \DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto') as created_at")]);

            $returnTOHubDate = SprintReattempt::
            where('sprint_reattempts.sprint_id', '=', $tasks->sprint_id)->orderBy('created_at')
                ->first();

            if (!empty($returnTOHubDate)) {
                $taskHistoryre = TaskHistory::where('sprint_id', '=', $returnTOHubDate->reattempt_of)->WhereNotIn('status_id', [17, 38])->orderBy('date')
                    //->where('active','=',1)
                    ->get(['status_id', \DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto') as created_at")]);

                foreach ($taskHistoryre as $history) {

                    $status[$history->status_id]['id'] = $history->status_id;
                    if ($history->status_id == 13) {
                        $status[$history->status_id]['description'] = 'At hub - processing';
                    } else {
                        $status[$history->status_id]['description'] = $this->statusmap($history->status_id);
                    }
                    $status[$history->status_id]['created_at'] = $history->created_at;

                }

            }
            if (!empty($returnTOHubDate)) {
                $returnTO2 = SprintReattempt::
                where('sprint_reattempts.sprint_id', '=', $returnTOHubDate->reattempt_of)->orderBy('created_at')
                    ->first();

                if (!empty($returnTO2)) {
                    $taskHistoryre = TaskHistory::where('sprint_id', '=', $returnTO2->reattempt_of)->WhereNotIn('status_id', [17, 38])->orderBy('date')
                        //->where('active','=',1)
                        ->get(['status_id', \DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto') as created_at")]);

                    foreach ($taskHistoryre as $history) {

                        $status2[$history->status_id]['id'] = $history->status_id;
                        if ($history->status_id == 13) {
                            $status2[$history->status_id]['description'] = 'At hub - processing';
                        } else {
                            $status2[$history->status_id]['description'] = $this->statusmap($history->status_id);
                        }
                        $status2[$history->status_id]['created_at'] = $history->created_at;

                    }

                }
            }

            //    dd($taskHistory);

            foreach ($taskHistory as $history) {
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

            if ($status != null) {
                $sort_key = array_column($status, 'created_at');
                array_multisort($sort_key, SORT_ASC, $status);
            }
            if ($status1 != null) {
                $sort_key = array_column($status1, 'created_at');
                array_multisort($sort_key, SORT_ASC, $status1);
            }
            if ($status2 != null) {
                $sort_key = array_column($status2, 'created_at');
                array_multisort($sort_key, SORT_ASC, $status2);
            }


            $data[$i]['status'] = $status;
            $data[$i]['status1'] = $status1;
            $data[$i]['status2'] = $status2;
            $i++;
        }


        return ['data' => $data, 'sprintId' => $sprintId];
        // return backend_view('orderdetailswtracknigid',['data'=>$data,'sprintId' => $sprintId,'reasons' => $reasons]);
    }

    public function getCtcProfile(Request $request, $id)
    {
        $ctc_id = $id;

        $data = $this->get_trackingorderdetails($ctc_id);

        $sprintId = $data['sprintId'];

        $data = $data['data'];
        return backend_view('ctc-entries.order_profile', compact('data', 'sprintId'));
    }

    public function getCtcExcel($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');
        }


        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');
        $postal_code = SlotPostalCode::whereIn('zone_id',[246,82,83])->pluck('postal_code')->toArray();
        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        $ctcVendorIds = CtcVendor::pluck('vendor_id');
        $ctc_data = CTCEntry::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereIn('boradless_dashboard.creator_id', $ctcVendorIds)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn('locations.postal_code',$postal_code)
            ->get();
        $ctc_array[] = ['JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Out For Delivery', 'Sorted Time', 'Actual Arrival @ CX', 'CTC tracking #', 'Status'];

        foreach ($ctc_data as $ctc) {
            $picked_up_at = '';
            $sorted_at = '';
            $delivered_at = '';
            if ($ctc->picked_up_at) {
                $picked_up_at = new \DateTime($ctc->picked_up_at, new \DateTimeZone('UTC'));
                $picked_up_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                $picked_up_at->format('Y-m-d H:i:s');
            }
            if ($ctc->sorted_at) {
                $sorted_at = new \DateTime($ctc->sorted_at, new \DateTimeZone('UTC'));
                $sorted_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                $sorted_at->format('Y-m-d H:i:s');
            }
            if ($ctc->delivered_at) {
                $delivered_at = new \DateTime($ctc->delivered_at, new \DateTimeZone('UTC'));
                $delivered_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                $delivered_at->format('Y-m-d H:i:s');
            }
            $ctc_array[] = [
                'JoeyCo Order #' => strval($ctc->sprint_id),
                'Route Number' => $ctc->route_id ? strval('R-' . $ctc->route_id . '-' . $ctc->ordinal) : '',
                'Joey' => $ctc->joey_name ? strval($ctc->joey_name . ' (' . $ctc->joey_id . ')') : '',
                'Customer Address' => strval($ctc->address_line_1),
                'Out For Delivery' => $picked_up_at,
                'Sorter Time' => $sorted_at,
                'Actual Arrival @ CX' => $delivered_at,
                'CTC tracking #' => strval(substr($ctc->tracking_id, ($pos = strrpos($ctc->tracking_id, '_')) == false ? 0 : $pos + 1)),
                'Status' => $ctc->task_status_id ? strval(self::$status[$ctc->task_status_id]) :''
            ];
        }

        Excel::create('CTC Data ' . $date . '', function ($excel) use ($ctc_array) {
            $excel->setTitle('CTC Data');
            $excel->sheet('CTC Data', function ($sheet) use ($ctc_array) {
                $sheet->fromArray($ctc_array, null, 'A1', false, false);
            });
        })->download('csv');
    }

}
