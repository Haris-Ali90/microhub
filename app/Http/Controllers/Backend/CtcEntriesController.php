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
use App\JoeyRouteLocations;
use App\JoeyRoutes;
use App\Locations;
use App\MicroHubPostalCodes;
use App\Reason;
use App\SlotPostalCode;
use App\SlotsPostalCode;
use App\Sprint;
use App\MerchantIds;
use App\SprintReattempt;
use App\SprintTaskHistory;
use App\TaskHistory;
use App\TrackingDelay;
use App\TrackingNote;
use App\UserDevice;
use App\UserNotification;
use App\ZoneRouting;
use Illuminate\Http\Request;
use App\Ctc;
use App\Task;
use App\Notes;
use App\Ctc_count;
use App\CtcVendor;
use App\MicroHubZones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
//use Illuminate\Database\Eloquent\Builder;

class CtcEntriesController extends BackendController
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
        '153' => 'Miss sorted to be reattempt',
        '154' => 'Joey unable to complete the route', '155' => 'To be re-attempted tomorrow');

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
            '153' => 'Miss sorted to be reattempt',
            '154' => 'Joey unable to complete the route', '155' => 'To be re-attempted tomorrow');
        return $statusid[$id];
    }

    /**
     * Get CTC Dashboard
     */
    public function getCtcDashboardBroker(Request $request)
    {

        $city = !empty($request->get('city')) ? $request->get('city') : 'all';
        $status_code = array_intersect_key(self::$status, [61 => '', 124 => '', 121 => '', 133 => '', 17 => '', 113 => '', 114 => '', 116 => '', 117 => '', 118 => '', 132 => '', 138 => '', 139 => '', 144 => '', 104 => '', 105 => '', 106 => '', 107 => '',
            108 => '', 109 => '', 110 => '', 111 => '', 112 => '', 131 => '', 135 => '', 136 => '']);
        return backend_view('ctc-entries.ctc_dashboard-broker', compact('status_code','city'));
    }

    /**
     * Yajra call after  CTC Dashboard
     */
    public function getCtcDashboardBrokerData(Datatables $datatables, Request $request)
    {
        $sprintId = 0;
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");

        $city_data = $request->city;
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $ctcVendorIds = CtcVendor::pluck('vendor_id');

        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if (!empty($request->get('tracking_id'))) {
            $task_id = MerchantIds::where('tracking_id', $request->get('tracking_id'))->where('deleted_at', null)->first();
            if ($task_id) {
                $sprint = Task::where('id', $task_id->task_id)->first();
                $sprintId = $sprint->sprint_id;
            }
        }
        //dd($sprintId);
        if (!empty($request->get('route_id'))) {
            $task_ids = JoeyRouteLocations::where('route_id', $request->get('route_id'))->where('deleted_at', null)->pluck('task_id');

            if ($task_ids) {
                $sprintIds = Task::whereIn('id', $task_ids)->pluck('sprint_id');
            }
        }
        if (!empty($request->get('tracking_id'))) {
            $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
                ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
                ->where('boradless_dashboard.sprint_id', $sprintId)
                ->where('boradless_dashboard.deleted_at', null)
                ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
                ->distinct()
                ->select(['boradless_dashboard.*'
                ]);
        } else if (!empty($request->get('route_id'))) {
            $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
                ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
                ->whereIn('boradless_dashboard.sprint_id', $sprintIds)
                ->where('boradless_dashboard.deleted_at', null)
                ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
                ->distinct()
                ->select(['boradless_dashboard.*'
                ]);
        } else {
            // $ctcVendorIds = CtcVendor::pluck('vendor_id');
            $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
                ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
                ->whereIn('boradless_dashboard.creator_id', $ctcVendorIds)->where('boradless_dashboard.created_at','>',$start)->where('boradless_dashboard.created_at','<',$end)
                ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
                ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
                ->distinct()
                ->select(['boradless_dashboard.*'
                ]);
        }

        if (!empty($request->get('status'))) {
            $sprint_status = new Sprint();
            if ($request->get('status') == 1) {
                $statusIds = $sprint_status->getStatusCodes('competed');
            } elseif ($request->get('status') == 2) {
                $statusIds = $sprint_status->getStatusCodes('return');
            } else {
                $statusIds = [$request->get('status')];
            }

            $query = $query->whereIn('task_status_id', $statusIds);
        }
        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->addColumn('sprint_id', static function ($record) {
                return $record->sprint_id ?  $record->sprint_id : '';
            })
            ->editColumn('task_status_id', static function ($record) {
                $current_status = $record->task_status_id;
                if ($record->task_status_id == 17) {
                    $preStatus = \App\SprintTaskHistory
                        ::where('sprint_id', '=', $record->sprint_id)
                        ->where('status_id', '!=', '17')
                        ->orderBy('id', 'desc')->first();
                    if (!empty($preStatus)) {
                        $current_status = $preStatus->status_id;
                    }
                }
                if ($current_status == 13) {
                    return "At hub - processing";
                } else {
                    return self::$status[$current_status];
                }
            })
            ->addColumn('route_id', static function ($record) {
                return $record->route_id ? 'R-' . $record->route_id . '-' . $record->ordinal : '';
            })
            ->addColumn('joey_name', static function ($record) {
                return $record->joey_name ? $record->joey_name . ' (' . $record->joey_id . ')' : '';
            })
            ->addColumn('tracking_id', static function ($record) {
                return substr($record->tracking_id, ($pos = strrpos($record->tracking_id, '_')) == false ? 0 : $pos + 1);
            })
            ->addColumn('eta_time', static function ($record) {
                if ($record->eta_time){
                    $eta_time = new \DateTime(date('Y-m-d H:i:s', strtotime("+1 day", $record->eta_time)), new \DateTimeZone('UTC'));
                    $eta_time->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $eta_time->format('Y-m-d H:i:s');
                }
            })
            ->addColumn('store_name', static function ($record) {
                return $record->store_name ? $record->store_name : '';
            })
            ->addColumn('customer_name', static function ($record) {
                return $record->customer_name ? $record->customer_name : '';
            })
            ->addColumn('weight', static function ($record) {
                return $record->weight ? $record->weight : '';
            })
            ->addColumn('address_line_2', static function ($record) {
                if(isset($record->address_line_1))
                {
                    return $record->address_line_1;
                }
                elseif (isset($record->address_line_2))
                {
                    return $record->address_line_2;
                }
                else
                {
                    return $record->address_line_3 ? $record->address_line_3 : '';
                }
            })
            ->addColumn('action', static function ($record) {
                return backend_view('ctc-entries.broker-action', compact('record'));
            })
            ->make(true);

    }

    /**
     * Get CTC Order detail
     */
    public function ctcBrokerProfile(Request $request, $id)
    {
        $ctc_data = $this->get_trackingorderdetails($id);
        $sprintId = $ctc_data['sprintId'];
        $data = $ctc_data['data'];
        return backend_view('ctc-entries.ctc_broker_profile', compact('data', 'sprintId'));
    }

    /**
     * Get CTC Dashboard
     */
    public function getCtcDashboard(Request $request)
    {

        $city = !empty($request->get('city')) ? $request->get('city') : 'all';
        $status_code = array_intersect_key(self::$status, [61 => '', 124 => '', 121 => '', 133 => '', 17 => '', 113 => '', 114 => '', 116 => '', 117 => '', 118 => '', 132 => '', 138 => '', 139 => '', 144 => '', 104 => '', 105 => '', 106 => '', 107 => '',
            108 => '', 109 => '', 110 => '', 111 => '', 112 => '', 131 => '', 135 => '', 136 => '']);
        return backend_view('ctc-entries.ctc_dashboard', compact('status_code','city'));
    }

    /**
     * Yajra call after  CTC Dashboard
     */
    public function getCtcDashboardData(Datatables $datatables, Request $request)
    {

        $sprintId = 0;
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");

        $city_data = $request->city;
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->whereNull('deleted_at')->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $ctcVendorIds = CtcVendor::pluck('vendor_id');
        //dd($postal_code);
        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        if (!empty($request->get('tracking_id'))) {
            $task_id = MerchantIds::where('tracking_id', $request->get('tracking_id'))->where('deleted_at', null)->first();
            if ($task_id) {
                $sprint = Task::where('id', $task_id->task_id)->first();
                $sprintId = $sprint->sprint_id;
            }
        }
        //dd($sprintId);
        if (!empty($request->get('route_id'))) {
            $task_ids = JoeyRouteLocations::where('route_id', $request->get('route_id'))->where('deleted_at', null)->pluck('task_id');

            if ($task_ids) {
                $sprintIds = Task::whereIn('id', $task_ids)->pluck('sprint_id');
            }
        }
        if (!empty($request->get('tracking_id'))) {
            $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
                ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
                ->whereIn('creator_id', $boradlessVendorIds)
                ->where('boradless_dashboard.sprint_id', $sprintId)
                ->where('boradless_dashboard.deleted_at', null)
                ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
                ->distinct()
                ->select(['boradless_dashboard.*'
                ]);
        }
        else if (!empty($request->get('route_id')))
        {

            $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
                ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
                ->whereIn('creator_id', $boradlessVendorIds)
                ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
                ->whereIn('boradless_dashboard.sprint_id', $sprintIds)
                ->where('boradless_dashboard.deleted_at', null)
                ->distinct()
                ->select(['boradless_dashboard.*'
                ]);
        }
        else {

            // $ctcVendorIds = CtcVendor::pluck('vendor_id');
            $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
                ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
                ->whereIn('creator_id', $boradlessVendorIds)
                ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
                //->whereIn('boradless_dashboard.creator_id', $ctcVendorIds)
                ->where('boradless_dashboard.created_at','>',$start)
                ->where('boradless_dashboard.created_at','<',$end)
                ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
                ->distinct()
                ->select(['boradless_dashboard.*'
                ]);
        }
        if (!empty($request->get('status'))) {
            $sprint_status = new Sprint();
            if ($request->get('status') == 1) {
                $statusIds = $sprint_status->getStatusCodes('competed');
            } elseif ($request->get('status') == 2) {
                $statusIds = $sprint_status->getStatusCodes('return');
            } else {
                $statusIds = [$request->get('status')];
            }

            $query = $query->whereIn('task_status_id', $statusIds);
        }
        /*$task_ids = $query->pluck('task_id')->toArray();
        $taskData = Task::whereIn('id',$task_ids)->pluck('location_id')->toArray();
        $location = Locations::where('id',$taskData)->pluck('postal_code')->toArray();*/

        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {

                return $record->id;
            })
            ->addColumn('sprint_id', static function ($record) {
                return $record->sprint_id ?  $record->sprint_id : '';
            })
            ->editColumn('task_status_id', static function ($record) {
                $current_status = $record->task_status_id;
                if ($record->task_status_id == 17) {
                    $preStatus = \App\SprintTaskHistory
                        ::where('sprint_id', '=', $record->sprint_id)
                        ->where('status_id', '!=', '17')
                        ->orderBy('id', 'desc')->first();
                    if (!empty($preStatus)) {
                        $current_status = $preStatus->status_id;
                    }
                }
                if ($current_status == 13) {
                    return "At hub - processing";
                } else {
                    return self::$status[$current_status];
                }
            })
            ->addColumn('route_id', static function ($record) {
                return $record->route_id ? 'R-' . $record->route_id . '-' . $record->ordinal : '';
            })
            ->addColumn('joey_name', static function ($record) {
                return $record->joey_name ? $record->joey_name . ' (' . $record->joey_id . ')' : '';
            })
            ->addColumn('tracking_id', static function ($record) {
                return substr($record->tracking_id, ($pos = strrpos($record->tracking_id, '_')) == false ? 0 : $pos + 1);
            })
            ->addColumn('eta_time', static function ($record) {
                if ($record->eta_time){
                    $eta_time = new \DateTime(date('Y-m-d H:i:s', strtotime("+1 day", $record->eta_time)), new \DateTimeZone('UTC'));
                    $eta_time->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $eta_time->format('Y-m-d H:i:s');
                }
            })
            ->addColumn('store_name', static function ($record) {
                return $record->store_name ? $record->store_name : '';
            })
            ->addColumn('customer_name', static function ($record) {
                return $record->customer_name ? $record->customer_name : '';
            })
            ->addColumn('weight', static function ($record) {
                return $record->weight ? $record->weight : '';
            })
            ->addColumn('address_line_2', static function ($record) {
                if(isset($record->address_line_1))
                {
                    return $record->address_line_1;
                }
                elseif (isset($record->address_line_2))
                {
                    return $record->address_line_2;
                }
                else
                {
                    return $record->address_line_3 ? $record->address_line_3 : '';
                }
            })
            ->addColumn('action', static function ($record) {
                return backend_view('ctc-entries.action', compact('record'));
            })
            ->make(true);

    }

    /**
     * Get CTC Order detail
     */
    public function ctcProfile(Request $request, $id)
    {
        $ctc_data = $this->get_trackingorderdetails($id);
        $sprintId = $ctc_data['sprintId'];
        $data = $ctc_data['data'];
        return backend_view('ctc-entries.ctc_profile', compact('data', 'sprintId'));
    }

    /**
     * Get CTC Dashboard Excel Report
     */
    public function ctcDashboardExcel($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');

        }
        $file_name = new \DateTime($date);
        $file_name = $file_name->format("M d, Y");
        $file_name = "Last Mile Tracking File " . $file_name . ".csv";
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $ctcVendorIds = CtcVendor::pluck('vendor_id');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);


        $ctc_data = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.updated_at','>',$start)
            ->where('boradless_dashboard.updated_at','<',$end)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->get();
        //$ctc_data = Sprint::whereIn('id', $sprintIds)->where('deleted_at', null)->where('is_reattempt','=', 0)->get();
        // header info for browser


        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name);
        header('Pragma: no-cache');
        header('Expires: 0');

        // echo "JoeyCo Order\tRoute\tJoey\tStore Name\tCustomer Name\tCustomer Address\tPostal Code\tCity Name\tWeight\tPickup From Store\t1st Attempt - At Hub Processing\t1st Attempt - Out For Delivery\t1st Attempt - Estimated Customer Delivery Time\t1st Attempt - Delivery\t1st Attempt - Shipment Delivery Status\t2nd Attempt - At Hub Processing\t2nd Attempt - Out For Delivery\t2nd Attempt - Estimated Customer Delivery Time\t2nd Attempt - Delivery\t2nd Attempt - Shipment Delivery Status\t3rd Attempt - At Hub Processing\t3rd Attempt - Out For Delivery\t3rd Attempt - Estimated Customer Delivery Time\t3rd Attempt - Delivery\t3rd Attempt - Shipment Delivery Status\tShipment Tracking #\tActual Delivery Status\tActual Delivery\tShipment Tracking Link\tJoyeCo Notes / Comments\t\n";
        echo "JoeyCo Order,Route,Joey,Store Name,Customer Name,Customer Address,Postal Code,City Name,Weight,Pickup From Store,1st Attempt - At Hub Processing,1st Attempt - Out For Delivery,1st Attempt - Estimated Customer Delivery Time,1st Attempt - Delivery,1st Attempt - Shipment Delivery Status,2nd Attempt - At Hub Processing,2nd Attempt - Out For Delivery,2nd Attempt - Estimated Customer Delivery Time,2nd Attempt - Delivery,2nd Attempt - Shipment Delivery Status,3rd Attempt - At Hub Processing,3rd Attempt - Out For Delivery,3rd Attempt - Estimated Customer Delivery Time,3rd Attempt - Delivery,3rd Attempt - Shipment Delivery Status,Shipment Tracking #,Actual Delivery Status,Actual Delivery,Shipment Tracking Link,JoyeCo Notes / Comments,\n";

        // $ctc_array[] = ['Joeyco Order', 'Route', 'Joey', 'Store Name', 'Customer Name', 'Customer Address', 'Postal Code', 'City Name', 'Weight', 'Pickup From Store', 'At Hub Processing', 'Out For Delivery', 'Estimated Customer delivery time', 'Actual Customer delivery time', 'Shipment tracking #', 'Shipment tracking link', 'Shipment Delivery Status', 'JoyeCo Notes / Comments', 'Returned to HUB 2', '2nd Attempt Pick up', '2nd Attempt Delivery', 'Returned to HUB 3', '3rd Attempt Pick up', '3rd Attempt Delivery'];

        foreach ($ctc_data as $ctc_rec) {

            $ctc = null;
            if ($ctc_rec->sprintReattempts) {
                if ($ctc_rec->sprintReattempts->reattempts_left == 0) {
                    $ctc =  $firstSprint = BoradlessDashboard::where('sprint_id', '=', $ctc_rec->sprintReattempts->reattempt_of)->first();
                }
                else
                {
                    $ctc = $ctc_rec;
                }
            }
            else
            {
                $ctc = $ctc_rec;
            }
            $pickup3 = "";
            $hubreturned3 = "";
            $hubpickup3 = "";
            $deliver3 = "";
            $eta_time3 = "";
            $status3 = "";
            $pickup2 = "";
            $hubreturned2 = "";
            $hubpickup2 = "";
            $deliver2 = "";
            $eta_time2 = "";
            $status2 = "";
            $notes = '';
            $check_actual = false;
            $pickup = $ctc->pickupFromStore()->pickup;
            $hubreturned = "";//$ctc->atHubProcessing()->athub;
            $hubpickup = "";// $ctc->outForDelivery()->outdeliver;
            $deliver = "";//$ctc->deliveryTime()->delivery_time;
            $actual_delivery = $ctc->actualDeliveryTime()->actual_delivery;
            $actual_delivery_status = '';

            $eta_time = "";
            if ($pickup) {
                $eta_time = date('Y-m-d', strtotime("+1 day", strtotime($pickup))).' 21:00:00';
            }
            $status = $ctc->task_status_id;
            if ($ctc->task_status_id == 17) {
                $preStatus = \App\SprintTaskHistory::where('sprint_id', '=', $ctc->sprint_id)
                    ->where('status_id', '!=', '17')
                    ->orderBy('id', 'desc')->first();
                if (!empty($preStatus)) {
                    $status = $preStatus->status_id;
                }
            }
            if ($ctc->actualDeliveryTime()->actual_delivery != null) {
                $check_actual = true;
                $actual_delivery_status = $ctc->actualDeliveryTime()->status_id;

            }
            $notes1 = Notes::where('object_id', $ctc->sprint_id)->pluck('note');
            $i = 0;
            foreach ($notes1 as $note) {
                if ($i == 0)
                    $notes = $notes . $note;
                else
                    $notes = $notes . ', ' . $note;
            }
            if ($ctc->sprintReattempts) {
                if ($ctc->sprintReattempts->reattempts_left == 0) {

                    $hubreturned3 = $ctc->atHubProcessing()->athub;
                    $hubpickup3 = $ctc->outForDelivery()->outdeliver;
                    $deliver3 = $ctc->deliveryTime()->delivery_time;
                    if ($hubreturned3) {
                        $eta_time3 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned3))).' 21:00:00';
                    }
                    $status3 = $ctc->task_status_id;
                    if ($ctc->task_status_id == 17) {
                        $preStatus = \App\SprintTaskHistory
                            ::where('sprint_id', '=', $ctc->sprint_id)
                            ->where('status_id', '!=', '17')
                            ->orderBy('id', 'desc')->first();
                        if (!empty($preStatus)) {
                            $status3 = $preStatus->status_id;
                        }
                    }

                    $secondAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $ctc->sprintReattempts->reattempt_of)->orderBy('created_at', 'ASC')
                        ->get(['status_id', \DB::raw("CONVERT_TZ(sprint__tasks_history.created_at,'UTC','America/Toronto') as created_at")]);
                    if (!empty($secondAttempt)) {

                        foreach ($secondAttempt as $secAttempt) {

                            if (in_array($secAttempt->status_id, [133])) {
                                $hubreturned2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if ($secAttempt->status_id == 121) {
                                $hubpickup2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if (in_array($secAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143,141])) {
                                $deliver2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }

                            $eta = BoradlessDashboard::where('sprint_id', $ctc->sprintReattempts->reattempt_of)->first();
                            if ($hubreturned2) {
                                $eta_time2 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned2))).' 21:00:00';
                            }
                            $status2 = $eta->task_status_id;
                            if ($eta->task_status_id == 17) {
                                $preStatus = \App\SprintTaskHistory
                                    ::where('sprint_id', '=', $eta->sprint_id)
                                    ->where('status_id', '!=', '17')
                                    ->orderBy('id', 'desc')->first();
                                if (!empty($preStatus)) {
                                    $status2 = $preStatus->status_id;
                                }
                            }

                            if (in_array($secAttempt->status_id, [17,113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                if (!$check_actual) {
                                    $actual_delivery = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                                }
                            }
                            if (in_array($secAttempt->status_id, [113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                if (!$check_actual) {
                                    $actual_delivery_status = $secAttempt->status_id;

                                }
                            }

                        }
                    }

                    $firstSprint = \App\SprintReattempt::where('sprint_id', '=', $ctc->sprintReattempts->reattempt_of)->first();
                    if (!empty($firstSprint)) {
                        $firstAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $firstSprint->reattempt_of)->orderBy('created_at', 'ASC')->
                        get(['status_id', \DB::raw("CONVERT_TZ(sprint__tasks_history.created_at,'UTC','America/Toronto') as created_at")]);
                        if (!empty($firstAttempt)) {

                            foreach ($firstAttempt as $firstAttempt) {
                                if ($firstAttempt->status_id == 125) {
                                    $pickup = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                if (in_array($firstAttempt->status_id, [124])) {
                                    $hubreturned = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                if ($firstAttempt->status_id == 121) {
                                    $hubpickup = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                if (in_array($firstAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143,141])) {
                                    $deliver = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                /* if (in_array($firstAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 104, 105, 140, 110])) {
                                     $actual_delivery = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                 }*/
                                $eta = BoradlessDashboard::where('sprint_id', $firstSprint->reattempt_of)->first();
                                if ($pickup) {
                                    $eta_time = date('Y-m-d', strtotime("+1 day", strtotime($pickup))).' 21:00:00';
                                }
                                $status = $eta->task_status_id;
                                if ($eta->task_status_id == 17) {
                                    $preStatus = \App\SprintTaskHistory
                                        ::where('sprint_id', '=', $eta->sprint_id)
                                        ->where('status_id', '!=', '17')
                                        ->orderBy('id', 'desc')->first();
                                    if (!empty($preStatus)) {
                                        $status = $preStatus->status_id;
                                    }
                                }
                            }

                        }
                    }
                }
                if ($ctc->sprintReattempts->reattempts_left == 1) {

                    $hubreturned3 = $ctc->atHubProcessing()->athub;
                    $hubpickup3 = $ctc->outForDelivery()->outdeliver;
                    $deliver3 = $ctc->deliveryTime()->delivery_time;
                    if ($hubreturned3) {
                        $eta_time3 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned3))).' 21:00:00';
                    }
                    $status3 = $ctc->task_status_id;
                    if ($ctc->task_status_id == 17) {
                        $preStatus = \App\SprintTaskHistory
                            ::where('sprint_id', '=', $ctc->sprint_id)
                            ->where('status_id', '!=', '17')
                            ->orderBy('id', 'desc')->first();
                        if (!empty($preStatus)) {
                            $status3 = $preStatus->status_id;
                        }
                    }

                    $secondAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $ctc->sprintReattempts->reattempt_of)->orderBy('created_at', 'ASC')
                        ->get(['status_id', \DB::raw("CONVERT_TZ(sprint__tasks_history.created_at,'UTC','America/Toronto') as created_at")]);
                    if (!empty($secondAttempt)) {

                        foreach ($secondAttempt as $secAttempt) {

                            if (in_array($secAttempt->status_id, [133])) {
                                $hubreturned2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if ($secAttempt->status_id == 121) {
                                $hubpickup2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if (in_array($secAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143,141])) {
                                $deliver2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }

                            $eta = BoradlessDashboard::where('sprint_id', $ctc->sprintReattempts->reattempt_of)->first();
                            if ($hubreturned2) {
                                $eta_time2 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned2))).' 21:00:00';
                            }
                            $status2 = $eta->task_status_id;
                            if ($eta->task_status_id == 17) {
                                $preStatus = \App\SprintTaskHistory
                                    ::where('sprint_id', '=', $eta->sprint_id)
                                    ->where('status_id', '!=', '17')
                                    ->orderBy('id', 'desc')->first();
                                if (!empty($preStatus)) {
                                    $status2 = $preStatus->status_id;
                                }
                            }

                            if (in_array($secAttempt->status_id, [17,113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                if (!$check_actual) {
                                    $actual_delivery = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                                }
                            }
                            if (in_array($secAttempt->status_id, [113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                if (!$check_actual) {
                                    $actual_delivery_status = $secAttempt->status_id;

                                }
                            }

                        }
                    }

                    $firstSprint = \App\SprintReattempt::where('sprint_id', '=', $ctc->sprintReattempts->reattempt_of)->first();
                    if (!empty($firstSprint)) {
                        $firstAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $firstSprint->reattempt_of)->orderBy('created_at', 'ASC')->
                        get(['status_id', \DB::raw("CONVERT_TZ(sprint__tasks_history.created_at,'UTC','America/Toronto') as created_at")]);
                        if (!empty($firstAttempt)) {

                            foreach ($firstAttempt as $firstAttempt) {
                                if ($firstAttempt->status_id == 125) {
                                    $pickup = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                if (in_array($firstAttempt->status_id, [124])) {
                                    $hubreturned = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                if ($firstAttempt->status_id == 121) {
                                    $hubpickup = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                if (in_array($firstAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143,141])) {
                                    $deliver = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                /* if (in_array($firstAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 104, 105, 140, 110])) {
                                     $actual_delivery = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                 }*/
                                $eta = BoradlessDashboard::where('sprint_id', $firstSprint->reattempt_of)->first();
                                if ($pickup) {
                                    $eta_time = date('Y-m-d', strtotime("+1 day", strtotime($pickup))).' 21:00:00';
                                }
                                $status = $eta->task_status_id;
                                if ($eta->task_status_id == 17) {
                                    $preStatus = \App\SprintTaskHistory
                                        ::where('sprint_id', '=', $eta->sprint_id)
                                        ->where('status_id', '!=', '17')
                                        ->orderBy('id', 'desc')->first();
                                    if (!empty($preStatus)) {
                                        $status = $preStatus->status_id;
                                    }
                                }
                            }

                        }
                    }
                }
                if ($ctc->sprintReattempts->reattempts_left == 2) {

                    $hubreturned2 = $ctc->atHubProcessing()->athub;
                    $hubpickup2 = $ctc->outForDelivery()->outdeliver;
                    $deliver2 = $ctc->deliveryTime()->delivery_time;

                    if ($hubreturned2) {
                        $eta_time2 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned2))).' 21:00:00';
                    }
                    $status2 = $ctc->task_status_id;
                    if ($ctc->task_status_id == 17) {
                        $preStatus = \App\SprintTaskHistory
                            ::where('sprint_id', '=', $ctc->sprint_id)
                            ->where('status_id', '!=', '17')
                            ->orderBy('id', 'desc')->first();
                        if (!empty($preStatus)) {
                            $status2 = $preStatus->status_id;
                        }
                    }

                    $secondAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $ctc->sprintReattempts->reattempt_of)->orderBy('created_at', 'ASC')->
                    get(['status_id', \DB::raw("CONVERT_TZ(sprint__tasks_history.created_at,'UTC','America/Toronto') as created_at")]);
                    if (!empty($secondAttempt)) {
                        date_default_timezone_set('America/Toronto');
                        foreach ($secondAttempt as $secAttempt) {
                            if ($secAttempt->status_id == 125) {
                                $pickup = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if (in_array($secAttempt->status_id, [124])) {
                                $hubreturned = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if ($secAttempt->status_id == 121) {
                                $hubpickup = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if (in_array($secAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143,141])) {
                                $deliver = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }

                            $eta = BoradlessDashboard::where('sprint_id', $ctc->sprintReattempts->reattempt_of)->first();
                            if ($pickup) {
                                $eta_time = date('Y-m-d', strtotime("+1 day", strtotime($pickup))).' 21:00:00';
                            }
                            $status = $eta->task_status_id;
                            if ($eta->task_status_id == 17) {
                                $preStatus = \App\SprintTaskHistory
                                    ::where('sprint_id', '=', $eta->sprint_id)
                                    ->where('status_id', '!=', '17')
                                    ->orderBy('id', 'desc')->first();
                                if (!empty($preStatus)) {
                                    $status = $preStatus->status_id;
                                }
                            }
                            if (in_array($secAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                if (!$check_actual) {
                                    $actual_delivery = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                                }
                            }
                            if (in_array($secAttempt->status_id, [113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                if (!$check_actual) {
                                    $actual_delivery_status = $secAttempt->status_id;
                                }
                            }
                        }
                    }
                }
            } else {
                $hubreturned = $ctc->atHubProcessingFirst()->athub;
                $hubpickup = $ctc->outForDelivery()->outdeliver;
                $deliver = $ctc->deliveryTime()->delivery_time;
            }

            echo $ctc->sprint_id . ",";

            if ($ctc->route_id) {
                echo 'R-' . $ctc->route_id . '-' . $ctc->ordinal . ",";
            } else {
                echo " " . ",";
            }

            if ($ctc->joey_name) {
                echo str_replace(",", "-", $ctc->joey_name . ' (' . $ctc->joey_id . ')') . ",";
            } else {
                echo "" . ",";
            }

            if ($ctc->store_name) {
                echo str_replace(",","-",$ctc->store_name ) . ",";
            } else {
                echo "" . ",";
            }

            if ($ctc->customer_name) {
                echo str_replace(",","-",$ctc->customer_name ) . ",";
            } else {
                echo "" . ",";
            }

            if ($ctc->sprintCtcTasks) {
                if ($ctc->sprintCtcTasks->taskMerchants) {
                    echo str_replace(",","-",$ctc->sprintCtcTasks->taskMerchants->address_line2 ) . ",";
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }

            if ($ctc->sprintCtcTasks) {
                if ($ctc->sprintCtcTasks->task_Location) {
                    echo str_replace(",","-",$ctc->sprintCtcTasks->task_Location->postal_code )  . ",";
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }
            if ($ctc->sprintCtcTasks) {
                if ($ctc->sprintCtcTasks->task_Location) {
                    if ($ctc->sprintCtcTasks->task_Location->city) {
                        echo str_replace(",","-",$ctc->sprintCtcTasks->task_Location->city->name )  . ",";
                    } else {
                        echo "" . ",";
                    }
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }

            if ($ctc->sprintCtcTasks) {
                if ($ctc->sprintCtcTasks->taskMerchants) {
                    echo $ctc->sprintCtcTasks->taskMerchants->weight . $ctc->sprintCtcTasks->taskMerchants->weight_unit . ",";
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }

            echo $pickup . ",";

            echo $hubreturned . ",";


            echo $hubpickup . ",";


            echo $eta_time . ",";


            echo $deliver . ",";

            if (!empty($status)) {
                echo ($status == 13) ? "At hub - processing" . "," : str_replace(",","-",self::$status[$status])  . ",";
            } else {
                echo "" . ",";
            }
            echo $hubreturned2 . ",";
            echo $hubpickup2 . ",";
            echo $eta_time2 . ",";
            echo $deliver2 . ",";
            if (!empty($status2)) {
                echo ($status2 == 13) ? "At hub - processing" . "," : str_replace(",","-",self::$status[$status2] ) . ",";
            } else {
                echo "" . ",";
            }
            echo $hubreturned3 . ",";
            echo $hubpickup3 . ",";
            echo $eta_time3 . ",";
            echo $deliver3 . ",";
            if (!empty($status3)) {
                echo ($status3 == 13) ? "At hub - processing" . "," : str_replace(",","-",self::$status[$status3] ) . ",";
            } else {
                echo "" . ",";
            }


            if ($ctc->tracking_id) {
                if (str_contains($ctc->tracking_id, 'old_')) {
                    echo substr($ctc->tracking_id, strrpos($ctc->tracking_id, '_') + 1) . ",";
                }
                else
                {
                    echo $ctc->tracking_id . ",";
                }
            } else {
                echo "" . ",";
            }
//            echo ($actual_delivery_status == 13) ? "At hub - processing"."\t" : self::$status[$actual_delivery_status] . "\t";
            if (!empty($actual_delivery_status)) {
                echo ($actual_delivery_status == 13) ? "At hub - processing" . "," : str_replace(",","-",self::$status[$actual_delivery_status])  . ",";
            } else {
                echo "" . ",";
            }
            echo $actual_delivery . ",";
            if ($ctc->tracking_id) {
                if (str_contains($ctc->tracking_id, 'old_')) {
                    echo "https://www.joeyco.com/track-order/" . substr($ctc->tracking_id, strrpos($ctc->tracking_id, '_') + 1) . ",";
                }
                else{
                    echo "https://www.joeyco.com/track-order/" .$ctc->tracking_id. ",";
                }
            } else {
                echo '' . ",";
            }


            echo $notes . ",";
            echo "\n";


        }

    }

    /**
     * Get CTC Dashboard Excel OTD Report
     */
    public function ctcDashboardExcelOtdReport($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');
            $otd_date = date('Y-m-d');
            $otd_date = date('Y-m-d', strtotime($otd_date . ' -1 days'));
        } else {
            $otd_date = $date;
            $otd_date = date('Y-m-d', strtotime($otd_date . ' -1 days'));
        }

        $start_dt = new DateTime($otd_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $end_dt = new DateTime($otd_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $file_name = new \DateTime($date);
        $file_name = $file_name->format("M d, Y");
        $file_name = "Last Mile Reporting OTD Report " . $file_name . ".csv";
        $sprint_id = SprintTaskHistory::where('created_at','>',$start)->where('created_at','<',$end)->where('status_id', 125)->pluck('sprint_id');

        $ctc_data = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->whereIn('boradless_dashboard.sprint_id', $sprint_id)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->get();
        //$ctc_data = Sprint::whereIn('id',$sprintIds)->where('deleted_at', null)->where('is_reattempt','=', 0)->get();
        // header info for browser


        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name);
        header('Pragma: no-cache');
        header('Expires: 0');

        // echo "Shipment Tracking #\tPickup From Store\t1st Attempt - At Hub Processing\t1st Attempt - Out For Delivery\t1st Attempt - Estimated Customer Delivery Time\t1st Attempt - Delivery\t1st Attempt - Shipment Delivery Status\t2nd Attempt - At Hub Processing\t2nd Attempt - Out For Delivery\t2nd Attempt - Estimated Customer Delivery Time\t2nd Attempt - Delivery\t2nd Attempt - Shipment Delivery Status\t3rd Attempt - At Hub Processing\t3rd Attempt - Out For Delivery\t3rd Attempt - Estimated Customer Delivery Time\t3rd Attempt - Delivery\t3rd Attempt - Shipment Delivery Status\tActual Delivery Status\tActual Delivery\tShipment Tracking Link\tJoyeCo Notes / Comments\t\n";

        echo "Shipment Tracking #,Pickup From Store,1st Attempt - At Hub Processing,1st Attempt - Out For Delivery,1st Attempt - Estimated Customer Delivery Time,1st Attempt - Delivery,1st Attempt - Shipment Delivery Status,2nd Attempt - At Hub Processing,2nd Attempt - Out For Delivery,2nd Attempt - Estimated Customer Delivery Time,2nd Attempt - Delivery,2nd Attempt - Shipment Delivery Status,3rd Attempt - At Hub Processing,3rd Attempt - Out For Delivery,3rd Attempt - Estimated Customer Delivery Time,3rd Attempt - Delivery,3rd Attempt - Shipment Delivery Status,Actual Delivery Status,Actual Delivery,Shipment Tracking Link,JoyeCo Notes / Comments,\n";

        // $ctc_array[] = ['Joeyco Order', 'Route', 'Joey', 'Store Name', 'Customer Name', 'Customer Address', 'Postal Code', 'City Name', 'Weight', 'Pickup From Store', 'At Hub Processing', 'Out For Delivery', 'Estimated Customer delivery time', 'Actual Customer delivery time', 'Shipment tracking #', 'Shipment tracking link', 'Shipment Delivery Status', 'JoyeCo Notes / Comments', 'Returned to HUB 2', '2nd Attempt Pick up', '2nd Attempt Delivery', 'Returned to HUB 3', '3rd Attempt Pick up', '3rd Attempt Delivery'];

        foreach ($ctc_data as $ctc) {
            $trackingid = '';
            if ($ctc->tracking_id) {
                if (strpos($ctc->tracking_id, 'old') !== false) {
                    $trackingid = substr($ctc->tracking_id, strrpos($ctc->tracking_id, '_') + 1);
                } else {
                    $trackingid = $ctc->tracking_id;
                }
            }
            if(!$ctc->sprintReattempts) {
                //$customer_route = CustomerRoutingTrackingId::where('tracking_id', $trackingid)->first();
                //if (!$customer_route) {
                if (date("Y-m-d", strtotime($ctc->pickupFromStoreOtd($otd_date)->pickup)) == $otd_date) {
                    $pickup3 = "";
                    $hubreturned3 = "";
                    $hubpickup3 = "";
                    $deliver3 = "";
                    $eta_time3 = "";
                    $status3 = "";
                    $pickup2 = "";
                    $hubreturned2 = "";
                    $hubpickup2 = "";
                    $deliver2 = "";
                    $eta_time2 = "";
                    $status2 = "";
                    $notes = '';
                    $pickup = $ctc->pickupFromStoreOtd($otd_date)->pickup;
                    $hubreturned = $ctc->atHubProcessingOtd()->athub;
                    $hubpickup = $ctc->outForDelivery()->outdeliver;
                    $deliver = $ctc->deliveryTimeOTD()->delivery_time;
                    $actual_delivery = $ctc->actualDeliveryTime()->actual_delivery;
                    $actual_delivery_status = '';

                    $eta_time = "";
                    if ($pickup) {
                        $eta_time = date('Y-m-d', strtotime("+1 day", strtotime($pickup))).' 21:00:00';
                    }
                    $status = $ctc->task_status_id;
                    if ($ctc->task_status_id == 17) {
                        $preStatus = \App\SprintTaskHistory
                            ::where('sprint_id', '=', $ctc->sprint_id)
                            ->where('status_id', '!=', '17')
                            ->orderBy('id', 'desc')->first();
                        if (!empty($preStatus)) {
                            $status = $preStatus->status_id;
                        }
                    }
                    if ($ctc->actualDeliveryTime()->actual_delivery != null) {
                        $actual_delivery_status = $ctc->actualDeliveryTime()->status_id;
                    }
                    $notes1 = Notes::where('object_id', $ctc->sprint_id)->pluck('note');
                    $i = 0;
                    foreach ($notes1 as $note) {
                        if ($i == 0)
                            $notes = $notes . $note;
                        else
                            $notes = $notes . ', ' . $note;
                    }
                    if ($ctc->sprintReattemptsOTD) {


                        $secondAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $ctc->sprintReattemptsOTD->sprint_id)->orderBy('created_at', 'ASC')
                            ->get(['status_id', \DB::raw("CONVERT_TZ(sprint__tasks_history.created_at,'UTC','America/Toronto') as created_at")]);
                        if (!empty($secondAttempt)) {

                            foreach ($secondAttempt as $secAttempt) {

                                /* if ($secAttempt->status_id == 125) {
                                     $pickup = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                                 }*/
                                if (in_array($secAttempt->status_id, [133])) {
                                    $hubreturned2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                                }
                                if ($secAttempt->status_id == 121) {
                                    $hubpickup2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                                }
                                if (in_array($secAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143,141])) {
                                    $deliver2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                                }
                                if (in_array($secAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                    $actual_delivery = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                                }
                                $eta = BoradlessDashboard::where('sprint_id', $ctc->sprintReattemptsOTD->sprint_id)->first();
                                if ($hubreturned2) {
                                    $eta_time2 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned2))).' 21:00:00';
                                }
                                $status2 = $eta->task_status_id;
                                if ($eta->task_status_id == 17) {
                                    $preStatus = \App\SprintTaskHistory
                                        ::where('sprint_id', '=', $eta->sprint_id)
                                        ->where('status_id', '!=', '17')
                                        ->orderBy('id', 'desc')->first();
                                    if (!empty($preStatus)) {
                                        $status2 = $preStatus->status_id;
                                    }
                                }
                                if (in_array($secAttempt->status_id, [113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                    $actual_delivery_status = $secAttempt->status_id;
                                }

                            }
                        }

                        $firstSprint = \App\SprintReattempt::where('reattempt_of', '=', $ctc->sprintReattemptsOTD->sprint_id)->first();
                        if (!empty($firstSprint)) {
                            $firstAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $firstSprint->sprint_id)->orderBy('created_at', 'ASC')->
                            get(['status_id', \DB::raw("CONVERT_TZ(sprint__tasks_history.created_at,'UTC','America/Toronto') as created_at")]);
                            if (!empty($firstAttempt)) {

                                foreach ($firstAttempt as $firstAttempt) {
                                    /* if ($firstAttempt->status_id == 125) {
                                         $pickup3 = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                     }*/
                                    if (in_array($firstAttempt->status_id, [133])) {
                                        $hubreturned3 = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                    }
                                    if ($firstAttempt->status_id == 121) {
                                        $hubpickup3 = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                    }
                                    if (in_array($firstAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143,141])) {
                                        $deliver3 = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                    }
                                    if (in_array($firstAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                        $actual_delivery = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                    }
                                    $eta = BoradlessDashboard::where('sprint_id', $firstSprint->sprint_id)->first();
                                    if ($hubreturned3) {
                                        $eta_time3 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned3))).' 21:00:00';
                                    }
                                    $status3 = $eta->task_status_id;
                                    if ($eta->task_status_id == 17) {
                                        $preStatus = \App\SprintTaskHistory
                                            ::where('sprint_id', '=', $eta->sprint_id)
                                            ->where('status_id', '!=', '17')
                                            ->orderBy('id', 'desc')->first();
                                        if (!empty($preStatus)) {
                                            $status3 = $preStatus->status_id;
                                        }
                                    }
                                    if (in_array($firstAttempt->status_id, [ 113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                        $actual_delivery_status = $firstAttempt->status_id;
                                    }
                                }

                            }
                        }


                    } else {
                        $hubreturned = $ctc->atHubProcessingFirst()->athub;
                    }
                    if ($ctc->tracking_id) {
                        if (strpos($ctc->tracking_id, 'old') !== false) {
                            echo substr($ctc->tracking_id, strrpos($ctc->tracking_id, '_') + 1) . ",";
                        } else {
                            echo $ctc->tracking_id . ",";
                        }
                    } else {
                        echo "" . ",";
                    }

                    echo $pickup . ",";
                    if (!empty($hubreturned)) {
                        echo $hubreturned . ",";
                    } else {
                        echo $ctc->atHubProcessingOtd()->athub . ",";
                    }

                    if (!empty($hubpickup)) {
                        echo $hubpickup . ",";
                    } else {
                        echo $ctc->outForDelivery()->outdeliver . ",";
                    }

                    echo $eta_time . ",";


                    if (!empty($deliver)) {
                        echo $deliver . ",";
                    } else {
                        echo $ctc->deliveryTimeOTD()->delivery_time . ",";
                    }
                    if (!empty($status)) {
                        echo ($status == 13) ? "At hub - processing" . "," : str_replace(",", "-", self::$status[$status]) . ",";
                    } else {
                        echo "" . ",";
                    }
                    echo $hubreturned2 . ",";
                    echo $hubpickup2 . ",";
                    echo $eta_time2 . ",";
                    echo $deliver2 . ",";
                    if (!empty($status2)) {
                        echo ($status2 == 13) ? "At hub - processing" . "," : str_replace(",", "-", self::$status[$status2]) . ",";
                    } else {
                        echo "" . ",";
                    }
                    echo $hubreturned3 . ",";
                    echo $hubpickup3 . ",";
                    echo $eta_time3 . ",";
                    echo $deliver3 . ",";
                    if (!empty($status3)) {
                        echo ($status3 == 13) ? "At hub - processing" . "," : str_replace(",", "-", self::$status[$status3]) . ",";
                    } else {
                        echo "" . ",";
                    }
                    if (!empty($actual_delivery_status)) {
                        echo ($actual_delivery_status == 13) ? "At hub - processing" . "," : str_replace(",", "-", self::$status[$actual_delivery_status]) . ",";
                    } else {
                        echo "" . ",";
                    }
                    echo $actual_delivery . ",";
                    if ($ctc->tracking_id) {
                        if (strpos($ctc->tracking_id, 'old') !== false) {
                            echo "https://www.joeyco.com/track-order/" . substr($ctc->tracking_id, strrpos($ctc->tracking_id, '_') + 1) . ",";
                        } else {
                            echo "https://www.joeyco.com/track-order/" . $ctc->tracking_id . ",";
                        }
                    } else {
                        echo '' . ",";
                    }


                    echo $notes . ",";
                    echo "\n";

                }
            }
        }
        //  }
    }


    public function ctcTotalCards($date, $type)
    {
        $response = [];
        $date = !empty($date) ? $date : date("Y-m-d");
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $ctcVendorIds = CtcVendor::pluck('vendor_id');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $taskIds = DB::table('boradless_dashboard')->join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->pluck('boradless_dashboard.task_id');

        $ctc = new BoradlessDashboard();
        $ctc_count = $ctc->getCtcCounts($taskIds, $type);
        $response['ctc_count'] = $ctc_count;
        return $response;
    }

    public function ctcInProgressOrders($date, $type)
    {
        $response = [];
        $date = !empty($date) ? $date : date("Y-m-d");

        $ctcVendorIds = CtcVendor::pluck('vendor_id');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $taskIds = DB::table('boradless_dashboard')->join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->pluck('boradless_dashboard.task_id');

        $ctc = new BoradlessDashboard();
        $ctc_count = $ctc->getInprogressOrders($taskIds, $type);
        $response['ctc_inprogess_count'] = $ctc_count;
        return $response;
    }

    public function getCtcYesterdayOrderData($date)
    {
        $response = [];
        $date = !empty($date) ? $date : date("Y-m-d");
        $ctcVendorIds = CtcVendor::pluck('vendor_id');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $yesterday_return_orders = DB::table('boradless_dashboard')
            ->whereIn('creator_id', $boradlessVendorIds)
            ->join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->join('sprint_reattempts', 'boradless_dashboard.sprint_id', '=', 'sprint_reattempts.sprint_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->count();
        $response['yesterday_return_orders'] = $yesterday_return_orders;
        return $response;
    }

    public function getCtcCustomRouteData($date)
    {
        $response = [];
        $date = !empty($date) ? $date : date("Y-m-d");
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $custom_route = DB::table('boradless_dashboard')->join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->where('boradless_dashboard.is_custom_route', 1)
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->count();
        $response['custom_route'] = $custom_route;
        return $response;
    }

    public function getCtcCards(Request $request)
    {
        $type = 'all';
        return backend_view('ctc-entries.ctc_card_dashboard', compact( 'type'));
    }


    public function getCtc(Request $request)
    {
        $type = 'total';
        return backend_view('ctc-entries.ctc_order_dashboard', compact( 'type'));
    }

    public function getCtcData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $ctcVendorIds = CtcVendor::pluck('vendor_id');

        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->select(['boradless_dashboard.*'
            ]);
        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('sprint_id', static function ($record) {
                return $record->sprint_id ;
            })
            ->editColumn('picked_up_at', static function ($record) {
                if ($record->picked_up_at) {
                    $picked_up_at = new \DateTime($record->picked_up_at, new \DateTimeZone('UTC'));
                    $picked_up_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $picked_up_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('sorted_at', static function ($record) {
                if ($record->sorted_at) {
                    $sorted_at = new \DateTime($record->sorted_at, new \DateTimeZone('UTC'));
                    $sorted_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $sorted_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('delivered_at', static function ($record) {
                if ($record->delivered_at) {
                    $delivered_at = new \DateTime($record->delivered_at, new \DateTimeZone('UTC'));
                    $delivered_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $delivered_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('joey_name', static function ($record) {
                return $record->joey_name ? $record->joey_name . ' (' . $record->joey_id . ')' : '';
            })
            ->editColumn('route_id', static function ($record) {
                return $record->route_id ? 'R-' . $record->route_id . '-' . $record->ordinal : '';
            })
            ->editColumn('task_status_id', static function ($record) {
                if ($record->task_status_id) {
                    return self::$status[$record->task_status_id];
                }
                else
                {
                    return '';
                }

            })
            ->editColumn('order_image', static function ($record) {
                if (isset($record->order_image)) {
                    return '<img onClick="ShowLightBox(this);" style = "width:50px;height:50px" src = "' . $record->order_image . '" />';
                } else {
                    return '';
                }
            })
            ->editColumn('tracking_id', static function ($record) {
                return substr($record->tracking_id, ($pos = strrpos($record->tracking_id, '_')) == false ? 0 : $pos + 1);
            })
            ->addColumn('action', static function ($record) {
                return backend_view('ctc-entries.order_action', compact('record'));
            })
            ->make(true);
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
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $ctc_data = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->get();
        $ctc_array[] = ['JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Out For Delivery', 'Sorted Time', 'Actual Arrival @ CX', 'Last Mile Reporting tracking #', 'Status'];

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
                'Last Mile tracking #' => strval(substr($ctc->tracking_id, ($pos = strrpos($ctc->tracking_id, '_')) == false ? 0 : $pos + 1)),
                'Status' => $ctc->task_status_id ? strval(self::$status[$ctc->task_status_id]) :''
            ];
        }

        Excel::create('Last Mile Reporting ' . $date . '', function ($excel) use ($ctc_array) {
            $excel->setTitle('Last Mile Reporting');
            $excel->sheet('Last Mile Reporting', function ($sheet) use ($ctc_array) {
                $sheet->fromArray($ctc_array, null, 'A1', false, false);
            });
        })->download('csv');
    }


    public function getCtcSorter(Request $request)
    {
        $title_name = 'Last Mile Reporting';
        $type = 'sorted';
        return backend_view('ctc-entries.sorted_order', compact('title_name',  'type'));
    }

    public function ctcSortedData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $ctcVendorIds = CtcVendor::pluck('vendor_id');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->where(['boradless_dashboard.task_status_id' => 133])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->select(['boradless_dashboard.*'
            ]);
        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('sprint_id', static function ($record) {
                return $record->sprint_id ;
            })
            ->editColumn('picked_up_at', static function ($record) {
                if ($record->picked_up_at) {
                    $picked_up_at = new \DateTime($record->picked_up_at, new \DateTimeZone('UTC'));
                    $picked_up_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $picked_up_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('sorted_at', static function ($record) {
                if ($record->sorted_at) {
                    $sorted_at = new \DateTime($record->sorted_at, new \DateTimeZone('UTC'));
                    $sorted_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $sorted_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('delivered_at', static function ($record) {
                if ($record->delivered_at) {
                    $delivered_at = new \DateTime($record->delivered_at, new \DateTimeZone('UTC'));
                    $delivered_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $delivered_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('joey_name', static function ($record) {
                return $record->joey_name ? $record->joey_name . ' (' . $record->joey_id . ')' : '';
            })
            ->editColumn('route_id', static function ($record) {
                return $record->route_id ? 'R-' . $record->route_id . '-' . $record->ordinal : '';
            })
            ->editColumn('task_status_id', static function ($record) {
                if ($record->task_status_id) {
                    return self::$status[$record->task_status_id];
                }
                else
                {
                    return '';
                }
            })
            ->editColumn('order_image', static function ($record) {
                if (isset($record->order_image)) {
                    return '<img onClick="ShowLightBox(this);" style = "width:50px;height:50px" src = "' . $record->order_image . '" />';
                } else {
                    return '';
                }
            })
            ->editColumn('tracking_id', static function ($record) {

                return substr($record->tracking_id, ($pos = strrpos($record->tracking_id, '_')) == false ? 0 : $pos + 1);

            })
            ->addColumn('action', static function ($record) {
                return backend_view('ctc-entries.action_sorted', compact('record'));
            })
            ->make(true);
    }

    public function ctcsortedDetail(Request $request, $id)
    {
        $ctc_id = base64_decode($id);
        $data = $this->get_trackingorderdetails($ctc_id);
        $sprintId = $data['sprintId'];
        $data = $data['data'];

        return backend_view('ctc-entries.ctc_sorted_detail', compact('data', 'sprintId'));
    }

    public function ctcSortedExcel($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');
        }
        $ctcVendorIds = CtcVendor::pluck('vendor_id');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $ctc_data = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->where(['boradless_dashboard.task_status_id' => 133])
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->get();
        $ctc_array[] = ['JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Out For Delivery', 'Sorted Time', 'Actual Arrival @ CX', 'Last Mile tracking #', 'Status'];
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
                'Last Mile tracking #' => strval(substr($ctc->tracking_id, ($pos = strrpos($ctc->tracking_id, '_')) == false ? 0 : $pos + 1)),
                'Status' => $ctc->task_status_id ? strval(self::$status[$ctc->task_status_id]):''
            ];

        }
        Excel::create('Last Mile Sorted Data ' . $date . '', function ($excel) use ($ctc_array) {
            $excel->setTitle('Last Mile Sorted Data');
            $excel->sheet('Last Mile Sorted Data', function ($sheet) use ($ctc_array) {
                $sheet->fromArray($ctc_array, null, 'A1', false, false);
            });
        })->download('csv');
    }


    public function getCtchub(Request $request)
    {
        $title_name = 'Last Mile Reporting';
        $type = 'picked';
        return backend_view('ctc-entries.pickup_hub', compact('title_name',  'type'));
    }

    public function ctcPickedUpData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $ctcVendorIds = CtcVendor::pluck('vendor_id');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->select(['boradless_dashboard.*'
            ])
            ->where(['task_status_id' => 121]);
        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('sprint_id', static function ($record) {
                return $record->sprint_id ;
            })
            ->editColumn('picked_up_at', static function ($record) {
                if ($record->picked_up_at) {
                    $picked_up_at = new \DateTime($record->picked_up_at, new \DateTimeZone('UTC'));
                    $picked_up_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $picked_up_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('sorted_at', static function ($record) {
                if ($record->sorted_at) {
                    $sorted_at = new \DateTime($record->sorted_at, new \DateTimeZone('UTC'));
                    $sorted_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $sorted_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('delivered_at', static function ($record) {
                if ($record->delivered_at) {
                    $delivered_at = new \DateTime($record->delivered_at, new \DateTimeZone('UTC'));
                    $delivered_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $delivered_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('joey_name', static function ($record) {
                return $record->joey_name ? $record->joey_name . ' (' . $record->joey_id . ')' : '';
            })
            ->editColumn('route_id', static function ($record) {
                return $record->route_id ? 'R-' . $record->route_id . '-' . $record->ordinal : '';
            })
            ->editColumn('task_status_id', static function ($record) {
                if ($record->task_status_id) {
                    return self::$status[$record->task_status_id];
                }
                else
                {
                    return '';
                }
            })
            ->editColumn('order_image', static function ($record) {
                if (isset($record->order_image)) {
                    return '<img onClick="ShowLightBox(this);" style = "width:50px;height:50px" src = "' . $record->order_image . '" />';
                } else {
                    return '';
                }
            })
            ->editColumn('tracking_id', static function ($record) {
                return substr($record->tracking_id, ($pos = strrpos($record->tracking_id, '_')) == false ? 0 : $pos + 1);
            })
            ->addColumn('action', static function ($record) {
                return backend_view('ctc-entries.action_pickup', compact('record'));
            })
            ->make(true);
    }

    public function ctcpickupDetail(Request $request, $id)
    {
        $ctc_id = base64_decode($id);
        $data = $this->get_trackingorderdetails($ctc_id);
        $sprintId = $data['sprintId'];
        $data = $data['data'];
        return backend_view('ctc-entries.ctc_pickup_detail', compact('data', 'sprintId'));
    }

    public function ctcPickedupExcel($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');
        }

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $ctc_data = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where(['boradless_dashboard.task_status_id' => 121])
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->get();
        $ctc_array[] = ['JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Out For Delivery', 'Sorted Time', 'Actual Arrival @ CX', 'Last Mile tracking #', 'Status'];
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
                'Last Mile tracking #' => strval(substr($ctc->tracking_id, ($pos = strrpos($ctc->tracking_id, '_')) == false ? 0 : $pos + 1)),
                'Status' => $ctc->task_status_id? strval(self::$status[$ctc->task_status_id]): ''
            ];
        }
        Excel::create('Last Mile Picked Up Data' . $date . '', function ($excel) use ($ctc_array) {
            $excel->setTitle('Last Mile Picked Up Data');
            $excel->sheet('Last Mile Picked Up Data', function ($sheet) use ($ctc_array) {
                $sheet->fromArray($ctc_array, null, 'A1', false, false);
            });
        })->download('csv');
    }


    public function getCtcscan(Request $request)
    {
        $title_name = 'Last Mile Reporting';
        $type = 'scan';
        return backend_view('ctc-entries.not_scanned_orders', compact('title_name', 'type'));
    }

    public function ctcNotScanData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $ctcVendorIds = CtcVendor::pluck('vendor_id');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereNotIn('task_status_id', [38, 36])
            ->whereIn('boradless_dashboard.task_status_id', [61, 13])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->select(['boradless_dashboard.*'
            ]);

        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('sprint_id', static function ($record) {
                return $record->sprint_id ;
            })
            ->editColumn('picked_up_at', static function ($record) {
                if ($record->picked_up_at) {
                    $picked_up_at = new \DateTime($record->picked_up_at, new \DateTimeZone('UTC'));
                    $picked_up_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $picked_up_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('sorted_at', static function ($record) {
                if ($record->sorted_at) {
                    $sorted_at = new \DateTime($record->sorted_at, new \DateTimeZone('UTC'));
                    $sorted_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $sorted_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('delivered_at', static function ($record) {
                if ($record->delivered_at) {
                    $delivered_at = new \DateTime($record->delivered_at, new \DateTimeZone('UTC'));
                    $delivered_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $delivered_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('joey_name', static function ($record) {
                return $record->joey_name ? $record->joey_name . ' (' . $record->joey_id . ')' : '';
            })
            ->editColumn('route_id', static function ($record) {
                return $record->route_id ? 'R-' . $record->route_id . '-' . $record->ordinal : '';
            })
            ->editColumn('task_status_id', static function ($record) {
                if ($record->task_status_id) {
                    return self::$status[$record->task_status_id];
                }
                else{
                    return '';
                }
            })
            ->editColumn('order_image', static function ($record) {
                if (isset($record->order_image)) {
                    return '<img onClick="ShowLightBox(this);" style = "width:50px;height:50px" src = "' . $record->order_image . '" />';
                } else {
                    return '';
                }
            })
            ->editColumn('tracking_id', static function ($record) {
                return substr($record->tracking_id, ($pos = strrpos($record->tracking_id, '_')) == false ? 0 : $pos + 1);
            })
            ->addColumn('action', static function ($record) {
                return backend_view('ctc-entries.action_notscan', compact('record'));
            })
            ->make(true);
    }

    public function ctcnotscanDetail(Request $request, $id)
    {
        $ctc_id = base64_decode($id);
        $data = $this->get_trackingorderdetails($ctc_id);
        $sprintId = $data['sprintId'];
        $data = $data['data'];
        return backend_view('ctc-entries.ctc_notscan_detail', compact('data', 'sprintId'));
    }

    public function ctcNotscanExcel($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');
        }
        $ctcVendorIds = CtcVendor::pluck('vendor_id');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $ctc_data = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereIn('boradless_dashboard.task_status_id', [61, 13])
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->get();
        $ctc_array[] = ['JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Out For Delivery', 'Sorted Time', 'Actual Arrival @ CX', 'Last Mile tracking #', 'Status'];
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
                'Last Mile tracking #' => strval(substr($ctc->tracking_id, ($pos = strrpos($ctc->tracking_id, '_')) == false ? 0 : $pos + 1)),
                'Status' => strval(self::$status[$ctc->task_status_id])
            ];
        }
        Excel::create('Last Mile Not Scan Data' . $date . '', function ($excel) use ($ctc_array) {
            $excel->setTitle('Last Mile Not Scan Data');
            $excel->sheet('Last Mile Not Scan Data', function ($sheet) use ($ctc_array) {
                $sheet->fromArray($ctc_array, null, 'A1', false, false);
            });
        })->download('csv');
    }


    public function getCtcdelivered(Request $request)
    {
        $title_name = 'Last Mile Reporting';
        $type = 'delivered';
        return backend_view('ctc-entries.delivered_orders', compact('title_name',  'type'));
    }

    public function ctcDeliveredData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $ctcVendorIds = CtcVendor::pluck('vendor_id');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn('boradless_dashboard.task_status_id', [17, 113, 114, 116, 117, 118, 132, 138, 139, 144])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->select(['boradless_dashboard.*'
            ]);

        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('sprint_id', static function ($record) {
                return $record->sprint_id ;
            })
            ->editColumn('picked_up_at', static function ($record) {
                if ($record->picked_up_at) {
                    $picked_up_at = new \DateTime($record->picked_up_at, new \DateTimeZone('UTC'));
                    $picked_up_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $picked_up_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('sorted_at', static function ($record) {
                if ($record->sorted_at) {
                    $sorted_at = new \DateTime($record->sorted_at, new \DateTimeZone('UTC'));
                    $sorted_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $sorted_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('delivered_at', static function ($record) {
                if ($record->delivered_at) {
                    $delivered_at = new \DateTime($record->delivered_at, new \DateTimeZone('UTC'));
                    $delivered_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $delivered_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('joey_name', static function ($record) {
                return $record->joey_name ? $record->joey_name . ' (' . $record->joey_id . ')' : '';
            })
            ->editColumn('route_id', static function ($record) {
                return $record->route_id ? 'R-' . $record->route_id . '-' . $record->ordinal : '';
            })
            ->editColumn('task_status_id', static function ($record) {
                if ($record->task_status_id) {
                    return self::$status[$record->task_status_id];
                }
                else{
                    return '';
                }
            })
            ->editColumn('order_image', static function ($record) {
                if (isset($record->order_image)) {
                    return '<img onClick="ShowLightBox(this);" style = "width:50px;height:50px" src = "' . $record->order_image . '" />';
                } else {
                    return '';
                }
            })
            ->editColumn('tracking_id', static function ($record) {
                return substr($record->tracking_id, ($pos = strrpos($record->tracking_id, '_')) == false ? 0 : $pos + 1);
            })
            ->addColumn('action', static function ($record) {
                return backend_view('ctc-entries.action_delivered', compact('record'));
            })
            ->make(true);
    }

    public function ctcdeliveredDetail(Request $request, $id)
    {
        $ctc_id = base64_decode($id);
        $data = $this->get_trackingorderdetails($ctc_id);

        $sprintId = $data['sprintId'];
        $data = $data['data'];
        return backend_view('ctc-entries.ctc_delivered_detail', compact('data', 'sprintId'));
    }

    public function ctcDeliveredExcel($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');
        }

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);


        $ctc_data = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn('boradless_dashboard.task_status_id', [17, 113, 114, 116, 117, 118, 132, 138, 139, 144])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->get();
        $ctc_array[] = ['JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Out For Delivery', 'Sorted Time', 'Actual Arrival @ CX', 'Last Mile tracking #', 'Status'];
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
                'Last Mile tracking #' => strval(substr($ctc->tracking_id, ($pos = strrpos($ctc->tracking_id, '_')) == false ? 0 : $pos + 1)),
                'Status' => strval(self::$status[$ctc->task_status_id])
            ];
        }
        Excel::create('Last Mile Delivered Data' . $date . '', function ($excel) use ($ctc_array) {
            $excel->setTitle('Last Mile Delivered Data');
            $excel->sheet('Last Mile Delivered Data', function ($sheet) use ($ctc_array) {
                $sheet->fromArray($ctc_array, null, 'A1', false, false);
            });
        })->download('csv');
    }


    public function getCtcreturned(Request $request)
    {
        $title_name = 'Last Mile Reporting';
        $type = 'return';
        return backend_view('ctc-entries.returned_orders', compact('title_name',  'type'));
    }

    public function ctcReturnedData(Datatables $datatables, Request $request)
    {

        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $ctcVendorIds = CtcVendor::pluck('vendor_id');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn('boradless_dashboard.task_status_id', [101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 131, 135, 136, 143])
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->select(['boradless_dashboard.*'
            ]);

        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('sprint_id', static function ($record) {
                return $record->sprint_id ;
            })
            ->editColumn('picked_up_at', static function ($record) {
                if ($record->picked_up_at) {
                    $picked_up_at = new \DateTime($record->picked_up_at, new \DateTimeZone('UTC'));
                    $picked_up_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $picked_up_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('sorted_at', static function ($record) {
                if ($record->sorted_at) {
                    $sorted_at = new \DateTime($record->sorted_at, new \DateTimeZone('UTC'));
                    $sorted_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $sorted_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('delivered_at', static function ($record) {
                if ($record->delivered_at) {
                    $delivered_at = new \DateTime($record->delivered_at, new \DateTimeZone('UTC'));
                    $delivered_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $delivered_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('returned_at', static function ($record) {
                if ($record->returned_at) {
                    $returned_at = new \DateTime($record->returned_at, new \DateTimeZone('UTC'));
                    $returned_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $returned_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('hub_return_scan', static function ($record) {
                if ($record->hub_return_scan) {
                    $hub_return_scan = new \DateTime($record->hub_return_scan, new \DateTimeZone('UTC'));
                    $hub_return_scan->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $hub_return_scan->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('joey_name', static function ($record) {
                return $record->joey_name ? $record->joey_name . ' (' . $record->joey_id . ')' : '';
            })
            ->editColumn('route_id', static function ($record) {
                return $record->route_id ? 'R-' . $record->route_id . '-' . $record->ordinal : '';
            })
            ->editColumn('task_status_id', static function ($record) {
                if ($record->task_status_id) {
                    return self::$status[$record->task_status_id];
                }
                else{
                    return '';
                }
            })
            ->editColumn('order_image', static function ($record) {
                if (isset($record->order_image)) {
                    return '<img onClick="ShowLightBox(this);" style = "width:50px;height:50px" src = "' . $record->order_image . '" />';
                } else {
                    return '';
                }
            })
            ->editColumn('tracking_id', static function ($record) {
                return substr($record->tracking_id, ($pos = strrpos($record->tracking_id, '_')) == false ? 0 : $pos + 1);
            })
            ->addColumn('action', static function ($record) {
                return backend_view('ctc-entries.action_returned', compact('record'));
            })
            ->make(true);
    }

    public function ctcreturnedDetail(Request $request, $id)
    {
        $ctc_id = base64_decode($id);
        $data = $this->get_trackingorderdetails($ctc_id);
        $sprintId = $data['sprintId'];
        $data = $data['data'];
        return backend_view('ctc-entries.ctc_returned_detail', compact('data', 'sprintId'));
    }

    public function ctcReturnedExcel($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');
        }

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $ctc_data = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn('boradless_dashboard.task_status_id', [101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 131, 135, 136, 143])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->get();
        $ctc_array[] = ['JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Out For Delivery', 'Sorted Time', 'Joey Returned Scan', 'Hub Returned Scan', 'Last Mile tracking #', 'Status'];
        foreach ($ctc_data as $ctc) {
            $picked_up_at = '';
            $sorted_at = '';
            // $delivered_at = '';
            $returned_at = '';
            $hub_return_scan = '';
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
            if ($ctc->returned_at) {
                $returned_at = new \DateTime($ctc->returned_at, new \DateTimeZone('UTC'));
                $returned_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                $returned_at->format('Y-m-d H:i:s');
            }
            if ($ctc->hub_return_scan) {
                $hub_return_scan = new \DateTime($ctc->hub_return_scan, new \DateTimeZone('UTC'));
                $hub_return_scan->setTimeZone(new \DateTimeZone('America/Toronto'));
                $hub_return_scan->format('Y-m-d H:i:s');
            }
            $ctc_array[] = [
                'JoeyCo Order #' => strval($ctc->sprint_id),
                'Route Number' => $ctc->route_id ? strval('R-' . $ctc->route_id . '-' . $ctc->ordinal) : '',
                'Joey' => $ctc->joey_name ? strval($ctc->joey_name . ' (' . $ctc->joey_id . ')') : '',
                'Customer Address' => strval($ctc->address_line_1),
                'Out For Delivery' => $picked_up_at,
                'Sorter Time' => $sorted_at,
                'Joey Returned Scan' => $returned_at,
                'Hub Returned Scan' => $hub_return_scan,
                'Last Mile tracking #' => strval(substr($ctc->tracking_id, ($pos = strrpos($ctc->tracking_id, '_')) == false ? 0 : $pos + 1)),
                'Status' => strval(self::$status[$ctc->task_status_id])
            ];
        }
        Excel::create('Last Mile Returned Data ' . $date . '', function ($excel) use ($ctc_array) {
            $excel->setTitle('Last Mile Returned Data');
            $excel->sheet('Last Mile Returned Data', function ($sheet) use ($ctc_array) {
                $sheet->fromArray($ctc_array, null, 'A1', false, false);
            });
        })->download('csv');
    }


    public function getCtcNotreturned(Request $request)
    {
        $title_name = 'CTC';
        $type = 'return';
        return backend_view('ctc-entries.not_returned_orders', compact('title_name',  'type'));
    }

    public function ctcNotReturnedData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $ctcVendorIds = CtcVendor::pluck('vendor_id');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn('boradless_dashboard.task_status_id', [101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 131, 135, 136, 143])
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereNull('boradless_dashboard.hub_return_scan')
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->select(['boradless_dashboard.*'
            ]);

        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('sprint_id', static function ($record) {
                return $record->sprint_id ;
            })
            ->editColumn('picked_up_at', static function ($record) {
                if ($record->picked_up_at) {
                    $picked_up_at = new \DateTime($record->picked_up_at, new \DateTimeZone('UTC'));
                    $picked_up_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $picked_up_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('sorted_at', static function ($record) {
                if ($record->sorted_at) {
                    $sorted_at = new \DateTime($record->sorted_at, new \DateTimeZone('UTC'));
                    $sorted_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $sorted_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('delivered_at', static function ($record) {
                if ($record->delivered_at) {
                    $delivered_at = new \DateTime($record->delivered_at, new \DateTimeZone('UTC'));
                    $delivered_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $delivered_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('returned_at', static function ($record) {
                if ($record->returned_at) {
                    $returned_at = new \DateTime($record->returned_at, new \DateTimeZone('UTC'));
                    $returned_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $returned_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('hub_return_scan', static function ($record) {
                if ($record->hub_return_scan) {
                    $hub_return_scan = new \DateTime($record->hub_return_scan, new \DateTimeZone('UTC'));
                    $hub_return_scan->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $hub_return_scan->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('joey_name', static function ($record) {
                return $record->joey_name ? $record->joey_name . ' (' . $record->joey_id . ')' : '';
            })
            ->editColumn('route_id', static function ($record) {
                return $record->route_id ? 'R-' . $record->route_id . '-' . $record->ordinal : '';
            })
            ->editColumn('task_status_id', static function ($record) {
                if ($record->task_status_id) {
                    return self::$status[$record->task_status_id];
                }
                else{
                    return '';
                }
            })
            ->editColumn('order_image', static function ($record) {
                if (isset($record->order_image)) {
                    return '<img onClick="ShowLightBox(this);" style = "width:50px;height:50px" src = "' . $record->order_image . '" />';
                } else {
                    return '';
                }
            })
            ->editColumn('tracking_id', static function ($record) {
                return substr($record->tracking_id, ($pos = strrpos($record->tracking_id, '_')) == false ? 0 : $pos + 1);
            })
            ->addColumn('action', static function ($record) {
                return backend_view('ctc-entries.action_notreturned', compact('record'));
            })
            ->make(true);
    }

    public function ctcNotReturnedDetail(Request $request, $id)
    {
        $ctc_id = base64_decode($id);
        $data = $this->get_trackingorderdetails($ctc_id);
        $sprintId = $data['sprintId'];
        $data = $data['data'];

        return backend_view('ctc-entries.ctc_notreturned_detail', compact('data', 'sprintId'));
    }

    public function ctcNotReturnedExcel($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');
        }

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $ctc_data = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.is_custom_route', 0)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereNull('boradless_dashboard.hub_return_scan')
            ->whereIn('boradless_dashboard.task_status_id', [101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 131, 135, 136, 143])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->get();
        $ctc_array[] = ['JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Out For Delivery', 'Sorted Time', 'Joey Returned Scan', 'Hub Returned Scan', 'Last Mile tracking #', 'Status'];
        foreach ($ctc_data as $ctc) {
            $picked_up_at = '';
            $sorted_at = '';
            // $delivered_at = '';
            $returned_at = '';
            $hub_return_scan = '';
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
            if ($ctc->returned_at) {
                $returned_at = new \DateTime($ctc->returned_at, new \DateTimeZone('UTC'));
                $returned_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                $returned_at->format('Y-m-d H:i:s');
            }
            if ($ctc->hub_return_scan) {
                $hub_return_scan = new \DateTime($ctc->hub_return_scan, new \DateTimeZone('UTC'));
                $hub_return_scan->setTimeZone(new \DateTimeZone('America/Toronto'));
                $hub_return_scan->format('Y-m-d H:i:s');
            }
            $ctc_array[] = [
                'JoeyCo Order #' => strval($ctc->sprint_id),
                'Route Number' => $ctc->route_id ? strval('R-' . $ctc->route_id . '-' . $ctc->ordinal) : '',
                'Joey' => $ctc->joey_name ? strval($ctc->joey_name . ' (' . $ctc->joey_id . ')') : '',
                'Customer Address' => strval($ctc->address_line_1),
                'Out For Delivery' => $picked_up_at,
                'Sorter Time' => $sorted_at,
                'Joey Returned Scan' => $returned_at,
                'Hub Returned Scan' => $hub_return_scan,
                'Last Mile tracking #' => strval(substr($ctc->tracking_id, ($pos = strrpos($ctc->tracking_id, '_')) == false ? 0 : $pos + 1)),
                'Status' => strval(self::$status[$ctc->task_status_id])
            ];
        }
        Excel::create('Returns Not Received At Hub ' . $date . '', function ($excel) use ($ctc_array) {
            $excel->setTitle('Returns Not Received At Hub');
            $excel->sheet('Returns Not Received At Hub', function ($sheet) use ($ctc_array) {
                $sheet->fromArray($ctc_array, null, 'A1', false, false);
            });
        })->download('csv');
    }

    public function ctcNotReturnedExcelTrackingIds($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');
        }

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $ctc_data = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('created_at','>',$start)
            ->where('created_at','<',$end)
            ->where('is_custom_route', 0)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->whereNotIn('task_status_id', [38, 36])
            ->whereNull('hub_return_scan')
            ->whereIn('task_status_id', [101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 131, 135, 136, 143])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->get();
        $ctc_array[] = ['CTC tracking #'];
        foreach ($ctc_data as $ctc) {
            $ctc_array[] = [
                'Last Mile tracking #' => strval(substr($ctc->tracking_id, ($pos = strrpos($ctc->tracking_id, '_')) == false ? 0 : $pos + 1))
            ];
        }
        Excel::create('Returns Not Received Tracking ' . $date . '', function ($excel) use ($ctc_array) {
            $excel->setTitle('Returns Not Received Tracking');
            $excel->sheet('Returns Not Received At Hub', function ($sheet) use ($ctc_array) {
                $sheet->fromArray($ctc_array, null, 'A1', false, false);
            });
        })->download('csv');
    }


    public function getCtcCustomRoute(Request $request)
    {
        $title_name = 'Last Mile Reporting';
        $type = 'custom';
        return backend_view('ctc-entries.custom_route', compact('title_name',  'type'));
    }

    public function ctcCustomRouteData(Datatables $datatables, Request $request)
    {

        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");

        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.is_custom_route', 1)
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->select(['boradless_dashboard.*'
            ]);

        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('sprint_id', static function ($record) {
                return $record->sprint_id ;
            })
            ->editColumn('picked_up_at', static function ($record) {
                if ($record->picked_up_at) {
                    $picked_up_at = new \DateTime($record->picked_up_at, new \DateTimeZone('UTC'));
                    $picked_up_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $picked_up_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('sorted_at', static function ($record) {
                if ($record->sorted_at) {
                    $sorted_at = new \DateTime($record->sorted_at, new \DateTimeZone('UTC'));
                    $sorted_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $sorted_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('delivered_at', static function ($record) {
                if ($record->delivered_at) {
                    $delivered_at = new \DateTime($record->delivered_at, new \DateTimeZone('UTC'));
                    $delivered_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                    return $delivered_at->format('Y-m-d H:i:s');
                } else {
                    return '';
                }
            })
            ->editColumn('joey_name', static function ($record) {
                return $record->joey_name ? $record->joey_name . ' (' . $record->joey_id . ')' : '';
            })
            ->editColumn('route_id', static function ($record) {
                return $record->route_id ? 'R-' . $record->route_id . '-' . $record->ordinal : '';
            })
            ->editColumn('task_status_id', static function ($record) {
                if ($record->task_status_id) {
                    return self::$status[$record->task_status_id];
                }
                else{
                    return '';
                }
            })
            ->editColumn('order_image', static function ($record) {
                if (isset($record->order_image)) {
                    return '<img onClick="ShowLightBox(this);" style = "width:50px;height:50px" src = "' . $record->order_image . '" />';
                } else {
                    return '';
                }
            })
            ->editColumn('tracking_id', static function ($record) {
                return substr($record->tracking_id, ($pos = strrpos($record->tracking_id, '_')) == false ? 0 : $pos + 1);
            })
            ->addColumn('action', static function ($record) {
                return backend_view('ctc-entries.action_custom_route', compact('record'));
            })
            ->make(true);
    }

    public function ctcCustomRouteDetail(Request $request, $id)
    {
        $ctc_id = base64_decode($id);
        $data = $this->get_trackingorderdetails($ctc_id);
        $sprintId = $data['sprintId'];
        $data = $data['data'];

        return backend_view('ctc-entries.ctc_custom_route_detail', compact('data', 'sprintId'));
    }

    public function ctcCustomRouteExcel($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');
        }


        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $ctc_data = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.is_custom_route', 1)
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->get();
        $ctc_array[] = ['JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Out For Delivery', 'Sorted Time', 'Actual Arrival @ CX', 'Last Mile tracking #', 'Status'];
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
                'Last Mile tracking #' => strval(substr($ctc->tracking_id, ($pos = strrpos($ctc->tracking_id, '_')) == false ? 0 : $pos + 1)),
                'Status' => strval(self::$status[$ctc->task_status_id])
            ];
        }
        Excel::create('LastMile Custom Route Data' . $date . '', function ($excel) use ($ctc_array) {
            $excel->setTitle('LastMile Custom Route Data');
            $excel->sheet('LastMile Custom Route Data', function ($sheet) use ($ctc_array) {
                $sheet->fromArray($ctc_array, null, 'A1', false, false);
            });
        })->download('csv');
    }

    /**
     * Get CTC Route Info
     */
    public function getRouteinfo(Request $request)
    {
        $show_message = $request->message;
        if(!is_null($show_message))
        {
            $current_url  = $request->url();
            $query_string = http_build_query( $request->except(['message'] ) );
            return redirect($current_url.'?'.$query_string)
                ->with('alert-success', $show_message);
        }

        $date = $request->input('datepicker');
        // dd($date);
        if ($date == null) {
            $date = date("Y-m-d");
        }


        $ctc_info = JoeyRoutes::join('joey_route_locations', 'joey_routes.id', '=', 'joey_route_locations.route_id')
            ->join('sprint__tasks','joey_route_locations.task_id', '=', 'sprint__tasks.id')
            ->where('joey_routes.date', 'like', $date . "%")
            ->where('joey_routes.hub', auth()->user()->hub_id)
            ->whereNotIn('sprint__tasks.status_id', [36])
            ->where('joey_routes.deleted_at', null)
            ->where('joey_route_locations.deleted_at', null)
            ->orderBy('joey_routes.id', 'ASC')
            ->groupBy('joey_routes.id')
            ->select('joey_routes.*')
            ->get();

        $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();

        //getting flag categories
        $flagCategories =  CustomerFlagCategories::where('parent_id', 0)
            ->where('is_enable', 1)
            ->whereNull('deleted_at')
            ->get();

        if ($ctc_info->isEmpty()) {
            $counts['route_counts'] = 0;
            $counts['TotalOrderDrops'] = 0;
            $counts['TotalSortedOrders'] = 0;
            $counts['TotalOrderPicked'] = 0;
            $counts['TotalOrderDropsCompleted'] = 0;
            $counts['TotalOrderReturn'] = 0;
            $counts['TotalOrderNotScan'] = 0;
            $counts['TotalOrderUnattempted'] = 0;
            return backend_view('ctc-entries.ctc_route_info', compact('ctc_info', 'flagCategories', 'ctcVendorIds', 'counts'));
        } else {
            foreach ($ctc_info as $boradless_route) {

                $TotalOrderDrops[] = $boradless_route->TotalOrderDropsCount();
                $TotalSortedOrders[] = $boradless_route->TotalSortedOrdersCount();
                $TotalOrderPicked[] = $boradless_route->TotalOrderPickedCount();
                $TotalOrderDropsCompleted[] = $boradless_route->TotalOrderDropsCompletedCount();
                $TotalOrderReturn[] = $boradless_route->TotalOrderReturnCount();
                $TotalOrderNotScan[] = $boradless_route->TotalOrderNotScanCount();
                $TotalOrderUnattempted[] = $boradless_route->TotalOrderUnattemptedCount();

            }
            $counts['route_counts'] = $ctc_info->count() ? $ctc_info->count() : 0;
            $counts['TotalOrderDrops'] = $TotalOrderDrops ? array_sum($TotalOrderDrops) : 0;
            $counts['TotalSortedOrders'] = $TotalSortedOrders ? array_sum($TotalSortedOrders) : 0;
            $counts['TotalOrderPicked'] = $TotalOrderPicked ? array_sum($TotalOrderPicked) : 0;
            $counts['TotalOrderDropsCompleted'] = $TotalOrderDropsCompleted ? array_sum($TotalOrderDropsCompleted) : 0;
            $counts['TotalOrderReturn'] = $TotalOrderReturn ? array_sum($TotalOrderReturn) : 0;
            $counts['TotalOrderNotScan'] = $TotalOrderNotScan ? array_sum($TotalOrderNotScan) : 0;
            $counts['TotalOrderUnattempted'] = $TotalOrderUnattempted ? array_sum($TotalOrderUnattempted) : 0;
        }


        return backend_view('ctc-entries.ctc_route_info', compact('ctc_info','flagCategories','ctcVendorIds', 'counts'));
    }

    /**
     * Route Mark Delay
     */
    public function routeMarkDelay(Request $request)
    {
        $data = $request->all();

        $route = JoeyRouteLocations::join('sprint__tasks', 'joey_route_locations.task_id', '=', 'sprint__tasks.id')
            ->leftJoin('merchantids', 'merchantids.task_id', '=', 'sprint__tasks.id')
            ->where('route_id', '=', $data['route_id'])
            ->whereNull('joey_route_locations.deleted_at')
            ->whereNotNull('merchantids.tracking_id')
            ->get(['merchantids.tracking_id', 'sprint__tasks.id', 'sprint__tasks.sprint_id']);
        $deliver_status = $this->getStatusCodes('competed');
        $return_status = $this->getStatusCodes('return');
        $createRecord = [];
        foreach ($route as $rot) {

            $status_array = array_merge($deliver_status, $return_status);
            $sprint = Sprint::where('id', '=', $rot->sprint_id)->whereNotIn('status_id', $status_array)->first();

            if ($sprint) {
                $createRecord[] = [
                    'tracking_id' => $rot->tracking_id,
                    'date' => $data['date'],
                ];
                Sprint::where('id', '=', $rot->sprint_id)->update(['status_id' => 255]);
                $task = Task::where('id', '=', $rot->id)->where('type', 'dropoff')->whereNotIn('status_id', [$deliver_status, $return_status])->first();
                if ($task) {
                    Task::where('id', '=', $rot->id)->where('type', 'dropoff')->update(['status_id' => 255]);
                }
                $taskHistoryRecord = [
                    'sprint__tasks_id' => $rot->id,
                    'sprint_id' => $rot->sprint_id,
                    'status_id' => 255,
                    'date' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'active' => 1
                ];
                SprintTaskHistory::insert($taskHistoryRecord);
            }

        }
        if (count($createRecord) > 0) {
            TrackingDelay::insert($createRecord);
        }
        return response()->json(['status' => '1']);
    }


    /**
     * Get CTC Route Info excel report
     */
    public function ctcRouteinfoExcel($date = null)
    {
        //setting up current date if null
        if ($date == null) {
            $date = date('Y-m-d');
        }

        /*getting csv file data*/
        $ctc_route_data = JoeyRoutes::join('joey_route_locations', 'joey_routes.id', '=', 'joey_route_locations.route_id')
            ->where('joey_routes.date', 'like', $date . "%")
            ->where('joey_routes.hub', 17)
            ->whereIn('joey_routes.zone',[246,82,83])
            ->where('joey_routes.deleted_at', null)
            ->where('joey_route_locations.deleted_at', null)
            ->orderBy('joey_routes.id', 'ASC')
            ->groupBy('joey_routes.id')
            ->select('joey_routes.*')
            ->get();
        //JoeyRoutes::where(\DB::raw("CONVERT_TZ(date,'UTC','America/Toronto')"),'like',$date."%")->where('hub',17)->get();

        //checking if data is null then return null
        if (count($ctc_route_data) <= 0) {
            // if the data null ten return empty array
            return [];
        }

        // init data variable
        $data = [];
        $csv_header = ['Route No', 'Joey Name', 'No of drops', 'No of picked', 'No of drops completed', 'No of Returns', 'No of unattempted'];
        $data[0] = $csv_header;

        $iteration = 1;
        foreach ($ctc_route_data as $ctc_route) {
            $joey_name = ($ctc_route->joey) ? $ctc_route->Joey->first_name . ' ' . $ctc_route->Joey->last_name : '';
            $data[$iteration] = [
                $ctc_route->id,
                $joey_name,
                $ctc_route->TotalOrderDropsCount(),
                $ctc_route->TotalOrderPickedCount(),
                $ctc_route->TotalOrderDropsCompletedCount(),
                $ctc_route->TotalOrderReturnCount(),
                $ctc_route->TotalOrderUnattemptedCount()
            ];
            $iteration++;
        }
        return $data;
    }

    /**
     * Get CTC Hub Route edit
     */
    public function ctcHubRouteEdit(Request $request, $routeId, $hubId)
    {

        $tracking_id = null;

        $status = null;
        $route = JoeyRouteLocations::join('sprint__tasks', 'joey_route_locations.task_id', '=', 'sprint__tasks.id')
            ->leftJoin('merchantids', 'merchantids.task_id', '=', 'sprint__tasks.id')
            ->join('locations', 'location_id', '=', 'locations.id')
            ->join('sprint__sprints', 'sprint_id', '=', 'sprint__sprints.id')
            ->leftJoin('sprint__contacts', 'sprint__contacts.id', '=', 'sprint__tasks.contact_id')
            ->whereNull('sprint__sprints.deleted_at')
            ->where('route_id', '=', $routeId)
            ->whereNull('joey_route_locations.deleted_at')
            ->whereNotNull('merchantids.tracking_id')
            ->orderBy('joey_route_locations.ordinal', 'asc');
        if (!empty($request->get('tracking-id'))) {
            $tracking_id = $request->get('tracking-id');
            $route = $route->where('merchantids.tracking_id', '=', $request->get('tracking-id'));
        }

        if (!empty($request->get('status'))) {
            $status = $request->get('status');
            $route = $route->where('sprint__sprints.status_id', '=', $request->get('status'));
        }
        $route = $route->get(['joey_route_locations.id', 'merchantids.merchant_order_num', 'joey_route_locations.task_id', 'merchantids.tracking_id',
            'sprint_id', 'type', 'start_time', 'end_time', 'address', 'postal_code'
            , 'joey_route_locations.arrival_time', 'joey_route_locations.finish_time', 'sprint__sprints.status_id', 'sprint__tasks.sprint_id', 'sprint__sprints.creator_id',
            'joey_route_locations.distance', 'sprint__contacts.name', 'sprint__contacts.phone', 'joey_route_locations.route_id', 'joey_route_locations.ordinal']);

        $checkJoey=JoeyRoutes::where('id', $routeId)->whereNull('deleted_at')->whereNotNull('joey_id')->first();
        $joey=null;
        if($checkJoey!=null){
            $joey= $checkJoey->joey??null;
        }

        $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();

        return backend_view('ctc-entries.edit-hub-route', ['route' => $route, 'hub_id' => $hubId, 'tracking_id' => $tracking_id, 'status_select' => $status,'ctcVendorIds'=>$ctcVendorIds, 'joey' => $joey]);
    }

    /**
     * Render Model flag history table view
     */
    public function flagHistoryModelHtmlRender(Request $request)
    {
        $request_data = $request->all();

        $joey_flags_history = FlagHistory::where('sprint_id',$request->sprint)
            ->orderBy('id', 'DESC')
            ->where('unflaged_by','=',0)
            ->get();

        //getting flag categories
        $flagCategories =  CustomerFlagCategories::where('parent_id', 0)
            ->where('is_enable', 1)
            ->whereNull('deleted_at')
            ->get();

        $html =  view('backend.ctc-entries.sub-views.ajax-render-view-edit-hub-route-flag-model',
            compact(
                'joey_flags_history',
                'flagCategories',
                'request_data'
            )
        )->render();

        return response()->json(['status' => true,'html'=>$html]);
    }

    /**
     * Get CTC Tracking Order Detail
     */
    public function getCtctrackingorderdetails($sprintId)
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
            //->whereNull('joey_route_locations.deleted_at')
            ->orderBy('ordinal', 'DESC')->take(1)
            ->get(array('sprint__tasks.*', 'joey_routes.id as route_id',\DB::raw("CONVERT_TZ(joey_routes.date,'UTC','America/Toronto') as route_date"), 'locations.address', 'locations.suite', 'locations.postal_code', 'sprint__contacts.name', 'sprint__contacts.phone', 'sprint__contacts.email',
                'joeys.first_name as joey_firstname', 'joeys.id as joey_id',
                'joeys.last_name as joey_lastname', 'vendors.first_name as merchant_firstname', 'vendors.last_name as merchant_lastname', 'merchantids.scheduled_duetime'
            , 'joeys.id as joey_id', 'merchantids.tracking_id', 'joeys.phone as joey_contact', 'joey_route_locations.ordinal as stop_number'));

        $i = 0;

        $data = [];

        foreach ($result as $tasks) {
            $status2 = array();
            $status = array();
            $status1 = array();
            $data[$i] = $tasks;
            $taskHistory = SprintTaskHistory::where('sprint_id', '=', $tasks->sprint_id)->WhereNotIn('status_id', [17, 38])->orderBy('id')
                //->where('active','=',1)
                ->get(['status_id', 'created_at']);

            $returnTOHubDate = SprintReattempt::
            where('sprint_reattempts.sprint_id', '=', $tasks->sprint_id)->orderBy('created_at')
                ->first();

            if (!empty($returnTOHubDate)) {
                $taskHistoryre = SprintTaskHistory::where('sprint_id', '=', $returnTOHubDate->reattempt_of)->WhereNotIn('status_id', [17, 38])->orderBy('id')
                    //->where('active','=',1)
                    ->get(['status_id', 'created_at']);

                foreach ($taskHistoryre as $history) {

                    $status[$history->status_id]['id'] = $history->status_id;
                    if ($history->status_id == 13) {
                        $status[$history->status_id]['description'] = 'At hub - processing';
                    } else {
                        $status[$history->status_id]['description'] = $this->statusmap($history->status_id);
                    }
                    $status[$history->status_id]['created_at'] = date('Y-m-d H:i:s', strtotime($history->created_at) - 14400);

                }

            }
            if (!empty($returnTOHubDate)) {
                $returnTO2 = SprintReattempt::
                where('sprint_reattempts.sprint_id', '=', $returnTOHubDate->reattempt_of)->orderBy('created_at')
                    ->first();

                if (!empty($returnTO2)) {
                    $taskHistoryre = SprintTaskHistory::where('sprint_id', '=', $returnTO2->reattempt_of)->WhereNotIn('status_id', [17, 38])->orderBy('id')
                        //->where('active','=',1)
                        ->get(['status_id', 'created_at']);

                    foreach ($taskHistoryre as $history) {

                        $status2[$history->status_id]['id'] = $history->status_id;
                        if ($history->status_id == 13) {
                            $status2[$history->status_id]['description'] = 'At hub - processing';
                        } else {
                            $status2[$history->status_id]['description'] = $this->statusmap($history->status_id);
                        }
                        $status2[$history->status_id]['created_at'] = date('Y-m-d H:i:s', strtotime($history->created_at) - 14400);

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
                    $status1[$history->status_id]['created_at'] = date('Y-m-d H:i:s', strtotime($history->created_at) - 14400);
                }
                else{
                    if ($history->created_at >= $tasks->route_date){
                        $status1[$history->status_id]['id'] = $history->status_id;

                        if ($history->status_id == 13) {
                            $status1[$history->status_id]['description'] = 'At hub - processing';
                        } else {
                            $status1[$history->status_id]['description'] = $this->statusmap($history->status_id);
                        }
                        $status1[$history->status_id]['created_at'] = date('Y-m-d H:i:s', strtotime($history->created_at) - 14400);
                    }
                }
            }
            $data[$i]['status'] = $status;
            $data[$i]['status1'] = $status1;
            $data[$i]['status2'] = $status2;
            $i++;
        }
        return backend_view('ctc-entries.orderdetailswtracknigid', ['data' => $data, 'sprintId' => $sprintId]);
    }


    /**
     * Get CTC Reporting
     */
    public function getCtcReporting(Request $request)
    {

        $from_date = !empty($request->get('fromdatepicker')) ? $request->get('fromdatepicker') : date("Y-m-d");
        $to_date = !empty($request->get('todatepicker')) ? $request->get('todatepicker') : date("Y-m-d");
        $city = !empty($request->get('city')) ? $request->get('city') : 'all';

        $interval = date_diff(date_create($from_date), date_create($to_date));

        if ($interval->days > 14) {
            session()->flash('alert-danger', 'The date range selected must be less then or equal to 15 days');
            return redirect('ctcreporting');
        }
        $all_dates = array();
        $range_from_date = new Carbon($from_date);
        $range_to_date = new Carbon($to_date);
        while ($range_from_date->lte($range_to_date)) {
            $all_dates[] = $range_from_date->toDateString();

            $range_from_date->addDay();
        }

        $start_dt = new DateTime($from_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($to_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $sprint_ids = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereNotIn('task_status_id', [38, 36])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->pluck('id');

        //$sprint_ids = Sprint::whereIn('id', $sprintids)->where('deleted_at', null)->where('is_reattempt','=', 0)->pluck('id');
        $sprint = new BoradlessDashboard();
        $ctc_count = $sprint->getSprintCounts($sprint_ids);

        foreach ($all_dates as $range_date) {

            $start_dt = new DateTime($range_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($range_date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            $sprint_ids = BoradlessDashboard::where('created_at','>',$start)->where('created_at','<',$end)
                ->whereNotIn('task_status_id', [38, 36])->pluck('id');
            //$sprint_ids = Sprint::whereIn('id', $sprintids)->where('deleted_at', null)->where('is_reattempt','=', 0)->pluck('id');
            $sprint = new BoradlessDashboard();
            $ctc_range_count[$range_date] = $sprint->getSprintCounts($sprint_ids);
        }


        return backend_view('ctc-entries.reporting.ctc_reporting', compact('ctc_count', 'ctc_range_count','city'));
    }

    /**
     * Get CTC Route Info
     */
    public function getCtcReportingData(Datatables $datatables, Request $request)
    {
        if ($request->ajax()) {


            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $data_for = $request->data_for;
            $city_data = $request->city_data;

            $darwynnLtdVendorId=[477627];
            $wildfForkVendorId =[477625,477635,477633];
            $boradlessVendorId = [477542];
            $walmartVendorId = [477587,477621];
            $shipheroVendorId = [477559];
            $logxVendorId = [477661];
            $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
            $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
            $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
            $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
            $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
            $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

            $start_dt = new DateTime($from_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');
            //Stop For Now
            //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
            //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
            $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
            $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
            $postal_code = array_merge($slot_postal_code);
            $end_dt = new DateTime($from_date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
                ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
                ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
                ->where('boradless_dashboard.created_at','>',$start)
                ->where('boradless_dashboard.created_at','<',$end)
                ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
                ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
                ->distinct()
                ->select(['boradless_dashboard.*'
                ]);

            //$query = Sprint::whereIn('id', $sprintids)->where('deleted_at', null)->where('is_reattempt','=', 0);

            //$query = DB::table('ctc_dashboard')->whereBetween(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"), [$from_date, $to_date]);
            $sprint = new BoradlessDashboard();
            // useing fillters
            ($data_for == 'picked-up') ? $query->where('task_status_id', 125) : $query;
            ($data_for == 'at-hub') ? $query->whereIn('task_status_id', [124, 13]) : $query;
            ($data_for == 'at-store') ? $query->where('task_status_id', 61) : $query;
            ($data_for == 'sorted-order') ? $query->where('task_status_id', 133) : $query;
            ($data_for == 'out-for-delivery') ? $query->where('task_status_id', 121) : $query;
            ($data_for == 'delivered-order') ? $query->whereIn('task_status_id', $sprint->getStatusCodes('competed')) : $query;
            ($data_for == 'returned') ? $query->whereIn('task_status_id', $sprint->getStatusCodes('return'))->where('task_status_id', '!=', 111) : $query;
            ($data_for == 'returned-to-merchant') ? $query->where('task_status_id', 111) : $query;

            // selecting the columns
            /* $query->select([
                 'id',
                 'sprint_status',
                 'created_at',
                 'updated_at',
                 'tracking_id'
             ]);*/

            return $datatables->eloquent($query)
                ->editColumn('status_id', static function ($record) {
                    return self::$status[$record->task_status_id];
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
                ->addColumn('action', static function ($record) {
                    return backend_view('ctc-entries.action', compact('record'));
                })
                ->editColumn('created_at', static function ($record) {
                    return (new \DateTime($record->created_at))->setTimezone(new \DateTimeZone('America/Toronto'))->format('Y-m-d H:i:s');
                })
                ->editColumn('updated_at', static function ($record) {
                    return (new \DateTime($record->updated_at))->setTimezone(new \DateTimeZone('America/Toronto'))->format('Y-m-d H:i:s');
                })
                ->make(true);

        }
    }

    /**
     * Get CTC OTD Graph
     */
    public function statistics_otd_index(Request $request)
    {
        return backend_view('ctc-entries.otd.statistics_otd_dashboard');
    }

    /**
     * Get Day CTC OTD Graph
     */
    public function ajax_render_ctc_otd_day(Request $request)
    {
        $date = date("Y-m-d");
        $sprint = new Sprint();
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereIn('boradless_dashboard.task_status_id', $sprint->getStatusCodes('competed'))
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->select(['boradless_dashboard.*'
            ]);
        //$query = Sprint::whereIn('id', $sprintIds)->where('deleted_at', null)->where('is_reattempt','=', 0)->get();

        $totalcount = 0;
        $totallates = 0;
        if (!empty($query)) {
            foreach ($query as $record) {
                $createdTimestamp = strtotime($record->created_at);
                $day = date('D', $createdTimestamp);

                if ($day == 'Sat') {

                    if ($record->deliveryTime()->delivery_time != NULL && $record->ctcAtHubProcessingFirst()->athub && date('d-m-Y', strtotime("+2 day", strtotime($record->ctcAtHubProcessingFirst()->athub))) . " 21:00:00" < date('d-m-Y H:i:s', strtotime($record->deliveryTime()->delivery_time)))
                    {
                        $totallates++;
                    }
                } else {

                    if ($record->deliveryTime()->delivery_time != NULL && $record->ctcAtHubProcessingFirst()->athub && date('d-m-Y', strtotime("+1 day", strtotime($record->ctcAtHubProcessingFirst()->athub))) . " 21:00:00" < date('d-m-Y H:i:s', strtotime($record->deliveryTime()->delivery_time)))
                    {

                        $totallates++;
                    }
                }
                $totalcount++;
            }
            if ($totalcount == 0) {
                $totalcount = 1;
            }
            $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 0), 'y2' => 100 - round((($totallates / $totalcount) * 100), 0)];
        } else {
            $odt_data_1 = ['y1' => 100, 'y2' => 0];
        }
        return response()->json(array('status' => true, 'for' => 'pie_chart1', 'data' => [$odt_data_1]));
    }

    /**
     * Get Week CTC OTD Graph
     */
    public function ajax_render_ctc_otd_week(Request $request)
    {
        $date = date("Y-m-d");
        $sprint = new Sprint();
        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->whereIn('boradless_dashboard.task_status_id', $sprint->getStatusCodes('competed'))
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereBetween(\DB::raw("CONVERT_TZ(boradless_dashboard.created_at,'UTC','America/Toronto')"), [date('y-m-d', strtotime('-6 day', strtotime($date))) . ' 20:00:00', $date . " 19:59:59"])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->select(['boradless_dashboard.*'
            ]);

        //$query = Sprint::whereIn('id', $sprintIds)->where('deleted_at', null)->where('is_reattempt','=', 0)->get();

        $totalcount = 0;
        $totallates = 0;
        if (!empty($query)) {
            foreach ($query as $record) {
                $createdTimestamp = strtotime($record->created_at);
                $day = date('D', $createdTimestamp);
                if ($day == 'Sat') {
                    if ($record->deliveryTime()->delivery_time != NULL && $record->ctcAtHubProcessingFirst()->athub && date('d-m-Y', strtotime("+2 day", strtotime($record->ctcAtHubProcessingFirst()->athub))) . " 21:00:00" < date('d-m-Y H:i:s', strtotime($record->deliveryTime()->delivery_time))) {
                        $totallates++;
                    }
                } else {
                    if ($record->deliveryTime()->delivery_time != NULL && $record->ctcAtHubProcessingFirst()->athub && date('d-m-Y', strtotime("+1 day", strtotime($record->ctcAtHubProcessingFirst()->athub))) . " 21:00:00" < date('d-m-Y H:i:s', strtotime($record->deliveryTime()->delivery_time))) {
                        $totallates++;
                    }
                }
                $totalcount++;
            }
            if ($totalcount == 0) {
                $totalcount = 1;
            }
            $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 0), 'y2' => 100 - round((($totallates / $totalcount) * 100), 0)];
        } else {
            $odt_data_1 = ['y1' => 100, 'y2' => 0];
        }
        return response()->json(array('status' => true, 'for' => 'pie_chart2', 'data' => [$odt_data_1]));
    }

    /**
     * Get Month CTC OTD Graph
     */
    public function ajax_render_ctc_otd_month(Request $request)
    {
        $date = date("Y-m-d");
        $sprint = new Sprint();

        $darwynnLtdVendorId=[477627];
        $wildfForkVendorId =[477625,477635,477633];
        $boradlessVendorId = [477542];
        $walmartVendorId = [477587,477621];
        $shipheroVendorId = [477559];
        $logxVendorId = [477661];
        $ctcVendors = CtcVendor::whereNotIn('vendor_id',[477340,477341,477342,477343,477344,477345,477346])->pluck('vendor_id')->toArray();
        $boradlessVendorIds = array_merge($boradlessVendorId,$ctcVendors);
        $boradlessVendorIds = array_merge($walmartVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($shipheroVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($wildfForkVendorId,$boradlessVendorIds);
        $boradlessVendorIds = array_merge($darwynnLtdVendorId,$boradlessVendorIds,$logxVendorId);

        //Stop For Now
        //$microHubZone = MicroHubZones::where('hub_id',auth()->user()->hub_id)->pluck('zone_id')->toArray();
        //$microHubPostalCode = MicroHubPostalCodes::where('hub_id',auth()->user()->hub_id)->pluck('postal_code')->toArray();
        $microHubZone = ZoneRouting::where('hub_id',auth()->user()->hub_id)->pluck('id')->toArray();
        $slot_postal_code = SlotsPostalCode::whereIn('zone_id',$microHubZone)->pluck('postal_code')->toArray();
        $postal_code = array_merge($slot_postal_code);
        $query = BoradlessDashboard::join('sprint__tasks', 'sprint__tasks.id', '=', 'boradless_dashboard.task_id')
            ->join('locations', 'locations.id', '=', 'sprint__tasks.location_id')
            ->whereIn('boradless_dashboard.creator_id', $boradlessVendorIds)
            ->whereIn('boradless_dashboard.task_status_id', $sprint->getStatusCodes('competed'))
            ->whereNotIn('boradless_dashboard.task_status_id', [38, 36])
            ->whereBetween(\DB::raw("CONVERT_TZ(boradless_dashboard.created_at,'UTC','America/Toronto')"), [date('y-m-d', strtotime('-1 month', strtotime($date))) . ' 20:00:00', $date . " 19:59:59"])
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postal_code)
            ->distinct()
            ->select(['boradless_dashboard.*'
            ]);
        //$query = Sprint::whereIn('id', $sprintIds)->where('deleted_at', null)->where('is_reattempt','=', 0)->get();

        $totalcount = 0;
        $totallates = 0;
        if (!empty($query)) {
            foreach ($query as $record) {
                $createdTimestamp = strtotime($record->created_at);
                $day = date('D', $createdTimestamp);
                if ($day == 'Sat') {
                    if ($record->deliveryTime()->delivery_time != NULL && $record->ctcAtHubProcessingFirst()->athub && date('d-m-Y', strtotime("+2 day", strtotime($record->ctcAtHubProcessingFirst()->athub))) . " 21:00:00" < date('d-m-Y H:i:s', strtotime($record->deliveryTime()->delivery_time))) {
                        $totallates++;
                    }
                } else {
                    if ($record->deliveryTime()->delivery_time != NULL && $record->ctcAtHubProcessingFirst()->athub && date('d-m-Y', strtotime("+1 day", strtotime($record->ctcAtHubProcessingFirst()->athub))) . " 21:00:00" < date('d-m-Y H:i:s', strtotime($record->deliveryTime()->delivery_time))) {
                        $totallates++;
                    }
                }
                $totalcount++;
            }
            if ($totalcount == 0) {
                $totalcount = 1;
            }
            $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 0), 'y2' => 100 - round((($totallates / $totalcount) * 100), 0)];
        } else {
            $odt_data_1 = ['y1' => 100, 'y2' => 0];
        }
        return response()->json(array('status' => true, 'for' => 'pie_chart3', 'data' => [$odt_data_1]));
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


    public function addNote(Request $request)
    {
        $data=$request->all();

        $route=JoeyRoutes::where('id', $data['routeId'])->whereNull('deleted_at')->whereNotNull('joey_id')->first();
        if (isset($route->joey_id) && $route->joey_id!=null) {
            $deviceIds = UserDevice::where('user_id', $route->joey_id)->where('is_deleted_at', 0)->pluck('device_token');
            $subject = 'Customer Support';
            $message = $data['note'];
            Fcm::sendPush($subject, $message, 'trackingnote', null, $deviceIds);
            $payload = ['notification' => ['title' => $subject, 'body' => $message, 'click_action' => 'trackingnote'],
                'data' => ['data_title' => $subject, 'data_body' => $message, 'data_click_action' => 'trackingnote']];
            $createNotification = [
                'user_id' => $route->joey_id,
                'user_type' => 'Joey',
                'notification' => $subject,
                'notification_type' => 'trackingnote',
                'notification_data' => json_encode(["body" => $message]),
                'payload' => json_encode($payload),
                'is_silent' => 0,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            UserNotification::create($createNotification);
            TrackingNote::create(['user_id'=>Auth::id(),'tracking_id'=>$data['tracking_id'],'note'=>$data['note'],'type'=>'dashboard']);
        }
    }
    public function getNotes(Request $request)
    {
        $tracking_id=$request->get('tracking_id');

        $notes=TrackingNote::with('dashboard','joey')->where('tracking_id',$tracking_id)->orderBy('created_at',"ASC")->get()->toArray();
        return $notes;
    }

    /**
     * Get CTC Dashboard Excel Report
     */
    public function ctcMissingExcelReport($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');

        }
        $file_name = new \DateTime($date);
        $file_name = $file_name->format("M d, Y");
        $file_name = "Last Mile Tracking File " . $file_name . ".csv";

        $ctcVendorIds = CtcVendor::pluck('vendor_id');

        $taskIds = MerchantIds::whereIn('tracking_id',['JY91400470428','JY08200470550','JY78400469555','JY81900472020','JY08300471217','JY89700476573','JY15500477600','JY34000489390','JY44500489393','JY78500489402','JY93000489412','JY00100489414','JY14300489415','JY98000489422','JY12200489437','JY41800489480','JY67100489485','JY60100489488','JY20000489493','JY96900489499','JY38900489503','JY32500489526','JY60900489540','JY35700489550','JY69400489555','JY99900489558','JY33600489563','JY14600489572','JY21900489573','JY51000489594','JY32800489618','JY66200489625','JY78600489626','JY89400489627','JY88900489637','JY08900489640','JY19000489664','JY94100489684','JY92100489689','JY26000489694','JY04800489713','JY59100489737','JY69700489738','JY01900489741','JY66100489745','JY04800440030','JY81600454926','JY51200464735','JY77300465059','JY11000487630','JY22100433550','JY93300467215','JY48500470284','JY68800470861','JY43800471005','JY26900440765','JY66000444487','JY77500471906','JY48600474794','JY93700488838','JY51700441737','JY42400444332','JY53500444402','JY71200444431','JY51400445762','JY52000448816','JY32200456205','JY01100457034','JY39500457782','JY04300459154','JY28500463716','JY80300469280','JY51600469572','JY90100472084','JY96500472235','JY70300474835','JY66600477201','JY90600489720','JY47700489735','JY21400437946','JY51400437271','JY17500434117','JY86200442040','JY23700447079','JY03200455031','JY87900459205','JY20900460216','JY94300460811','JY44100464637','JY90600468017','JY59500468894','JY69000470836','JY31200482119','JY06900489272','JY80400476207','JY53400479344','JY43200462640','JY02700462261','JY36400463990','JY46600435847','JY49700452263','JY80200470432','JY82100489255','JY08800486310','JY76100487093','JY69800487304','JY68900487988','JY48800488869','JY67100488596'])
            ->where('deleted_at', null)->groupBy('tracking_id')->pluck('task_id')->toArray();
        $sprintIds = Task::whereIn('id',$taskIds)->where('deleted_at', null)->pluck('sprint_id')->toArray();
        $ctc_data = Sprint::whereIn('id', $sprintIds)->where('deleted_at', null)->where('is_reattempt','=',0)->get();
        // header info for browser


        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name);
        header('Pragma: no-cache');
        header('Expires: 0');

        // echo "JoeyCo Order\tRoute\tJoey\tStore Name\tCustomer Name\tCustomer Address\tPostal Code\tCity Name\tWeight\tPickup From Store\t1st Attempt - At Hub Processing\t1st Attempt - Out For Delivery\t1st Attempt - Estimated Customer Delivery Time\t1st Attempt - Delivery\t1st Attempt - Shipment Delivery Status\t2nd Attempt - At Hub Processing\t2nd Attempt - Out For Delivery\t2nd Attempt - Estimated Customer Delivery Time\t2nd Attempt - Delivery\t2nd Attempt - Shipment Delivery Status\t3rd Attempt - At Hub Processing\t3rd Attempt - Out For Delivery\t3rd Attempt - Estimated Customer Delivery Time\t3rd Attempt - Delivery\t3rd Attempt - Shipment Delivery Status\tShipment Tracking #\tActual Delivery Status\tActual Delivery\tShipment Tracking Link\tJoyeCo Notes / Comments\t\n";
        echo "JoeyCo Order,Route,Joey,Store Name,Customer Name,Customer Address,Postal Code,City Name,Weight,Pickup From Store,1st Attempt - At Hub Processing,1st Attempt - Out For Delivery,1st Attempt - Estimated Customer Delivery Time,1st Attempt - Delivery,1st Attempt - Shipment Delivery Status,2nd Attempt - At Hub Processing,2nd Attempt - Out For Delivery,2nd Attempt - Estimated Customer Delivery Time,2nd Attempt - Delivery,2nd Attempt - Shipment Delivery Status,3rd Attempt - At Hub Processing,3rd Attempt - Out For Delivery,3rd Attempt - Estimated Customer Delivery Time,3rd Attempt - Delivery,3rd Attempt - Shipment Delivery Status,Shipment Tracking #,Actual Delivery Status,Actual Delivery,Shipment Tracking Link,JoyeCo Notes / Comments,\n";

        // $ctc_array[] = ['Joeyco Order', 'Route', 'Joey', 'Store Name', 'Customer Name', 'Customer Address', 'Postal Code', 'City Name', 'Weight', 'Pickup From Store', 'At Hub Processing', 'Out For Delivery', 'Estimated Customer delivery time', 'Actual Customer delivery time', 'Shipment tracking #', 'Shipment tracking link', 'Shipment Delivery Status', 'JoyeCo Notes / Comments', 'Returned to HUB 2', '2nd Attempt Pick up', '2nd Attempt Delivery', 'Returned to HUB 3', '3rd Attempt Pick up', '3rd Attempt Delivery'];

        foreach ($ctc_data as $ctc_rec) {
            $ctc = null;
            if ($ctc_rec->sprintReattempts) {
                if ($ctc_rec->sprintReattempts->reattempts_left == 0) {
                    $ctc =  $firstSprint = Sprint::where('id', '=', $ctc_rec->sprintReattempts->reattempt_of)->first();
                }
                else
                {
                    $ctc = $ctc_rec;
                }
            }
            else
            {
                $ctc = $ctc_rec;
            }
            $pickup3 = "";
            $hubreturned3 = "";
            $hubpickup3 = "";
            $deliver3 = "";
            $eta_time3 = "";
            $status3 = "";
            $pickup2 = "";
            $hubreturned2 = "";
            $hubpickup2 = "";
            $deliver2 = "";
            $eta_time2 = "";
            $status2 = "";
            $notes = '';
            $check_actual = false;
            $pickup = $ctc->pickupFromStore()->pickup;
            $hubreturned = "";//$ctc->atHubProcessing()->athub;
            $hubpickup = "";// $ctc->outForDelivery()->outdeliver;
            $deliver = "";//$ctc->deliveryTime()->delivery_time;
            $actual_delivery = $ctc->actualDeliveryTime()->actual_delivery;
            $actual_delivery_status = '';

            $eta_time = "";
            if ($pickup) {
                $eta_time = date('Y-m-d', strtotime("+1 day", strtotime($pickup))).' 21:00:00';
            }
            $status = $ctc->status_id;
            if ($ctc->status_id == 17) {
                $preStatus = \App\SprintTaskHistory::where('sprint_id', '=', $ctc->id)
                    ->where('status_id', '!=', '17')
                    ->orderBy('id', 'desc')->first();
                if (!empty($preStatus)) {
                    $status = $preStatus->status_id;
                }
            }
            if ($ctc->actualDeliveryTime()->actual_delivery != null) {
                $check_actual = true;
                $actual_delivery_status = $ctc->actualDeliveryTime()->status_id;

            }
            $notes1 = Notes::where('object_id', $ctc->id)->pluck('note');
            $i = 0;
            foreach ($notes1 as $note) {
                if ($i == 0)
                    $notes = $notes . $note;
                else
                    $notes = $notes . ', ' . $note;
            }
            if ($ctc->sprintReattempts) {
                if ($ctc->sprintReattempts->reattempts_left == 0) {

                    $hubreturned3 = $ctc->atHubProcessing()->athub;
                    $hubpickup3 = $ctc->outForDelivery()->outdeliver;
                    $deliver3 = $ctc->deliveryTime()->delivery_time;
                    if ($hubreturned3) {
                        $eta_time3 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned3))).' 21:00:00';
                    }
                    $status3 = $ctc->status_id;
                    if ($ctc->status_id == 17) {
                        $preStatus = \App\SprintTaskHistory
                            ::where('sprint_id', '=', $ctc->id)
                            ->where('status_id', '!=', '17')
                            ->orderBy('id', 'desc')->first();
                        if (!empty($preStatus)) {
                            $status3 = $preStatus->status_id;
                        }
                    }

                    $secondAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $ctc->sprintReattempts->reattempt_of)->orderBy('created_at', 'ASC')
                        ->get(['status_id', \DB::raw("CONVERT_TZ(sprint__tasks_history.created_at,'UTC','America/Toronto') as created_at")]);
                    if (!empty($secondAttempt)) {

                        foreach ($secondAttempt as $secAttempt) {

                            if (in_array($secAttempt->status_id, [133])) {
                                $hubreturned2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if ($secAttempt->status_id == 121) {
                                $hubpickup2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if (in_array($secAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143,141])) {
                                $deliver2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }

                            $eta = Sprint::where('id', $ctc->sprintReattempts->reattempt_of)->first();
                            if ($hubreturned2) {
                                $eta_time2 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned2))).' 21:00:00';
                            }
                            $status2 = $eta->status_id;
                            if ($eta->status_id == 17) {
                                $preStatus = \App\SprintTaskHistory
                                    ::where('sprint_id', '=', $eta->id)
                                    ->where('status_id', '!=', '17')
                                    ->orderBy('id', 'desc')->first();
                                if (!empty($preStatus)) {
                                    $status2 = $preStatus->status_id;
                                }
                            }

                            if (in_array($secAttempt->status_id, [17,113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                if (!$check_actual) {
                                    $actual_delivery = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                                }
                            }
                            if (in_array($secAttempt->status_id, [113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                if (!$check_actual) {
                                    $actual_delivery_status = $secAttempt->status_id;

                                }
                            }

                        }
                    }

                    $firstSprint = \App\SprintReattempt::where('sprint_id', '=', $ctc->sprintReattempts->reattempt_of)->first();
                    if (!empty($firstSprint)) {
                        $firstAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $firstSprint->reattempt_of)->orderBy('created_at', 'ASC')->
                        get(['status_id', \DB::raw("CONVERT_TZ(sprint__tasks_history.created_at,'UTC','America/Toronto') as created_at")]);
                        if (!empty($firstAttempt)) {

                            foreach ($firstAttempt as $firstAttempt) {
                                if ($firstAttempt->status_id == 125) {
                                    $pickup = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                if (in_array($firstAttempt->status_id, [124])) {
                                    $hubreturned = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                if ($firstAttempt->status_id == 121) {
                                    $hubpickup = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                if (in_array($firstAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143,141])) {
                                    $deliver = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                /* if (in_array($firstAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 104, 105, 140, 110])) {
                                     $actual_delivery = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                 }*/
                                $eta = Sprint::where('id', $firstSprint->reattempt_of)->first();
                                if ($pickup) {
                                    $eta_time = date('Y-m-d', strtotime("+1 day", strtotime($pickup))).' 21:00:00';
                                }
                                $status = $eta->status_id;
                                if ($eta->status_id == 17) {
                                    $preStatus = \App\SprintTaskHistory
                                        ::where('sprint_id', '=', $eta->id)
                                        ->where('status_id', '!=', '17')
                                        ->orderBy('id', 'desc')->first();
                                    if (!empty($preStatus)) {
                                        $status = $preStatus->status_id;
                                    }
                                }
                            }

                        }
                    }
                }
                if ($ctc->sprintReattempts->reattempts_left == 1) {

                    $hubreturned3 = $ctc->atHubProcessing()->athub;
                    $hubpickup3 = $ctc->outForDelivery()->outdeliver;
                    $deliver3 = $ctc->deliveryTime()->delivery_time;
                    if ($hubreturned3) {
                        $eta_time3 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned3))).' 21:00:00';
                    }
                    $status3 = $ctc->status_id;
                    if ($ctc->status_id == 17) {
                        $preStatus = \App\SprintTaskHistory
                            ::where('sprint_id', '=', $ctc->id)
                            ->where('status_id', '!=', '17')
                            ->orderBy('id', 'desc')->first();
                        if (!empty($preStatus)) {
                            $status3 = $preStatus->status_id;
                        }
                    }

                    $secondAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $ctc->sprintReattempts->reattempt_of)->orderBy('created_at', 'ASC')
                        ->get(['status_id', \DB::raw("CONVERT_TZ(sprint__tasks_history.created_at,'UTC','America/Toronto') as created_at")]);
                    if (!empty($secondAttempt)) {

                        foreach ($secondAttempt as $secAttempt) {

                            if (in_array($secAttempt->status_id, [133])) {
                                $hubreturned2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if ($secAttempt->status_id == 121) {
                                $hubpickup2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if (in_array($secAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143,141])) {
                                $deliver2 = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }

                            $eta = Sprint::where('id', $ctc->sprintReattempts->reattempt_of)->first();
                            if ($hubreturned2) {
                                $eta_time2 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned2))).' 21:00:00';
                            }
                            $status2 = $eta->status_id;
                            if ($eta->status_id == 17) {
                                $preStatus = \App\SprintTaskHistory
                                    ::where('sprint_id', '=', $eta->id)
                                    ->where('status_id', '!=', '17')
                                    ->orderBy('id', 'desc')->first();
                                if (!empty($preStatus)) {
                                    $status2 = $preStatus->status_id;
                                }
                            }

                            if (in_array($secAttempt->status_id, [17,113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                if (!$check_actual) {
                                    $actual_delivery = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                                }
                            }
                            if (in_array($secAttempt->status_id, [113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                if (!$check_actual) {
                                    $actual_delivery_status = $secAttempt->status_id;

                                }
                            }

                        }
                    }

                    $firstSprint = \App\SprintReattempt::where('sprint_id', '=', $ctc->sprintReattempts->reattempt_of)->first();
                    if (!empty($firstSprint)) {
                        $firstAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $firstSprint->reattempt_of)->orderBy('created_at', 'ASC')->
                        get(['status_id', \DB::raw("CONVERT_TZ(sprint__tasks_history.created_at,'UTC','America/Toronto') as created_at")]);
                        if (!empty($firstAttempt)) {

                            foreach ($firstAttempt as $firstAttempt) {
                                if ($firstAttempt->status_id == 125) {
                                    $pickup = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                if (in_array($firstAttempt->status_id, [124])) {
                                    $hubreturned = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                if ($firstAttempt->status_id == 121) {
                                    $hubpickup = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                if (in_array($firstAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143,141])) {
                                    $deliver = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                }
                                /* if (in_array($firstAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 104, 105, 140, 110])) {
                                     $actual_delivery = date('20y-m-d H:i:s', strtotime($firstAttempt->created_at));
                                 }*/
                                $eta = Sprint::where('id', $firstSprint->reattempt_of)->first();
                                if ($pickup) {
                                    $eta_time = date('Y-m-d', strtotime("+1 day", strtotime($pickup))).' 21:00:00';
                                }
                                $status = $eta->status_id;
                                if ($eta->status_id == 17) {
                                    $preStatus = \App\SprintTaskHistory
                                        ::where('sprint_id', '=', $eta->id)
                                        ->where('status_id', '!=', '17')
                                        ->orderBy('id', 'desc')->first();
                                    if (!empty($preStatus)) {
                                        $status = $preStatus->status_id;
                                    }
                                }
                            }

                        }
                    }
                }
                if ($ctc->sprintReattempts->reattempts_left == 2) {

                    $hubreturned2 = $ctc->atHubProcessing()->athub;
                    $hubpickup2 = $ctc->outForDelivery()->outdeliver;
                    $deliver2 = $ctc->deliveryTime()->delivery_time;

                    if ($hubreturned2) {
                        $eta_time2 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned2))).' 21:00:00';
                    }
                    $status2 = $ctc->status_id;
                    if ($ctc->status_id == 17) {
                        $preStatus = \App\SprintTaskHistory
                            ::where('sprint_id', '=', $ctc->id)
                            ->where('status_id', '!=', '17')
                            ->orderBy('id', 'desc')->first();
                        if (!empty($preStatus)) {
                            $status2 = $preStatus->status_id;
                        }
                    }

                    $secondAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $ctc->sprintReattempts->reattempt_of)->orderBy('created_at', 'ASC')->
                    get(['status_id', \DB::raw("CONVERT_TZ(sprint__tasks_history.created_at,'UTC','America/Toronto') as created_at")]);
                    if (!empty($secondAttempt)) {
                        date_default_timezone_set('America/Toronto');
                        foreach ($secondAttempt as $secAttempt) {
                            if ($secAttempt->status_id == 125) {
                                $pickup = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if (in_array($secAttempt->status_id, [124])) {
                                $hubreturned = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if ($secAttempt->status_id == 121) {
                                $hubpickup = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }
                            if (in_array($secAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143,141])) {
                                $deliver = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                            }

                            $eta = Sprint::where('id', $ctc->sprintReattempts->reattempt_of)->first();
                            if ($pickup) {
                                $eta_time = date('Y-m-d', strtotime("+1 day", strtotime($pickup))).' 21:00:00';
                            }
                            $status = $eta->status_id;
                            if ($eta->status_id == 17) {
                                $preStatus = \App\SprintTaskHistory
                                    ::where('sprint_id', '=', $eta->id)
                                    ->where('status_id', '!=', '17')
                                    ->orderBy('id', 'desc')->first();
                                if (!empty($preStatus)) {
                                    $status = $preStatus->status_id;
                                }
                            }
                            if (in_array($secAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                if (!$check_actual) {
                                    $actual_delivery = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                                }
                            }
                            if (in_array($secAttempt->status_id, [113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                if (!$check_actual) {
                                    $actual_delivery_status = $secAttempt->status_id;
                                }
                            }
                        }
                    }
                }
            } else {
                $hubreturned = $ctc->atHubProcessingFirst()->athub;
                $hubpickup = $ctc->outForDelivery()->outdeliver;
                $deliver = $ctc->deliveryTime()->delivery_time;
            }

            echo $ctc->id . ",";

            if ($ctc->sprintCtcTasks) {
                if ($ctc->sprintCtcTasks->taskRouteLocation) {
                    echo 'R-' . $ctc->sprintCtcTasks->taskRouteLocation->route_id . '-' . $ctc->sprintCtcTasks->taskRouteLocation->ordinal . ",";
                } else {
                    echo " " . ",";
                }
            } else {
                echo " " . ",";
            }

            if ($ctc->sprintCtcTasks) {
                if ($ctc->sprintCtcTasks->taskRouteLocation) {
                    if ($ctc->sprintCtcTasks->taskRouteLocation->joeyRoute) {
                        if ($ctc->sprintCtcTasks->taskRouteLocation->joeyRoute->joey) {
                            echo str_replace(",","-", $ctc->sprintCtcTasks->taskRouteLocation->joeyRoute->joey->first_name . ' ' . $ctc->sprintCtcTasks->taskRouteLocation->joeyRoute->joey->last_name . ' (' . $ctc->sprintCtcTasks->taskRouteLocation->joeyRoute->joey_id . ')' ) . ",";
                        } else {
                            echo "" . ",";
                        }
                    } else {
                        echo "" . ",";
                    }
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }

            if ($ctc->sprintVendor) {
                echo str_replace(",","-",$ctc->sprintVendor->name ) . ",";
            } else {
                echo "" . ",";
            }

            if ($ctc->sprintCtcTasks) {
                if ($ctc->sprintCtcTasks->taskContact) {
                    echo str_replace(",","-",$ctc->sprintCtcTasks->taskContact->name ) . ",";
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }

            if ($ctc->sprintCtcTasks) {
                if ($ctc->sprintCtcTasks->taskMerchants) {
                    echo str_replace(",","-",$ctc->sprintCtcTasks->taskMerchants->address_line2 ) . ",";
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }

            if ($ctc->sprintCtcTasks) {
                if ($ctc->sprintCtcTasks->task_Location) {
                    echo str_replace(",","-",$ctc->sprintCtcTasks->task_Location->postal_code )  . ",";
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }
            if ($ctc->sprintCtcTasks) {
                if ($ctc->sprintCtcTasks->task_Location) {
                    if ($ctc->sprintCtcTasks->task_Location->city) {
                        echo str_replace(",","-",$ctc->sprintCtcTasks->task_Location->city->name )  . ",";
                    } else {
                        echo "" . ",";
                    }
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }

            if ($ctc->sprintCtcTasks) {
                if ($ctc->sprintCtcTasks->taskMerchants) {
                    echo $ctc->sprintCtcTasks->taskMerchants->weight . $ctc->sprintCtcTasks->taskMerchants->weight_unit . ",";
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }

            echo $pickup . ",";

            echo $hubreturned . ",";


            echo $hubpickup . ",";


            echo $eta_time . ",";


            echo $deliver . ",";

            if (!empty($status)) {
                echo ($status == 13) ? "At hub - processing" . "," : str_replace(",","-",self::$status[$status])  . ",";
            } else {
                echo "" . ",";
            }
            echo $hubreturned2 . ",";
            echo $hubpickup2 . ",";
            echo $eta_time2 . ",";
            echo $deliver2 . ",";
            if (!empty($status2)) {
                echo ($status2 == 13) ? "At hub - processing" . "," : str_replace(",","-",self::$status[$status2] ) . ",";
            } else {
                echo "" . ",";
            }
            echo $hubreturned3 . ",";
            echo $hubpickup3 . ",";
            echo $eta_time3 . ",";
            echo $deliver3 . ",";
            if (!empty($status3)) {
                echo ($status3 == 13) ? "At hub - processing" . "," : str_replace(",","-",self::$status[$status3] ) . ",";
            } else {
                echo "" . ",";
            }


            if ($ctc->sprintCtcTasks) {
                if ($ctc->sprintCtcTasks->taskMerchants) {
                    if (str_contains($ctc->sprintCtcTasks->taskMerchants->tracking_id, 'old_')) {
                        echo substr($ctc->sprintCtcTasks->taskMerchants->tracking_id, strrpos($ctc->sprintCtcTasks->taskMerchants->tracking_id, '_') + 1) . ",";
                    }
                    else
                    {
                        echo $ctc->sprintCtcTasks->taskMerchants->tracking_id . ",";
                    }
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }
//            echo ($actual_delivery_status == 13) ? "At hub - processing"."\t" : self::$status[$actual_delivery_status] . "\t";
            if (!empty($actual_delivery_status)) {
                echo ($actual_delivery_status == 13) ? "At hub - processing" . "," : str_replace(",","-",self::$status[$actual_delivery_status])  . ",";
            } else {
                echo "" . ",";
            }
            echo $actual_delivery . ",";
            if ($ctc->sprintCtcTasks) {
                if ($ctc->sprintCtcTasks->taskMerchants) {
                    if (str_contains($ctc->sprintCtcTasks->taskMerchants->tracking_id, 'old_')) {
                        echo "https://www.joeyco.com/track-order/" . substr($ctc->sprintCtcTasks->taskMerchants->tracking_id, strrpos($ctc->sprintCtcTasks->taskMerchants->tracking_id, '_') + 1) . ",";
                    }
                    else{
                        echo "https://www.joeyco.com/track-order/" .$ctc->sprintCtcTasks->taskMerchants->tracking_id. ",";
                    }
                } else {
                    echo '' . ",";
                }
            } else {
                echo '' . ",";
            }


            echo $notes . ",";
            echo "\n";

        }

    }
    public function getLocationMap($id,Request $request)
    {

        $date=$request->input('date');
        if($id==0)
        {
            $hub=17;
            $id=[477542,477255,477254,477283,477284,477286,477287,477288,477289,477307,477308,477309,477310,477311,477312,477313,477314,477292,477294,477315,477317,477316,477295,477302,477303,477304,477305,477306,477296,477290,477297,477298,477299,477300,477320,477301,477318,477334,477335,477336,477337,477338,477339,477171,477559,477625,477607,477587,477621,477627];
        }
        elseif($id==477260)
        {
            $hub=16;
        }
        elseif($id==477282 || $id==477340 ||  $id==477341 ||  $id==477342 || $id==477343 ||  $id==477344 ||  $id==477345 || $id==477346 || $id== 476592 || $id== 477631 || $id==477629)
        {
            $hub=19;
        }
        elseif($id==477607 || $id==477609 ||  $id==477613 ||  $id==477589 || $id==477641)
        {
            $hub=129;
        }
        elseif($id == 477631 || $id == 477629)
        {
            $hub=19;
        }
        //$date=date('Y-m-d',strtotime("-3 day", strtotime($date)));
        $datas=JoeyRoutes::
        where('hub','=',$hub)->
        where('joey_routes.date','like',$date."%")
            ->get(['joey_routes.id as route_id']);

        $value=[];
        $i=0;
        $key=[];
        foreach($datas as $data)
        {

            $location= JoeyRouteLocations::join('sprint__tasks','sprint__tasks.id','=','joey_route_locations.task_id')
                ->join('locations','locations.id','=','sprint__tasks.location_id')
                ->where('type','=','dropoff')
                ->where('joey_route_locations.route_id','=',$data->route_id)
                ->get(['locations.longitude','locations.latitude','sprint__tasks.sprint_id','locations.address','joey_route_locations.ordinal']);

            if(!empty( $location))
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
            //     $i=0;
            //     $k=0;
            //     for($i=0;$i<5;$i++){
            //     for($j=0;$j<5;$j++)
            //     {
            //         $value[$i][$j]['longitude']=-79.627221+$k;
            //         $value[$i][$j]['latitude']=43.630173+$k;
            //         $value[$i][$j]['route_id']=$i;
            //         $value[$i][$j]['sprint_id']=$j;
            //         $value[$i][$j]['address']='5030 Maingate Dr';
            //         $k++;

            //     }
            // }
            $i++;
        }

        $value['key']=$key;

        return json_encode($value);
    }
    public function remainigRouteMap($route_id){

        $routes = JoeyRouteLocations::join('sprint__tasks','task_id','=','sprint__tasks.id')
            ->join('locations','location_id','=','locations.id')
            ->where('route_id','=',$route_id)
            ->whereNull('joey_route_locations.deleted_at')
            ->whereNotIn('status_id',[38,36,17,112,113,114,116,117,118,132,136,138,139,143,144,104,105,106,107,108,109,110,111,131,135])
            ->orderBy('joey_route_locations.ordinal')
            ->get(['type','route_id','joey_route_locations.ordinal','sprint_id','address','postal_code','latitude','longitude']);
        $i=0;

        if($routes->isEmpty()){
            $data = $i;
        }
        else{
            foreach($routes as $key => $route) {

                $data[$i]['type'] = $route->type;
                $data[$i]['route_id'] = $route->route_id;
                $data[$i]['ordinal'] = $route->ordinal;
                $data[$i]['sprint_id'] = $route->sprint_id;
                $data[$i]['address'] = $route->address;
                $data[$i]['postal_code'] = $route->postal_code;

                // $lat[0] = substr($route->latitude, 0, 2);
                // $lat[1] = substr($route->latitude, 2);
                // $data[$i]['latitude'] = floatval($lat[0].".".$lat[1]);
                $data[$i]['latitude'] = $route->latitude / 1000000;

                // $long[0] = substr($route->longitude, 0, 3);
                // $long[1] = substr($route->longitude, 3);
                // $data[$i]['longitude'] = floatval($long[0].".".$long[1]);
                $data[$i]['longitude'] = $route->longitude / 1000000;
                $i++;
            }
        }

        return json_encode($data);
    }

    public function RouteMap($route_id){

        $routes = JoeyRouteLocations::join('sprint__tasks','task_id','=','sprint__tasks.id')
            ->join('locations','location_id','=','locations.id')
            ->where('route_id','=',$route_id)
            ->whereNotIn('status_id',[38,36])
            ->whereNull('joey_route_locations.deleted_at')
            ->orderBy('joey_route_locations.ordinal')
            ->get(['type','route_id','joey_route_locations.ordinal','sprint_id','address','postal_code','latitude','longitude']);

        $i=0;
        $data=[];
        foreach($routes as $route){

            $data[] = $route;

            // $lat[0] = substr($route->latitude, 0, 2);
            // $lat[1] = substr($route->latitude, 2);
            // $data[$i]['latitude'] = floatval($lat[0].".".$lat[1]);
            $data[$i]['latitude'] = $route->latitude/1000000;

            // $long[0] = substr($route->longitude, 0, 3);
            // $long[1] = substr($route->longitude, 3);
            // $data[$i]['longitude'] = floatval($long[0].".".$long[1]);
            $data[$i]['longitude'] = $route->longitude/1000000;
            $i++;

        }

        return json_encode($data);
    }


}
