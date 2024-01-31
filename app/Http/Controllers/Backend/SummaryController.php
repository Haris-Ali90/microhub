<?php

namespace App\Http\Controllers\Backend;

use App\BrookerJoey;
use App\BrookerUser;
use App\CTCEntry;
use App\CtcVendor;
use App\CustomerRoutingTrackingId;
use App\FinanceVendorCity;
use App\AmazonEnteries;
use App\FinanceVendorCityDetail;
use App\Http\Traits\BasicModelFunctions;
use App\Joey;
use App\JoeyRouteLocations;
use App\JoeyRoutes;
use App\MerchantIds;
use App\HubStore;
use App\Setting;
use App\Sprint;
use App\SprintTaskHistory;
use App\Task;
use App\TorontoEntries;
use App\TrackingImageHistory;
use App\User;
use App\WarehouseJoeysCount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;
class SummaryController extends BackendController
{
    use BasicModelFunctions;

    public function getSummary(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


        $input = $request->all();
        $hubs = FinanceVendorCity::where('deleted_at', null)->get();
        $hub_id = $user->hub_id;
        $range_from_date = isset($input['datepicker1']) ? $input['datepicker1'] : date('Y-m-d');
        $range_to_date = isset($input['datepicker2']) ? $input['datepicker2'] : date('Y-m-d');
        $start_date = $range_from_date;
        $end_date = $range_to_date;

        $range_from_date = new Carbon($range_from_date);
        $range_to_date = new Carbon($range_to_date);
        while ($range_from_date->lte($range_to_date)) {
            $all_dates[] = $range_from_date->toDateString();
            $range_from_date->addDay();
        }


        return backend_view('statistics.summary', compact('hubs', 'hub_id', 'all_dates','start_date','end_date'));
    }

    public function getSummaryData(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


        $input = $request->all();
        $hub_id = $user->hub_id;
        $date = $input['date_filter'];
        $hub_name = FinanceVendorCity::where('id', $hub_id)->first();
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
        $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
        $sprint = new Sprint();

        $warehouse_data = [];
        #Total Packages
        $montreal_packages = 0;
        $ottawa_packages = 0;
        $ctc_packages = 0;

        #Total Route
        $montreal_route = [];
        $ottawa_route = [];
        $ctc_route = [];

        #Total damaged
        $montreal_damage = 0;
        $ottawa_damage = 0;
        $ctc_damage = 0;

        #Total Not Receive
        $montreal_not_receive = 0;
        $ottawa_not_receive = 0;
        $ctc_not_receive = 0;

        #Total Picked Order
        $montreal_picked_order = 0;
        $ottawa_picked_order = 0;
        $ctc_picked_order = 0;

        #Total Mis Sort
        $montreal_mis_order = 0;
        $ottawa_mis_order = 0;
        $ctc_mis_order = 0;

        #Closing Team
        $montreal_return_order = [];
        $ottawa_return_order = [];
        $ctc_return_order = [];
        $montreal_return_scan = [];
        $ottawa_return_scan = [];
        $ctc_return_scan = [];

        #Lost Package
        $montreal_lost_package = 0;
        $ottawa_lost_package = 0;
        $ctc_lost_package = 0;

        $totallates = 0;
        $totalcount = 0;
        $warehouse_data[0] = [
            'sorter_count' => 0,
            'setup_start_time' => null,
            'setup_end_time' => null,
            'start_sorting_time' => null,
            'end_sorting_time' => null,
            'internal_sorter_count' => 0,
            'brooker_sorter_count' => 0,
            'dispensing_start_time' => null,
            'dispensing_end_time' => null,
            'dispensed_route' => 0,
            'manager_on_duty' => ''
        ];
        $firstOfMonth = date("Y", strtotime($date)) . '-' . date("m", strtotime($date)) . '-01';

        $number_of_sorters = WarehouseJoeysCount::where('hub_id', $hub_id)->where('date', $date)->first();
        if ($number_of_sorters) {
            $warehouse_data[0]['setup_start_time'] = isset($number_of_sorters->setup_start_time) ? date('H:i A', strtotime($number_of_sorters->setup_start_time)) : null;
            $warehouse_data[0]['setup_end_time'] = isset($number_of_sorters->setup_end_time) ? date('H:i A', strtotime($number_of_sorters->setup_end_time)) : null;
            $warehouse_data[0]['start_sorting_time'] = isset($number_of_sorters->start_sorting_time) ? date('H:i A', strtotime($number_of_sorters->start_sorting_time)) : null;
            $warehouse_data[0]['end_sorting_time'] = isset($number_of_sorters->end_sorting_time) ? date('H:i A', strtotime($number_of_sorters->end_sorting_time)) : null;
            $warehouse_data[0]['internal_sorter_count'] = isset($number_of_sorters->internal_sorter_count) ? $number_of_sorters->internal_sorter_count : 0;
            $warehouse_data[0]['brooker_sorter_count'] = isset($number_of_sorters->brooker_sorter_count) ? $number_of_sorters->brooker_sorter_count : 0;
            $warehouse_data[0]['dispensing_start_time'] = isset($number_of_sorters->dispensing_start_time) ? date('H:i A', strtotime($number_of_sorters->dispensing_start_time)) : null;
            $warehouse_data[0]['dispensing_end_time'] = isset($number_of_sorters->dispensing_end_time) ? date('H:i A', strtotime($number_of_sorters->dispensing_end_time)) : null;
            $warehouse_data[0]['dispensed_route'] = isset($number_of_sorters->dispensed_route) ? $number_of_sorters->dispensed_route : 0;
            $warehouse_data[0]['manager_on_duty'] = isset($number_of_sorters->Manager) ? $number_of_sorters->Manager->name : '';


        }
        $status = array_merge($this->getStatusCodes('competed'), $this->getStatusCodes('return'));
        if (in_array('477260', $vendors)) {
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');


            $sprintIds = TorontoEntries::where('created_at','>',$start)->where('created_at','<',$end)
                ->whereIn('creator_id', $data)->pluck('sprint_id')->toArray();

            #Total Packages
            $montreal_packages = TorontoEntries::whereIn('sprint_id', $sprintIds)->whereNotNull('sorted_at')->count();

            #Item Damaged Packages
            $montreal_damage = TorontoEntries::whereIn('sprint_id', $sprintIds)->where('task_status_id', 105)->count();

            #Total Route
            $montreal_route = JoeyRoutes::where('hub',16)->where('date', 'like', $date . "%")->pluck('id')->toArray();

            #Total Not Receive
            $montreal_not_receive = TorontoEntries::whereIn('sprint_id', $sprintIds)->where('task_status_id', 61)->count();

            #Total Lost Packaged
            $montreal_lost_package = TorontoEntries::whereIn('sprint_id', $sprintIds)->where('task_status_id', 141)->count();

            #Total Picked Order
            $montreal_picked_order = TorontoEntries::whereIn('sprint_id', $sprintIds)->whereNotNull('picked_up_at')->count();

            #Total Mis Sort
            $montreal_mis_order = TorontoEntries::whereIn('sprint_id', $sprintIds)->where('task_status_id', 140)->count();

            #Total Return
            $montreal_return_order = TorontoEntries::whereIn('sprint_id', $sprintIds)->whereIn('task_status_id', $this->getStatusCodes('return'))->pluck('id')->toArray();
            $montreal_return_scan = TorontoEntries::whereIn('sprint_id', $sprintIds)->whereIn('task_status_id', $this->getStatusCodes('return'))->whereNotNull('hub_return_scan')->pluck('id')->toArray();

            #Order Deliver or Return
            $query = TorontoEntries::whereIn('sprint_id', $sprintIds)->whereIn('task_status_id', $status)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);

            if (!empty($query)) {
                foreach ($query as $record) {
                    if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                        if (!is_null($record->delivered_at) && $record->delivered_at > $date . " 21:00:00") {
                            $totallates++;
                        }
                    }
                    if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                        if (!is_null($record->returned_at) && $record->returned_at > $date . " 21:00:00") {
                            $totallates++;
                        }
                    }
                    $totalcount++;
                }
            }
        }
        if (in_array('477282', $vendors)) {
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');


            $sprintIds = TorontoEntries::where('created_at','>',$start)->where('created_at','<',$end)
                ->whereIn('creator_id', $data)->pluck('sprint_id')->toArray();

            #Total Packages
            $ottawa_packages = TorontoEntries::whereIn('sprint_id', $sprintIds)->whereNotNull('sorted_at')->count();

            #Item Damaged Packages
            $ottawa_damage = TorontoEntries::whereIn('sprint_id', $sprintIds)->where('task_status_id', 105)->count();

            #Total Route
                 $ottawa_route = JoeyRoutes::where('hub',19)->where('date', 'like', $date . "%")->pluck('id')->toArray();

            #Total Not Receive
            $ottawa_not_receive = TorontoEntries::whereIn('sprint_id', $sprintIds)->where('task_status_id', 61)->count();

            #Total Picked Order
            $ottawa_picked_order =  TorontoEntries::whereIn('sprint_id', $sprintIds)->whereNotNull('picked_up_at')->count();

            #Total Lost Packaged
            $ottawa_lost_package = TorontoEntries::whereIn('sprint_id', $sprintIds)->where('task_status_id', 141)->count();

            #Total Mis Sort
            $ottawa_mis_order = TorontoEntries::whereIn('sprint_id', $sprintIds)->where('task_status_id', 140)->count();

            #Total Return
            $ottawa_return_order = TorontoEntries::whereIn('sprint_id', $sprintIds)->whereIn('task_status_id', $this->getStatusCodes('return'))->pluck('id')->toArray();
            $ottawa_return_scan = TorontoEntries::whereIn('sprint_id', $sprintIds)->whereIn('task_status_id', $this->getStatusCodes('return'))->whereNotNull('hub_return_scan')->pluck('id')->toArray();

            #Order Deliver or Return
            $query = TorontoEntries::whereIn('sprint_id', $sprintIds)->whereIn('task_status_id', $status)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);

            if (!empty($query)) {
                foreach ($query as $record) {
                    if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                        if (!is_null($record->delivered_at) && $record->delivered_at > $date . " 21:00:00") {
                            $totallates++;
                        }
                    }
                    if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                        if (!is_null($record->returned_at) && $record->returned_at > $date . " 21:00:00") {
                            $totallates++;
                        }
                    }
                    $totalcount++;
                }
            }
        }
        if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
            $ctc_ids = array_intersect($ctcVendorIds, $vendors);

            $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            $sprintIds = CTCEntry::whereIn('creator_id', $ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)
                ->pluck('sprint_id')->toArray();

            #Total Packages
            $ctc_packages = CTCEntry::whereIn('sprint_id', $sprintIds)->whereNotNull('sorted_at')->count();

            #Item Damaged Packages
            $ctc_damage = CTCEntry::whereIn('sprint_id', $sprintIds)->where('task_status_id', 105)->count();

            #Total Route
            $ctc_route = JoeyRoutes::where('hub',17)->where('date', 'like', $date . "%")->pluck('id')->toArray();

            #Total Not Receive
            $ctc_not_receive = CTCEntry::whereIn('sprint_id', $sprintIds)->where('task_status_id', 61)->count();

            #Total Lost Packaged
            $ctc_lost_package = CTCEntry::whereIn('sprint_id', $sprintIds)->where('task_status_id', 141)->count();

            #Total Picked Order
            $ctc_picked_order = CTCEntry::whereIn('sprint_id', $sprintIds)->whereNotNull('picked_up_at')->count();

            #Total Mis Sort
            $ctc_mis_order = CTCEntry::whereIn('sprint_id', $sprintIds)->where('task_status_id', 140)->count();

            #Total Return
            $ctc_return_order = CTCEntry::whereIn('sprint_id', $sprintIds)->whereIn('task_status_id', $this->getStatusCodes('return'))->pluck('id')->toArray();
            $ctc_return_scan = CTCEntry::whereIn('sprint_id', $sprintIds)->whereIn('task_status_id', $this->getStatusCodes('return'))->whereNotNull('hub_return_scan')->pluck('id')->toArray();

            #Order Deliver or Return
            $ctc_date = date('Y-m-d', strtotime($date . ' -1 days'));


            $start_dt = new DateTime($ctc_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($ctc_date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');


            $sprint_id = SprintTaskHistory::where('created_at','>',$start)->where('created_at','<',$end)->where('status_id', 125)->pluck('sprint_id');
            $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)->whereIn('task_status_id', $status)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);

            if (!empty($query)) {
                foreach ($query as $record) {
                    if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                        if (!is_null($record->delivered_at) && $record->delivered_at > $date . " 21:00:00") {
                            $totallates++;
                        }
                    }
                    if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                        if (!is_null($record->returned_at) && $record->returned_at > $date . " 21:00:00") {
                            $totallates++;
                        }
                    }
                    $totalcount++;
                }
            }
        }

        $total_return_order = count($montreal_return_order) + count($ottawa_return_order) + count($ctc_return_order);
        $total_return_scan = count($montreal_return_scan) + count($ottawa_return_scan) + count($ctc_return_scan);
        $total_packages = $montreal_packages + $ottawa_packages + $ctc_packages;
        $total_mis_order = $montreal_mis_order + $ottawa_mis_order + $ctc_mis_order;
        $missing_stolen_packages = $montreal_lost_package + $ottawa_lost_package + $ctc_lost_package;

        $warehouse_data[0]['total_packages'] = $total_packages;
        $warehouse_data[0]['total_damaged_packages'] = $montreal_damage + $ottawa_damage + $ctc_damage;
        $warehouse_data[0]['total_system_routes'] = count($montreal_route) + count($ottawa_route) + count($ctc_route);
        $warehouse_data[0]['total_not_receive'] = $montreal_not_receive + $ottawa_not_receive + $ctc_not_receive;
        $warehouse_data[0]['total_mis_order'] = $total_mis_order;
        $warehouse_data[0]['total_picked_order'] = $montreal_picked_order + $ottawa_picked_order + $ctc_picked_order;
        $warehouse_data[0]['total_same_day_returns'] = $total_return_order;
        $warehouse_data[0]['total_return_scan'] = $total_return_scan;
        $warehouse_data[0]['total_not_return_scan'] = $total_return_order - $total_return_scan;
        $warehouse_data[0]['total_completed_deliveries_before_9pm'] = $totalcount - $totallates;
        $warehouse_data[0]['total_completed_deliveries_after_9pm'] = $totallates;
        $warehouse_data[0]['missing_stolen_packages'] = $missing_stolen_packages;

        #Dispencing Accuracy
        $dispencing_accuracy = 0;
        if ($total_packages >= 1) {
            $dispencing_accuracy = round((($missing_stolen_packages + $total_mis_order) / $total_packages) * 100, 2);
        }
        $warehouse_data[0]['dispencing_accuracy'] = $dispencing_accuracy . '%';

        #Dispencing Accuracy 2
        $dispencing_accuracy_2 = 0;
        if ($total_packages >= 1) {
            $dispencing_accuracy_2 = round(100 - ((($missing_stolen_packages + $total_mis_order) / $total_packages) * 100), 2);
        }
        $warehouse_data[0]['dispencing_accuracy_2'] = $dispencing_accuracy_2 . '%';

        #OTD
        $otd = 0;
        if ($totalcount > 0) {
            $otd = 100 - round((($totallates / $totalcount) * 100), 2);
        }
        $warehouse_data[0]['otd'] = round($otd, 2) . '%';

        #Mis Sort Ratio
        $mis_ratio = 0;
        if ($total_packages >= 1) {
            $mis_ratio = round(($total_mis_order / $total_packages) * 100, 2);
        }
        $warehouse_data[0]['total_mis_ratio'] = $mis_ratio . '%';

        #lost packaged ration
        $lost_packages = 0;
        if ($total_packages >= 1) {
            $lost_packages = round(($missing_stolen_packages / $total_packages) * 100);
        }
        $warehouse_data[0]['lost_packages'] = $lost_packages . '%';

        $total_route = 0;
        $normal_route = 0;
        $custom_route = 0;
        $big_box_route = 0;

        $routeIds = array_merge($montreal_route, $ottawa_route, $ctc_route);
        if (!empty($routeIds)) {
            $route_data = JoeyRoutes::whereIn('id',$routeIds)->where('date', 'like', $date . "%")->whereNull('deleted_at')->get();
            foreach ($route_data as $route) {
                $route_location_check = DB::table('joey_route_locations')->where('route_id', $route->id)->whereNull('deleted_at')->first();
                if ($route_location_check) {
                    if ($route->zone != null) {
                        $is_custom_check = \DB::table("zones_routing")->where('id', $route->zone)->whereNull('is_custom_routing')->first();
                        if ($is_custom_check) {
                            $normal_route++;
                        } else {
                            $route_location = DB::table('joey_route_locations')->where('route_id', $route->id)->first();
                            if ($route_location) {
                                $tracking = DB::table('merchantids')->where('task_id', $route_location->task_id)->first();
                                if ($tracking) {
                                    $custom_route_data = DB::table('custom_routing_tracking_id')->where('tracking_id', $tracking->tracking_id)->first();
                                    if ($custom_route_data) {
                                        if ($custom_route_data->is_big_box == 1) {
                                            $big_box_route++;
                                        } else {
                                            $custom_route++;
                                        }
                                    } else {
                                        $custom_route++;
                                    }
                                } else {
                                    $custom_route++;
                                }
                            } else {
                                $custom_route++;
                            }
                        }
                    } else {
                        $route_location = DB::table('joey_route_locations')->where('route_id', $route->id)->first();
                        if ($route_location) {
                            $tracking = DB::table('merchantids')->where('task_id', $route_location->task_id)->first();
                            if ($tracking) {
                                $custom_route_data = DB::table('custom_routing_tracking_id')->where('tracking_id', $tracking->tracking_id)->first();
                                if ($custom_route_data) {
                                    if ($custom_route_data->is_big_box == 1) {
                                        $big_box_route++;
                                    } else {
                                        $custom_route++;
                                    }
                                } else {
                                    $custom_route++;
                                }
                            } else {
                                $custom_route++;
                            }
                        } else {
                            $custom_route++;
                        }
                    }
                    $total_route++;
                }
            }
        }
        $warehouse_data[0]['overall_total_manual_routes'] = $custom_route + $big_box_route;
        $warehouse_data[0]['total_route'] = $total_route;
        $warehouse_data[0]['total_normal_route'] = $normal_route;
        $warehouse_data[0]['total_custom_route'] = $custom_route;
        $warehouse_data[0]['total_big_box_route'] = $big_box_route;
        $warehouse_data[0]['date'] = date('F d, Y', strtotime($date));
        $warehouse_data[0]['day'] = 'Day ' . date('d', strtotime($date));
        $week_count = intval(date("W", strtotime($date))) - intval(date("W", strtotime($firstOfMonth)));
        $week = '';
        if ($week_count == 1) {
            $week = '1st Week';
        } elseif ($week_count == 2) {
            $week = '2nd Week';
        } elseif ($week_count == 3) {
            $week = '3rd Week';
        } elseif ($week_count == 4) {
            $week = '4th Week';
        } else {
            $week = '';
        }
        $warehouse_data[0]['week'] = $week;


        return $warehouse_data;
    }
}
