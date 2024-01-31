<?php

namespace App\Http\Controllers\Backend;

use App\BoradlessDashboard;
use App\Classes\Fcm;
use App\CustomerFlagCategories;
use App\FlagHistory;
use App\Http\Traits\BasicModelFunctions;
use App\JoeyRouteLocations;
use App\JoeyRoutes;
use App\Reason;
use App\Sprint;
use App\MerchantIds;
use App\SprintReattempt;
use App\SprintTaskHistory;
use App\TaskHistory;
use App\TrackingDelay;
use App\TrackingNote;
use App\UserDevice;
use App\UserNotification;
use Illuminate\Http\Request;
use App\Task;
use App\Notes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;


class ShipHeroController extends BackendController
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
     * Get ShipHero Dashboard
     */
    public function getShipHeroDashboard(Request $request)
    {

        $city = !empty($request->get('city')) ? $request->get('city') : 'all';
        $status_code = array_intersect_key(self::$status, [61 => '', 124 => '', 121 => '', 133 => '', 17 => '', 113 => '', 114 => '', 116 => '', 117 => '', 118 => '', 132 => '', 138 => '', 139 => '', 144 => '', 104 => '', 105 => '', 106 => '', 107 => '',
            108 => '', 109 => '', 110 => '', 111 => '', 112 => '', 131 => '', 135 => '', 136 => '']);
        return backend_view('ship_hero_dashboard.ship_hero_dashboard', compact('status_code','city'));
    }

    /**
     * Yajra call after ShipHero Dashboard
     */
    public function getShipHeroDashboardData(Datatables $datatables, Request $request)
    {
        $sprintId = 0;
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");

        $shipheroVendorIds = 477542;

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
        if (!empty($request->get('route_id'))) {
            $task_ids = JoeyRouteLocations::where('route_id', $request->get('route_id'))->where('deleted_at', null)->pluck('task_id');

            if ($task_ids) {
                $sprintIds = Task::whereIn('id', $task_ids)->pluck('sprint_id');
            }
        }
        if (!empty($request->get('tracking_id'))) {
            $query = BoradlessDashboard::where('sprint_id', $sprintId)->where('deleted_at', null);
        }
        else if (!empty($request->get('route_id'))) {
            $query = BoradlessDashboard::whereIn('sprint_id', $sprintIds)->where('deleted_at', null);
        }
        else {
            $query = BoradlessDashboard::where('creator_id', $shipheroVendorIds)->where('created_at','>',$start)->where('created_at','<',$end)
                ->whereNotIn('task_status_id', [38, 36]);
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
                return $record->address_line_2 ? $record->address_line_2 : '';
            })
            ->addColumn('action', static function ($record) {
                return backend_view('ship_hero_dashboard.action', compact('record'));
            })
            ->make(true);

    }

    /**
     * Get ShipHero Dashboard Excel Report
     */
    public function shipHeroDashboardExcel($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');

        }
        $file_name = new \DateTime($date);
        $file_name = $file_name->format("M d, Y");
        $file_name = "Shiphero Tracking File " . $file_name . ".csv";

        $shipheroVendorIds = 477542;

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        $shiphero_data = BoradlessDashboard::where('creator_id', $shipheroVendorIds)->where('updated_at','>',$start)->where('updated_at','<',$end)
            ->whereNotIn('task_status_id', [38, 36])->get();
        // header info for browser


        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name);
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "JoeyCo Order,Route,Joey,Store Name,Customer Name,Customer Address,Postal Code,City Name,Weight,Pickup From Store,1st Attempt - At Hub Processing,1st Attempt - Out For Delivery,1st Attempt - Estimated Customer Delivery Time,1st Attempt - Delivery,1st Attempt - Shipment Delivery Status,2nd Attempt - At Hub Processing,2nd Attempt - Out For Delivery,2nd Attempt - Estimated Customer Delivery Time,2nd Attempt - Delivery,2nd Attempt - Shipment Delivery Status,3rd Attempt - At Hub Processing,3rd Attempt - Out For Delivery,3rd Attempt - Estimated Customer Delivery Time,3rd Attempt - Delivery,3rd Attempt - Shipment Delivery Status,Shipment Tracking #,Actual Delivery Status,Actual Delivery,Shipment Tracking Link,JoyeCo Notes / Comments,\n";


        foreach ($shiphero_data as $boradless_rec) {

            $boradless = null;
            if ($boradless_rec->sprintReattempts) {
                if ($boradless_rec->sprintReattempts->reattempts_left == 0) {
                    $boradless =  $firstSprint = BoradlessDashboard::where('sprint_id', '=', $boradless_rec->sprintReattempts->reattempt_of)->first();
                }
                else
                {
                    $boradless = $boradless_rec;
                }
            }
            else
            {
                $boradless = $boradless_rec;
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
            $pickup = $boradless->pickupFromStore()->pickup;
            $hubreturned = "";//$boradless->atHubProcessing()->athub;
            $hubpickup = "";// $boradless->outForDelivery()->outdeliver;
            $deliver = "";//$boradless->deliveryTime()->delivery_time;
            $actual_delivery = $boradless->actualDeliveryTime()->actual_delivery;
            $actual_delivery_status = '';

            $eta_time = "";
            if ($pickup) {
                $eta_time = date('Y-m-d', strtotime("+1 day", strtotime($pickup))).' 21:00:00';
            }
            $status = $boradless->task_status_id;
            if ($boradless->task_status_id == 17) {
                $preStatus = \App\SprintTaskHistory::where('sprint_id', '=', $boradless->sprint_id)
                    ->where('status_id', '!=', '17')
                    ->orderBy('id', 'desc')->first();
                if (!empty($preStatus)) {
                    $status = $preStatus->status_id;
                }
            }
            if ($boradless->actualDeliveryTime()->actual_delivery != null) {
                $check_actual = true;
                $actual_delivery_status = $boradless->actualDeliveryTime()->status_id;

            }
            $notes1 = Notes::where('object_id', $boradless->sprint_id)->pluck('note');
            $i = 0;
            foreach ($notes1 as $note) {
                if ($i == 0)
                    $notes = $notes . $note;
                else
                    $notes = $notes . ', ' . $note;
            }
            if ($boradless->sprintReattempts) {
                if ($boradless->sprintReattempts->reattempts_left == 0) {

                    $hubreturned3 = $boradless->atHubProcessing()->athub;
                    $hubpickup3 = $boradless->outForDelivery()->outdeliver;
                    $deliver3 = $boradless->deliveryTime()->delivery_time;
                    if ($hubreturned3) {
                        $eta_time3 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned3))).' 21:00:00';
                    }
                    $status3 = $boradless->task_status_id;
                    if ($boradless->task_status_id == 17) {
                        $preStatus = \App\SprintTaskHistory
                            ::where('sprint_id', '=', $boradless->sprint_id)
                            ->where('status_id', '!=', '17')
                            ->orderBy('id', 'desc')->first();
                        if (!empty($preStatus)) {
                            $status3 = $preStatus->status_id;
                        }
                    }

                    $secondAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $boradless->sprintReattempts->reattempt_of)->orderBy('created_at', 'ASC')
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

                            $eta = BoradlessDashboard::where('sprint_id', $boradless->sprintReattempts->reattempt_of)->first();
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

                    $firstSprint = \App\SprintReattempt::where('sprint_id', '=', $boradless->sprintReattempts->reattempt_of)->first();
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
                if ($boradless->sprintReattempts->reattempts_left == 1) {

                    $hubreturned3 = $boradless->atHubProcessing()->athub;
                    $hubpickup3 = $boradless->outForDelivery()->outdeliver;
                    $deliver3 = $boradless->deliveryTime()->delivery_time;
                    if ($hubreturned3) {
                        $eta_time3 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned3))).' 21:00:00';
                    }
                    $status3 = $boradless->task_status_id;
                    if ($boradless->task_status_id == 17) {
                        $preStatus = \App\SprintTaskHistory
                            ::where('sprint_id', '=', $boradless->sprint_id)
                            ->where('status_id', '!=', '17')
                            ->orderBy('id', 'desc')->first();
                        if (!empty($preStatus)) {
                            $status3 = $preStatus->status_id;
                        }
                    }

                    $secondAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $boradless->sprintReattempts->reattempt_of)->orderBy('created_at', 'ASC')
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

                            $eta = BoradlessDashboard::where('sprint_id', $boradless->sprintReattempts->reattempt_of)->first();
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

                    $firstSprint = \App\SprintReattempt::where('sprint_id', '=', $boradless->sprintReattempts->reattempt_of)->first();
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
                if ($boradless->sprintReattempts->reattempts_left == 2) {

                    $hubreturned2 = $boradless->atHubProcessing()->athub;
                    $hubpickup2 = $boradless->outForDelivery()->outdeliver;
                    $deliver2 = $boradless->deliveryTime()->delivery_time;

                    if ($hubreturned2) {
                        $eta_time2 = date('Y-m-d', strtotime("+1 day", strtotime($hubreturned2))).' 21:00:00';
                    }
                    $status2 = $boradless->task_status_id;
                    if ($boradless->task_status_id == 17) {
                        $preStatus = \App\SprintTaskHistory
                            ::where('sprint_id', '=', $boradless->sprint_id)
                            ->where('status_id', '!=', '17')
                            ->orderBy('id', 'desc')->first();
                        if (!empty($preStatus)) {
                            $status2 = $preStatus->status_id;
                        }
                    }

                    $secondAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $boradless->sprintReattempts->reattempt_of)->orderBy('created_at', 'ASC')->
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

                            $eta = BoradlessDashboard::where('sprint_id', $boradless->sprintReattempts->reattempt_of)->first();
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
                $hubreturned = $boradless->atHubProcessingFirst()->athub;
                $hubpickup = $boradless->outForDelivery()->outdeliver;
                $deliver = $boradless->deliveryTime()->delivery_time;
            }

            echo $boradless->sprint_id . ",";

            if ($boradless->route_id) {
                echo 'R-' . $boradless->route_id . '-' . $boradless->ordinal . ",";
            } else {
                echo " " . ",";
            }

            if ($boradless->joey_name) {
                echo str_replace(",", "-", $boradless->joey_name . ' (' . $boradless->joey_id . ')') . ",";
            } else {
                echo "" . ",";
            }

            if ($boradless->store_name) {
                echo str_replace(",","-",$boradless->store_name ) . ",";
            } else {
                echo "" . ",";
            }

            if ($boradless->customer_name) {
                echo str_replace(",","-",$boradless->customer_name ) . ",";
            } else {
                echo "" . ",";
            }

            if ($boradless->sprintBoradlessTasks) {
                if ($boradless->sprintBoradlessTasks->taskMerchants) {
                    echo str_replace(",","-",$boradless->sprintBoradlessTasks->taskMerchants->address_line2 ) . ",";
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }

            if ($boradless->sprintBoradlessTasks) {
                if ($boradless->sprintBoradlessTasks->task_Location) {
                    echo str_replace(",","-",$boradless->sprintBoradlessTasks->task_Location->postal_code )  . ",";
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }
            if ($boradless->sprintBoradlessTasks) {
                if ($boradless->sprintBoradlessTasks->task_Location) {
                    if ($boradless->sprintBoradlessTasks->task_Location->city) {
                        echo str_replace(",","-",$boradless->sprintBoradlessTasks->task_Location->city->name )  . ",";
                    } else {
                        echo "" . ",";
                    }
                } else {
                    echo "" . ",";
                }
            } else {
                echo "" . ",";
            }

            if ($boradless->sprintBoradlessTasks) {
                if ($boradless->sprintBoradlessTasks->taskMerchants) {
                    echo $boradless->sprintBoradlessTasks->taskMerchants->weight . $boradless->sprintBoradlessTasks->taskMerchants->weight_unit . ",";
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


            if ($boradless->tracking_id) {
                if (str_contains($boradless->tracking_id, 'old_')) {
                    echo substr($boradless->tracking_id, strrpos($boradless->tracking_id, '_') + 1) . ",";
                }
                else
                {
                    echo $boradless->tracking_id . ",";
                }
            } else {
                echo "" . ",";
            }
            if (!empty($actual_delivery_status)) {
                echo ($actual_delivery_status == 13) ? "At hub - processing" . "," : str_replace(",","-",self::$status[$actual_delivery_status])  . ",";
            } else {
                echo "" . ",";
            }
            echo $actual_delivery . ",";
            if ($boradless->tracking_id) {
                if (str_contains($boradless->tracking_id, 'old_')) {
                    echo "https://www.joeyco.com/track-order/" . substr($boradless->tracking_id, strrpos($boradless->tracking_id, '_') + 1) . ",";
                }
                else{
                    echo "https://www.joeyco.com/track-order/" .$boradless->tracking_id. ",";
                }
            } else {
                echo '' . ",";
            }


            echo $notes . ",";
            echo "\n";


        }

    }

    /**
     * Get ShipHero Dashboard Excel OTD Report
     */
    public function shipHeroDashboardExcelOtdReport($date = null)
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

        $end_dt = new DateTime($otd_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        $file_name = new \DateTime($date);
        $file_name = $file_name->format("M d, Y");
        $file_name = "ShipHero OTD Report " . $file_name . ".csv";
        $sprint_id = SprintTaskHistory::where('created_at','>',$start)->where('created_at','<',$end)->where('status_id', 125)->pluck('sprint_id');

        $shipheroVendorIds = 477542;
        $shiphero_data = BoradlessDashboard::where('creator_id', $shipheroVendorIds)->whereIn('sprint_id', $sprint_id)->whereNotIn('task_status_id', [38, 36])->get();
        // header info for browser


        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name);
        header('Pragma: no-cache');
        header('Expires: 0');


        echo "Shipment Tracking #,Pickup From Store,1st Attempt - At Hub Processing,1st Attempt - Out For Delivery,1st Attempt - Estimated Customer Delivery Time,1st Attempt - Delivery,1st Attempt - Shipment Delivery Status,2nd Attempt - At Hub Processing,2nd Attempt - Out For Delivery,2nd Attempt - Estimated Customer Delivery Time,2nd Attempt - Delivery,2nd Attempt - Shipment Delivery Status,3rd Attempt - At Hub Processing,3rd Attempt - Out For Delivery,3rd Attempt - Estimated Customer Delivery Time,3rd Attempt - Delivery,3rd Attempt - Shipment Delivery Status,Actual Delivery Status,Actual Delivery,Shipment Tracking Link,JoyeCo Notes / Comments,\n";


        foreach ($shiphero_data as $boradless) {
            $trackingid = '';
            if ($boradless->tracking_id) {
                if (strpos($boradless->tracking_id, 'old') !== false) {
                    $trackingid = substr($boradless->tracking_id, strrpos($boradless->tracking_id, '_') + 1);
                } else {
                    $trackingid = $boradless->tracking_id;
                }
            }
            if(!$boradless->sprintReattempts) {
                if (date("Y-m-d", strtotime($boradless->pickupFromStoreOtd($otd_date)->pickup)) == $otd_date) {
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
                    $pickup = $boradless->pickupFromStoreOtd($otd_date)->pickup;
                    $hubreturned = $boradless->atHubProcessingOtd()->athub;
                    $hubpickup = $boradless->outForDelivery()->outdeliver;
                    $deliver = $boradless->deliveryTimeOTD()->delivery_time;
                    $actual_delivery = $boradless->actualDeliveryTime()->actual_delivery;
                    $actual_delivery_status = '';

                    $eta_time = "";
                    if ($pickup) {
                        $eta_time = date('Y-m-d', strtotime("+1 day", strtotime($pickup))).' 21:00:00';
                    }
                    $status = $boradless->task_status_id;
                    if ($boradless->task_status_id == 17) {
                        $preStatus = \App\SprintTaskHistory
                            ::where('sprint_id', '=', $boradless->sprint_id)
                            ->where('status_id', '!=', '17')
                            ->orderBy('id', 'desc')->first();
                        if (!empty($preStatus)) {
                            $status = $preStatus->status_id;
                        }
                    }
                    if ($boradless->actualDeliveryTime()->actual_delivery != null) {
                        $actual_delivery_status = $boradless->actualDeliveryTime()->status_id;
                    }
                    $notes1 = Notes::where('object_id', $boradless->sprint_id)->pluck('note');
                    $i = 0;
                    foreach ($notes1 as $note) {
                        if ($i == 0)
                            $notes = $notes . $note;
                        else
                            $notes = $notes . ', ' . $note;
                    }
                    if ($boradless->sprintReattemptsOTD) {


                        $secondAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $boradless->sprintReattemptsOTD->sprint_id)->orderBy('created_at', 'ASC')
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
                                if (in_array($secAttempt->status_id, [17, 113, 114, 116, 117, 118, 132, 138, 139, 144])) {
                                    $actual_delivery = date('20y-m-d H:i:s', strtotime($secAttempt->created_at));
                                }
                                $eta = BoradlessDashboard::where('sprint_id', $boradless->sprintReattemptsOTD->sprint_id)->first();
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

                        $firstSprint = \App\SprintReattempt::where('reattempt_of', '=', $boradless->sprintReattemptsOTD->sprint_id)->first();
                        if (!empty($firstSprint)) {
                            $firstAttempt = \App\SprintTaskHistory::where('sprint_id', '=', $firstSprint->sprint_id)->orderBy('created_at', 'ASC')->
                            get(['status_id', \DB::raw("CONVERT_TZ(sprint__tasks_history.created_at,'UTC','America/Toronto') as created_at")]);
                            if (!empty($firstAttempt)) {

                                foreach ($firstAttempt as $firstAttempt) {

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
                        $hubreturned = $boradless->atHubProcessingFirst()->athub;
                    }
                    if ($boradless->tracking_id) {
                        if (strpos($boradless->tracking_id, 'old') !== false) {
                            echo substr($boradless->tracking_id, strrpos($boradless->tracking_id, '_') + 1) . ",";
                        } else {
                            echo $boradless->tracking_id . ",";
                        }
                    } else {
                        echo "" . ",";
                    }

                    echo $pickup . ",";
                    if (!empty($hubreturned)) {
                        echo $hubreturned . ",";
                    } else {
                        echo $boradless->atHubProcessingOtd()->athub . ",";
                    }

                    if (!empty($hubpickup)) {
                        echo $hubpickup . ",";
                    } else {
                        echo $boradless->outForDelivery()->outdeliver . ",";
                    }

                    echo $eta_time . ",";


                    if (!empty($deliver)) {
                        echo $deliver . ",";
                    } else {
                        echo $boradless->deliveryTimeOTD()->delivery_time . ",";
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
                    if ($boradless->tracking_id) {
                        if (strpos($boradless->tracking_id, 'old') !== false) {
                            echo "https://www.joeyco.com/track-order/" . substr($boradless->tracking_id, strrpos($boradless->tracking_id, '_') + 1) . ",";
                        } else {
                            echo "https://www.joeyco.com/track-order/" . $boradless->tracking_id . ",";
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

    /**
     * Get ShipHero Order detail
     */
    public function shipHeroProfile(Request $request, $id)
    {
        $shiphero_data = $this->get_trackingorderdetails($id);

        $sprintId = $shiphero_data['sprintId'];
        $data = $shiphero_data['data'];

        return backend_view('ship_hero_dashboard.ship_hero_profile', compact('data', 'sprintId'));
    }

    public function get_trackingorderdetails($sprintId)
    {
        //dd($sprintId);
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
                ->get(['status_id', \DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto') as created_at")]);

            $returnTOHubDate = SprintReattempt::where('sprint_reattempts.sprint_id', '=', $tasks->sprint_id)
                ->orderBy('created_at')
                ->first();

            if (!empty($returnTOHubDate)) {
                $taskHistoryre = TaskHistory::where('sprint_id', '=', $returnTOHubDate->reattempt_of)->WhereNotIn('status_id', [17, 38])->orderBy('date')
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
                $returnTO2 = SprintReattempt::where('sprint_reattempts.sprint_id', '=', $returnTOHubDate->reattempt_of)
                    ->orderBy('created_at')
                    ->first();

                if (!empty($returnTO2)) {
                    $taskHistoryre = TaskHistory::where('sprint_id', '=', $returnTO2->reattempt_of)->WhereNotIn('status_id', [17, 38])->orderBy('date')
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
    }

    public function getShipHeroCards(Request $request)
    {
        $type = 'all';
        return backend_view('ship_hero_dashboard.ship_hero_card_dashboard', compact( 'type'));
    }

    public function shipHeroTotalCards($date, $type)
    {
        $response = [];
        $date = !empty($date) ? $date : date("Y-m-d");

        $boradlessVendorIds = 477542;

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $taskIds = DB::table('boradless_dashboard')->where('creator_id', $boradlessVendorIds)
            ->where('created_at','>',$start)
            ->where('created_at','<',$end)
            ->where('is_custom_route', 0)
            ->whereNotIn('task_status_id', [38, 36])
            ->whereNull('deleted_at')
            ->pluck('task_id');

        $boradless = new BoradlessDashboard();
        $boradless_count = $boradless->getBoradlessCounts($taskIds, $type);
        $response['boradless_count'] = $boradless_count;
        return $response;
    }

    public function shipHeroInProgressOrders($date, $type)
    {
        $response = [];
        $date = !empty($date) ? $date : date("Y-m-d");

        $boradlessVendorIds = 477542;

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $taskIds = DB::table('boradless_dashboard')->where('creator_id', $boradlessVendorIds)
            ->where('created_at','>',$start)
            ->where('created_at','<',$end)
            ->where('is_custom_route', 0)
            ->whereNotIn('task_status_id', [38, 36])
            ->whereNull('deleted_at')
            ->pluck('task_id');

        $boradless = new BoradlessDashboard();
        $boradless_count = $boradless->getInprogressOrders($taskIds, $type);
        $response['boradless_inprogess_count'] = $boradless_count;
        return $response;
    }

    public function shipHeroCustomRouteData($date)
    {
        $response = [];
        $date = !empty($date) ? $date : date("Y-m-d");

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        $boradlessVendorIds = 477542;
        $custom_route = DB::table('boradless_dashboard')->where('creator_id', $boradlessVendorIds)
            ->where('created_at','>',$start)
            ->where('created_at','<',$end)
            ->whereNull('deleted_at')
            ->where('is_custom_route', 1)
            ->count();
        $response['custom_route'] = $custom_route;
        return $response;
    }

    public function getShipHeroYesterdayOrderData($date)
    {
        $response = [];
        $date = !empty($date) ? $date : date("Y-m-d");
        $boradlessVendorIds = 477542;

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        $yesterday_return_orders = DB::table('boradless_dashboard')->where('creator_id', $boradlessVendorIds)
            ->join('sprint_reattempts', 'boradless_dashboard.sprint_id', '=', 'sprint_reattempts.sprint_id')
            ->where('boradless_dashboard.created_at','>',$start)
            ->where('boradless_dashboard.created_at','<',$end)
            ->whereNull('deleted_at')
            ->count();
        $response['yesterday_return_orders'] = $yesterday_return_orders;
        return $response;
    }

    public function getShipHero(Request $request)
    {
        $type = 'total';
        return backend_view('ship_hero_dashboard.ship_hero_order_dashboard', compact( 'type'));
    }

    public function getShipHeroData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $boradlessVendorIds = 477542;


        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        $query = BoradlessDashboard::where('created_at','>',$start)
            ->where('created_at','<',$end)
            ->where('creator_id', $boradlessVendorIds)
            ->where('is_custom_route', 0)
            ->whereNotIn('task_status_id', [38, 36]);
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
                return backend_view('boradless_dashboard.order_action', compact('record'));
            })
            ->make(true);
    }

    public function getShipHeroReturned(Request $request)
    {
        $title_name = 'Borderless';
        $type = 'return';
        return backend_view('ship_hero_dashboard.returned_orders', compact('title_name',  'type'));
    }

    public function shipHeroReturnedData(Datatables $datatables, Request $request)
    {

        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $boradlessVendorIds = 477542;

        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        $query = BoradlessDashboard::where('created_at','>',$start)->where('created_at','<',$end)->where('creator_id', $boradlessVendorIds)->whereNotIn('task_status_id', [38, 36])
            ->whereIn('task_status_id', [101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 131, 135, 136, 143])
            ->where('is_custom_route', 0);

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
                return backend_view('boradless_dashboard.action_returned', compact('record'));
            })
            ->make(true);
    }

    public function getShipHeroNotReturned(Request $request)
    {
        $title_name = 'Borderless';
        $type = 'return';
        return backend_view('ship_hero_dashboard.not_returned_orders', compact('title_name',  'type'));
    }

    public function shipHeroNotReturnedData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $boradlessVendorIds = 477542;

        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        $query = BoradlessDashboard::where('created_at','>',$start)->where('created_at','<',$end)->where('creator_id', $boradlessVendorIds)->whereNotIn('task_status_id', [38, 36])
            ->whereIn('task_status_id', [101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 131, 135, 136, 143])
            ->where('is_custom_route', 0)
            ->whereNull('hub_return_scan');

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
                return backend_view('boradless_dashboard.action_notreturned', compact('record'));
            })
            ->make(true);
    }

    public function getShipHeroSorter(Request $request)
    {
        $title_name = 'Borderless';
        $type = 'sorted';
        return backend_view('ship_hero_dashboard.sorted_order', compact('title_name',  'type'));
    }

    public function shipHeroSortedData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $boradlessVendorIds = 477542;

        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        $query = BoradlessDashboard::where('created_at','>',$start)->where('created_at','<',$end)->where('creator_id', $boradlessVendorIds)->where('is_custom_route', 0)->whereNotIn('task_status_id', [38, 36])
            ->where(['task_status_id' => 133]);
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
                return backend_view('boradless_dashboard.action_sorted', compact('record'));
            })
            ->make(true);
    }

    public function shipHeroSortedExcel($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');
        }
        $boradlessVendorIds = 477542;

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $boradless_data = BoradlessDashboard::where('created_at','>',$start)->where('created_at','<',$end)->where('creator_id', $boradlessVendorIds)->where('is_custom_route', 0)->where(['task_status_id' => 133])->whereNotIn('task_status_id', [38, 36])->get();
        $boradless_array[] = ['JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Out For Delivery', 'Sorted Time', 'Actual Arrival @ CX', 'Boradless tracking #', 'Status'];
        foreach ($boradless_data as $boradless) {
            $picked_up_at = '';
            $sorted_at = '';
            $delivered_at = '';
            if ($boradless->picked_up_at) {
                $picked_up_at = new \DateTime($boradless->picked_up_at, new \DateTimeZone('UTC'));
                $picked_up_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                $picked_up_at->format('Y-m-d H:i:s');
            }
            if ($boradless->sorted_at) {
                $sorted_at = new \DateTime($boradless->sorted_at, new \DateTimeZone('UTC'));
                $sorted_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                $sorted_at->format('Y-m-d H:i:s');
            }
            if ($boradless->delivered_at) {
                $delivered_at = new \DateTime($boradless->delivered_at, new \DateTimeZone('UTC'));
                $delivered_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                $delivered_at->format('Y-m-d H:i:s');
            }
            $boradless_array[] = [
                'JoeyCo Order #' => strval($boradless->sprint_id),
                'Route Number' => $boradless->route_id ? strval('R-' . $boradless->route_id . '-' . $boradless->ordinal) : '',
                'Joey' => $boradless->joey_name ? strval($boradless->joey_name . ' (' . $boradless->joey_id . ')') : '',
                'Customer Address' => strval($boradless->address_line_1),
                'Out For Delivery' => $picked_up_at,
                'Sorter Time' => $sorted_at,
                'Actual Arrival @ CX' => $delivered_at,
                'Boradless tracking #' => strval(substr($boradless->tracking_id, ($pos = strrpos($boradless->tracking_id, '_')) == false ? 0 : $pos + 1)),
                'Status' => $boradless->task_status_id ? strval(self::$status[$boradless->task_status_id]):''
            ];

        }
        Excel::create('BorderLess Sorted Data ' . $date . '', function ($excel) use ($boradless_array) {
            $excel->setTitle('BorderLess Sorted Data');
            $excel->sheet('BorderLess Sorted Data', function ($sheet) use ($boradless_array) {
                $sheet->fromArray($boradless_array, null, 'A1', false, false);
            });
        })->download('csv');
    }

    public function getShipHeroHub(Request $request)
    {
        $title_name = 'Borderless';
        $type = 'picked';
        return backend_view('boradless_dashboard.pickup_hub', compact('title_name',  'type'));
    }

    public function shipHeroPickedUpData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $boradlessVendorIds = 477542;

        $start_dt = new DateTime($today_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($today_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        $query = BoradlessDashboard::where('created_at','>',$start)->where('created_at','<',$end)->where('creator_id', $boradlessVendorIds)->where('is_custom_route', 0)->whereNotIn('task_status_id', [38, 36])
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
                return backend_view('boradless_dashboard.action_pickup', compact('record'));
            })
            ->make(true);
    }

    public function shipHeroPickupDetail(Request $request, $id)
    {
        $boradless_id = base64_decode($id);
        $data = $this->get_trackingorderdetails($boradless_id);
        $sprintId = $data['sprintId'];
        $data = $data['data'];
        return backend_view('boradless_dashboard.boradless_pickup_detail', compact('data', 'sprintId'));
    }

    public function shipHeroPickedUpExcel($date = null)
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

        $boradlessVendorIds = 477542;
        $boradless_data = BoradlessDashboard::where('created_at','>',$start)->where('created_at','<',$end)->where('is_custom_route', 0)->where('creator_id', $boradlessVendorIds)->where(['task_status_id' => 121])->whereNotIn('task_status_id', [38, 36])->get();
        $boradless_array[] = ['JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Out For Delivery', 'Sorted Time', 'Actual Arrival @ CX', 'Boradless tracking #', 'Status'];
        foreach ($boradless_data as $boradless) {
            $picked_up_at = '';
            $sorted_at = '';
            $delivered_at = '';
            if ($boradless->picked_up_at) {
                $picked_up_at = new \DateTime($boradless->picked_up_at, new \DateTimeZone('UTC'));
                $picked_up_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                $picked_up_at->format('Y-m-d H:i:s');
            }
            if ($boradless->sorted_at) {
                $sorted_at = new \DateTime($boradless->sorted_at, new \DateTimeZone('UTC'));
                $sorted_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                $sorted_at->format('Y-m-d H:i:s');
            }
            if ($boradless->delivered_at) {
                $delivered_at = new \DateTime($boradless->delivered_at, new \DateTimeZone('UTC'));
                $delivered_at->setTimeZone(new \DateTimeZone('America/Toronto'));
                $delivered_at->format('Y-m-d H:i:s');
            }
            $boradless_array[] = [
                'JoeyCo Order #' => strval($boradless->sprint_id),
                'Route Number' => $boradless->route_id ? strval('R-' . $boradless->route_id . '-' . $boradless->ordinal) : '',
                'Joey' => $boradless->joey_name ? strval($boradless->joey_name . ' (' . $boradless->joey_id . ')') : '',
                'Customer Address' => strval($boradless->address_line_1),
                'Out For Delivery' => $picked_up_at,
                'Sorter Time' => $sorted_at,
                'Actual Arrival @ CX' => $delivered_at,
                'Boradless tracking #' => strval(substr($boradless->tracking_id, ($pos = strrpos($boradless->tracking_id, '_')) == false ? 0 : $pos + 1)),
                'Status' => $boradless->task_status_id? strval(self::$status[$boradless->task_status_id]): ''
            ];
        }
        Excel::create('BorderLess Picked Up Data ' . $date . '', function ($excel) use ($boradless_array) {
            $excel->setTitle('BorderLess Picked Up Data');
            $excel->sheet('BorderLess Picked Up Data', function ($sheet) use ($boradless_array) {
                $sheet->fromArray($boradless_array, null, 'A1', false, false);
            });
        })->download('csv');
    }



}
