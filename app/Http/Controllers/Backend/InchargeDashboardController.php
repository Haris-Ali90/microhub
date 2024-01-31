<?php

namespace App\Http\Controllers\Backend;

use App\Agreements;
use App\AgreementsUser;
use App\AmazonEnteries;
use App\DeliveryProcessType;
use App\FinanceVendorCity;
use App\Http\Controllers\Backend\BackendController;
use App\HubStore;
use App\JoeyRoute;
use App\RouteHistory;
use App\Locations;
use App\MicroHubAssign;
use App\MicroHubOrder;
use App\SlotsPostalCode;
use App\Post;
use App\Slots;
use App\SprintHistory;
use App\Task;
use App\TaskHistory;
use App\TorontoEntries;
use App\ZoneRouting;
use Illuminate\Http\Request;
use App\Sprint;
use App\Http\Requests;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Hub;
use App\HubProcess;

use App\Teachers;
use App\Institute;
use App\Amazon;
use App\Amazon_count;
use App\Ctc;
use App\Ctc_count;
use App\CoursesRequest;
use date;
use DB;
use whereBetween;
use Carbon\Carbon;
use PDFlib;


use App\AlertSystem;
use App\BrookerJoey;
use App\BrookerUser;
use App\CTCEntry;
use App\CtcVendor;
use App\CustomerRoutingTrackingId;
use App\FinanceVendorCityDetail;
use App\FlagHistory;
use App\Http\Traits\BasicModelFunctions;
use App\HubZones;
use App\Joey;
use App\JoeyRouteLocations;
use App\JoeyRoutes;
use App\MerchantIds;
use App\Setting;
use App\SprintTaskHistory;
use App\TrackingImageHistory;
use App\WarehouseJoeysCount;
use DateTime;
use DateTimeZone;

class InchargeDashboardController extends BackendController
{


    use BasicModelFunctions;


    public function getIndex(Request $request)
    {


        $user = Auth::user();
        $pass = $user->password;
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        if($user->userType == 'user' || $user->userType == 'admin'){

            //Mark:- Getting vendors_id from HubStore on the bases of hub_id -- Daniyal Khan
            $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $hubshow = false;
            if (auth()->user()->role_type == 1){
                $hubshow = true;

                //new condition...
                $statistics = $user->hub_id;

            }
            else {
                $statistics = explode(',',$user->statistics);

            }

            $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                ->where('user_id', $user->id)
                ->get()->toArray();

            //new-logic
            $hub_id = $user->hub_id;
            $hubs = FinanceVendorCity::where('deleted_at', null)->get();
            $hub_name = FinanceVendorCityDetail::where('vendor_city_realtions_id', $user->hub_id)->first();

            $hub_title = Hub::where('id', $user->hub_id)->get();



            return backend_view('incharge.dashboard', compact('date','hub_name','hub_id','hubs','hubshow','statistics','hub_title','assigned_hub') );

        }else{
            if($hub == "all") {


                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $hubshow = false;
                if (auth()->user()->role_type == 1){
                    $hubshow = true;

                    //new condition...
                    $statistics = $user->hub_id;

                }
                else {
                    $statistics = explode(',',$user->statistics);

                }

                $hub_id = $hub;
                $hubs = FinanceVendorCity::where('deleted_at', null)->get();
                $hub_name = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->first();

                $hub_title = Hub::whereIn('id', $assigned_hub)->get();



                return backend_view('incharge.dashboard', compact('date','hub_name','hub_id','hubs','hubshow','statistics','hub_title','assigned_hub') );

            }else{

                //Mark:- Getting vendors_id from HubStore on the bases of hub_id -- Daniyal Khan
                $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");


                $user  = User::where('id',auth()->user()->id)->first();
                $hubshow = false;
                if (auth()->user()->role_type == 1){
                    $hubshow = true;

                    //new condition...
                    $statistics = $user->hub_id;

                }
                else {
                    $statistics = explode(',',$user->statistics);

                }

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->get()->toArray();

                //new-logic
                $hub_id = $input['hub_id'];
                $hubs = FinanceVendorCity::where('deleted_at', null)->get();
                $hub_name = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->first();

                $hub_title = Hub::where('id', $input['hub_id'])->get();



                return backend_view('incharge.dashboard', compact('date','hub_name','hub_id','hubs','hubshow','statistics','hub_title','assigned_hub') );

            }
        }



    }

    public function statisticsFlagOrderListPieChartData(Request $request)
    {


        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
//            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));


            $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            $statistics_id = 17 ;
            $statistics_id = ($statistics_id == '' || $statistics_id == null) ? 0: $statistics_id ;

            $hubIds = HubZones:://whereIn('zone_id',DB::raw('select zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id = '.$data.') '))
            whereIn('zone_id', function ($query) use ($statistics_id) {
                $query->select(
                    \Illuminate\Support\Facades\DB::raw('zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id in (' . $statistics_id . ')) ')
                );
            })
                ->pluck('hub_id')->toArray();
            $return_data = [
                'legend' => [],
                'data' => [],
            ];
            // getting data
            $FlagHistoryData = FlagHistory::whereNull('deleted_at')
                ->whereBetween('created_at', [$start, $end])
                ->whereIn('hub_id', $hubIds)
                ->whereNull('unflaged_date')
                ->get();

            // looping on data
            foreach ($FlagHistoryData as $FlagHistory) {
                $category_nama = $FlagHistory->flag_cat_name;
                //pushing data into legend
                array_push($return_data['legend'], $category_nama);
                // now checking the value exist or not
                if (isset($return_data['data'][$category_nama])) {

                    $return_data['data'][$category_nama]['value'] = $return_data['data'][$category_nama]['value'] + 1;
                } else {
                    $return_data['data'][$category_nama] = ['name' => $category_nama, "value" => 1];
                }
            }

            // setting responce
            $return_data['legend'] = array_unique($return_data['legend'], SORT_REGULAR);
            $return_data['data'] = array_values($return_data['data']);

            return response()->json(['status' => true, 'body' => $return_data]);
        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));


                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $statistics_id = 17;
                $statistics_id = ($statistics_id == '' || $statistics_id == null) ? 0: $statistics_id ;

                $hubIds = HubZones:://whereIn('zone_id',DB::raw('select zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id = '.$data.') '))
                whereIn('zone_id', function ($query) use ($statistics_id) {
                    $query->select(
                        \Illuminate\Support\Facades\DB::raw('zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id in (' . $statistics_id . ')) ')
                    );
                })
                    ->pluck('hub_id')->toArray();
                $return_data = [
                    'legend' => [],
                    'data' => [],
                ];
                // getting data
                $FlagHistoryData = FlagHistory::whereNull('deleted_at')
                    ->whereBetween('created_at', [$start, $end])
                    ->whereIn('hub_id', $hubIds)
                    ->whereNull('unflaged_date')
                    ->get();

                // looping on data
                foreach ($FlagHistoryData as $FlagHistory) {
                    $category_nama = $FlagHistory->flag_cat_name;
                    //pushing data into legend
                    array_push($return_data['legend'], $category_nama);
                    // now checking the value exist or not
                    if (isset($return_data['data'][$category_nama])) {

                        $return_data['data'][$category_nama]['value'] = $return_data['data'][$category_nama]['value'] + 1;
                    } else {
                        $return_data['data'][$category_nama] = ['name' => $category_nama, "value" => 1];
                    }
                }

                // setting responce
                $return_data['legend'] = array_unique($return_data['legend'], SORT_REGULAR);
                $return_data['data'] = array_values($return_data['data']);

                return response()->json(['status' => true, 'body' => $return_data]);
            }else{
                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");


                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));


                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $statistics_id = 17 ;
                $statistics_id = ($statistics_id == '' || $statistics_id == null) ? 0: $statistics_id ;

                $hubIds = HubZones:://whereIn('zone_id',DB::raw('select zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id = '.$data.') '))
                whereIn('zone_id', function ($query) use ($statistics_id) {
                    $query->select(
                        \Illuminate\Support\Facades\DB::raw('zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id in (' . $statistics_id . ')) ')
                    );
                })
                    ->pluck('hub_id')->toArray();
                $return_data = [
                    'legend' => [],
                    'data' => [],
                ];
                // getting data
                $FlagHistoryData = FlagHistory::whereNull('deleted_at')
                    ->whereBetween('created_at', [$start, $end])
                    ->whereIn('hub_id', $hubIds)
                    ->whereNull('unflaged_date')
                    ->get();

                // looping on data
                foreach ($FlagHistoryData as $FlagHistory) {
                    $category_nama = $FlagHistory->flag_cat_name;
                    //pushing data into legend
                    array_push($return_data['legend'], $category_nama);
                    // now checking the value exist or not
                    if (isset($return_data['data'][$category_nama])) {

                        $return_data['data'][$category_nama]['value'] = $return_data['data'][$category_nama]['value'] + 1;
                    } else {
                        $return_data['data'][$category_nama] = ['name' => $category_nama, "value" => 1];
                    }
                }

                // setting responce
                $return_data['legend'] = array_unique($return_data['legend'], SORT_REGULAR);
                $return_data['data'] = array_values($return_data['data']);

                return response()->json(['status' => true, 'body' => $return_data]);
            }

        }


    }

    public function getInchargeDayOtd(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date . " 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date . " 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        if( $user->userType == 'user' || $user->userType == 'admin'){

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $user->hub_id)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $sprint = new Sprint();

            $totalcount = 0;
            $totallates = 0;

            $all_dates = [];
            $range_from_date = new Carbon($date);
            $range_to_date = new Carbon($date);
            while ($range_from_date->lte($range_to_date)) {
                $all_dates[] = $range_from_date->toDateString();
                $range_from_date->addDay();
            }
            foreach ($all_dates as $range_date) {
                if (in_array('477260', $vendors)) {

                    $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }

                if (in_array('477282', $vendors)) {

                    $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }

                if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                    $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                    $all_status_ids = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144,101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 112, 113, 131, 135, 136, 143,13, 61, 124 , 133, 121 , 255];

                    $sprint_id = SprintTaskHistory::where('created_at', '>', $start)
                        ->where('created_at', '<', $end)
                        ->whereIn('status_id', $all_status_ids)
                        ->pluck('sprint_id');

                    $query = CTCEntry::whereIn('creator_id', $ctc_ids)
                        ->whereIn('sprint_id', $sprint_id)
                        ->where('is_custom_route', 0)
                        ->get();



                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }
            }

            if ($totalcount == 0) {
                $totalcount = 1;
            }
            $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2), 'ontime' => $totalcount - $totallates, 'offtime' => $totallates];


            return $odt_data_1;

        }else{
            if($hub == "all") {
                $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::whereIn('hub_id', $assigned_hub)->pluck("vendor_id");

                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
                $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
                $sprint = new Sprint();

                $totalcount = 0;
                $totallates = 0;

                $all_dates = [];
                $range_from_date = new Carbon(date('Y-m-d', strtotime('-6 day', strtotime($date))));
                $range_to_date = new Carbon($date);
                while ($range_from_date->lte($range_to_date)) {
                    $all_dates[] = $range_from_date->toDateString();
                    $range_from_date->addDay();
                }
                foreach ($all_dates as $range_date) {
                    if (in_array('477260', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }

                    if (in_array('477282', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }

                    if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                        $ctc_date = date('Y-m-d', strtotime($range_date . ' -1 days'));
                        $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                        $start_dt = new DateTime($ctc_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($ctc_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $sprint_id = SprintTaskHistory::where('created_at', '>', $start)->where('created_at', '<', $end)->where('status_id', 125)->pluck('sprint_id');
                        $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }
                }

                if ($totalcount == 0) {
                    $totalcount = 1;
                }
                $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2), 'ontime' => $totalcount - $totallates, 'offtime' => $totallates];


                return $odt_data_1;
            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $hub_id = $input['hub_id'];
                $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
                $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
                $sprint = new Sprint();

                $totalcount = 0;
                $totallates = 0;

                $all_dates = [];
                $range_from_date = new Carbon(date('Y-m-d', strtotime('-6 day', strtotime($date))));
                $range_to_date = new Carbon($date);
                while ($range_from_date->lte($range_to_date)) {
                    $all_dates[] = $range_from_date->toDateString();
                    $range_from_date->addDay();
                }
                foreach ($all_dates as $range_date) {
                    if (in_array('477260', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }

                    if (in_array('477282', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }

                    if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                        $ctc_date = date('Y-m-d', strtotime($range_date . ' -1 days'));
                        $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                        $start_dt = new DateTime($ctc_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($ctc_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $sprint_id = SprintTaskHistory::where('created_at', '>', $start)->where('created_at', '<', $end)->where('status_id', 125)->pluck('sprint_id');
                        $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }
                }

                if ($totalcount == 0) {
                    $totalcount = 1;
                }
                $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2), 'ontime' => $totalcount - $totallates, 'offtime' => $totallates];


                return $odt_data_1;

            }
        }

    }

    public function getInchargeWeekOtd(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date . " 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date . " 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        if( $user->userType == 'user' || $user->userType == 'admin'){

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $user->hub_id)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $sprint = new Sprint();

            $totalcount = 0;
            $totallates = 0;

            $all_dates = [];
            $range_from_date = new Carbon(date('Y-m-d', strtotime('-6 day', strtotime($date))));
            $range_to_date = new Carbon($date);
            while ($range_from_date->lte($range_to_date)) {
                $all_dates[] = $range_from_date->toDateString();
                $range_from_date->addDay();
            }
            foreach ($all_dates as $range_date) {
                if (in_array('477260', $vendors)) {



                    $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }

                if (in_array('477282', $vendors)) {

                    $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }

                if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                    $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                    $all_status_ids = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144,101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 112, 113, 131, 135, 136, 143,13, 61, 124 , 133, 121 , 255];

                    $sprint_id = SprintTaskHistory::where('created_at', '>', $start)
                        ->where('created_at', '<', $end)
                        ->whereIn('status_id', $all_status_ids)
                        ->pluck('sprint_id');

                    $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }
            }

            if ($totalcount == 0) {
                $totalcount = 1;
            }
            $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2), 'ontime' => $totalcount - $totallates, 'offtime' => $totallates];


            return $odt_data_1;

        }else{
            if($hub == "all") {
                $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::whereIn('hub_id', $assigned_hub)->pluck("vendor_id");

                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
                $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
                $sprint = new Sprint();

                $totalcount = 0;
                $totallates = 0;

                $all_dates = [];
                $range_from_date = new Carbon(date('Y-m-d', strtotime('-6 day', strtotime($date))));
                $range_to_date = new Carbon($date);
                while ($range_from_date->lte($range_to_date)) {
                    $all_dates[] = $range_from_date->toDateString();
                    $range_from_date->addDay();
                }
                foreach ($all_dates as $range_date) {
                    if (in_array('477260', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }

                    if (in_array('477282', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }

                    if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                        $ctc_date = date('Y-m-d', strtotime($range_date . ' -1 days'));
                        $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                        $start_dt = new DateTime($ctc_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($ctc_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $sprint_id = SprintTaskHistory::where('created_at', '>', $start)->where('created_at', '<', $end)->where('status_id', 125)->pluck('sprint_id');
                        $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }
                }

                if ($totalcount == 0) {
                    $totalcount = 1;
                }
                $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2), 'ontime' => $totalcount - $totallates, 'offtime' => $totallates];


                return $odt_data_1;
            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $hub_id = $input['hub_id'];
                $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
                $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
                $sprint = new Sprint();

                $totalcount = 0;
                $totallates = 0;

                $all_dates = [];
                $range_from_date = new Carbon(date('Y-m-d', strtotime('-6 day', strtotime($date))));
                $range_to_date = new Carbon($date);
                while ($range_from_date->lte($range_to_date)) {
                    $all_dates[] = $range_from_date->toDateString();
                    $range_from_date->addDay();
                }
                foreach ($all_dates as $range_date) {
                    if (in_array('477260', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }

                    if (in_array('477282', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }

                    if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                        $ctc_date = date('Y-m-d', strtotime($range_date . ' -1 days'));
                        $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                        $start_dt = new DateTime($ctc_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($ctc_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $sprint_id = SprintTaskHistory::where('created_at', '>', $start)->where('created_at', '<', $end)->where('status_id', 125)->pluck('sprint_id');
                        $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }
                }

                if ($totalcount == 0) {
                    $totalcount = 1;
                }
                $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2), 'ontime' => $totalcount - $totallates, 'offtime' => $totallates];


                return $odt_data_1;

            }
        }

    }

    public function getInchargeMonthOtd(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date . " 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date . " 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $user->hub_id)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $sprint = new Sprint();

            $totalcount = 0;
            $totallates = 0;

            $all_dates = [];
            $range_from_date = new Carbon(date('Y-m-d', strtotime('-1 month', strtotime($date))));
            $range_to_date = new Carbon($date);
            while ($range_from_date->lte($range_to_date)) {
                $all_dates[] = $range_from_date->toDateString();
                $range_from_date->addDay();
            }
            foreach ($all_dates as $range_date) {
                if (in_array('477260', $vendors)) {

                    $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {

                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }

                    }
                }

                if (in_array('477282', $vendors)) {

                    $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }

                if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                    $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                    $all_status_ids = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144,101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 112, 113, 131, 135, 136, 143,13, 61, 124 , 133, 121 , 255];

                    $sprint_id = SprintTaskHistory::where('created_at', '>', $start)->where('created_at', '<', $end)->whereIn('status_id', $all_status_ids)->pluck('sprint_id');
                    $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }
            }

            if ($totalcount == 0) {
                $totalcount = 1;
            }
            $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2), 'ontime' => $totalcount - $totallates, 'offtime' => $totallates];


            return $odt_data_1;

        }else{
            if($hub == "all") {

                $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");


                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
                $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
                $sprint = new Sprint();

                $totalcount = 0;
                $totallates = 0;

                $all_dates = [];
                $range_from_date = new Carbon(date('Y-m-d', strtotime('-1 month', strtotime($date))));
                $range_to_date = new Carbon($date);
                while ($range_from_date->lte($range_to_date)) {
                    $all_dates[] = $range_from_date->toDateString();
                    $range_from_date->addDay();
                }
                foreach ($all_dates as $range_date) {
                    if (in_array('477260', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {

                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }

                        }
                    }

                    if (in_array('477282', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }

                    if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                        $ctc_date = date('Y-m-d', strtotime($range_date . ' -1 days'));
                        $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                        $start_dt = new DateTime($ctc_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($ctc_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $sprint_id = SprintTaskHistory::where('created_at', '>', $start)->where('created_at', '<', $end)->where('status_id', 125)->pluck('sprint_id');
                        $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }
                }

                if ($totalcount == 0) {
                    $totalcount = 1;
                }
                $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2), 'ontime' => $totalcount - $totallates, 'offtime' => $totallates];


                return $odt_data_1;
            }else{



                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");


                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
                $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
                $sprint = new Sprint();

                $totalcount = 0;
                $totallates = 0;

                $all_dates = [];
                $range_from_date = new Carbon(date('Y-m-d', strtotime('-1 month', strtotime($date))));
                $range_to_date = new Carbon($date);
                while ($range_from_date->lte($range_to_date)) {
                    $all_dates[] = $range_from_date->toDateString();
                    $range_from_date->addDay();
                }
                foreach ($all_dates as $range_date) {
                    if (in_array('477260', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {

                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }

                        }
                    }

                    if (in_array('477282', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }

                    if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                        $ctc_date = date('Y-m-d', strtotime($range_date . ' -1 days'));
                        $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                        $start_dt = new DateTime($ctc_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($ctc_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $sprint_id = SprintTaskHistory::where('created_at', '>', $start)->where('created_at', '<', $end)->where('status_id', 125)->pluck('sprint_id');
                        $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }
                }

                if ($totalcount == 0) {
                    $totalcount = 1;
                }
                $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2), 'ontime' => $totalcount - $totallates, 'offtime' => $totallates];


                return $odt_data_1;

            }
        }


    }

    public function getInchargeSixMonthOtd(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date . " 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date . " 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $user->hub_id)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $sprint = new Sprint();

            $totalcount = 0;
            $totallates = 0;

            $all_dates = [];
            $range_from_date = new Carbon(date('Y-m-d', strtotime('-6 month', strtotime($date))));
            $range_to_date = new Carbon($date);
            while ($range_from_date->lte($range_to_date)) {
                $all_dates[] = $range_from_date->toDateString();
                $range_from_date->addDay();
            }
            foreach ($all_dates as $range_date) {
                if (in_array('477260', $vendors)) {

                    $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {

                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }

                    }
                }

                if (in_array('477282', $vendors)) {

                    $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }

                if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                    $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                    $all_status_ids = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144,101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 112, 113, 131, 135, 136, 143,13, 61, 124 , 133, 121 , 255];

                    $sprint_id = SprintTaskHistory::where('created_at', '>', $start)->where('created_at', '<', $end)->whereIn('status_id', $all_status_ids)->pluck('sprint_id');
                    $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }
            }

            if ($totalcount == 0) {
                $totalcount = 1;
            }
            $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2), 'ontime' => $totalcount - $totallates, 'offtime' => $totallates];


            return $odt_data_1;

        }else{
            if($hub == "all") {

                $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");


                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
                $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
                $sprint = new Sprint();

                $totalcount = 0;
                $totallates = 0;

                $all_dates = [];
                $range_from_date = new Carbon(date('Y-m-d', strtotime('-1 month', strtotime($date))));
                $range_to_date = new Carbon($date);
                while ($range_from_date->lte($range_to_date)) {
                    $all_dates[] = $range_from_date->toDateString();
                    $range_from_date->addDay();
                }
                foreach ($all_dates as $range_date) {
                    if (in_array('477260', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {

                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }

                        }
                    }

                    if (in_array('477282', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }

                    if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                        $ctc_date = date('Y-m-d', strtotime($range_date . ' -1 days'));
                        $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                        $start_dt = new DateTime($ctc_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($ctc_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $sprint_id = SprintTaskHistory::where('created_at', '>', $start)->where('created_at', '<', $end)->where('status_id', 125)->pluck('sprint_id');
                        $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }
                }

                if ($totalcount == 0) {
                    $totalcount = 1;
                }
                $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2), 'ontime' => $totalcount - $totallates, 'offtime' => $totallates];


                return $odt_data_1;
            }else{



                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");


                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
                $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
                $sprint = new Sprint();

                $totalcount = 0;
                $totallates = 0;

                $all_dates = [];
                $range_from_date = new Carbon(date('Y-m-d', strtotime('-1 month', strtotime($date))));
                $range_to_date = new Carbon($date);
                while ($range_from_date->lte($range_to_date)) {
                    $all_dates[] = $range_from_date->toDateString();
                    $range_from_date->addDay();
                }
                foreach ($all_dates as $range_date) {
                    if (in_array('477260', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {

                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }

                        }
                    }

                    if (in_array('477282', $vendors)) {
                        $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                        $start_dt = new DateTime($amazon_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($amazon_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at', '>', $start)->where('created_at', '<', $end)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }

                    if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                        $ctc_date = date('Y-m-d', strtotime($range_date . ' -1 days'));
                        $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                        $start_dt = new DateTime($ctc_date . " 00:00:00", new DateTimezone('America/Toronto'));
                        $start_dt->setTimeZone(new DateTimezone('UTC'));
                        $start = $start_dt->format('Y-m-d H:i:s');

                        $end_dt = new DateTime($ctc_date . " 23:59:59", new DateTimezone('America/Toronto'));
                        $end_dt->setTimeZone(new DateTimezone('UTC'));
                        $end = $end_dt->format('Y-m-d H:i:s');


                        $sprint_id = SprintTaskHistory::where('created_at', '>', $start)->where('created_at', '<', $end)->where('status_id', 125)->pluck('sprint_id');
                        $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)
                            ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                                \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                        if (!empty($query)) {
                            foreach ($query as $record) {
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                    if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                    if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                        $totallates++;
                                    }
                                }
                                $totalcount++;
                            }
                        }
                    }
                }

                if ($totalcount == 0) {
                    $totalcount = 1;
                }
                $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2), 'ontime' => $totalcount - $totallates, 'offtime' => $totallates];


                return $odt_data_1;

            }
        }


    }

    public function getAllCounts(Request $request)
    {


        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';


        if( $user->userType == 'user' || $user->userType == 'admin'){

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $user->hub_id)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();

            $montreal_count = [
                'total' => 0,
                'sorted' => 0,
                'pickup' => 0,
                'delivered_order' => 0,
                'return_orders' => 0,
                'hub_return_scan' => 0,
                'notscan' => 0,
                'reattempted' => 0,
                'completion_ratio' =>0
            ];
            $ottawa_count = [
                'total' => 0,
                'sorted' => 0,
                'pickup' => 0,
                'delivered_order' => 0,
                'return_orders' => 0,
                'hub_return_scan' => 0,
                'notscan' => 0,
                'reattempted' => 0,
                'completion_ratio' =>0
            ];
            $ctc_count = [
                'total' => 0,
                'sorted' => 0,
                'pickup' => 0,
                'delivered_order' => 0,
                'return_orders' => 0,
                'hub_return_scan' => 0,
                'notscan' => 0,
                'reattempted' => 0,
                'completion_ratio' =>0
            ];

            if (in_array('477260', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
                $amazon = new TorontoEntries();
                $montreal_count = $amazon->getAmazonCountsForLoop($taskIds, 'all');

            }

            if (in_array('477282', $vendors)) {
                $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
                $amazon = new TorontoEntries();
                $ottawa_count = $amazon->getAmazonCountsForLoop($taskIds, 'all');
            }

            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $taskIds = DB::table('ctc_entries')->where('is_custom_route', 0)->whereIn('creator_id',$ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)->pluck('task_id');
                $ctc = new CTCEntry();
                $ctc_count = $ctc->getCtcCounts($taskIds, 'all');
            }
            $counts['total'] = $montreal_count['total']+$ottawa_count['total']+ $ctc_count['total'];
            $counts['sorted'] = $montreal_count['sorted']+$ottawa_count['sorted']+$ctc_count['sorted'];
            $counts['pickup'] = $montreal_count['pickup']+$ottawa_count['pickup']+$ctc_count['pickup'];
            $counts['delivered_order'] = $montreal_count['delivered_order']+$ottawa_count['delivered_order']+$ctc_count['delivered_order'];
            $counts['return_orders'] = $montreal_count['return_orders']+$ottawa_count['return_orders']+$ctc_count['return_orders'];
            $counts['hub_return_scan'] = $montreal_count['hub_return_scan']+$ottawa_count['hub_return_scan']+$ctc_count['hub_return_scan'];
            $counts['hub_not_return_scan'] = $counts['return_orders']-$counts['hub_return_scan'];
            $counts['notscan'] = $montreal_count['notscan']+$ottawa_count['notscan']+$ctc_count['notscan'];
            $counts['reattempted'] = $montreal_count['reattempted']+$ottawa_count['reattempted']+$ctc_count['reattempted'];

            if ($counts['pickup'] > 0) {
                $counts['completion_ratio'] = round(($counts['delivered_order'] / $counts['pickup']) * 100, 2).'%';
            }
            else
            {
                $counts['completion_ratio']  = 0.00.'%';
            }
            //  ($montreal_count['completion_ratio']+$ottawa_count['completion_ratio']+$ctc_count['completion_ratio']).'%';

            return $counts;
        }else{
            if($hub == "all") {

                $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::whereIn('hub_id', $assigned_hub)->pluck("vendor_id");

                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
                $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();

                $montreal_count = [
                    'total' => 0,
                    'sorted' => 0,
                    'pickup' => 0,
                    'delivered_order' => 0,
                    'return_orders' => 0,
                    'hub_return_scan' => 0,
                    'notscan' => 0,
                    'reattempted' => 0,
                    'completion_ratio' =>0
                ];
                $ottawa_count = [
                    'total' => 0,
                    'sorted' => 0,
                    'pickup' => 0,
                    'delivered_order' => 0,
                    'return_orders' => 0,
                    'hub_return_scan' => 0,
                    'notscan' => 0,
                    'reattempted' => 0,
                    'completion_ratio' =>0
                ];
                $ctc_count = [
                    'total' => 0,
                    'sorted' => 0,
                    'pickup' => 0,
                    'delivered_order' => 0,
                    'return_orders' => 0,
                    'hub_return_scan' => 0,
                    'notscan' => 0,
                    'reattempted' => 0,
                    'completion_ratio' =>0
                ];

                if (in_array('477260', $vendors)) {
//                    $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                        ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
                    $amazon = new TorontoEntries();
                    $montreal_count = $amazon->getAmazonCountsForLoop($taskIds, 'all');

                }

                if (in_array('477282', $vendors)) {
                    $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                        ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
                    $amazon = new TorontoEntries();
                    $ottawa_count = $amazon->getAmazonCountsForLoop($taskIds, 'all');
                }

                if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                    $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $taskIds = DB::table('ctc_entries')->where('is_custom_route', 0)->whereIn('creator_id',$ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)->pluck('task_id');
                    $ctc = new CTCEntry();
                    $ctc_count = $ctc->getCtcCounts($taskIds, 'all');
                }
                $counts['total'] = $montreal_count['total']+$ottawa_count['total']+ $ctc_count['total'];
                $counts['sorted'] = $montreal_count['sorted']+$ottawa_count['sorted']+$ctc_count['sorted'];
                $counts['pickup'] = $montreal_count['pickup']+$ottawa_count['pickup']+$ctc_count['pickup'];
                $counts['delivered_order'] = $montreal_count['delivered_order']+$ottawa_count['delivered_order']+$ctc_count['delivered_order'];
                $counts['return_orders'] = $montreal_count['return_orders']+$ottawa_count['return_orders']+$ctc_count['return_orders'];
                $counts['hub_return_scan'] = $montreal_count['hub_return_scan']+$ottawa_count['hub_return_scan']+$ctc_count['hub_return_scan'];
                $counts['hub_not_return_scan'] = $counts['return_orders']-$counts['hub_return_scan'];
                $counts['notscan'] = $montreal_count['notscan']+$ottawa_count['notscan']+$ctc_count['notscan'];
                $counts['reattempted'] = $montreal_count['reattempted']+$ottawa_count['reattempted']+$ctc_count['reattempted'];

                if ($counts['pickup'] > 0) {
                    $counts['completion_ratio'] = round(($counts['delivered_order'] / $counts['pickup']) * 100, 2).'%';
                }
                else
                {
                    $counts['completion_ratio']  = 0.00.'%';
                }
                //  ($montreal_count['completion_ratio']+$ottawa_count['completion_ratio']+$ctc_count['completion_ratio']).'%';

                return $counts;

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
                $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();

                $montreal_count = [
                    'total' => 0,
                    'sorted' => 0,
                    'pickup' => 0,
                    'delivered_order' => 0,
                    'return_orders' => 0,
                    'hub_return_scan' => 0,
                    'notscan' => 0,
                    'reattempted' => 0,
                    'completion_ratio' =>0
                ];
                $ottawa_count = [
                    'total' => 0,
                    'sorted' => 0,
                    'pickup' => 0,
                    'delivered_order' => 0,
                    'return_orders' => 0,
                    'hub_return_scan' => 0,
                    'notscan' => 0,
                    'reattempted' => 0,
                    'completion_ratio' =>0
                ];
                $ctc_count = [
                    'total' => 0,
                    'sorted' => 0,
                    'pickup' => 0,
                    'delivered_order' => 0,
                    'return_orders' => 0,
                    'hub_return_scan' => 0,
                    'notscan' => 0,
                    'reattempted' => 0,
                    'completion_ratio' =>0
                ];

                if (in_array('477260', $vendors)) {
//                    $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                        ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
                    $amazon = new TorontoEntries();
                    $montreal_count = $amazon->getAmazonCountsForLoop($taskIds, 'all');

                }

                if (in_array('477282', $vendors)) {
                    $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                        ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
                    $amazon = new TorontoEntries();
                    $ottawa_count = $amazon->getAmazonCountsForLoop($taskIds, 'all');
                }

                if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                    $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $taskIds = DB::table('ctc_entries')->where('is_custom_route', 0)->whereIn('creator_id',$ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)->pluck('task_id');
                    $ctc = new CTCEntry();
                    $ctc_count = $ctc->getCtcCounts($taskIds, 'all');
                }
                $counts['total'] = $montreal_count['total']+$ottawa_count['total']+ $ctc_count['total'];
                $counts['sorted'] = $montreal_count['sorted']+$ottawa_count['sorted']+$ctc_count['sorted'];
                $counts['pickup'] = $montreal_count['pickup']+$ottawa_count['pickup']+$ctc_count['pickup'];
                $counts['delivered_order'] = $montreal_count['delivered_order']+$ottawa_count['delivered_order']+$ctc_count['delivered_order'];
                $counts['return_orders'] = $montreal_count['return_orders']+$ottawa_count['return_orders']+$ctc_count['return_orders'];
                $counts['hub_return_scan'] = $montreal_count['hub_return_scan']+$ottawa_count['hub_return_scan']+$ctc_count['hub_return_scan'];
                $counts['hub_not_return_scan'] = $counts['return_orders']-$counts['hub_return_scan'];
                $counts['notscan'] = $montreal_count['notscan']+$ottawa_count['notscan']+$ctc_count['notscan'];
                $counts['reattempted'] = $montreal_count['reattempted']+$ottawa_count['reattempted']+$ctc_count['reattempted'];

                if ($counts['pickup'] > 0) {
                    $counts['completion_ratio'] = round(($counts['delivered_order'] / $counts['pickup']) * 100, 2).'%';
                }
                else
                {
                    $counts['completion_ratio']  = 0.00.'%';
                }
                //  ($montreal_count['completion_ratio']+$ottawa_count['completion_ratio']+$ctc_count['completion_ratio']).'%';

                return $counts;
            }
        }

    }

    public function getInprogress(Request $request)
    {


        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        if( $user->userType == 'user' || $user->userType == 'admin'){ //User Data to get the logged in details...

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $input = $request->all();
            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $user->hub_id;
            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $user->hub_id)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();

            $montreal_count = [
                'remaining_sorted' => 0,
                'remaining_pickup' => 0,
                'remaining_route' => 0,
            ];
            $ottawa_count = [
                'remaining_sorted' => 0,
                'remaining_pickup' => 0,
                'remaining_route' => 0,
            ];
            $ctc_count = [
                'remaining_sorted' => 0,
                'remaining_pickup' => 0,
                'remaining_route' => 0,
            ];

            if (in_array('477260', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
                $amazon = new TorontoEntries();
                $montreal_count = $amazon->getInprogressOrders($taskIds, 'all');
            }

            if (in_array('477282', $vendors)) {
                $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
                $amazon = new TorontoEntries();
                $ottawa_count = $amazon->getInprogressOrders($taskIds, 'all');
            }

            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $taskIds = DB::table('ctc_entries')->where('is_custom_route', 0)->whereIn('creator_id',$ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)->pluck('task_id');
                $ctc = new CTCEntry();
                $ctc_count = $ctc->getInprogressOrders($taskIds, 'all');
            }
            $counts['remaining_sorted'] = $montreal_count['remaining_sorted']+$ottawa_count['remaining_sorted']+$ctc_count['remaining_sorted'];
            $counts['remaining_pickup'] = $montreal_count['remaining_pickup']+$ottawa_count['remaining_pickup']+$ctc_count['remaining_pickup'];
            $counts['remaining_route'] = $montreal_count['remaining_route']+$ottawa_count['remaining_route']+$ctc_count['remaining_route'];
            return $counts;
        }else{
            if($hub == "all") {

                $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::whereIn('hub_id', $assigned_hub)->pluck("vendor_id");

                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $hub_id = $user->hub_id;
                $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
                $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();

                $montreal_count = [
                    'remaining_sorted' => 0,
                    'remaining_pickup' => 0,
                    'remaining_route' => 0,
                ];
                $ottawa_count = [
                    'remaining_sorted' => 0,
                    'remaining_pickup' => 0,
                    'remaining_route' => 0,
                ];
                $ctc_count = [
                    'remaining_sorted' => 0,
                    'remaining_pickup' => 0,
                    'remaining_route' => 0,
                ];

                if (in_array('477260', $vendors)) {
//                    $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                        ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
                    $amazon = new TorontoEntries();
                    $montreal_count = $amazon->getInprogressOrders($taskIds, 'all');
                }

                if (in_array('477282', $vendors)) {
                    $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                        ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
                    $amazon = new TorontoEntries();
                    $ottawa_count = $amazon->getInprogressOrders($taskIds, 'all');
                }

                if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                    $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $taskIds = DB::table('ctc_entries')->where('is_custom_route', 0)->whereIn('creator_id',$ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)->pluck('task_id');
                    $ctc = new CTCEntry();
                    $ctc_count = $ctc->getInprogressOrders($taskIds, 'all');
                }
                $counts['remaining_sorted'] = $montreal_count['remaining_sorted']+$ottawa_count['remaining_sorted']+$ctc_count['remaining_sorted'];
                $counts['remaining_pickup'] = $montreal_count['remaining_pickup']+$ottawa_count['remaining_pickup']+$ctc_count['remaining_pickup'];
                $counts['remaining_route'] = $montreal_count['remaining_route']+$ottawa_count['remaining_route']+$ctc_count['remaining_route'];
                return $counts;
            }
            else{
                //User Data to get the logged in details...
                $user = Auth::user();

                //Getting vendors_id from HubStore on the bases of hub_id...
                $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $input = $request->all();
                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $hub_id = $input['hub_id'];
                $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
                $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();

                $montreal_count = [
                    'remaining_sorted' => 0,
                    'remaining_pickup' => 0,
                    'remaining_route' => 0,
                ];
                $ottawa_count = [
                    'remaining_sorted' => 0,
                    'remaining_pickup' => 0,
                    'remaining_route' => 0,
                ];
                $ctc_count = [
                    'remaining_sorted' => 0,
                    'remaining_pickup' => 0,
                    'remaining_route' => 0,
                ];

                if (in_array('477260', $vendors)) {
//                    $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                        ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
                    $amazon = new TorontoEntries();
                    $montreal_count = $amazon->getInprogressOrders($taskIds, 'all');
                }

                if (in_array('477282', $vendors)) {
                    $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                        ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
                    $amazon = new TorontoEntries();
                    $ottawa_count = $amazon->getInprogressOrders($taskIds, 'all');
                }

                if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                    $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $taskIds = DB::table('ctc_entries')->where('is_custom_route', 0)->whereIn('creator_id',$ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)->pluck('task_id');
                    $ctc = new CTCEntry();
                    $ctc_count = $ctc->getInprogressOrders($taskIds, 'all');
                }
                $counts['remaining_sorted'] = $montreal_count['remaining_sorted']+$ottawa_count['remaining_sorted']+$ctc_count['remaining_sorted'];
                $counts['remaining_pickup'] = $montreal_count['remaining_pickup']+$ottawa_count['remaining_pickup']+$ctc_count['remaining_pickup'];
                $counts['remaining_route'] = $montreal_count['remaining_route']+$ottawa_count['remaining_route']+$ctc_count['remaining_route'];
                return $counts;
            }

        }

    }


    //Mark:- First Mile Functions Starts From Here
    public function FirstMileDetails(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');


        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        if( $user->userType == 'user' || $user->userType == 'admin'){

            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])
                ->whereNull('deleted_at')
                ->groupBy('vendor_id')
                ->pluck("vendor_id")
                ->toArray();


            $Total_sprint = Sprint::whereIn('creator_id', $vendors_data)
                ->whereNull('deleted_at')
                ->whereNotIn('status_id', [36])
                ->where('created_at', 'LIKE', $date.'%')
                ->pluck('id')
                ->toArray();

            $Total_order = TaskHistory::whereIn('sprint_id', $Total_sprint)
                ->whereIn('status_id',[61,24])
                ->groupBy('sprint_id')
                ->get();


            $Picked_order = TaskHistory::whereIn('sprint_id', $Total_sprint)
                ->groupBy('sprint_id')
                ->where('status_id',125)
                ->get();

            $Remaining_order = CTCEntry::whereIn('creator_id', $vendors_data)
                ->whereBetween('created_at',[$start,$end])
                ->whereNull('deleted_at')
                ->whereIn('task_status_id',[61,24])
                ->get();

            $Total_routes = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->where('mile_type',1)
                ->where('date', 'LIKE', $date.'%')
                ->pluck('joey_id')
                ->toArray();

            $Total_joeys = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->where('mile_type',1)
                ->whereNotNull('joey_id')
                ->where('date', 'LIKE', $date.'%')
                ->groupBy('joey_id')
                ->pluck('joey_id')
                ->toArray();


//            $completed_route = TaskHistory::whereIn('sprint_id', $Total_sprint)
//                ->groupBy('sprint_id')
//                ->where('status_id',124)
//                ->get();

            $completed_route = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->where('mile_type',1)
                ->whereNotNull('joey_id')
                ->where('route_completed',1)
                ->where('date', 'LIKE', $date.'%')
                ->pluck('id')
                ->toArray();

            $ongoing_route = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->where('mile_type',1)
                ->whereNotNull('joey_id')
                ->where('route_completed',0)
                ->where('date', 'LIKE', $date.'%')
                ->pluck('id')
                ->toArray();


//            $ongoing_route = TaskHistory::whereIn('sprint_id', $Total_sprint)
//                ->groupBy('sprint_id')
//                ->where('status_id',149)
//                ->get();


            $delayed_route = CTCEntry::whereIn('route_id',$Total_routes)
                ->whereNull('deleted_at')
                ->where('task_status_id',149)
                ->get();


            $firstmiledata = [
                'total_orders' => count($Total_order),
                'collected_orders' => count($Picked_order),
                'total_vendors' => count($vendors_data),
                'remaining_orders'=>  count($Remaining_order),
                'routes'=> count($Total_routes),
                'joeys'=> count($Total_joeys),
                'completed_routes'=> count($completed_route),
                'ongoing_routes'=> count($ongoing_route),
                'delayed_routes'=> count($delayed_route)
            ];


            return $firstmiledata;

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Total_order = TorontoEntries::join('joey_routes','joey_routes.id','=','ctc_entries.route_id')
                    ->whereIn('creator_id', $vendors_data)
                    ->where('joey_routes.mile_type',1)
                    ->where('ctc_entries.created_at','>',$start)
                    ->where('ctc_entries.created_at','<',$end)
                    ->get();

                $Remaining_order = TorontoEntries::join('joey_routes','joey_routes.id','=','ctc_entries.route_id')
                    ->whereIn('creator_id', $vendors_data)
                    ->whereIn('ctc_entries.task_status_id', [147])
                    ->where('joey_routes.mile_type',1)
                    ->where('ctc_entries.created_at','>',$start)
                    ->where('ctc_entries.created_at','<',$end)
                    ->get();

                $Picked_id = TorontoEntries::join('joey_routes','joey_routes.id','=','ctc_entries.route_id')
                    ->whereIn('creator_id', $vendors_data)
                    ->where('joey_routes.mile_type',1)
                    ->where('ctc_entries.created_at','>',$start)
                    ->where('ctc_entries.created_at','<',$end)
                    ->pluck('ctc_entries.sprint_id')
                    ->toArray();

                $Picked_order_id = TaskHistory::whereIn('sprint_id', $Picked_id)
                    ->whereIn('status_id',[124])
                    ->pluck('sprint_id')
                    ->toArray();

                $Picked_order = CTCEntry::whereIn('sprint_id', $Picked_order_id)
                    ->get();

                $Total_routes = JoeyRoute::whereIn('hub',$assigned_hub)->where('mile_type',1)->pluck('joey_id')->toArray();
                $Total_joeys = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',1)
                    ->whereNotNull('joey_routes.joey_id')
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->groupBy('joey_id')
                    ->get();


                $completed_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',147)
                    ->get();

                $ongoing_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',149)
                    ->get();

                $delayed_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',149)
                    ->get();

                $firstmiledata = [
                    'total_orders' => count($Total_order),
                    'collected_orders' => count($Picked_order),
                    'total_vendors' => count($vendors_data),
                    'remaining_orders'=>  count($Remaining_order),
                    'routes'=> count($Total_routes),
                    'joeys'=> count($Total_joeys),
                    'completed_routes'=> count($completed_route),
                    'ongoing_routes'=> count($ongoing_route),
                    'delayed_routes'=> count($delayed_route)
                ];

                return $firstmiledata;

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $Total_order = TorontoEntries::join('joey_routes','joey_routes.id','=','ctc_entries.route_id')
                    ->whereIn('creator_id', $vendors_data)
                    ->where('joey_routes.mile_type',1)
                    ->where('ctc_entries.created_at','>',$start)
                    ->where('ctc_entries.created_at','<',$end)
                    ->get();

                $Remaining_order = TorontoEntries::join('joey_routes','joey_routes.id','=','ctc_entries.route_id')
                    ->whereIn('creator_id', $vendors_data)
                    ->whereIn('ctc_entries.task_status_id', [147])
                    ->where('joey_routes.mile_type',1)
                    ->where('ctc_entries.created_at','>',$start)
                    ->where('ctc_entries.created_at','<',$end)
                    ->get();

                $Picked_id = TorontoEntries::join('joey_routes','joey_routes.id','=','ctc_entries.route_id')
                    ->whereIn('creator_id', $vendors_data)
                    ->where('joey_routes.mile_type',1)
                    ->where('ctc_entries.created_at','>',$start)
                    ->where('ctc_entries.created_at','<',$end)
                    ->pluck('ctc_entries.sprint_id')
                    ->toArray();

                $Picked_order_id = TaskHistory::whereIn('sprint_id', $Picked_id)
                    ->whereIn('status_id',[124])
                    ->pluck('sprint_id')
                    ->toArray();

                $Picked_order = CTCEntry::whereIn('sprint_id', $Picked_order_id)
                    ->get();

                $Total_routes = JoeyRoute::where('hub',$input['hub_id'])->where('mile_type',1)->pluck('joey_id')->toArray();

                $Total_joeys = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$input['hub_id'])
                    ->where('joey_routes.mile_type',1)
                    ->whereNotNull('joey_routes.joey_id')
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->groupBy('joey_id')
                    ->get();


                $completed_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',147)
                    ->get();

                $ongoing_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',149)
                    ->get();

                $delayed_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',149)
                    ->get();

                $firstmiledata = [
                    'total_orders' => count($Total_order),
                    'collected_orders' => count($Picked_order),
                    'total_vendors' => count($vendors_data),
                    'remaining_orders'=>  count($Remaining_order),
                    'routes'=> count($Total_routes),
                    'joeys'=> count($Total_joeys),
                    'completed_routes'=> count($completed_route),
                    'ongoing_routes'=> count($ongoing_route),
                    'delayed_routes'=> count($delayed_route)
                ];

                return $firstmiledata;

            }
        }

    }

    public function FirstMileOrdersDetails(Request $request){

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';


        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        if( $user->userType == 'user' || $user->userType == 'admin'){

            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $Total_order = TorontoEntries::whereIn('creator_id', $vendors_data)->where('created_at','>',$start)->where('created_at','<',$end)
                ->where('is_custom_route', 0)->get();

            return $Total_order;

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Total_order = TorontoEntries::whereIn('creator_id', $vendors_data)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->where('is_custom_route', 0)->get();

                return $Total_order;

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $Total_order = TorontoEntries::whereIn('creator_id', $vendors_data)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->where('is_custom_route', 0)->get();

                return $Total_order;

            }
        }

    }

    public function getOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){

            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])
                ->whereNull('deleted_at')
                ->groupBy('vendor_id')
                ->pluck("vendor_id")
                ->toArray();


            $Total_sprint = Sprint::whereIn('creator_id', $vendors_data)
                ->whereNotIn('status_id', [36])
                ->whereNull('deleted_at')
                ->where('created_at', 'LIKE', $date.'%')
                ->pluck('id')
                ->toArray();

            $Total_order = TaskHistory::whereIn('sprint_id', $Total_sprint)
                ->whereIn('status_id',[61,24])
                ->groupBy('sprint_id')
                ->get();

            return backend_view('incharge.total_order', compact('Total_order'));
        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Total_order = TorontoEntries::join('joey_routes','joey_routes.id','=','ctc_entries.route_id')
                    ->whereIn('creator_id', $vendors_data)
                    ->where('joey_routes.mile_type',1)
                    ->where('ctc_entries.created_at','>',$start)
                    ->where('ctc_entries.created_at','<',$end)
                    ->get();

                return backend_view('incharge.total_order', compact('Total_order'));
            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $Total_order = TorontoEntries::join('joey_routes','joey_routes.id','=','ctc_entries.route_id')
                    ->whereIn('creator_id', $vendors_data)
                    ->where('joey_routes.mile_type',1)
                    ->where('ctc_entries.created_at','>',$start)
                    ->where('ctc_entries.created_at','<',$end)
                    ->get();

                return backend_view('incharge.total_order', compact('Total_order'));
            }

        }

    }

    public function getVendors(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';


        if( $user->userType == 'user' || $user->userType == 'admin'){

            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::join('vendors','vendors.id','=','hub_stores.vendor_id')
                ->whereNull('hub_stores.deleted_at')
                ->groupBy('vendor_id')
                ->where(['hub_id' => $user->hub_id])
                ->get();

            return backend_view('incharge.total_vendor', compact('vendors_data'));
        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                //Getting vendors data from HubStore on the bases of hub_id...
                $vendors_data = HubStore::join('vendors','vendors.id','=','hub_stores.vendor_id')
                    ->whereIn('hub_id' , $assigned_hub)
                    ->get();
                return backend_view('incharge.total_vendor', compact('vendors_data'));
            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::join('vendors','vendors.id','=','hub_stores.vendor_id')
                    ->where('hub_id' , $hub)
                    ->get();
                return backend_view('incharge.total_vendor', compact('vendors_data'));
            }

        }

    }

    public function getPickedOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])
                ->whereNull('deleted_at')
                ->groupBy('vendor_id')
                ->pluck("vendor_id")
                ->toArray();


            $Total_sprint = Sprint::whereIn('creator_id', $vendors_data)
                ->whereNull('deleted_at')
                ->whereNotIn('status_id', [36])
                ->where('created_at', 'LIKE', $date.'%')
                ->pluck('id')
                ->toArray();

            $Picked_order = TaskHistory::whereIn('sprint_id', $Total_sprint)
                ->where('status_id',125)
                ->groupBy('sprint_id')
                ->get();

            return backend_view('incharge.total_picked', compact('Picked_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Picked_id = TorontoEntries::join('joey_routes','joey_routes.id','=','ctc_entries.route_id')
                    ->whereIn('creator_id', $vendors_data)
                    ->where('joey_routes.mile_type',1)
                    ->where('ctc_entries.created_at','>',$start)
                    ->where('ctc_entries.created_at','<',$end)
                    ->pluck('ctc_entries.sprint_id')
                    ->toArray();

                $Picked_order_id = TaskHistory::whereIn('sprint_id', $Picked_id)
                    ->whereIn('status_id',[124])
                    ->pluck('sprint_id')
                    ->toArray();

                $Picked_order = CTCEntry::whereIn('sprint_id', $Picked_order_id)
                    ->get();

                return backend_view('incharge.total_picked', compact('Picked_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $Picked_id = TorontoEntries::join('joey_routes','joey_routes.id','=','ctc_entries.route_id')
                    ->whereIn('creator_id', $vendors_data)
                    ->where('joey_routes.mile_type',1)
                    ->where('ctc_entries.created_at','>',$start)
                    ->where('ctc_entries.created_at','<',$end)
                    ->pluck('ctc_entries.sprint_id')
                    ->toArray();

                $Picked_order_id = TaskHistory::whereIn('sprint_id', $Picked_id)
                    ->whereIn('status_id',[124])
                    ->pluck('sprint_id')
                    ->toArray();

                $Picked_order = CTCEntry::whereIn('sprint_id', $Picked_order_id)
                    ->get();


                return backend_view('incharge.total_picked', compact('Picked_order'));

            }
        }



    }

    public function getRemainingOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])
                ->whereNull('deleted_at')
                ->groupBy('vendor_id')
                ->pluck("vendor_id")
                ->toArray();

            $Remaining_order = CTCEntry::whereIn('creator_id', $vendors_data)
                ->whereBetween('created_at',[$start,$end])
                ->whereNull('deleted_at')
                ->whereIn('task_status_id',[61,24])
                ->get();

            return backend_view('incharge.total_remaining', compact('Remaining_order'));

        }else{

            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Remaining_order = TorontoEntries::join('joey_routes','joey_routes.id','=','ctc_entries.route_id')
                    ->whereIn('creator_id', $vendors_data)
                    ->whereIn('ctc_entries.task_status_id', [147])
                    ->where('joey_routes.mile_type',1)
                    ->where('ctc_entries.created_at','>',$start)
                    ->where('ctc_entries.created_at','<',$end)
                    ->get();
                return backend_view('incharge.total_remaining', compact('Remaining_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $Remaining_order = TorontoEntries::join('joey_routes','joey_routes.id','=','ctc_entries.route_id')
                    ->whereIn('creator_id', $vendors_data)
                    ->whereIn('ctc_entries.task_status_id', [147])
                    ->where('joey_routes.mile_type',1)
                    ->where('ctc_entries.created_at','>',$start)
                    ->where('ctc_entries.created_at','<',$end)
                    ->get();

                return backend_view('incharge.total_remaining', compact('Remaining_order'));

            }

        }

    }

    public function getFirstMileRoutes(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');


        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        if( $user->userType == 'user' || $user->userType == 'admin'){
            $Total_routes = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->where('mile_type',1)
                ->where('date', 'LIKE', $date.'%')
                ->get();

            return backend_view('incharge.total_routes', compact('Total_routes'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_routes = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',1)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                return backend_view('incharge.total_routes', compact('Total_routes'));

            }else{

                $Total_routes = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$hub)
                    ->where('joey_routes.mile_type',1)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                return backend_view('incharge.total_routes', compact('Total_routes'));

            }
        }

    }

    public function getFirstMileCompleteRoutes(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){
            $Total_routes = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->where('mile_type',1)
                ->whereNotNull('joey_id')
                ->where('route_completed',1)
                ->where('date', 'LIKE', $date.'%')
                ->get();

            return backend_view('incharge.complete_routes', compact('Total_routes'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_routes_id = JoeyRoute::whereIn('hub',$assigned_hub)
                    ->where('mile_type',1)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->pluck('joey_id')
                    ->toArray();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',147)
                    ->get();

                return backend_view('inchargemidmile.complete_routes', compact('Total_routes'));

            }else{

                $Total_routes_id = JoeyRoute::where('hub',$input['hub_id'])
                    ->where('mile_type',1)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->pluck('joey_id')
                    ->toArray();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',147)
                    ->get();

                return backend_view('inchargemidmile.complete_routes', compact('Total_routes'));

            }
        }



    }

    public function getFirstMileOngoingRoutes(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){
            $Total_routes = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->where('mile_type',1)
                ->whereNotNull('joey_id')
                ->where('route_completed',0)
                ->where('date', 'LIKE', $date.'%')
                ->get();

            return backend_view('incharge.ongoing_routes', compact('Total_routes'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_routes_id = JoeyRoute::whereIn('hub',$assigned_hub)
                    ->where('mile_type',1)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->pluck('joey_id')
                    ->toArray();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',149)
                    ->get();

                return backend_view('inchargemidmile.ongoing_routes', compact('Total_routes'));

            }else{

                $Total_routes_id = JoeyRoute::where('hub',$input['hub_id'])
                    ->where('mile_type',1)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->pluck('joey_id')
                    ->toArray();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',149)
                    ->get();

                return backend_view('inchargemidmile.ongoing_routes', compact('Total_routes'));

            }
        }



    }

    public function getFirstMileDelayRoutes(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){
            $Total_routes_id = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->where('mile_type',1)
                ->where('date','>',$start)
                ->where('date','<',$end)
                ->pluck('joey_id')
                ->toArray();

            $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                ->whereNull('deleted_at')
                ->where('task_status_id',149)
                ->get();
            return backend_view('incharge.delay_routes', compact('Total_routes'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_routes_id = JoeyRoute::whereIn('hub',$assigned_hub)
                    ->where('mile_type',1)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->pluck('joey_id')
                    ->toArray();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',149)
                    ->get();

                return backend_view('inchargemidmile.delay_routes', compact('Total_routes'));

            }else{

                $Total_routes_id = JoeyRoute::where('hub',$input['hub_id'])
                    ->where('mile_type',1)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->pluck('joey_id')
                    ->toArray();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',149)
                    ->get();
                return backend_view('inchargemidmile.delay_routes', compact('Total_routes'));

            }
        }



    }

    public function getFirstMileJoeys(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');


        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        if( $user->userType == 'user' || $user->userType == 'admin'){

            $Total_joeys = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->where('mile_type',1)
                ->whereNotNull('joey_id')
                ->where('date', 'LIKE', $date.'%')
                ->groupBy('joey_id')
                ->get();

            return backend_view('incharge.total_joeys', compact('Total_joeys'));

        }else{

            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_joeys = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',1)
                    ->whereNotNull('joey_routes.joey_id')
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->groupBy('joey_id')
                    ->get();

                return backend_view('incharge.total_joeys', compact('Total_joeys'));

            }else{

                $Total_joeys = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$input['hub_id'])
                    ->where('joey_routes.mile_type',1)
                    ->whereNotNull('joey_routes.joey_id')
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->groupBy('joey_id')
                    ->get();

                return backend_view('incharge.total_joeys', compact('Total_joeys'));

            }

        }


    }
    //Mark:- First Mile Functions Ends Here



    //Mark:- Mid Mile Functions Starts From Here
    public function MidMileDetails(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...

            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])
                ->whereNull('deleted_at')
                ->pluck("vendor_id");

            $Total_order_id = MicroHubOrder::where('hub_id', $user->hub_id)
                ->whereNull('deleted_at')
                ->where('created_at','>',$start)
                ->where('created_at','<',$end)
                ->pluck('sprint_id')
                ->toArray();

            $Total_order = Sprint::whereIn('id', $Total_order_id)
                ->whereNull('deleted_at')
                ->whereNotIn('status_id',[36])
                ->get();

            $Total_order_ids = MicroHubOrder::where('hub_id', $user->hub_id)
                ->whereNull('deleted_at')
                ->where('is_my_hub',0)
                ->where('created_at','>',$start)
                ->where('created_at','<',$end)
                ->pluck('sprint_id')
                ->toArray();

            $MyOrders_at_Other_Hub = Sprint::whereIn('id', $Total_order_ids)
                ->whereNull('deleted_at')
                ->whereNotIn('status_id',[36])
                ->get();

            $Microhub_users = User::where('hub_id',$user->hub_id)
                ->pluck('id')
                ->toArray();

            $Other_Orders_at_Myhub_id = MicroHubOrder::whereIn('scanned_by',$Microhub_users)
                ->where('is_my_hub',0)
                ->pluck('sprint_id')
                ->toArray();


            $Other_Orders_at_Myhub = Sprint::whereIn('status_id',[148,150])
                ->whereIn('id', $Other_Orders_at_Myhub_id)
                ->where('created_at','LIKE',$date."%")
//                ->groupBy('id')
                ->get();

            $users = User::whereNull('deleted_at')->where('hub_id',$user->hub_id)->pluck('id')->toArray();

            $Picked_Orders_id = MicroHubOrder::where('hub_id', $user->hub_id)
                ->whereNull('deleted_at')
                ->whereNotIn('scanned_by',$users)
                ->where('created_at','LIKE',$date."%")
                ->pluck('sprint_id')
                ->toArray();

            $Picked_Orders = TaskHistory::whereIn('sprint_id', $Picked_Orders_id)
                ->groupBy('sprint_id')
                ->where('status_id',149)
                ->get();

            $MyPicked_Orders_id = MicroHubOrder::where('hub_id', $user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('scanned_by',$users)
                ->where('created_at','LIKE',$date."%")
                ->pluck('sprint_id')
                ->toArray();

            $MyPicked_Orders = TaskHistory::whereIn('sprint_id', $MyPicked_Orders_id)
                ->groupBy('sprint_id')
                ->where('status_id',149)
                ->get();

//            dd($MyPicked_Orders,$users,$user->hub_id,$date,$MyPicked_Orders_id);

            $Sprint_ids = MicroHubOrder::where('hub_id', $user->hub_id)
                ->whereNull('deleted_at')
                ->where('created_at','>',$start)
                ->where('created_at','<',$end)
                ->pluck('sprint_id')
                ->toArray();

            $Delivered_on_other = TorontoEntries::whereIn('sprint_id', $Sprint_ids)
                ->whereNull('deleted_at')
                ->where('task_status_id',150)
                ->get();

            $Total_routes = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('mile_type',[2])
                ->where('date', 'LIKE', $date.'%')
                ->pluck('joey_id')
                ->toArray();

            $Total_joeys = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('mile_type',[2])
                ->whereNotNull('joey_id')
                ->where('date', 'LIKE', $date.'%')
                ->groupBy('joey_id')
                ->pluck('joey_id')
                ->toArray();

            $completed_route = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('mile_type',[2])
                ->whereNotNull('joey_id')
                ->where('route_completed',1)
                ->where('date', 'LIKE', $date.'%')
                ->pluck('id')
                ->toArray();

            $ongoing_route = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('mile_type',[2])
                ->whereNotNull('joey_id')
                ->where('route_completed',0)
                ->where('date', 'LIKE', $date.'%')
                ->pluck('id')
                ->toArray();

            $delayed_route = CTCEntry::whereIn('route_id',$Total_routes)
                ->whereNull('deleted_at')
                ->where('task_status_id',149)
                ->get();


            $midmiledata = [
                'total_orders' => count($Total_order),
                'collected_orders' => count($Picked_Orders),
                'mycollected_orders' => count($MyPicked_Orders),
                'my_orders_at_otherhub' => count($MyOrders_at_Other_Hub),
                'other_order_at_myhub' => count($Other_Orders_at_Myhub),
                'dropped_orders' => count($Delivered_on_other),
                'routes'=> count($Total_routes),
                'joeys'=> count($Total_joeys),
                'completed_routes'=> count($completed_route),
                'ongoing_routes'=> count($ongoing_route),
                'delayed_routes'=> count($delayed_route)

            ];

            return $midmiledata;

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Total_order = MicroHubOrder::join('ctc_entries','ctc_entries.sprint_id','=','orders_actual_hub.sprint_id')
                    ->whereIn('orders_actual_hub.hub_id', $assigned_hub)
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->get();

                $MyOrders_at_Other_Hub = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->where('orders_actual_hub.is_my_hub', 0)
                    ->whereIn('orders_actual_hub.hub_id', $assigned_hub)
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',148)
                    ->get();

                $Other_Orders_at_Myhub = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->where('orders_actual_hub.is_my_hub', 1)
                    ->whereIn('orders_actual_hub.hub_id', $assigned_hub)
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',148)
                    ->get();

                $Picked_Orders = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->whereIn('orders_actual_hub.hub_id', $assigned_hub)
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',149)
                    ->get();

                $Sprint_ids = MicroHubOrder::whereIn('hub_id', $assigned_hub)
                    ->where('created_at','>',$start)
                    ->where('created_at','<',$end)
                    ->pluck('sprint_id')
                    ->toArray();

                $Delivered_on_other = TorontoEntries::whereIn('sprint_id', $Sprint_ids)
                    ->where('task_status_id',150)
                    ->get();

                $Total_routes = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',2)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();


                $Total_joeys = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',2)
                    ->whereNotNull('joey_routes.joey_id')
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->groupBy('joey_id')
                    ->get();

                $completed_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',147)
                    ->get();

                $ongoing_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',149)
                    ->get();

                $delayed_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',149)
                    ->get();



                $midmiledata = [
                    'total_orders' => count($Total_order),
                    'collected_orders' => count($Picked_Orders),
                    'my_orders_at_otherhub' => count($MyOrders_at_Other_Hub),
                    'other_order_at_myhub' => count($Other_Orders_at_Myhub),
                    'dropped_orders' => count($Delivered_on_other),
                    'routes'=> count($Total_routes),
                    'joeys'=> count($Total_joeys),
                    'completed_routes'=> count($completed_route),
                    'ongoing_routes'=> count($ongoing_route),
                    'delayed_routes'=> count($delayed_route)

                ];
                return $midmiledata;

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $Total_order = MicroHubOrder::join('ctc_entries','ctc_entries.sprint_id','=','orders_actual_hub.sprint_id')
                    ->where('orders_actual_hub.hub_id', $input['hub_id'])
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->get();

                $MyOrders_at_Other_Hub = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->where('orders_actual_hub.is_my_hub', 0)
                    ->where('orders_actual_hub.hub_id', $input['hub_id'])
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',148)
                    ->get();

                $Other_Orders_at_Myhub = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->where('orders_actual_hub.is_my_hub', 1)
                    ->where('orders_actual_hub.hub_id', $input['hub_id'])
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',148)
                    ->get();

                $Picked_Orders = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->where('orders_actual_hub.hub_id', $input['hub_id'])
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',149)
                    ->get();

                $Sprint_ids = MicroHubOrder::where('hub_id', $input['hub_id'])
                    ->where('created_at','>',$start)
                    ->where('created_at','<',$end)
                    ->pluck('sprint_id')
                    ->toArray();

                $Delivered_on_other = TorontoEntries::whereIn('sprint_id', $Sprint_ids)
                    ->where('task_status_id',150)
                    ->get();

                $Total_routes = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$input['hub_id'])
                    ->where('joey_routes.mile_type',2)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                $Total_joeys = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$input['hub_id'])
                    ->where('joey_routes.mile_type',2)
                    ->whereNotNull('joey_routes.joey_id')
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->groupBy('joey_id')
                    ->get();

                $completed_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',147)
                    ->get();

                $ongoing_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',149)
                    ->get();

                $delayed_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',149)
                    ->get();



                $midmiledata = [
                    'total_orders' => count($Total_order),
                    'collected_orders' => count($Picked_Orders),
                    'my_orders_at_otherhub' => count($MyOrders_at_Other_Hub),
                    'other_order_at_myhub' => count($Other_Orders_at_Myhub),
                    'dropped_orders' => count($Delivered_on_other),
                    'routes'=> count($Total_routes),
                    'joeys'=> count($Total_joeys),
                    'completed_routes'=> count($completed_route),
                    'ongoing_routes'=> count($ongoing_route),
                    'delayed_routes'=> count($delayed_route)

                ];
                return $midmiledata;

            }
        }

    }

    public function getMidMileOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){

            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])
                ->whereNull('deleted_at')
                ->pluck("vendor_id");

            $Total_order_id = MicroHubOrder::where('hub_id', $user->hub_id)
                ->whereNull('deleted_at')
                ->where('created_at','>',$start)
                ->where('created_at','<',$end)
                ->pluck('sprint_id')
                ->toArray();

            $Total_order = Sprint::whereIn('id', $Total_order_id)
                ->whereNull('deleted_at')
                ->whereNotIn('status_id',[36])
                ->get();

            return backend_view('inchargemidmile.total_order', compact('Total_order'));
        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Total_order = MicroHubOrder::join('ctc_entries','ctc_entries.sprint_id','=','orders_actual_hub.sprint_id')
                    ->where('orders_actual_hub.hub_id', $user->hub_id)
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->get();



                return backend_view('inchargemidmile.total_order', compact('Total_order'));
            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $Total_order = MicroHubOrder::join('ctc_entries','ctc_entries.sprint_id','=','orders_actual_hub.sprint_id')
                    ->where('orders_actual_hub.hub_id', $user->hub_id)
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->get();


                return backend_view('inchargemidmile.total_order', compact('Total_order'));
            }
        }


    }

    public function getMidMilePickedOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){

            $users = User::whereNull('deleted_at')->where('hub_id',$user->hub_id)->pluck('id')->toArray();

            $Picked_Orders_id = MicroHubOrder::where('hub_id', $user->hub_id)
                ->whereNull('deleted_at')
                ->whereNotIn('scanned_by',$users)
                ->where('created_at','LIKE',$date."%")
                ->pluck('sprint_id')
                ->toArray();

            $Picked_order = TaskHistory::whereIn('sprint_id', $Picked_Orders_id)
                ->groupBy('sprint_id')
                ->where('status_id',149)
                ->get();

            return backend_view('inchargemidmile.total_picked', compact('Picked_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Picked_order = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->whereIn('orders_actual_hub.hub_id', $assigned_hub)
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',149)
                    ->get();

                return backend_view('inchargemidmile.total_picked', compact('Picked_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $Picked_order = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->where('orders_actual_hub.hub_id', $input['hub_id'])
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',149)
                    ->get();

                return backend_view('inchargemidmile.total_picked', compact('Picked_order'));

            }

        }


    }

    public function getMidMileMyPickedOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){

            $users = User::whereNull('deleted_at')->where('hub_id',$user->hub_id)->pluck('id')->toArray();

            $Picked_Orders_id = MicroHubOrder::where('hub_id', $user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('scanned_by',$users)
                ->where('created_at','LIKE',$date."%")
                ->pluck('sprint_id')
                ->toArray();

            $Picked_order = TaskHistory::whereIn('sprint_id', $Picked_Orders_id)
                ->groupBy('sprint_id')
                ->where('status_id',149)
                ->get();

            return backend_view('inchargemidmile.mytotal_picked', compact('Picked_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Picked_order = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->whereIn('orders_actual_hub.hub_id', $assigned_hub)
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',149)
                    ->get();

                return backend_view('inchargemidmile.total_picked', compact('Picked_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $Picked_order = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->where('orders_actual_hub.hub_id', $input['hub_id'])
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',149)
                    ->get();

                return backend_view('inchargemidmile.total_picked', compact('Picked_order'));

            }

        }


    }

    public function getMidMileRemainingOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){

            $Total_order_ids = MicroHubOrder::where('hub_id', $user->hub_id)
                ->whereNull('deleted_at')
                ->where('is_my_hub',0)
                ->where('created_at','>',$start)
                ->where('created_at','<',$end)
                ->pluck('sprint_id')
                ->toArray();

            $Remaining_order = Sprint::whereIn('id', $Total_order_ids)
                ->whereNull('deleted_at')
                ->get();

            return backend_view('inchargemidmile.total_remaining', compact('Remaining_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Remaining_order = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->where('orders_actual_hub.is_my_hub', 0)
                    ->whereIn('orders_actual_hub.hub_id', $assigned_hub)
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',148)
                    ->get();

                return backend_view('inchargemidmile.total_remaining', compact('Remaining_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $Remaining_order = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->where('orders_actual_hub.is_my_hub', 0)
                    ->whereIn('orders_actual_hub.hub_id', $input['hub_id'])
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',148)
                    ->get();

                return backend_view('inchargemidmile.total_remaining', compact('Remaining_order'));

            }
        }



    }

    public function getMidMileDropOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $Drop_order = TorontoEntries::whereIn('creator_id', $vendors_data)->whereIn('task_status_id',[150])->where('created_at','>',$start)->where('created_at','<',$end)
                ->where('is_custom_route', 0)->get();


            return backend_view('inchargemidmile.total_drop', compact('Drop_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Drop_order = TorontoEntries::whereIn('creator_id', $vendors_data)->whereNotIn('task_status_id',[150])->where('created_at','>',$start)->where('created_at','<',$end)
                    ->where('is_custom_route', 0)->get();

                return backend_view('inchargemidmile.total_drop', compact('Drop_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $Drop_order = TorontoEntries::whereIn('creator_id', $vendors_data)->whereIn('task_status_id',[150])->where('created_at','>',$start)->where('created_at','<',$end)
                    ->where('is_custom_route', 0)->get();


                return backend_view('inchargemidmile.total_drop', compact('Drop_order'));

            }
        }



    }

    public function getMyOtherHubOrder(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){


//            $MyOrders_at_Other_Hub = MicroHubOrder::join('sprint__sprints','sprint__sprints.id','=','orders_actual_hub.sprint_id')
//                ->where('orders_actual_hub.hub_id', $user->hub_id)
//                ->whereNull('orders_actual_hub.deleted_at')
//                ->whereNull('sprint__sprints.deleted_at')
//                ->where('orders_actual_hub.is_my_hub',0)
//                ->where('orders_actual_hub.created_at','>',$start)
//                ->where('orders_actual_hub.created_at','<',$end)
//                ->get() ;

            $Total_order_ids = MicroHubOrder::where('hub_id', $user->hub_id)
                ->whereNull('deleted_at')
                ->where('is_my_hub',0)
                ->where('created_at','>',$start)
                ->where('created_at','<',$end)
                ->pluck('sprint_id')
                ->toArray();

            $MyOrders_at_Other_Hub = Sprint::whereIn('id', $Total_order_ids)
                ->whereNull('deleted_at')
                ->whereNotIn('status_id',[36])
                ->get();

            return backend_view('inchargemidmile.myother_hub_order', compact('MyOrders_at_Other_Hub'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Sprint_ids = MicroHubOrder::where('is_my_hub', 0)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->pluck('sprint_id')->toArray();

                $MyOrders_at_Other_Hub = TorontoEntries::whereIn('id', $Sprint_ids)
                    ->where('task_status_id',148)
                    ->get();


                return backend_view('inchargemidmile.myother_hub_order', compact('MyOrders_at_Other_Hub'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $Sprint_ids = MicroHubOrder::where('is_my_hub', 0)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->pluck('sprint_id')->toArray();

                $MyOrders_at_Other_Hub = TorontoEntries::whereIn('id', $Sprint_ids)
                    ->where('task_status_id',148)
                    ->get();


                return backend_view('inchargemidmile.myother_hub_order', compact('MyOrders_at_Other_Hub'));

            }
        }



    }

    public function getOtherOrder(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){

            $Microhub_users = User::where('hub_id',$user->hub_id)
                ->pluck('id')
                ->toArray();

            $Other_Orders_at_Myhub_id = MicroHubOrder::whereIn('scanned_by',$Microhub_users)
                ->where('is_my_hub',0)
                ->pluck('sprint_id')
                ->toArray();

//            $Other_Orders_at_Myhub = TaskHistory::where('status_id',148)
//                ->whereIn('sprint_id', $Other_Orders_at_Myhub_id)
//                ->groupBy('sprint_id')
//                ->get();

            $Other_order = Sprint::whereIn('status_id',[148,150])
                ->whereIn('id', $Other_Orders_at_Myhub_id)
                ->where('created_at','LIKE',$date."%")
//                ->groupBy('id')
                ->get();

            return backend_view('inchargemidmile.other_order', compact('Other_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Other_order = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->where('orders_actual_hub.is_my_hub', 1)
                    ->where('orders_actual_hub.hub_id', $user->hub_id)
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',148)
                    ->get();

                return backend_view('inchargemidmile.other_order', compact('Other_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $Other_order = MicroHubOrder::join('sprint__tasks_history','sprint__tasks_history.sprint_id','=','orders_actual_hub.sprint_id')
                    ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__tasks_history.sprint_id')
                    ->where('orders_actual_hub.is_my_hub', 1)
                    ->where('orders_actual_hub.hub_id', $user->hub_id)
                    ->where('orders_actual_hub.created_at','>',$start)
                    ->where('orders_actual_hub.created_at','<',$end)
                    ->where('sprint__tasks_history.status_id',148)
                    ->get();

                return backend_view('inchargemidmile.other_order', compact('Other_order'));

            }
        }



    }

    public function getRemainCCOrder(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';


        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $user->hub_id;

//            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));


            $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            $CC_order = TorontoEntries::whereIn('creator_id', $vendors_data)->whereIn('task_status_id',[152])->where('created_at','>',$start)->where('created_at','<',$end)
                ->where('is_custom_route', 0)->get();

            return backend_view('inchargemidmile.cc_remaining', compact('CC_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $hub_id = 17;

//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $CC_order = TorontoEntries::whereIn('creator_id', $vendors_data)->whereIn('task_status_id',[152])->where('created_at','>',$start)->where('created_at','<',$end)
                    ->where('is_custom_route', 0)->get();

                return backend_view('inchargemidmile.cc_remaining', compact('CC_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $hub_id = 17;

//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));


                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $CC_order = TorontoEntries::whereIn('creator_id', $vendors_data)->whereIn('task_status_id',[152])->where('created_at','>',$start)->where('created_at','<',$end)
                    ->where('is_custom_route', 0)->get();

                return backend_view('inchargemidmile.cc_remaining', compact('CC_order'));

            }
        }



    }

    public function getPickCCOrder(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';


        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $user->hub_id;


            $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            $CC_order = TorontoEntries::whereIn('creator_id', $vendors_data)->whereIn('task_status_id',[151])->where('created_at','>',$start)->where('created_at','<',$end)
                ->where('is_custom_route', 0)->get();

            return backend_view('inchargemidmile.cc_picked', compact('CC_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $hub_id = 17;

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $CC_order = TorontoEntries::whereIn('creator_id', $vendors_data)->whereIn('task_status_id',[151])->where('created_at','>',$start)->where('created_at','<',$end)
                    ->where('is_custom_route', 0)->get();

                return backend_view('inchargemidmile.cc_picked', compact('CC_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
                $hub_id = 17;

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $CC_order = TorontoEntries::whereIn('creator_id', $vendors_data)->whereIn('task_status_id',[151])->where('created_at','>',$start)->where('created_at','<',$end)
                    ->where('is_custom_route', 0)->get();

                return backend_view('inchargemidmile.cc_picked', compact('CC_order'));

            }
        }



    }

    public function getMidMileRoutes(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');


        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){
            $Total_routes = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->where('mile_type',2)
                ->where('date', 'LIKE', $date.'%')
                ->get();


            return backend_view('inchargemidmile.total_routes', compact('Total_routes'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_routes = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',2)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();
                return backend_view('inchargemidmile.total_routes', compact('Total_routes'));

            }else{

                $Total_routes = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$input['hub_id'])
                    ->where('joey_routes.mile_type',2)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                return backend_view('inchargemidmile.total_routes', compact('Total_routes'));

            }
        }



    }

    public function getMidMileCompleteRoutes(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;


        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){

            $Total_routes = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->where('mile_type',2)
                ->whereNotNull('joey_id')
                ->where('route_completed',1)
                ->where('date', 'LIKE', $date.'%')
                ->get();


            return backend_view('inchargemidmile.complete_routes', compact('Total_routes'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_routes_id = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',2)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',147)
                    ->get();

                return backend_view('inchargemidmile.complete_routes', compact('Total_routes'));

            }else{

                $Total_routes_id = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$input['hub_id'])
                    ->where('joey_routes.mile_type',2)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',147)
                    ->get();

                return backend_view('inchargemidmile.complete_routes', compact('Total_routes'));

            }
        }



    }

    public function getMidMileOngoingRoutes(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;


        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){
            $Total_routes = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->where('mile_type',2)
                ->whereNotNull('joey_id')
                ->where('route_completed',0)
                ->where('date', 'LIKE', $date.'%')
                ->get();

            return backend_view('inchargemidmile.ongoing_routes', compact('Total_routes'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_routes_id = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',2)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',149)
                    ->get();

                return backend_view('inchargemidmile.ongoing_routes', compact('Total_routes'));

            }else{

                $Total_routes_id = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$input['hub_id'])
                    ->where('joey_routes.mile_type',2)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',149)
                    ->get();

                return backend_view('inchargemidmile.ongoing_routes', compact('Total_routes'));

            }
        }



    }

    public function getMidMileDelayRoutes(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){
            $Total_routes_id = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                ->whereNull('joeys.deleted_at')
                ->where('joey_routes.hub',$user->hub_id)
                ->where('joey_routes.mile_type',2)
                ->where('date','>',$start)
                ->where('date','<',$end)
                ->get();

            $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                ->whereNull('deleted_at')
                ->where('task_status_id',149)
                ->get();

            return backend_view('inchargemidmile.delay_routes', compact('Total_routes'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_routes_id = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',2)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',149)
                    ->get();

                return backend_view('inchargemidmile.delay_routes', compact('Total_routes'));

            }else{

                $Total_routes_id = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$input['hub_id'])
                    ->where('joey_routes.mile_type',2)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',149)
                    ->get();

                return backend_view('inchargemidmile.delay_routes', compact('Total_routes'));

            }
        }



    }

    public function getMidMileJoeys(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');


        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){


            $Total_joeys = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->where('mile_type',2)
                ->whereNotNull('joey_id')
                ->where('date', 'LIKE', $date.'%')
                ->groupBy('joey_id')
                ->get();

            return backend_view('inchargemidmile.total_joeys', compact('Total_joeys'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_joeys = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',2)
                    ->whereNotNull('joey_routes.joey_id')
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->groupBy('joey_id')
                    ->get();

                return backend_view('inchargemidmile.total_joeys', compact('Total_joeys'));

            }else{

                $Total_joeys = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$hub)
                    ->where('joey_routes.mile_type',2)
                    ->whereNotNull('joey_routes.joey_id')
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->groupBy('joey_id')
                    ->get();
                return backend_view('inchargemidmile.total_joeys', compact('Total_joeys'));

            }
        }

    }
    //Mark:- Mid Mile Functions Ends Here




    //Mark:- Last Mile Functions Starts From Here
    public function LastMileDetails(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


            $all_status_ids = [13,147];
            $Total_order = $this->getLastMileTotalOrder($user->hub_id,$all_status_ids,$date);

            $complete_status = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144];
            $completed_order = $this->getLastMileTotalOrder($user->hub_id,$complete_status,$date);

            $return_status = [101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 112, 113, 131, 135, 136, 143];
            $return_order = $this->getLastMileTotalOrder($user->hub_id,$return_status,$date);

            $unattempt_status = [13, 61, 124];
            $unattempted_order = $this->getLastMileTotalOrder($user->hub_id,$unattempt_status,$date);

            $sort_status = [133];
            $sorted_order = $this->getLastMileTotalOrder($user->hub_id,$sort_status,$date);

            $pickup_status = [121];
            $pickedup_order = $this->getLastMileTotalOrder($user->hub_id,$pickup_status,$date);

            $delay_status = [255];
            $delayed_order = $this->getLastMileTotalOrder($user->hub_id,$delay_status,$date);

            $Custom_order = $this->getLastMileCustom($user->hub_id,$date);

            $Total_routes = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('mile_type',[3])
                ->where('date', 'LIKE', $date.'%')
                ->pluck('joey_id')
                ->toArray();

            $Total_joeys = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('mile_type',[3])
                ->whereNotNull('joey_id')
                ->where('date', 'LIKE', $date.'%')
                ->groupBy('joey_id')
                ->pluck('joey_id')
                ->toArray();

            $completed_route_id = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('mile_type',[3])
                ->whereNotNull('joey_id')
                ->pluck('id')
                ->toArray();

            $completed_route = RouteHistory::whereIn('route_id',$completed_route_id)
                ->whereNull('deleted_at')
                ->where('status',2)
                ->whereNotNull('joey_id')
                ->where('created_at','like',$date."%")
                ->pluck('id')
                ->toArray();

            $ongoing_route = RouteHistory::whereIn('route_id',$completed_route_id)
                ->whereNull('deleted_at')
                ->where('status',3)
                ->whereNotNull('joey_id')
                ->where('created_at','like',$date."%")
                ->pluck('id')
                ->toArray();

            $delayed_route = CTCEntry::whereIn('route_id',$Total_routes)
                ->whereNull('deleted_at')
                ->where('task_status_id',149)
                ->get();


            $lastmiledata = [
                'custom_order'=> count($Custom_order),
                'total_orders' => count($Total_order),
                'collected_orders' => count($pickedup_order),
                'completed_orders' => count($completed_order),
                'return_orders' => count($return_order),
                'unattempted_order' => count($unattempted_order),
                'sorted_order' => count($sorted_order),
                'delayed_order' => count($delayed_order),
                'routes'=> count($Total_routes),
                'completed_routes'=> count($completed_route),
                'ongoing_routes'=> count($ongoing_route),
                'delayed_routes'=> count($delayed_route),
                'joeys'=> count($Total_joeys),
            ];

            return $lastmiledata;

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $all_status_ids = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144,101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 112, 113, 131, 135, 136, 143,13, 61, 124 , 133, 121 , 255];
                $Total_order = $this->getLastMileTotalOrder($assigned_hub,$all_status_ids,$start,$end);


                $complete_status = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144];
                $completed_order = $this->getLastMileTotalOrder($assigned_hub,$complete_status,$start,$end);

                $return_status = [101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 112, 113, 131, 135, 136, 143];
                $return_order = $this->getLastMileTotalOrder($assigned_hub,$return_status,$start,$end);

                $unattempt_status = [13, 61, 124];
                $unattempted_order = $this->getLastMileTotalOrder($assigned_hub,$unattempt_status,$start,$end);

                $sort_status = [133];
                $sorted_order = $this->getLastMileTotalOrder($assigned_hub,$sort_status,$start,$end);

                $pickup_status = [121];
                $pickedup_order = $this->getLastMileTotalOrder($assigned_hub,$pickup_status,$start,$end);

                $delay_status = [255];
                $delayed_order = $this->getLastMileTotalOrder($assigned_hub,$delay_status,$start,$end);

                $Total_routes = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',3)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();


                $Total_joeys = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',3)
                    ->whereNotNull('joey_routes.joey_id')
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->groupBy('joey_id')
                    ->get();

                $completed_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',147)
                    ->get();

                $ongoing_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',149)
                    ->get();

                $delayed_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',149)
                    ->get();


                $Custom_order = $this->getLastMileCustom($assigned_hub,$start,$end);
                $lastmiledata = [
                    'custom_order'=> count($Custom_order),
                    'total_orders' => count($Total_order),
                    'collected_orders' => count($pickedup_order),
                    'completed_orders' => count($completed_order),
                    'return_orders' => count($return_order),
                    'unattempted_order' => count($unattempted_order),
                    'sorted_order' => count($sorted_order),
                    'delayed_order' => count($delayed_order),
                    'routes'=> count($Total_routes),
                    'completed_routes'=> count($completed_route),
                    'ongoing_routes'=> count($ongoing_route),
                    'delayed_routes'=> count($delayed_route),
                    'joeys'=> count($Total_joeys),
                ];

                return $lastmiledata;
            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $all_status_ids = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144,101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 112, 113, 131, 135, 136, 143,13, 61, 124 , 133, 121 , 255];
                $Total_order = $this->getLastMileTotalOrder($input['hub_id'],$all_status_ids,$start,$end);


                $complete_status = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144];
                $completed_order = $this->getLastMileTotalOrder($input['hub_id'],$complete_status,$start,$end);

                $return_status = [101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 112, 113, 131, 135, 136, 143];
                $return_order = $this->getLastMileTotalOrder($input['hub_id'],$return_status,$start,$end);

                $unattempt_status = [13, 61, 124];
                $unattempted_order = $this->getLastMileTotalOrder($input['hub_id'],$unattempt_status,$start,$end);

                $sort_status = [133];
                $sorted_order = $this->getLastMileTotalOrder($input['hub_id'],$sort_status,$start,$end);

                $pickup_status = [121];
                $pickedup_order = $this->getLastMileTotalOrder($input['hub_id'],$pickup_status,$start,$end);

                $delay_status = [255];
                $delayed_order = $this->getLastMileTotalOrder($input['hub_id'],$delay_status,$start,$end);


                $Total_routes = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$input['hub_id'])
                    ->where('joey_routes.mile_type',3)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();


                $Total_joeys = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$input['hub_id'])
                    ->where('joey_routes.mile_type',3)
                    ->whereNotNull('joey_routes.joey_id')
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->groupBy('joey_id')
                    ->get();

                $completed_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',147)
                    ->get();

                $ongoing_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',149)
                    ->get();

                $delayed_route = CTCEntry::whereIn('route_id',$Total_routes)
                    ->where('task_status_id',149)
                    ->get();

                $Custom_order = $this->getLastMileCustom($input['hub_id'],$start,$end);

                $lastmiledata = [
                    'custom_order'=> count($Custom_order),
                    'total_orders' => count($Total_order),
                    'collected_orders' => count($pickedup_order),
                    'completed_orders' => count($completed_order),
                    'return_orders' => count($return_order),
                    'unattempted_order' => count($unattempted_order),
                    'sorted_order' => count($sorted_order),
                    'delayed_order' => count($delayed_order),
                    'routes'=> count($Total_routes),
                    'completed_routes'=> count($completed_route),
                    'ongoing_routes'=> count($ongoing_route),
                    'delayed_routes'=> count($delayed_route),
                    'joeys'=> count($Total_joeys),
                ];

                return $lastmiledata;

            }
        }


    }

    public function getLastMileOrderByStatus($vendor,$status,$start,$end){

        $user = Auth::user();

        $My_total_order = TorontoEntries::whereIn('creator_id', $vendor)
            ->whereIn('task_status_id',$status)
            ->where('created_at','>',$start)
            ->where('created_at','<',$end)
            ->pluck('task_id');

        $My_total_task = Task::whereIn('id', $My_total_order)
            ->where('type','dropoff')
            ->pluck('location_id')
            ->toArray();

        $My_order_postal = Locations::whereIn('id', $My_total_task)
            ->pluck('postal_code')
            ->toArray();


        $postal_code_array = array();
        foreach($My_order_postal as $data){
            $postal_code = explode(' ',$data);
            $postal_code = $postal_code[0];
            array_push($postal_code_array,$postal_code);
        }


        $My_hub_postal = Hub::where('id', $user->hub_id)
            ->pluck('postal__code')
            ->toArray();

        $My_hub_postal = explode(' ',$My_hub_postal[0]);
        $My_hub_postal = $My_hub_postal[0];


        $counter = 0;
        foreach($postal_code_array as $data){
            if($My_hub_postal == $data){
                $counter ++;
            }
        }

        return $counter;
    }

    public function LastMileOrdersDetails(Request $request){

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $Total_order = TorontoEntries::whereIn('creator_id', $vendors_data)->where('created_at','>',$start)->where('created_at','<',$end)
                ->where('is_custom_route', 0)->get(['id','sprint_id','task_id','creator_id','route_id','tracking_id','joey_id','picked_up_at','delivered_at'])->toArray();

            return $Total_order;

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Total_order = TorontoEntries::whereIn('creator_id', $vendors_data)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->where('is_custom_route', 0)->get(['id','sprint_id','task_id','creator_id','route_id','tracking_id','joey_id','picked_up_at','delivered_at'])->toArray();

                return $Total_order;

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $Total_order = TorontoEntries::whereIn('creator_id', $vendors_data)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->where('is_custom_route', 0)->get(['id','sprint_id','task_id','creator_id','route_id','tracking_id','joey_id','picked_up_at','delivered_at'])->toArray();

                return $Total_order;

            }
        }

    }

    public function getLastMileOrders(Request $request)
    {


        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


            $all_status_ids = [13,147];
            $Total_order = $this->getLastMileTotalOrder($user->hub_id,$all_status_ids,$date);

            return backend_view('inchargelastmile.total_order', compact('Total_order'));
        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $all_status_ids = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144,101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 112, 113, 131, 135, 136, 143,13, 61, 124 , 133, 121 , 255];

                $Total_order = $this->getLastMileTotalOrder($assigned_hub,$all_status_ids,$start,$end);

                return backend_view('inchargelastmile.total_order', compact('Total_order'));
            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->pluck("vendor_id");

                $all_status_ids = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144,101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 112, 113, 131, 135, 136, 143,13, 61, 124 , 133, 121 , 255];


                $Total_order = $this->getLastMileTotalOrder($input['hub_id'],$all_status_ids,$start,$end);

                return backend_view('inchargelastmile.total_order', compact('Total_order'));
            }

        }

    }

    public function getLastMileTotalOrder($hub,$status,$date){

        $zone_id = ZoneRouting::whereNull('deleted_at')->where('hub_id',$hub)->pluck('id')->first();
        $postals= SlotsPostalCode::where('zone_id','=',$zone_id)->pluck('postal_code')->toArray();

        $My_total_order = Task::join('sprint__sprints','sprint__tasks.sprint_id','=','sprint__sprints.id')
            ->join('locations','location_id','=','locations.id')
            ->where('sprint__tasks.type','=','dropoff')
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postals)
            ->whereIn('sprint__sprints.status_id',$status)
            ->whereNull('sprint__sprints.deleted_at')
            ->where('sprint__sprints.created_at','LIKE',$date."%")
            ->whereNotIn('sprint__sprints.status_id',[36])
            ->orderBy('locations.postal_code')
            ->get();

        return $My_total_order;
    }

    public function getLastMileCustom($hub,$date){


//        $user = User::where('hub_id', $hub)->pluck('id');
//
//        $My_total_order = MicroHubOrder::join('ctc_entries','ctc_entries.sprint_id','=','orders_actual_hub.sprint_id')
//            ->where('ctc_entries.is_custom_route', 1)
//            ->whereNull('ctc_entries.deleted_at')
//            ->where('orders_actual_hub.created_at', 'LIKE', $date.'%')
//            ->whereIn('orders_actual_hub.scanned_by',$user)
//            ->whereNull('orders_actual_hub.deleted_at')
//            ->get();

        $zone_id = ZoneRouting::whereNull('deleted_at')->where('hub_id',$hub)->pluck('id')->first();
        $postals= SlotsPostalCode::where('zone_id','=',$zone_id)->pluck('postal_code')->toArray();

        $My_total_order = Task::join('sprint__sprints','sprint__tasks.sprint_id','=','sprint__sprints.id')
            ->join('locations','location_id','=','locations.id')
            ->join('ctc_entries','ctc_entries.sprint_id','=','sprint__sprints.id')
            ->where('sprint__tasks.type','=','dropoff')
            ->whereIn(\DB::raw('SUBSTRING(locations.postal_code,1,3)'),$postals)
//            ->whereIn('sprint__sprints.status_id',$status)
            ->where('ctc_entries.is_custom_route',1)
            ->whereNull('sprint__sprints.deleted_at')
            ->where('sprint__sprints.created_at','LIKE',$date."%")
            ->whereNotIn('sprint__sprints.status_id',[36])
            ->orderBy('locations.postal_code')
            ->get();

        return $My_total_order;
    }

    public function getLastMileVendors(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';


        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->get();
            return backend_view('inchargelastmile.total_vendor', compact('vendors_data'));
        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                //Getting vendors data from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->get();
                return backend_view('inchargelastmile.total_vendor', compact('vendors_data'));
            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $hub])->get();
                return backend_view('inchargelastmile.total_vendor', compact('vendors_data'));
            }
        }


    }

    public function getLastMilePickedOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $pickup_status = [121];
            $Picked_order = $this->getLastMileTotalOrder($user->hub_id,$pickup_status,$date);

            return backend_view('inchargelastmile.total_picked', compact('Picked_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Picked_order = $this->getLastMileTotalOrder($assigned_hub,$pickup_status,$start,$end);

                return backend_view('inchargelastmile.total_picked', compact('Picked_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $Picked_order = $this->getLastMileTotalOrder($input['hub_id'],$pickup_status,$start,$end);

                return backend_view('inchargelastmile.total_picked', compact('Picked_order'));

            }
        }



    }

    public function getLastMileCompletedOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $complete_status = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144];
            $completed_order = $this->getLastMileTotalOrder($user->hub_id,$complete_status,$date);

            return backend_view('inchargelastmile.total_completed', compact('completed_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Picked_order = $this->getLastMileTotalOrder($assigned_hub,$complete_status,$start,$end);

                return backend_view('inchargelastmile.total_completed', compact('Picked_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $Picked_order = $this->getLastMileTotalOrder($input['hub_id'],$complete_status,$start,$end);

                return backend_view('inchargelastmile.total_completed', compact('Picked_order'));

            }
        }



    }

    public function getLastMileReturnOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $return_status = [101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 112, 113, 131, 135, 136, 143];
            $return_order = $this->getLastMileTotalOrder($user->hub_id,$return_status,$date);

            return backend_view('inchargelastmile.total_return', compact('return_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Picked_order = $this->getLastMileTotalOrder($assigned_hub,$return_status,$start,$end);

                return backend_view('inchargelastmile.total_return', compact('Picked_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $Picked_order = $this->getLastMileTotalOrder($input['hub_id'],$return_status,$start,$end);

                return backend_view('inchargelastmile.total_return', compact('Picked_order'));

            }
        }



    }

    public function getLastMileUnattemptOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $unattempt_status = [13, 61, 124];
            $unattempted_order = $this->getLastMileTotalOrder($user->hub_id,$unattempt_status,$date);

            return backend_view('inchargelastmile.total_unattempted', compact('unattempted_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Picked_order = $this->getLastMileTotalOrder($assigned_hub,$unattempt_status,$start,$end);

                return backend_view('inchargelastmile.total_unattempted', compact('Picked_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $Picked_order = $this->getLastMileTotalOrder($input['hub_id'],$unattempt_status,$start,$end);

                return backend_view('inchargelastmile.total_unattempted', compact('Picked_order'));

            }
        }



    }

    public function getLastMileSortOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $sort_status = [133];
            $sorted_order = $this->getLastMileTotalOrder($user->hub_id,$sort_status,$date);

            return backend_view('inchargelastmile.total_sorted', compact('sorted_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Picked_order = $this->getLastMileTotalOrder($assigned_hub,$sort_status,$start,$end);

                return backend_view('inchargelastmile.total_sorted', compact('Picked_order'));

            }else{

                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $Picked_order = $this->getLastMileTotalOrder($input['hub_id'],$sort_status,$start,$end);

                return backend_view('inchargelastmile.total_sorted', compact('Picked_order'));

            }
        }



    }

    public function getLastMileDelayOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $delay_status = [255];
            $delayed_order = $this->getLastMileTotalOrder($user->hub_id,$delay_status,$date);

            return backend_view('inchargelastmile.total_delayed', compact('delayed_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Picked_order = $this->getLastMileTotalOrder($assigned_hub,$delay_status,$start,$end);

                return backend_view('inchargelastmile.total_delayed', compact('Picked_order'));

            }else{


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $Picked_order = $this->getLastMileTotalOrder($input['hub_id'],$delay_status,$start,$end);

                return backend_view('inchargelastmile.total_delayed', compact('Picked_order'));

            }
        }



    }

    public function getLastMileCustomOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $Custom_order = $this->getLastMileCustom($user->hub_id,$date);

            return backend_view('inchargelastmile.total_custom', compact('Custom_order'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Picked_order = $this->getLastMileCustom($assigned_hub,$start,$end);

                return backend_view('inchargelastmile.total_custom', compact('Picked_order'));

            }else{

                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $Picked_order = $this->getLastMileCustom($input['hub_id'],$start,$end);

                return backend_view('inchargelastmile.total_custom', compact('Picked_order'));

            }
        }



    }

    public function getLastMileRemainingOrders(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){
            //Getting vendors_id from HubStore on the bases of hub_id...
            $vendors_data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

            $Remaining_order = TorontoEntries::whereIn('creator_id', $vendors_data)->whereIn('task_status_id',[124])->where('created_at','>',$start)->where('created_at','<',$end)
                ->where('is_custom_route', 0)->get();

            return backend_view('inchargelastmile.total_remaining', compact('Remaining_order'));

        }else{

            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();


                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::whereIn('hub_id',$assigned_hub)->pluck("vendor_id");

                $Remaining_order = TorontoEntries::whereIn('creator_id', $vendors_data)->whereNotIn('task_status_id',[124])->where('created_at','>',$start)->where('created_at','<',$end)
                    ->where('is_custom_route', 0)->get();

                return backend_view('inchargelastmile.total_remaining', compact('Remaining_order'));

            }else{

                //Getting vendors_id from HubStore on the bases of hub_id...
                $vendors_data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

                $Remaining_order = TorontoEntries::whereIn('creator_id', $vendors_data)->whereIn('task_status_id',[124])->where('created_at','>',$start)->where('created_at','<',$end)
                    ->where('is_custom_route', 0)->get();


                return backend_view('inchargelastmile.total_remaining', compact('Remaining_order'));

            }

        }


    }

    public function getLastMileRoutes(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');


        if( $user->userType == 'user' || $user->userType == 'admin'){

            $Total_routes = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('mile_type',[3])
                ->where('date', 'LIKE', $date.'%')
                ->pluck('joey_id')
                ->toArray();

            return backend_view('inchargelastmile.total_routes', compact('Total_routes'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_routes = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',3)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();
                return backend_view('inchargemidmile.total_routes', compact('Total_routes'));

            }else{

                $Total_routes = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$hub)
                    ->where('joey_routes.mile_type',3)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();
                return backend_view('inchargelastmile.total_routes', compact('Total_routes'));

            }

        }


    }

    public function getLastMileCompleteRoutes(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;

        if( $user->userType == 'user' || $user->userType == 'admin'){


            $completed_route_id = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('mile_type',[3])
                ->whereNotNull('joey_id')
                ->pluck('id')
                ->toArray();

            $Total_routes = RouteHistory::whereIn('route_id',$completed_route_id)
                ->whereNull('deleted_at')
                ->where('status',2)
                ->whereNotNull('joey_id')
                ->where('created_at','like',$date."%")
                ->pluck('id')
                ->toArray();


            return backend_view('inchargelastmile.complete_routes', compact('Total_routes'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_routes_id = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',3)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',147)
                    ->get();

                return backend_view('inchargelastmile.complete_routes', compact('Total_routes'));

            }else{

                $Total_routes_id = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$input['hub_id'])
                    ->where('joey_routes.mile_type',3)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',147)
                    ->get();

                return backend_view('inchargelastmile.complete_routes', compact('Total_routes'));

            }
        }



    }

    public function getLastMileOngoingRoutes(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;


        if( $user->userType == 'user' || $user->userType == 'admin'){

            $completed_route_id = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('mile_type',[3])
                ->whereNotNull('joey_id')
                ->pluck('id')
                ->toArray();

            $Total_routes = RouteHistory::whereIn('route_id',$completed_route_id)
                ->whereNull('deleted_at')
                ->where('status',3)
                ->whereNotNull('joey_id')
                ->where('created_at','like',$date."%")
                ->pluck('id')
                ->toArray();


            return backend_view('inchargelastmile.ongoing_routes', compact('Total_routes'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_routes_id = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',3)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',149)
                    ->get();

                return backend_view('inchargelastmile.ongoing_routes', compact('Total_routes'));

            }else{

                $Total_routes_id = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$input['hub_id'])
                    ->where('joey_routes.mile_type',3)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',149)
                    ->get();

                return backend_view('inchargelastmile.ongoing_routes', compact('Total_routes'));

            }
        }



    }

    public function getLastMileDelayRoutes(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');


        if( $user->userType == 'user' || $user->userType == 'admin'){
            $Total_routes = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('mile_type',[3])
                ->where('date', 'LIKE', $date.'%')
                ->pluck('joey_id')
                ->toArray();

            $Total_routes = CTCEntry::whereIn('route_id',$Total_routes)
                ->whereNull('deleted_at')
                ->where('task_status_id',149)
                ->get();

            return backend_view('inchargelastmile.delay_routes', compact('Total_routes'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_routes_id = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',3)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',149)
                    ->get();

                return backend_view('inchargelastmile.delay_routes', compact('Total_routes'));

            }else{

                $Total_routes_id = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$input['hub_id'])
                    ->where('joey_routes.mile_type',3)
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->get();

                $Total_routes = CTCEntry::whereIn('route_id',$Total_routes_id)
                    ->where('task_status_id',149)
                    ->get();

                return backend_view('inchargelastmile.delay_routes', compact('Total_routes'));

            }
        }



    }

    public function getLastMileJoeys(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');


        $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');



        if( $user->userType == 'user' || $user->userType == 'admin'){

            $Total_joeys = JoeyRoute::where('hub',$user->hub_id)
                ->whereNull('deleted_at')
                ->whereIn('mile_type',[3])
                ->whereNotNull('joey_id')
                ->where('date', 'LIKE', $date.'%')
                ->groupBy('joey_id')
                ->get();

            return backend_view('inchargelastmile.total_joeys', compact('Total_joeys'));

        }else{
            if($hub == "all") {

                $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                    ->where('user_id', $user->id)
                    ->pluck('hub_id')->toArray();

                $Total_joeys = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->whereIn('joey_routes.hub',$assigned_hub)
                    ->where('joey_routes.mile_type',3)
                    ->whereNotNull('joey_routes.joey_id')
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->groupBy('joey_id')
                    ->get();
                return backend_view('inchargelastmile.total_joeys', compact('Total_joeys'));

            }else{

                $Total_joeys = JoeyRoute::join('joeys','joeys.id','=','joey_routes.joey_id')
                    ->where('joey_routes.hub',$hub)
                    ->where('joey_routes.mile_type',3)
                    ->whereNotNull('joey_routes.joey_id')
                    ->where('date','>',$start)
                    ->where('date','<',$end)
                    ->groupBy('joey_id')
                    ->get();
                return backend_view('inchargelastmile.total_joeys', compact('Total_joeys'));

            }
        }


    }
    //Mark:- Last Mile Functions Ends Here



    public function getFailedOrders(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

        $input = $request->all();
        $date =   $input['datepicker'];
        $hub_id =  base64_decode($input['hub_id']);
        $type = base64_decode($input['type']);
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
        $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
        $sprint = new Sprint();
        $montreal =[];
        $ottawa = [];
        $ctc = [];
        $title = '';
        if ($type == 'failed') {$title = 'Failed Orders';}
        elseif ($type == 'failed_create'){$title = 'Failed Orders Created';}
        else{$title = 'Failed Order Not Created';}

        if (in_array('477260', $vendors)) {
//            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            $failedTrackingId = DB::table('xml_failed_orders')->join('mainfest_fields', 'mainfest_fields.trackingID', '=', 'xml_failed_orders.tracking_id')
                ->where('xml_failed_orders.created_at','>',$start)->where('xml_failed_orders.created_at','<',$end)->whereNotNull('mainfest_fields.trackingID')
                ->whereNull('mainfest_fields.deleted_at')->whereIn('vendor_id', $data)
                ->pluck('trackingID');

            if ($type == 'failed') {
                $montreal = DB::table('mainfest_fields')->whereIn('mainfest_fields.trackingID',$failedTrackingId)
                    ->get(['trackingID AS tracking_num','consigneeAddressLine1 AS address','consigneeAddressName AS customer_name','customerOrderNumber AS merchant_order_number']);
            }
            if ($type == 'failed_create') {
                $merchnatTracking = MerchantIds::whereIn('tracking_id', $failedTrackingId)->pluck('tracking_id')->toArray();
                $montreal = DB::table('mainfest_fields')->whereIn('mainfest_fields.trackingID',$merchnatTracking)
                    ->get(['trackingID AS tracking_num','consigneeAddressLine1 AS address','consigneeAddressName AS customer_name','customerOrderNumber AS merchant_order_number']);
            }
            if ($type == 'failed_not_create') {
                $merchnatTracking = MerchantIds::whereIn('tracking_id', $failedTrackingId)->pluck('tracking_id')->toArray();
                $not_create_tracking  = array_diff($failedTrackingId, $merchnatTracking);
                $montreal = DB::table('mainfest_fields')->whereIn('mainfest_fields.trackingID',$not_create_tracking)
                    ->get(['trackingID AS tracking_num','consigneeAddressLine1 AS address','consigneeAddressName AS customer_name','customerOrderNumber AS merchant_order_number']);
            }

        }

        if (in_array('477282', $vendors)) {
            $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            $failedTrackingId = DB::table('xml_failed_orders')->join('mainfest_fields', 'mainfest_fields.trackingID', '=', 'xml_failed_orders.tracking_id')
                ->where('xml_failed_orders.created_at','>',$start)->where('xml_failed_orders.created_at','<',$end)
                ->whereNotNull('mainfest_fields.trackingID')
                ->whereNull('mainfest_fields.deleted_at')->whereIn('vendor_id', $data)->pluck('trackingID');
            if ($type == 'failed') {
                $ottawa = DB::table('mainfest_fields')->whereIn('mainfest_fields.trackingID',$failedTrackingId)
                    ->get(['trackingID AS tracking_num','consigneeAddressLine1 AS address','consigneeAddressName AS customer_name','customerOrderNumber AS merchant_order_number']);
            }
            if ($type == 'failed_create') {
                $merchnatTracking = MerchantIds::whereIn('tracking_id', $failedTrackingId)->pluck('tracking_id')->toArray();
                $ottawa = DB::table('mainfest_fields')->whereIn('mainfest_fields.trackingID',$merchnatTracking)
                    ->get(['trackingID AS tracking_num','consigneeAddressLine1 AS address','consigneeAddressName AS customer_name','customerOrderNumber AS merchant_order_number']);
            }
            if ($type == 'failed_not_create') {
                $merchnatTracking = MerchantIds::whereIn('tracking_id', $failedTrackingId)->pluck('tracking_id')->toArray();
                $not_create_tracking  = array_diff($failedTrackingId, $merchnatTracking);
                $ottawa = DB::table('mainfest_fields')->whereIn('mainfest_fields.trackingID',$not_create_tracking)
                    ->get(['trackingID AS tracking_num','consigneeAddressLine1 AS address','consigneeAddressName AS customer_name','customerOrderNumber AS merchant_order_number']);
            }
        }

        if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
            $ctc_ids = array_intersect($ctcVendorIds, $vendors);

            $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');


            $failed_tracking_Ids = DB::table('ctc_failed_orders')->whereIn('vendor_id' ,$ctc_ids)
                ->where('ctc_failed_orders.created_at','>',$start)->where('ctc_failed_orders.created_at','<',$end)->pluck('tracking_num');

            if ($type == 'failed') {
                $ctc = DB::table('ctc_failed_orders')->whereIn('tracking_num',$failed_tracking_Ids)->get(['tracking_num','customer_name','address','merchant_order_number']);
            }
            if ($type == 'failed_create') {
                $merchnatTracking = MerchantIds::whereIn('tracking_id',$failed_tracking_Ids)->pluck('tracking_id')->toArray();
                $ctc = DB::table('ctc_failed_orders')->whereIn('tracking_num',$merchnatTracking)->get(['tracking_num','customer_name','address','merchant_order_number']);
            }
            if ($type == 'failed_not_create') {
                $merchnatTracking = MerchantIds::whereIn('tracking_id',$failed_tracking_Ids)->pluck('tracking_id')->toArray();
                $not_create_tracking  = array_diff($failed_tracking_Ids, $merchnatTracking);
                $ctc = DB::table('ctc_failed_orders')->whereIn('tracking_num',$not_create_tracking)->get(['tracking_num','customer_name','address','merchant_order_number']);
            }

        }

        $result = array_merge($montreal,$ottawa,$ctc);
        return backend_view('statistics.failed_detail', compact('result','title'));
    }

    public function getFailedCounts(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        if($hub == "all") {

            $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                ->where('user_id', $user->id)
                ->pluck('hub_id')->toArray();


            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::whereIn('hub_id', $assigned_hub)->pluck("vendor_id");

            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $input['hub_id'];
            $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $failed_order = 0;
            $system_failed_order = 0;
            $not_in_system_failed_order = 0;

            if (in_array('477260', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $failed_tracking_Ids = DB::table('xml_failed_orders')->join('mainfest_fields', 'mainfest_fields.trackingID', '=', 'xml_failed_orders.tracking_id')
                    ->where('xml_failed_orders.created_at','>',$start)->where('xml_failed_orders.created_at','<',$end)->whereNotNull('mainfest_fields.trackingID')
                    ->whereNull('mainfest_fields.deleted_at')->whereIn('vendor_id', $data)->pluck('tracking_id');

                $merchnatTracking = MerchantIds::whereIn('tracking_id', $failed_tracking_Ids)->pluck('tracking_id')->toArray();
                $failed_order = $failed_order + count($failed_tracking_Ids);
                $system_failed_order = $system_failed_order + count($merchnatTracking);
                $not_in_system_failed_order = $not_in_system_failed_order + count(array_diff($failed_tracking_Ids, $merchnatTracking));


            }

            if (in_array('477282', $vendors)) {
                $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $failed_tracking_Ids = DB::table('xml_failed_orders')->join('mainfest_fields', 'mainfest_fields.trackingID', '=', 'xml_failed_orders.tracking_id')
                    ->where('xml_failed_orders.created_at','>',$start)->where('xml_failed_orders.created_at','<',$end)->whereNotNull('mainfest_fields.trackingID')
                    ->whereNull('mainfest_fields.deleted_at')->whereIn('vendor_id', $data)->pluck('tracking_id');
                $merchnatTracking = MerchantIds::whereIn('tracking_id',$failed_tracking_Ids)->pluck('tracking_id')->toArray();
                $failed_order = $failed_order + count($failed_tracking_Ids);
                $system_failed_order = $system_failed_order + count($merchnatTracking);
                $not_in_system_failed_order = $not_in_system_failed_order + count(array_diff($failed_tracking_Ids, $merchnatTracking));
            }

            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $failed_tracking_Ids = DB::table('ctc_failed_orders')->whereIn('vendor_id' ,$ctc_ids)
                    ->where('ctc_failed_orders.created_at','>',$start)->where('ctc_failed_orders.created_at','<',$end)->pluck('tracking_num');
                $merchnatTracking = MerchantIds::whereIn('tracking_id',$failed_tracking_Ids)->pluck('tracking_id')->toArray();
                $failed_order = $failed_order + count($failed_tracking_Ids);
                $system_failed_order = $system_failed_order + count($merchnatTracking);
                $not_in_system_failed_order = $not_in_system_failed_order + count(array_diff($failed_tracking_Ids, $merchnatTracking));

            }
            $counts['failed'] = $failed_order;
            $counts['system_failed_order'] = $system_failed_order;
            $counts['not_in_system_failed_order'] = $not_in_system_failed_order;

            return $counts;
        }else{

            //User Data to get the logged in details...
            $user = Auth::user();

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");


            $input = $request->all();
            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $input['hub_id'];
            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $$input['hub_id'])->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $failed_order = 0;
            $system_failed_order = 0;
            $not_in_system_failed_order = 0;

            if (in_array('477260', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $failed_tracking_Ids = DB::table('xml_failed_orders')->join('mainfest_fields', 'mainfest_fields.trackingID', '=', 'xml_failed_orders.tracking_id')
                    ->where('xml_failed_orders.created_at','>',$start)->where('xml_failed_orders.created_at','<',$end)->whereNotNull('mainfest_fields.trackingID')
                    ->whereNull('mainfest_fields.deleted_at')->whereIn('vendor_id', $data)->pluck('tracking_id');

                $merchnatTracking = MerchantIds::whereIn('tracking_id', $failed_tracking_Ids)->pluck('tracking_id')->toArray();
                $failed_order = $failed_order + count($failed_tracking_Ids);
                $system_failed_order = $system_failed_order + count($merchnatTracking);
                $not_in_system_failed_order = $not_in_system_failed_order + count(array_diff($failed_tracking_Ids, $merchnatTracking));


            }

            if (in_array('477282', $vendors)) {
                $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $failed_tracking_Ids = DB::table('xml_failed_orders')->join('mainfest_fields', 'mainfest_fields.trackingID', '=', 'xml_failed_orders.tracking_id')
                    ->where('xml_failed_orders.created_at','>',$start)->where('xml_failed_orders.created_at','<',$end)->whereNotNull('mainfest_fields.trackingID')
                    ->whereNull('mainfest_fields.deleted_at')->whereIn('vendor_id', $data)->pluck('tracking_id');
                $merchnatTracking = MerchantIds::whereIn('tracking_id',$failed_tracking_Ids)->pluck('tracking_id')->toArray();
                $failed_order = $failed_order + count($failed_tracking_Ids);
                $system_failed_order = $system_failed_order + count($merchnatTracking);
                $not_in_system_failed_order = $not_in_system_failed_order + count(array_diff($failed_tracking_Ids, $merchnatTracking));
            }

            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $failed_tracking_Ids = DB::table('ctc_failed_orders')->whereIn('vendor_id' ,$ctc_ids)
                    ->where('ctc_failed_orders.created_at','>',$start)->where('ctc_failed_orders.created_at','<',$end)->pluck('tracking_num');
                $merchnatTracking = MerchantIds::whereIn('tracking_id',$failed_tracking_Ids)->pluck('tracking_id')->toArray();
                $failed_order = $failed_order + count($failed_tracking_Ids);
                $system_failed_order = $system_failed_order + count($merchnatTracking);
                $not_in_system_failed_order = $not_in_system_failed_order + count(array_diff($failed_tracking_Ids, $merchnatTracking));

            }
            $counts['failed'] = $failed_order;
            $counts['system_failed_order'] = $system_failed_order;
            $counts['not_in_system_failed_order'] = $not_in_system_failed_order;

            return $counts;
        }

    }

    public function getCustomCounts(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        if($hub == "all") {

            $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                ->where('user_id', $user->id)
                ->pluck('hub_id')->toArray();


            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::whereIn('hub_id', $assigned_hub)->pluck("vendor_id");

            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $input['hub_id'];
            $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();

            $custom_order = 0;

            if (in_array('477260', $vendors)) {

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $custom_order = $custom_order + DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                        ->whereIn('creator_id', $data)->where('is_custom_route', 1)->count();
            }
            if (in_array('477282', $vendors)) {

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $custom_order = $custom_order + DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                        ->whereIn('creator_id', $data)->where('is_custom_route', 1)->count();
            }

            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $custom_order = $custom_order + DB::table('ctc_entries')->whereIn('creator_id',$ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)->where('is_custom_route', 1)->count();
            }
            $counts['custom_order'] = $custom_order;
            return $counts;
        }else{

            //User Data to get the logged in details...
            $user = Auth::user();

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

            $input = $request->all();
            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $input['hub_id'];
            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();

            $custom_order = 0;

            if (in_array('477260', $vendors)) {

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $custom_order = $custom_order + DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                        ->whereIn('creator_id', $data)->where('is_custom_route', 1)->count();
            }
            if (in_array('477282', $vendors)) {

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $custom_order = $custom_order + DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                        ->whereIn('creator_id', $data)->where('is_custom_route', 1)->count();
            }

            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $custom_order = $custom_order + DB::table('ctc_entries')->whereIn('creator_id',$ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)->where('is_custom_route', 1)->count();
            }
            $counts['custom_order'] = $custom_order;
            return $counts;
        }
    }

    public function getManualCounts(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        if($hub == "all") {

            $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                ->where('user_id', $user->id)
                ->pluck('hub_id')->toArray();


            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::whereIn('hub_id', $assigned_hub)->pluck("vendor_id");

            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $input['hub_id'];
            $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $manual = 0;

            if (in_array('477260', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $trackingIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->pluck('tracking_id');


                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $manual = $manual + TrackingImageHistory::whereIn('tracking_id', $trackingIds)
                        ->where('created_at','>',$start)->where('created_at','<',$end)->count();
            }
            if (in_array('477282', $vendors)) {

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $trackingIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->pluck('tracking_id');

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $manual = $manual + TrackingImageHistory::whereIn('tracking_id', $trackingIds)
                        ->where('created_at','>',$start)->where('created_at','<',$end)->count();
            }

            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $trackingIds = DB::table('ctc_entries')->whereIn('creator_id',$ctc_ids)
                    ->where('created_at','>',$start)->where('created_at','<',$end)->pluck('tracking_id');
                $manual = $manual + TrackingImageHistory::whereIn('tracking_id', $trackingIds)
                        ->where('created_at','>',$start)->where('created_at','<',$end)->count();
            }
            $counts['manual'] = $manual;

            return $counts;
        }else{

            //User Data to get the logged in details...
            $user = Auth::user();

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");


            $input = $request->all();
            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $input['hub_id'];
            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $manual = 0;

            if (in_array('477260', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $trackingIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->pluck('tracking_id');


                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $manual = $manual + TrackingImageHistory::whereIn('tracking_id', $trackingIds)
                        ->where('created_at','>',$start)->where('created_at','<',$end)->count();
            }
            if (in_array('477282', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $trackingIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->pluck('tracking_id');

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $manual = $manual + TrackingImageHistory::whereIn('tracking_id', $trackingIds)
                        ->where('created_at','>',$start)->where('created_at','<',$end)->count();
            }

            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $trackingIds = DB::table('ctc_entries')->whereIn('creator_id',$ctc_ids)
                    ->where('created_at','>',$start)->where('created_at','<',$end)->pluck('tracking_id');
                $manual = $manual + TrackingImageHistory::whereIn('tracking_id', $trackingIds)
                        ->where('created_at','>',$start)->where('created_at','<',$end)->count();
            }
            $counts['manual'] = $manual;

            return $counts;
        }
    }

    public function getRouteDataCounts(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        if($hub == "all") {

            $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                ->where('user_id', $user->id)
                ->pluck('hub_id')->toArray();


            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::whereIn('hub_id', $assigned_hub)->pluck("vendor_id");

            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();

            $routeIds = [];
            $ottawa_routeIds = [];
            $montreal_routeIds = [];
            $ctc_routeIds = [];
            $total_route = 0;
            $normal_route = 0;
            $custom_route = 0;
            $big_box_route = 0;


            if (in_array('477260', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));
                //$montreal_routeIds = AmazonEnteries::where(DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), 'like', $amazon_date . "%")->where(['creator_id' => 477260])->pluck('route_id')->toArray();
                $montreal_routeIds = JoeyRoutes::where('hub',$user->hub_id)->where('date', 'like', $date . "%")->pluck('id')->toArray();
            }
            if (in_array('477282', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));
                //$ottawa_routeIds = AmazonEnteries::where(DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), 'like', $amazon_date . "%")->where(['creator_id' => 477282])->pluck('route_id')->toArray();
                $ottawa_routeIds = JoeyRoutes::where('hub',$user->hub_id)->where('date', 'like', $date . "%")->pluck('id')->toArray();
            }

            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);
                //$ctc_routeIds = CTCEntry::whereIn('creator_id',$ctc_ids)->where(\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), 'like', $date . "%")->pluck('route_id')->toArray();
                $ctc_routeIds = JoeyRoutes::where('hub',$user->hub_id)->where('date', 'like', $date . "%")->pluck('id')->toArray();
            }
            $routeIds = array_merge($montreal_routeIds,$ottawa_routeIds,$ctc_routeIds);

            $route_data = JoeyRoutes::whereIn('id',$routeIds)->where('date', 'like', $date . "%")->whereNull('deleted_at')->get();
            foreach ($route_data as $route){
                $route_location_check  = DB::table('joey_route_locations')->where('route_id', $route->id)->whereNull('deleted_at')->first();
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
            $counts['total_route']= $total_route;
            $counts['normal_route']= $normal_route;
            $counts['custom_route']= $custom_route;
            $counts['big_box_route']= $big_box_route;
            return $counts;
        }else{


            //User Data to get the logged in details...
            $user = Auth::user();

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");


            $input = $request->all();
            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $input['hub_id'];
            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();

            $routeIds = [];
            $ottawa_routeIds = [];
            $montreal_routeIds = [];
            $ctc_routeIds = [];
            $total_route = 0;
            $normal_route = 0;
            $custom_route = 0;
            $big_box_route = 0;


            if (in_array('477260', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));
                //$montreal_routeIds = AmazonEnteries::where(DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), 'like', $amazon_date . "%")->where(['creator_id' => 477260])->pluck('route_id')->toArray();
                $montreal_routeIds = JoeyRoutes::where('hub',$user->hub_id)->where('date', 'like', $date . "%")->pluck('id')->toArray();
            }
            if (in_array('477282', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));
                //$ottawa_routeIds = AmazonEnteries::where(DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), 'like', $amazon_date . "%")->where(['creator_id' => 477282])->pluck('route_id')->toArray();
                $ottawa_routeIds = JoeyRoutes::where('hub',$user->hub_id)->where('date', 'like', $date . "%")->pluck('id')->toArray();
            }

            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);
                //$ctc_routeIds = CTCEntry::whereIn('creator_id',$ctc_ids)->where(\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), 'like', $date . "%")->pluck('route_id')->toArray();
                $ctc_routeIds = JoeyRoutes::where('hub',$user->hub_id)->where('date', 'like', $date . "%")->pluck('id')->toArray();
            }
            $routeIds = array_merge($montreal_routeIds,$ottawa_routeIds,$ctc_routeIds);

            $route_data = JoeyRoutes::whereIn('id',$routeIds)->where('date', 'like', $date . "%")->whereNull('deleted_at')->get();
            foreach ($route_data as $route){
                $route_location_check  = DB::table('joey_route_locations')->where('route_id', $route->id)->whereNull('deleted_at')->first();
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
            $counts['total_route']= $total_route;
            $counts['normal_route']= $normal_route;
            $counts['custom_route']= $custom_route;
            $counts['big_box_route']= $big_box_route;
            return $counts;
        }
    }

    public function getRouteDataDetail(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


        $input = $request->all();
        $type = base64_decode($input['type']);
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $input['hub_id'];
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
        $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();

        $routeIds = [];
        $ottawa_routeIds = [];
        $montreal_routeIds = [];
        $ctc_routeIds = [];

        $total_route = [];
        $normal_route = [];
        $custom_route = [];
        $big_box_route = [];



        if (in_array('477260', $vendors)) {
//            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));
            //$montreal_routeIds = AmazonEnteries::where(DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), 'like', $amazon_date . "%")->where(['creator_id' => 477260])->pluck('route_id')->toArray();
            $montreal_routeIds = JoeyRoutes::where('hub',$user->hub_id)->where('date', 'like', $date . "%")->pluck('id')->toArray();
        }
        if (in_array('477282', $vendors)) {
//            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));
            //$ottawa_routeIds = AmazonEnteries::where(DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), 'like', $amazon_date . "%")->where(['creator_id' => 477282])->pluck('route_id')->toArray();
            $ottawa_routeIds = JoeyRoutes::where('hub',$user->hub_id)->where('date', 'like', $date . "%")->pluck('id')->toArray();
        }

        if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
            $ctc_ids = array_intersect($ctcVendorIds, $vendors);
            //$ctc_routeIds = CTCEntry::whereIn('creator_id',$ctc_ids)->where(\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), 'like', $date . "%")->pluck('route_id')->toArray();
            $ctc_routeIds = JoeyRoutes::where('hub',$user->hub_id)->where('date', 'like', $date . "%")->pluck('id')->toArray();
        }
        $routeIds = array_merge($montreal_routeIds,$ottawa_routeIds,$ctc_routeIds);

        $route_data = JoeyRoutes::whereIn('id',$routeIds)->where('date', 'like', $date . "%")->whereNull('deleted_at')->get();
        foreach ($route_data as $route){
            $route_location_check  = DB::table('joey_route_locations')->where('route_id', $route->id)->whereNull('deleted_at')->first();
            if ($route_location_check) {
                if ($route->zone != null) {
                    $is_custom_check = \DB::table("zones_routing")->where('id', $route->zone)->whereNull('is_custom_routing')->first();
                    if ($is_custom_check) {
                        $normal_route[] = $route->id;
                    } else {
                        $route_location = DB::table('joey_route_locations')->where('route_id', $route->id)->first();
                        if ($route_location) {
                            $tracking = DB::table('merchantids')->where('task_id', $route_location->task_id)->first();
                            if ($tracking) {
                                $custom_route_data = DB::table('custom_routing_tracking_id')->where('tracking_id', $tracking->tracking_id)->first();
                                if ($custom_route_data) {
                                    if ($custom_route_data->is_big_box == 1) {
                                        $big_box_route[] = $route->id;
                                    } else {
                                        $custom_route[] = $route->id;
                                    }
                                } else {
                                    $custom_route[] = $route->id;
                                }
                            } else {
                                $custom_route[] = $route->id;
                            }
                        } else {
                            $custom_route[] = $route->id;
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
                                    $big_box_route[] = $route->id;
                                } else {
                                    $custom_route[] = $route->id;
                                }
                            } else {
                                $custom_route[] = $route->id;
                            }
                        } else {
                            $custom_route[] = $route->id;
                        }
                    } else {
                        $custom_route[] = $route->id;
                    }
                }
                $total_route[] = $route->id;
            }
        }
        $route_details = [];
        if ($type == 'total_route') {
            $title = 'Total Route';
            $route_details = JoeyRoutes::whereIn('id',$routeIds)->whereIn('id',$total_route)->whereNull('deleted_at')->get();
        }
        elseif ($type == 'normal_route') {
            $title = 'Normal Route';
            $route_details = JoeyRoutes::whereIn('id',$routeIds)->whereIn('id',$normal_route)->whereNull('deleted_at')->get();
        }
        elseif($type == 'custom_route') {
            $title = 'Custom Route';
            $route_details = JoeyRoutes::whereIn('id',$routeIds)->whereIn('id',$custom_route)->whereNull('deleted_at')->get();
        }
        else {
            $title = 'Big Box Route';
            $route_details = JoeyRoutes::whereIn('id',$routeIds)->whereIn('id',$big_box_route)->whereNull('deleted_at')->get();
        }

        return backend_view('statistics.route_detail', compact('route_details','title'));
    }

    public function getOnTimeCounts(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        if($hub == "all") {

            $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                ->where('user_id', $user->id)
                ->pluck('hub_id')->toArray();


            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::whereIn('hub_id', $assigned_hub)->pluck("vendor_id");

            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');

            $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $setting = AlertSystem::whereIn('hub_id', $assigned_hub)->first();

            $sorted_hour = 0;
            $sorted_mint = 0;
            $pickup_hour = 0;
            $pickup_mint = 0;

            $montreal_sort_TrackingIds = [];
            $montreal_pick_TrackingIds = [];
            $ottawa_sort_TrackingIds = [];
            $ottawa_pick_TrackingIds = [];
            $ctc_sort_TrackingIds = [];
            $ctc_pick_TrackingIds = [];

            if (in_array('477260', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $montreal_tracking_ids = TorontoEntries::whereIn('creator_id', $data)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->pluck('tracking_id')->toArray();
                $Custom_tracking_ids = CustomerRoutingTrackingId::whereIn('tracking_id',$montreal_tracking_ids)->pluck('tracking_id')->toArray();
                $montreal_sort_TrackingIds = array_diff($montreal_tracking_ids,$Custom_tracking_ids);

            }
            if (in_array('477282', $vendors)) {
                $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $ottawa_tracking_ids = TorontoEntries::whereIn('creator_id', $data)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->pluck('tracking_id')->toArray();
                $Custom_tracking_ids = CustomerRoutingTrackingId::whereIn('tracking_id',$ottawa_tracking_ids)->pluck('tracking_id')->toArray();
                $ottawa_sort_TrackingIds = array_diff($ottawa_tracking_ids,$Custom_tracking_ids);

            }
            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $ctc_tracking_ids = CTCEntry::whereIn('creator_id',$ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->pluck('tracking_id')->toArray();
                $Custom_tracking_ids = CustomerRoutingTrackingId::whereIn('tracking_id',$ctc_tracking_ids)->pluck('tracking_id')->toArray();
                $ctc_sort_TrackingIds = array_diff($ctc_tracking_ids,$Custom_tracking_ids);

            }

            $sort = array_merge($montreal_sort_TrackingIds,$ottawa_sort_TrackingIds,$ctc_sort_TrackingIds);


            $sort_task_ids = MerchantIds::whereIn('tracking_id',$sort)->pluck('task_id')->toArray();

            $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');


            $first_sort_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->where('status_id','133')->where('created_at','>',$start)->where('created_at','<',$end)
                ->orderBy('created_at', 'ASC')->first();
            $last_sort_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->where('status_id','133')->where('created_at','>',$start)->where('created_at','<',$end)
                ->orderBy('created_at', 'DESC')->first();

            if (!empty($first_sort_order)) {
                if ($first_sort_order->created_at) {
                    $date1 = new \DateTime($first_sort_order->created_at);
                    $date2 = new \DateTime($last_sort_order->created_at);
                    $diff = $date2->diff($date1);
                    if ($diff->d > 0) {
                        $sorted_hour = $sorted_hour + $diff->d * 24;
                    }
                    $sorted_hour = $sorted_hour + $diff->h;
                    $sorted_mint = $sorted_mint + $diff->i;
                }
            }


            $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            $first_pick_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->where('status_id','121')->where('created_at','>',$start)->where('created_at','<',$end)
                ->orderBy('created_at', 'ASC')->first();
            $last_pick_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->where('status_id','121')->where('created_at','>',$start)->where('created_at','<',$end)
                ->orderBy('created_at', 'DESC')->first();

            if (!empty($first_pick_order)) {
                if ($first_pick_order->created_at) {
                    $date1 = new \DateTime($first_pick_order->created_at);
                    $date2 = new \DateTime($last_pick_order->created_at);
                    $diff = $date2->diff($date1);
                    if ($diff->d > 0) {
                        $pickup_hour = $pickup_hour + $diff->d * 24;
                    }
                    $pickup_hour = $pickup_hour + $diff->h;
                    $pickup_mint = $pickup_mint + $diff->i;
                }
            }
            if ($setting) {
                if ($setting->sorting_time <= $sorted_hour) {
                    if ($sorted_mint > 0) {
                        $setting->sendEmail($subject = 'Sorting Time Alert', $email = 'ahmed@joeyco.com', $name = 'JoeyCo', $message = 'You are receiving this email because sorting time greater then system sorted time.');
                    }
                }

                if ($setting->pickup_time <= $pickup_hour) {
                    if ($pickup_mint > 0) {
                        $setting->sendEmail($subject = 'PickUp Time Alert', $email = 'ahmed@joeyco.com', $name = 'JoeyCo', $message = 'You are receiving this email because pickup time greater then system pickup time.');
                    }
                }
            }
            if($sorted_hour < 10){
                $sorted_hour = '0'.$sorted_hour;
            }
            if($sorted_mint < 10){
                $sorted_mint = '0'.$sorted_mint;
            }
            if($pickup_hour < 10){
                $pickup_hour = '0'.$pickup_hour;
            }
            if($pickup_mint < 10){
                $pickup_mint = '0'.$pickup_mint;
            }

            $counts['sorting'] = $sorted_hour.':'.$sorted_mint;
            $counts['pickup'] = $pickup_hour.':'.$pickup_mint;
            return  $counts;
        }else{


            //User Data to get the logged in details...
            $user = Auth::user();

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");


            $input = $request->all();
            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $input['hub_id'];
            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $setting = AlertSystem::where('hub_id', $input['hub_id'])->first();

            $sorted_hour = 0;
            $sorted_mint = 0;
            $pickup_hour = 0;
            $pickup_mint = 0;

            $montreal_sort_TrackingIds = [];
            $montreal_pick_TrackingIds = [];
            $ottawa_sort_TrackingIds = [];
            $ottawa_pick_TrackingIds = [];
            $ctc_sort_TrackingIds = [];
            $ctc_pick_TrackingIds = [];

            if (in_array('477260', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $montreal_tracking_ids = TorontoEntries::whereIn('creator_id', $data)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->pluck('tracking_id')->toArray();
                $Custom_tracking_ids = CustomerRoutingTrackingId::whereIn('tracking_id',$montreal_tracking_ids)->pluck('tracking_id')->toArray();
                $montreal_sort_TrackingIds = array_diff($montreal_tracking_ids,$Custom_tracking_ids);

            }
            if (in_array('477282', $vendors)) {
                $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $ottawa_tracking_ids = TorontoEntries::whereIn('creator_id', $data)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->pluck('tracking_id')->toArray();
                $Custom_tracking_ids = CustomerRoutingTrackingId::whereIn('tracking_id',$ottawa_tracking_ids)->pluck('tracking_id')->toArray();
                $ottawa_sort_TrackingIds = array_diff($ottawa_tracking_ids,$Custom_tracking_ids);

            }
            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $ctc_tracking_ids = CTCEntry::whereIn('creator_id',$ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->pluck('tracking_id')->toArray();
                $Custom_tracking_ids = CustomerRoutingTrackingId::whereIn('tracking_id',$ctc_tracking_ids)->pluck('tracking_id')->toArray();
                $ctc_sort_TrackingIds = array_diff($ctc_tracking_ids,$Custom_tracking_ids);

            }

            $sort = array_merge($montreal_sort_TrackingIds,$ottawa_sort_TrackingIds,$ctc_sort_TrackingIds);


            $sort_task_ids = MerchantIds::whereIn('tracking_id',$sort)->pluck('task_id')->toArray();

            $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');


            $first_sort_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->where('status_id','133')->where('created_at','>',$start)->where('created_at','<',$end)
                ->orderBy('created_at', 'ASC')->first();
            $last_sort_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->where('status_id','133')->where('created_at','>',$start)->where('created_at','<',$end)
                ->orderBy('created_at', 'DESC')->first();

            if (!empty($first_sort_order)) {
                if ($first_sort_order->created_at) {
                    $date1 = new \DateTime($first_sort_order->created_at);
                    $date2 = new \DateTime($last_sort_order->created_at);
                    $diff = $date2->diff($date1);
                    if ($diff->d > 0) {
                        $sorted_hour = $sorted_hour + $diff->d * 24;
                    }
                    $sorted_hour = $sorted_hour + $diff->h;
                    $sorted_mint = $sorted_mint + $diff->i;
                }
            }


            $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            $first_pick_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->where('status_id','121')->where('created_at','>',$start)->where('created_at','<',$end)
                ->orderBy('created_at', 'ASC')->first();
            $last_pick_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->where('status_id','121')->where('created_at','>',$start)->where('created_at','<',$end)
                ->orderBy('created_at', 'DESC')->first();

            if (!empty($first_pick_order)) {
                if ($first_pick_order->created_at) {
                    $date1 = new \DateTime($first_pick_order->created_at);
                    $date2 = new \DateTime($last_pick_order->created_at);
                    $diff = $date2->diff($date1);
                    if ($diff->d > 0) {
                        $pickup_hour = $pickup_hour + $diff->d * 24;
                    }
                    $pickup_hour = $pickup_hour + $diff->h;
                    $pickup_mint = $pickup_mint + $diff->i;
                }
            }
            if ($setting) {
                if ($setting->sorting_time <= $sorted_hour) {
                    if ($sorted_mint > 0) {
                        $setting->sendEmail($subject = 'Sorting Time Alert', $email = 'ahmed@joeyco.com', $name = 'JoeyCo', $message = 'You are receiving this email because sorting time greater then system sorted time.');
                    }
                }

                if ($setting->pickup_time <= $pickup_hour) {
                    if ($pickup_mint > 0) {
                        $setting->sendEmail($subject = 'PickUp Time Alert', $email = 'ahmed@joeyco.com', $name = 'JoeyCo', $message = 'You are receiving this email because pickup time greater then system pickup time.');
                    }
                }
            }
            if($sorted_hour < 10){
                $sorted_hour = '0'.$sorted_hour;
            }
            if($sorted_mint < 10){
                $sorted_mint = '0'.$sorted_mint;
            }
            if($pickup_hour < 10){
                $pickup_hour = '0'.$pickup_hour;
            }
            if($pickup_mint < 10){
                $pickup_mint = '0'.$pickup_mint;
            }

            $counts['sorting'] = $sorted_hour.':'.$sorted_mint;
            $counts['pickup'] = $pickup_hour.':'.$pickup_mint;
            return  $counts;
        }
    }

    public function getStatistics(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();
        $hub_id = $input['hub'];

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        if($hub == "all") {

            $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                ->where('user_id', $user->id)
                ->pluck('hub_id')->toArray();

            $hub_name = FinanceVendorCity::whereIn('id', $assigned_hub)->first();
            $joey = Joey::whereIn('id',$assigned_hub)->first();
            $joey_id = $input['rec'];
        }else{

            $hub_name = FinanceVendorCity::where('id', $hub_id)->first();
            $joey = Joey::where('id', base64_decode($input['rec']))->first();
            $joey_id = $input['rec'];
        }

        return backend_view('statistics.joey', compact('hub_id', 'hub_name', 'joey_id', 'joey'));
    }

    public function getTopTenJoeys(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        if($hub == "all") {

            $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                ->where('user_id', $user->id)
                ->pluck('hub_id')->toArray();


            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::whereIn('hub_id', $assigned_hub)->pluck("vendor_id");

            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $sprint = new Sprint();
            $joeyList =[];
            $joeyTrackingListList =[];

            if (in_array('477260', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $query = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->get(['tracking_id','joey_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                        \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                if (!empty($query)) {
                    foreach ($query as $record) {
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                            if (!is_null($record->delivered_at)) {
                                if ($record->joey_id) {
                                    if ($record->delivered_at <= $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }

                            }
                        }
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                            if (!is_null($record->returned_at)) {
                                if ($record->joey_id) {
                                    if ($record->returned_at <= $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                    }
                }
            }
            if (in_array('477282', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $query = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->get(['tracking_id','joey_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                        \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                if (!empty($query)) {
                    foreach ($query as $record) {
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                            if (!is_null($record->delivered_at)) {
                                if ($record->joey_id) {
                                    if ($record->delivered_at <= $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                            if (!is_null($record->returned_at) ) {
                                if ($record->joey_id) {
                                    if ($record->returned_at <= $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                    }
                }
            }
            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);
                $ctc_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($ctc_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($ctc_date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $sprint_id = SprintTaskHistory::where('created_at','>',$start)->where('created_at','<',$end)->where('status_id', 125)->pluck('sprint_id');
                $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)->get(['tracking_id','joey_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                    \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                if (!empty($query)) {
                    foreach ($query as $record) {
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                            if (!is_null($record->delivered_at)) {
                                if ($record->joey_id) {
                                    if ($record->delivered_at <= $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                            if (!is_null($record->returned_at)) {
                                if ($record->joey_id) {
                                    if ($record->returned_at <= $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                    }
                }
            }
            arsort($joeyList);

            $joeyList = array_slice($joeyList, 0, 10, true);

            $deliverytime=[];
            $ststausList = array_merge($sprint->getStatusCodes('competed'),$sprint->getStatusCodes('return'));
            foreach ($joeyList as $key=>$value)
            {
                $sort_task_ids = MerchantIds::whereIn('tracking_id',$joeyTrackingListList[$key])->pluck('task_id')->toArray();

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $first_drop_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->whereIn('status_id',$ststausList)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->orderBy('created_at', 'ASC')->first();
                $last_drop_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->whereIn('status_id',$ststausList)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->orderBy('created_at', 'DESC')->first();
                $drop_hour= 0;
                $drop_mint = 0;
                if (!empty($first_drop_order)) {
                    if ($first_drop_order->created_at) {
                        $date1 = new \DateTime($first_drop_order->created_at);
                        $date2 = new \DateTime($last_drop_order->created_at);
                        $diff = $date2->diff($date1);
                        if ($diff->d > 0) {
                            $drop_hour = $drop_hour + $diff->d * 24;
                        }
                        $drop_hour = $drop_hour + $diff->h;
                        $drop_mint = $drop_mint + $diff->i;
                    }
                }
                $total_mint = ($drop_hour * 60) + $drop_mint;
                if ($total_mint) {
                    $deliverytime[$key] = ($value / $total_mint) * 100;
                }
                else{
                    $deliverytime[$key] = 0;
                }
            }
            arsort($deliverytime);


            $finalList = [];
            $i = 0;
            foreach ($deliverytime as $key=>$value)
            {
                $joey =Joey::where('id',$key)->first();
                $finalList[$i]['name'] = $joey->first_name." ".$joey->last_name;
                $finalList[$i]['image'] = $joey->image != null ? $joey->image : url('/').'/images/profile_images/default.png';
                $finalList[$i]['count'] = $joeyList[$key];
                $finalList[$i]['joey_id'] = $joey->id;
                $finalList[$i]['encode_joey_id'] = base64_encode($joey->id);
                $i++;
            }
            return $finalList;
        }else{

            //User Data to get the logged in details...
            $user = Auth::user();

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");


            $input = $request->all();
            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $input['hub_id'];
            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $sprint = new Sprint();
            $joeyList =[];
            $joeyTrackingListList =[];

            if (in_array('477260', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $query = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->get(['tracking_id','joey_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                        \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                if (!empty($query)) {
                    foreach ($query as $record) {
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                            if (!is_null($record->delivered_at)) {
                                if ($record->joey_id) {
                                    if ($record->delivered_at <= $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }

                            }
                        }
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                            if (!is_null($record->returned_at)) {
                                if ($record->joey_id) {
                                    if ($record->returned_at <= $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                    }
                }
            }
            if (in_array('477282', $vendors)) {
//                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $query = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->get(['tracking_id','joey_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                        \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                if (!empty($query)) {
                    foreach ($query as $record) {
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                            if (!is_null($record->delivered_at)) {
                                if ($record->joey_id) {
                                    if ($record->delivered_at <= $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                            if (!is_null($record->returned_at) ) {
                                if ($record->joey_id) {
                                    if ($record->returned_at <= $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                    }
                }
            }
            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);
                $ctc_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($ctc_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($ctc_date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $sprint_id = SprintTaskHistory::where('created_at','>',$start)->where('created_at','<',$end)->where('status_id', 125)->pluck('sprint_id');
                $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)->get(['tracking_id','joey_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                    \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                if (!empty($query)) {
                    foreach ($query as $record) {
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                            if (!is_null($record->delivered_at)) {
                                if ($record->joey_id) {
                                    if ($record->delivered_at <= $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                            if (!is_null($record->returned_at)) {
                                if ($record->joey_id) {
                                    if ($record->returned_at <= $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                    }
                }
            }
            arsort($joeyList);

            $joeyList = array_slice($joeyList, 0, 10, true);

            $deliverytime=[];
            $ststausList = array_merge($sprint->getStatusCodes('competed'),$sprint->getStatusCodes('return'));
            foreach ($joeyList as $key=>$value)
            {
                $sort_task_ids = MerchantIds::whereIn('tracking_id',$joeyTrackingListList[$key])->pluck('task_id')->toArray();

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $first_drop_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->whereIn('status_id',$ststausList)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->orderBy('created_at', 'ASC')->first();
                $last_drop_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->whereIn('status_id',$ststausList)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->orderBy('created_at', 'DESC')->first();
                $drop_hour= 0;
                $drop_mint = 0;
                if (!empty($first_drop_order)) {
                    if ($first_drop_order->created_at) {
                        $date1 = new \DateTime($first_drop_order->created_at);
                        $date2 = new \DateTime($last_drop_order->created_at);
                        $diff = $date2->diff($date1);
                        if ($diff->d > 0) {
                            $drop_hour = $drop_hour + $diff->d * 24;
                        }
                        $drop_hour = $drop_hour + $diff->h;
                        $drop_mint = $drop_mint + $diff->i;
                    }
                }
                $total_mint = ($drop_hour * 60) + $drop_mint;
                if ($total_mint) {
                    $deliverytime[$key] = ($value / $total_mint) * 100;
                }
                else{
                    $deliverytime[$key] = 0;
                }
            }
            arsort($deliverytime);


            $finalList = [];
            $i = 0;
            foreach ($deliverytime as $key=>$value)
            {
                $joey =Joey::where('id',$key)->first();
                $finalList[$i]['name'] = $joey->first_name." ".$joey->last_name;
                $finalList[$i]['image'] = $joey->image != null ? $joey->image : url('/').'/images/profile_images/default.png';
                $finalList[$i]['count'] = $joeyList[$key];
                $finalList[$i]['joey_id'] = $joey->id;
                $finalList[$i]['encode_joey_id'] = base64_encode($joey->id);
                $i++;
            }
            return $finalList;
        }
    }

    public function getLeastTenJoeys(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        if($hub == "all") {

            $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                ->where('user_id', $user->id)
                ->pluck('hub_id')->toArray();


            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::whereIn('hub_id', $assigned_hub)->pluck("vendor_id");
            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $sprint = new Sprint();
            $joeyList =[];
            $joeyTrackingListList =[];

            if (in_array('477260', $vendors)) {


//               $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $query = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->get(['tracking_id','joey_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                        \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                if (!empty($query)) {
                    foreach ($query as $record) {
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                            if (!is_null($record->delivered_at)) {
                                if ($record->joey_id) {
                                    if ($record->delivered_at > $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                            if (!is_null($record->returned_at)) {
                                if ($record->joey_id) {
                                    if ($record->returned_at > $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                    }
                }
            }

            if (in_array('477282', $vendors)) {
//               $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $ottawaSprintIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->get(['tracking_id','joey_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                        \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                if (!empty($query)) {
                    foreach ($query as $record) {
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                            if (!is_null($record->delivered_at) ) {
                                if ($record->joey_id) {
                                    if ($record->delivered_at > $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                            if (!is_null($record->returned_at)) {
                                if ($record->joey_id) {
                                    if ($record->returned_at > $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                    }
                }
            }
            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);
                $ctc_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($ctc_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($ctc_date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $sprint_id = SprintTaskHistory::where('created_at','>',$start)->where('created_at','<',$end)->where('status_id', 125)->pluck('sprint_id');
                $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)->get(['tracking_id','joey_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                    \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                if (!empty($query)) {
                    foreach ($query as $record) {
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                            if (!is_null($record->delivered_at)) {
                                if ($record->joey_id) {
                                    if ($record->delivered_at > $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                            if (!is_null($record->returned_at)) {
                                if ($record->joey_id) {
                                    if ($record->returned_at > $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                    }
                }

            }
            arsort($joeyList);
            $joeyList = array_slice($joeyList, 0, 10, true);

            $deliverytime=[];
            $ststausList = array_merge($sprint->getStatusCodes('competed'),$sprint->getStatusCodes('return'));
            foreach ($joeyList as $key=>$value)
            {
                $sort_task_ids = MerchantIds::whereIn('tracking_id',$joeyTrackingListList[$key])->pluck('task_id')->toArray();

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $first_drop_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->whereIn('status_id',$ststausList)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->orderBy('created_at', 'ASC')->first();
                $last_drop_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->whereIn('status_id',$ststausList)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->orderBy('created_at', 'DESC')->first();
                $drop_hour= 0;
                $drop_mint = 0;
                if (!empty($first_drop_order)) {
                    if ($first_drop_order->created_at) {
                        $date1 = new \DateTime($first_drop_order->created_at);
                        $date2 = new \DateTime($last_drop_order->created_at);
                        $diff = $date2->diff($date1);
                        if ($diff->d > 0) {
                            $drop_hour = $drop_hour + $diff->d * 24;
                        }
                        $drop_hour = $drop_hour + $diff->h;
                        $drop_mint = $drop_mint + $diff->i;
                    }
                }
                $total_mint = ($drop_hour * 60) + $drop_mint;
                if ($total_mint) {
                    $deliverytime[$key] = ($value / $total_mint) * 100;
                }
                else{
                    $deliverytime[$key] = 0;
                }
            }
            arsort($deliverytime);
            $finalList = [];
            $i = 0;
            foreach ($deliverytime as $key=>$value)
            {
                $joey =Joey::where('id',$key)->first();
                $finalList[$i]['name'] = $joey->first_name." ".$joey->last_name;
                $finalList[$i]['image'] = $joey->image != null ? $joey->image : url('/').'/images/profile_images/default.png';
                $finalList[$i]['count'] = $joeyList[$key];
                $finalList[$i]['joey_id'] = $joey->id;
                $finalList[$i]['encode_joey_id'] = base64_encode($joey->id);
                $i++;
            }

            return $finalList;
        }else{

            //User Data to get the logged in details...
            $user = Auth::user();

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");


            $input = $request->all();
            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $input['hub_id'];
            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $sprint = new Sprint();
            $joeyList =[];
            $joeyTrackingListList =[];

            if (in_array('477260', $vendors)) {


//               $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $query = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->get(['tracking_id','joey_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                        \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                if (!empty($query)) {
                    foreach ($query as $record) {
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                            if (!is_null($record->delivered_at)) {
                                if ($record->joey_id) {
                                    if ($record->delivered_at > $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                            if (!is_null($record->returned_at)) {
                                if ($record->joey_id) {
                                    if ($record->returned_at > $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                    }
                }
            }

            if (in_array('477282', $vendors)) {
//               $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $ottawaSprintIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->get(['tracking_id','joey_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                        \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                if (!empty($query)) {
                    foreach ($query as $record) {
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                            if (!is_null($record->delivered_at) ) {
                                if ($record->joey_id) {
                                    if ($record->delivered_at > $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                            if (!is_null($record->returned_at)) {
                                if ($record->joey_id) {
                                    if ($record->returned_at > $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                    }
                }
            }
            if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
                $ctc_ids = array_intersect($ctcVendorIds, $vendors);
                $ctc_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($ctc_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($ctc_date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $sprint_id = SprintTaskHistory::where('created_at','>',$start)->where('created_at','<',$end)->where('status_id', 125)->pluck('sprint_id');
                $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)->get(['tracking_id','joey_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                    \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                if (!empty($query)) {
                    foreach ($query as $record) {
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                            if (!is_null($record->delivered_at)) {
                                if ($record->joey_id) {
                                    if ($record->delivered_at > $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                        if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                            if (!is_null($record->returned_at)) {
                                if ($record->joey_id) {
                                    if ($record->returned_at > $date . " 21:00:00") {
                                        if (!isset($joeyList[$record->joey_id])) {
                                            $joeyList[$record->joey_id] = 1;
                                        } else {
                                            $joeyList[$record->joey_id] = $joeyList[$record->joey_id] + 1;
                                        }
                                    }
                                    $joeyTrackingListList[$record->joey_id][]= $record->tracking_id;
                                }
                            }
                        }
                    }
                }

            }
            arsort($joeyList);
            $joeyList = array_slice($joeyList, 0, 10, true);

            $deliverytime=[];
            $ststausList = array_merge($sprint->getStatusCodes('competed'),$sprint->getStatusCodes('return'));
            foreach ($joeyList as $key=>$value)
            {
                $sort_task_ids = MerchantIds::whereIn('tracking_id',$joeyTrackingListList[$key])->pluck('task_id')->toArray();

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $first_drop_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->whereIn('status_id',$ststausList)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->orderBy('created_at', 'ASC')->first();
                $last_drop_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->whereIn('status_id',$ststausList)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->orderBy('created_at', 'DESC')->first();
                $drop_hour= 0;
                $drop_mint = 0;
                if (!empty($first_drop_order)) {
                    if ($first_drop_order->created_at) {
                        $date1 = new \DateTime($first_drop_order->created_at);
                        $date2 = new \DateTime($last_drop_order->created_at);
                        $diff = $date2->diff($date1);
                        if ($diff->d > 0) {
                            $drop_hour = $drop_hour + $diff->d * 24;
                        }
                        $drop_hour = $drop_hour + $diff->h;
                        $drop_mint = $drop_mint + $diff->i;
                    }
                }
                $total_mint = ($drop_hour * 60) + $drop_mint;
                if ($total_mint) {
                    $deliverytime[$key] = ($value / $total_mint) * 100;
                }
                else{
                    $deliverytime[$key] = 0;
                }
            }
            arsort($deliverytime);
            $finalList = [];
            $i = 0;
            foreach ($deliverytime as $key=>$value)
            {
                $joey =Joey::where('id',$key)->first();
                $finalList[$i]['name'] = $joey->first_name." ".$joey->last_name;
                $finalList[$i]['image'] = $joey->image != null ? $joey->image : url('/').'/images/profile_images/default.png';
                $finalList[$i]['count'] = $joeyList[$key];
                $finalList[$i]['joey_id'] = $joey->id;
                $finalList[$i]['encode_joey_id'] = base64_encode($joey->id);
                $i++;
            }

            return $finalList;
        }
    }

    public function getGraph(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        if($hub == "all") {

            $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                ->where('user_id', $user->id)
                ->pluck('hub_id')->toArray();


            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::whereIn('hub_id', $assigned_hub)->pluck("vendor_id");


            $type = $input['type'];
            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $sprint = new Sprint();

            $all_dates = [];
            if ($type == 'week') {
                $range_from_date = new Carbon(date('Y-m-d', strtotime('-6 day', strtotime($date))));
            }
            else
            {
                $range_from_date = new Carbon(date('Y-m-d', strtotime('-1 month', strtotime($date))));
            }
            $range_to_date = new Carbon($date);
            while ($range_from_date->lte($range_to_date)) {
                $all_dates[] = $range_from_date->toDateString();
                $range_from_date->addDay();
            }
            $odt_data_1=[];

            foreach ($all_dates as $range_date) {
                $totalcount = 0;
                $totallates = 0;
                if (in_array('477260', $vendors)) {
                    $amazon_date  = date('Y-m-d', strtotime($range_date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');

                    $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at','>',$start)->where('created_at','<',$end)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }

                if (in_array('477282', $vendors)) {
                    $amazon_date  = date('Y-m-d', strtotime($range_date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');

                    $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at','>',$start)->where('created_at','<',$end)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }

                if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                    $ctc_date  = date('Y-m-d', strtotime($range_date . ' -1 days'));
                    $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                    $start_dt = new DateTime($ctc_date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($ctc_date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $sprint_id = SprintTaskHistory::where('created_at','>',$start)->where('created_at','<',$end)->where('status_id', 125)->pluck('sprint_id');
                    $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }
                if ($totalcount == 0)
                {
                    $totalcount = 1;
                }
                $odt_data_1[$range_date] = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2),'ontime'=>  $totalcount - $totallates  , 'offtime'=> $totallates];
            }

            return $odt_data_1;
        }else{


            //User Data to get the logged in details...
            $user = Auth::user();

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

            $input = $request->all();
            $type = $input['type'];
            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $input['hub_id'];
            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $sprint = new Sprint();

            $all_dates = [];
            if ($type == 'week') {
                $range_from_date = new Carbon(date('Y-m-d', strtotime('-6 day', strtotime($date))));
            }
            else
            {
                $range_from_date = new Carbon(date('Y-m-d', strtotime('-1 month', strtotime($date))));
            }
            $range_to_date = new Carbon($date);
            while ($range_from_date->lte($range_to_date)) {
                $all_dates[] = $range_from_date->toDateString();
                $range_from_date->addDay();
            }
            $odt_data_1=[];

            foreach ($all_dates as $range_date) {
                $totalcount = 0;
                $totallates = 0;
                if (in_array('477260', $vendors)) {
                    $amazon_date  = date('Y-m-d', strtotime($range_date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');

                    $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at','>',$start)->where('created_at','<',$end)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }

                if (in_array('477282', $vendors)) {
                    $amazon_date  = date('Y-m-d', strtotime($range_date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');

                    $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at','>',$start)->where('created_at','<',$end)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }

                if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                    $ctc_date  = date('Y-m-d', strtotime($range_date . ' -1 days'));
                    $ctc_ids = array_intersect($ctcVendorIds, $vendors);

                    $start_dt = new DateTime($ctc_date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($ctc_date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $sprint_id = SprintTaskHistory::where('created_at','>',$start)->where('created_at','<',$end)->where('status_id', 125)->pluck('sprint_id');
                    $query = CTCEntry::whereIn('creator_id', $ctc_ids)->whereIn('sprint_id', $sprint_id)
                        ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at > $range_date . " 21:00:00") {
                                    $totallates++;
                                }
                            }
                            $totalcount++;
                        }
                    }
                }
                if ($totalcount == 0)
                {
                    $totalcount = 1;
                }
                $odt_data_1[$range_date] = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2),'ontime'=>  $totalcount - $totallates  , 'offtime'=> $totallates];
            }

            return $odt_data_1;

        }
    }

    public function getBroker(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();
        $input = $request->all();

        $hub = $request->get('hub_id') ? $request->get('hub_id') : 'all';

        if($hub == "all") {

            $assigned_hub = MicroHubAssign::join('hubs', 'microhub_manager_assign.hub_id', '=', 'hubs.id')
                ->where('user_id', $user->id)
                ->pluck('hub_id')->toArray();


            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::whereIn('hub_id', $assigned_hub)->pluck("vendor_id");


            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $vendors = FinanceVendorCityDetail::whereIn('vendor_city_realtions_id', $assigned_hub)->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $brokers = BrookerUser::whereNull('deleted_at')->get();
            $sprint = new Sprint();
            $brookerList = [];
            $joeyTrackingListList=[];

            foreach ($brokers as $broker) {
                $totalcount = 0;
                $totalonTime = 0;
                $brooker_joeys = BrookerJoey::where('brooker_id',$broker->id)->pluck('joey_id')->toArray();

                if (in_array('477260', $vendors)) {
//                    $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $query = TorontoEntries::whereIn('creator_id', $data)->whereIn('joey_id',$brooker_joeys)
                        ->where('created_at','>',$start)->where('created_at','<',$end)->get(['tracking_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);

                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at <= $date . " 21:00:00") {
                                    $totalonTime++;
                                }
                                $joeyTrackingListList[$broker->id][]= $record->tracking_id;
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at <= $date . " 21:00:00") {
                                    $totalonTime++;
                                }
                                $joeyTrackingListList[$broker->id][]= $record->tracking_id;
                            }
                            $totalcount++;
                        }
                    }
                }

                if (in_array('477282', $vendors)) {
//                    $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $query = TorontoEntries::whereIn('creator_id', $data)->whereIn('joey_id',$brooker_joeys)
                        ->where('created_at','>',$start)->where('created_at','<',$end)->get(['tracking_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!!is_null($record->delivered_at) && $record->delivered_at <= $date . " 21:00:00") {
                                    $totalonTime++;
                                }
                                $joeyTrackingListList[$broker->id][]= $record->tracking_id;
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at <= $date . " 21:00:00") {
                                    $totalonTime++;
                                }
                                $joeyTrackingListList[$broker->id][]= $record->tracking_id;
                            }
                            $totalcount++;
                        }
                    }
                }

                if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                    $ctc_ids = array_intersect($ctcVendorIds, $vendors);
                    $ctc_date = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($ctc_date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($ctc_date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $sprint_id = SprintTaskHistory::where('created_at','>',$start)->where('created_at','<',$end)->where('status_id', 125)->pluck('sprint_id');
                    $query = CTCEntry::whereIn('creator_id', $ctc_ids)
                        ->whereIn('joey_id',$brooker_joeys)->whereIn('sprint_id', $sprint_id)->get(['tracking_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at <= $date . " 21:00:00") {
                                    $totalonTime++;
                                }
                                $joeyTrackingListList[$broker->id][]= $record->tracking_id;
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at <= $date . " 21:00:00") {
                                    $totalonTime++;
                                }
                                $joeyTrackingListList[$broker->id][]= $record->tracking_id;
                            }
                            $totalcount++;
                        }
                    }
                }
                if($totalcount > 0) {
                    $brookerList[$broker->id] = $totalonTime;
                }

            }
            arsort($brookerList);
            $brookerList = array_slice($brookerList, 0, 10, true);

            $deliverytime=[];
            $ststausList = array_merge($sprint->getStatusCodes('competed'),$sprint->getStatusCodes('return'));
            foreach ($brookerList as $key=>$value)
            {
                $sort_task_ids = MerchantIds::whereIn('tracking_id',$joeyTrackingListList[$key])->pluck('task_id')->toArray();

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $first_drop_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->whereIn('status_id',$ststausList)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->orderBy('created_at', 'ASC')->first();
                $last_drop_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->whereIn('status_id',$ststausList)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->orderBy('created_at', 'DESC')->first();
                $drop_hour= 0;
                $drop_mint = 0;
                if (!empty($first_drop_order)) {
                    if ($first_drop_order->created_at) {
                        $date1 = new \DateTime($first_drop_order->created_at);
                        $date2 = new \DateTime($last_drop_order->created_at);
                        $diff = $date2->diff($date1);
                        if ($diff->d > 0) {
                            $drop_hour = $drop_hour + $diff->d * 24;
                        }
                        $drop_hour = $drop_hour + $diff->h;
                        $drop_mint = $drop_mint + $diff->i;
                    }
                }
                $total_mint = ($drop_hour * 60) + $drop_mint;
                if ($total_mint) {
                    $deliverytime[$key] = ($value / $total_mint) * 100;
                }
                else{
                    $deliverytime[$key] = 0;
                }
            }
            arsort($deliverytime);

            $finalList = [];
            $i = 0;
            foreach ($deliverytime as $key=>$value)
            {
                $brooker =BrookerUser::where('id',$key)->first();
                $finalList[$i]['name'] = $brooker->name;
                $finalList[$i]['count'] = $brookerList[$key];
                $finalList[$i]['brooker_id'] = base64_encode($brooker->id);
                $i++;
            }

            return $finalList;
        }else{

            //User Data to get the logged in details...
            $user = Auth::user();

            //Getting vendors_id from HubStore on the bases of hub_id...
            $data = HubStore::where(['hub_id' => $input['hub_id']])->pluck("vendor_id");

            $input = $request->all();

            $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
            $hub_id = $input['hub_id'];
            $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $input['hub_id'])->pluck('vendors_id')->toArray();
            $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
            $brokers = BrookerUser::whereNull('deleted_at')->get();
            $sprint = new Sprint();
            $brookerList = [];
            $joeyTrackingListList=[];

            foreach ($brokers as $broker) {
                $totalcount = 0;
                $totalonTime = 0;
                $brooker_joeys = BrookerJoey::where('brooker_id',$broker->id)->pluck('joey_id')->toArray();

                if (in_array('477260', $vendors)) {
//                    $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $query = TorontoEntries::whereIn('creator_id', $data)->whereIn('joey_id',$brooker_joeys)
                        ->where('created_at','>',$start)->where('created_at','<',$end)->get(['tracking_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);

                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at <= $date . " 21:00:00") {
                                    $totalonTime++;
                                }
                                $joeyTrackingListList[$broker->id][]= $record->tracking_id;
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at <= $date . " 21:00:00") {
                                    $totalonTime++;
                                }
                                $joeyTrackingListList[$broker->id][]= $record->tracking_id;
                            }
                            $totalcount++;
                        }
                    }
                }

                if (in_array('477282', $vendors)) {
//                    $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $query = TorontoEntries::whereIn('creator_id', $data)->whereIn('joey_id',$brooker_joeys)
                        ->where('created_at','>',$start)->where('created_at','<',$end)->get(['tracking_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!!is_null($record->delivered_at) && $record->delivered_at <= $date . " 21:00:00") {
                                    $totalonTime++;
                                }
                                $joeyTrackingListList[$broker->id][]= $record->tracking_id;
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at <= $date . " 21:00:00") {
                                    $totalonTime++;
                                }
                                $joeyTrackingListList[$broker->id][]= $record->tracking_id;
                            }
                            $totalcount++;
                        }
                    }
                }

                if (count(array_intersect($ctcVendorIds, $vendors)) > 0) {
                    $ctc_ids = array_intersect($ctcVendorIds, $vendors);
                    $ctc_date = date('Y-m-d', strtotime($date . ' -1 days'));

                    $start_dt = new DateTime($ctc_date." 00:00:00", new DateTimezone('America/Toronto'));
                    $start_dt->setTimeZone(new DateTimezone('UTC'));
                    $start = $start_dt->format('Y-m-d H:i:s');

                    $end_dt = new DateTime($ctc_date." 23:59:59", new DateTimezone('America/Toronto'));
                    $end_dt->setTimeZone(new DateTimezone('UTC'));
                    $end = $end_dt->format('Y-m-d H:i:s');


                    $sprint_id = SprintTaskHistory::where('created_at','>',$start)->where('created_at','<',$end)->where('status_id', 125)->pluck('sprint_id');
                    $query = CTCEntry::whereIn('creator_id', $ctc_ids)
                        ->whereIn('joey_id',$brooker_joeys)->whereIn('sprint_id', $sprint_id)->get(['tracking_id','task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                            \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
                    if (!empty($query)) {
                        foreach ($query as $record) {
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('competed'))) {
                                if (!is_null($record->delivered_at) && $record->delivered_at <= $date . " 21:00:00") {
                                    $totalonTime++;
                                }
                                $joeyTrackingListList[$broker->id][]= $record->tracking_id;
                            }
                            if (in_array($record->task_status_id, $sprint->getStatusCodes('return'))) {
                                if (!is_null($record->returned_at) && $record->returned_at <= $date . " 21:00:00") {
                                    $totalonTime++;
                                }
                                $joeyTrackingListList[$broker->id][]= $record->tracking_id;
                            }
                            $totalcount++;
                        }
                    }
                }
                if($totalcount > 0) {
                    $brookerList[$broker->id] = $totalonTime;
                }

            }
            arsort($brookerList);
            $brookerList = array_slice($brookerList, 0, 10, true);

            $deliverytime=[];
            $ststausList = array_merge($sprint->getStatusCodes('competed'),$sprint->getStatusCodes('return'));
            foreach ($brookerList as $key=>$value)
            {
                $sort_task_ids = MerchantIds::whereIn('tracking_id',$joeyTrackingListList[$key])->pluck('task_id')->toArray();

                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $first_drop_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->whereIn('status_id',$ststausList)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->orderBy('created_at', 'ASC')->first();
                $last_drop_order = SprintTaskHistory::whereIn('sprint__tasks_id',$sort_task_ids)->whereIn('status_id',$ststausList)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->orderBy('created_at', 'DESC')->first();
                $drop_hour= 0;
                $drop_mint = 0;
                if (!empty($first_drop_order)) {
                    if ($first_drop_order->created_at) {
                        $date1 = new \DateTime($first_drop_order->created_at);
                        $date2 = new \DateTime($last_drop_order->created_at);
                        $diff = $date2->diff($date1);
                        if ($diff->d > 0) {
                            $drop_hour = $drop_hour + $diff->d * 24;
                        }
                        $drop_hour = $drop_hour + $diff->h;
                        $drop_mint = $drop_mint + $diff->i;
                    }
                }
                $total_mint = ($drop_hour * 60) + $drop_mint;
                if ($total_mint) {
                    $deliverytime[$key] = ($value / $total_mint) * 100;
                }
                else{
                    $deliverytime[$key] = 0;
                }
            }
            arsort($deliverytime);

            $finalList = [];
            $i = 0;
            foreach ($deliverytime as $key=>$value)
            {
                $brooker =BrookerUser::where('id',$key)->first();
                $finalList[$i]['name'] = $brooker->name;
                $finalList[$i]['count'] = $brookerList[$key];
                $finalList[$i]['brooker_id'] = base64_encode($brooker->id);
                $i++;
            }

            return $finalList;
        }
    }


}
