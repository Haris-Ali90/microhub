<?php

namespace App\Http\Controllers\Backend;

use App\Agreements;
use App\AgreementsUser;
use App\AmazonEnteries;
use App\DeliveryProcessType;
use App\FinanceVendorCity;
use App\HubStore;
use App\MicroHubAssign;
use App\Post;
use App\TorontoEntries;
use Illuminate\Http\Request;
use App\Sprint;
use App\Http\Requests;
use App\Http\Controllers\Backend\BackendController;

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

class DashboardController extends BackendController
{


    use BasicModelFunctions;



    public function getIndex(Request $request)
    {

        $auth_user = Auth::user();
        $pass = $auth_user->password;

        //Mark:- If user is logged in first time, Forcefully let him change the password -- Daniyal Khan
        if($auth_user->is_active == 0){
            return backend_view('auth.newpassword',compact('pass'));
        }

        // $has_user_signed_agreement = AgreementsUser::where('user_id',$auth_user->id)->where('user_type','microhub')->whereNotNull('signed_at')->pluck('id');

        //Mark:- If user is logged in and his agreement is not signed yet, It will redirect it to Agreement Page -- Daniyal Khan
        // if(count($has_user_signed_agreement)==0){
        //     return redirect('microhub/cookies');
        // }

        // //Mark:- If user is logged in and has no permission even dashboard, It will redirect to a landing blank page -- Daniyal Khan
        // if(can_user_access_route("dashboard") == false){
        //     return view('backend/landing');
        // }

        // //This needs to be confirmed before push
        // if($auth_user->userType == 'incharge' || $auth_user->userType == 'user' || $auth_user->userType == 'admin'){

        //     return redirect ('incharge/dashboard');
        // }
        //Mark:- Getting vendors_id from HubStore on the bases of hub_id -- Daniyal Khan
        $data = HubStore::where(['hub_id' => $auth_user->hub_id])->pluck("vendor_id");

        $input = $request->all();
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

        //new-logic
        $hub_id = $auth_user->hub_id;
        $hubs = FinanceVendorCity::where('deleted_at', null)->get();
        $hub_name = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->first();

        $hub_title = Hub::where('id', $hub_id)->get();

        //Mark:- If user is logged in as a incharge, Let him Redirect to New Dashboard -- Daniyal Khan
        if($auth_user->userType == 'incharge'){

            //Mark:- This will fetch all the microhub belongs to logged in user.
//            $assigned_hub = MicroHubAssign::where('user_id', $auth_user->id)->pluck('hub_id')->toArray();

            $assigned_hub=MicroHubAssign::join('hubs','microhub_manager_assign.hub_id','=','hubs.id')
                ->where('user_id', $auth_user->id)
                ->get()->toArray();
            return redirect ('incharge/dashboard');
        }else{
            return backend_view('dashboard', compact('hub_name','hub_id','hubs','hubshow','statistics','hub_title') );
        }

    }

    public function statisticsFlagOrderListPieChartData(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


        $data = $request->all();

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));


        $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
        $start_dt->setTimeZone(new DateTimezone('UTC'));
        $start = $start_dt->format('Y-m-d H:i:s');

        $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
        $end_dt->setTimeZone(new DateTimezone('UTC'));
        $end = $end_dt->format('Y-m-d H:i:s');

        $statistics_id = $hub_id ;
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

    public function getDayOtd(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


        $input = $request->all();

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
        $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
        $sprint = new Sprint();

        $totalcount = 0;
        $totallates = 0;

        if (in_array('477260', $vendors)) {
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));


            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at','>',$start)->where('created_at','<',$end)
                ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                    \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
            if (!empty($query)) {
                foreach ($query as $record) {
                    if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                        if (!is_null($record->delivered_at) && $record->delivered_at > $date . " 21:00:00") {
                            $totallates++;
                        }
                    }
                    if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                        if (!is_null($record->returned_at)  && $record->returned_at > $date . " 21:00:00") {
                            $totallates++;
                        }
                    }
                    $totalcount++;
                }
            }
        }

        if (in_array('477282', $vendors)) {
            $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');


            $query = TorontoEntries::whereIn('creator_id', $data)->where('created_at','>',$start)->where('created_at','<',$end)
                ->where('is_custom_route', 0)->get(['task_status_id', \DB::raw("CONVERT_TZ(delivered_at,'UTC','America/Toronto') as delivered_at"),
                    \DB::raw("CONVERT_TZ(returned_at,'UTC','America/Toronto') as returned_at")]);
            if (!empty($query)) {
                foreach ($query as $record) {
                    if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                        if (!is_null($record->delivered_at) && $record->delivered_at > $date . " 21:00:00") {
                            $totallates++;
                        }
                    }
                    if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                        if (!is_null($record->returned_at) && $record->returned_at > $date . " 21:00:00") {
                            $totallates++;
                        }
                    }
                    $totalcount++;
                }
            }
        }

        if (count(array_intersect($ctcVendorIds, $vendors))> 0) {
            $ctc_ids = array_intersect($ctcVendorIds, $vendors);
            $ctc_date  = date('Y-m-d', strtotime($date . ' -1 days'));

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
                    if (in_array($record->task_status_id,$sprint->getStatusCodes('competed'))) {
                        if (!is_null($record->delivered_at) && $record->delivered_at > $date . " 21:00:00") {
                            $totallates++;
                        }
                    }
                    if (in_array($record->task_status_id,$sprint->getStatusCodes('return'))) {
                        if (!is_null($record->returned_at) && $record->returned_at > $date . " 21:00:00") {
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
        $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2) ,'ontime'=>  $totalcount - $totallates  , 'offtime'=> $totallates ];

        return $odt_data_1;
    }

    public function getWeekOtd(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


        $input = $request->all();

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
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

                $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at','>',$start)->where('created_at','<',$end)
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

                $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');


                $query = TorontoEntries:: whereIn('creator_id', $data)->where('created_at','>',$start)->where('created_at','<',$end)
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
        }

        if ($totalcount == 0)
        {
            $totalcount = 1;
        }
        $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2) ,'ontime'=>  $totalcount - $totallates  , 'offtime'=> $totallates ];


        return $odt_data_1;
    }

    public function getMonthOtd(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

        $input = $request->all();

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
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

                $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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
                $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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
                $ctc_date = date('Y-m-d', strtotime($range_date . ' -1 days'));
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
        }

        if ($totalcount == 0)
        {
            $totalcount = 1;
        }
        $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2) ,'ontime'=>  $totalcount - $totallates  , 'offtime'=> $totallates ];


        return $odt_data_1;
    }

    public function getYearOtd(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

        $input = $request->all();

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
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
                $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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
                $amazon_date = date('Y-m-d', strtotime($range_date . ' -1 days'));

                $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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
                $ctc_date = date('Y-m-d', strtotime($range_date . ' -1 days'));
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
        }
        if ($totalcount == 0)
        {
            $totalcount = 1;
        }
        $odt_data_1 = ['y1' => round(($totallates / $totalcount) * 100, 2), 'y2' => round(100 - (($totallates / $totalcount) * 100), 2) ,'ontime'=>  $totalcount - $totallates  , 'offtime'=> $totallates ];


        return $odt_data_1;
    }

    public function getAllCounts(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

        $input = $request->all();
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
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
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');


            $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
            $amazon = new TorontoEntries();
            $montreal_count = $amazon->getAmazonCountsForLoop($taskIds, 'all');

            dd($montreal_count);
        }

        if (in_array('477282', $vendors)) {
            $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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

    public function getInprogress(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

        $input = $request->all();
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
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
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');


            $taskIds = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                ->whereIn('creator_id', $data)->where('is_custom_route', 0)->pluck('task_id');
            $amazon = new TorontoEntries();
            $montreal_count = $amazon->getInprogressOrders($taskIds, 'all');
        }

        if (in_array('477282', $vendors)) {
            $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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

    public function getOrders(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

        $input = $request->all();
        $date =   $input['datepicker'];
        $hub_id =  base64_decode($user->hub_id);
        $type = base64_decode($input['type']);
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
        $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
        $sprint = new Sprint();
        $montreal =[];
        $ottawa = [];
        $ctc = [];
        $title = '';
        if ($type == 'return') {$title = 'Return Orders';}
        elseif ($type == 'hub_return'){$title = 'Hub Return Orders';}
        elseif($type == 'not_hub_return'){$title = 'Hub Not Return Orders';}
        elseif($type == 'custom'){$title = 'Custom Route Orders';}
        elseif($type == 'reattempted'){$title = 'Reattempted Orders';}
        else{$title = 'Not Scan';}
        if (in_array('477260', $vendors)) {
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            if ($type == 'return') {
                $montreal = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->whereIn('task_status_id', $sprint->getStatusCodes('return'))->get();
            }
            if ($type == 'hub_return') {
                $montreal = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->whereIn('task_status_id', $sprint->getStatusCodes('return'))
                    ->whereNotNull('hub_return_scan')->get();
            }
            if ($type == 'not_hub_return') {
                $montreal = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->whereIn('task_status_id', $sprint->getStatusCodes('return'))
                    ->whereNull('hub_return_scan')->get();
            }
            if ($type == 'not_scan'){
                $montreal = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->whereIn('task_status_id',[61,125,124])
                    ->whereNull('hub_return_scan')->get();
            }
            if ($type == 'reattempted'){
                $montreal = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->whereIn('task_status_id',[13])
                    ->whereNull('hub_return_scan')->get();
            }

            if ($type == 'custom'){
                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $montreal = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 1)
                    ->whereNull('hub_return_scan')->get();
            }
        }

        if (in_array('477282', $vendors)) {
            $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));


            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            if ($type == 'return') {
                $ottawa = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->whereIn('task_status_id', $sprint->getStatusCodes('return'))
                    ->get();
            }
            if ($type == 'hub_return') {
                $ottawa = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->whereIn('task_status_id', $sprint->getStatusCodes('return'))
                    ->whereNotNull('hub_return_scan')->get();
            }
            if ($type == 'not_hub_return') {
                $ottawa = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->whereIn('task_status_id', $sprint->getStatusCodes('return'))
                    ->whereNull('hub_return_scan')->get();
            }
            if ($type == 'not_scan'){
                $ottawa = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->whereIn('task_status_id',[61])
                    ->whereNull('hub_return_scan')->get();
            }
            if ($type == 'reattempted'){
                $ottawa = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 0)->whereIn('task_status_id',[13])
                    ->whereNull('hub_return_scan')->get();
            }
            if ($type == 'custom') {
                $start_dt = new DateTime($date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($date." 23:59:59", new DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');

                $ottawa = DB::table('ctc_entries')->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('creator_id', $data)->where('is_custom_route', 1)
                    ->whereNull('hub_return_scan')->get();
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


            if ($type == 'return') {
                $ctc = DB::table('ctc_entries')->where('is_custom_route', 0)->whereIn('creator_id', $ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('task_status_id', $sprint->getStatusCodes('return'))->get();
            }
            if ($type == 'hub_return') {
                $ctc = DB::table('ctc_entries')->where('is_custom_route', 0)->whereIn('creator_id', $ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('task_status_id', $sprint->getStatusCodes('return'))->whereNotNull('hub_return_scan')->get();
            }
            if ($type == 'not_hub_return') {
                $ctc = DB::table('ctc_entries')->where('is_custom_route', 0)->whereIn('creator_id', $ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('task_status_id', $sprint->getStatusCodes('return'))->whereNull('hub_return_scan')->get();
            }
            if ($type == 'not_scan') {
                $ctc = DB::table('ctc_entries')->where('is_custom_route', 0)->whereIn('creator_id', $ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('task_status_id', [61])->whereNull('hub_return_scan')->get();
            }
            if ($type == 'reattempted'){
                $ctc = DB::table('ctc_entries')->where('is_custom_route', 0)->whereIn('creator_id', $ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereIn('task_status_id', [13])->whereNull('hub_return_scan')->get();
            }
            if ($type == 'custom') {
                $ctc = DB::table('ctc_entries')->where('is_custom_route', 1)->whereIn('creator_id', $ctc_ids)->where('created_at','>',$start)->where('created_at','<',$end)
                    ->whereNull('hub_return_scan')->get();
            }
        }

        $result = array_merge( $montreal, $ottawa, $ctc );
        return backend_view('statistics.detail', compact('result','title'));
    }

    public function getFailedOrders(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

        $input = $request->all();
        $date =   $input['datepicker'];
        $hub_id =  base64_decode($user->hub_id);
        $type = base64_decode($input['type']);
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
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
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


        $input = $request->all();
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
        $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
        $failed_order = 0;
        $system_failed_order = 0;
        $not_in_system_failed_order = 0;

        if (in_array('477260', $vendors)) {
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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

    public function getCustomCounts(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

        $input = $request->all();
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
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

    public function getManualCounts(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


        $input = $request->all();
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
        $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
        $manual = 0;

        if (in_array('477260', $vendors)) {
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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

    public function getRouteDataCounts(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


        $input = $request->all();
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
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
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));
            //$montreal_routeIds = AmazonEnteries::where(DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), 'like', $amazon_date . "%")->where(['creator_id' => 477260])->pluck('route_id')->toArray();
            $montreal_routeIds = JoeyRoutes::where('hub',$user->hub_id)->where('date', 'like', $date . "%")->pluck('id')->toArray();
        }
        if (in_array('477282', $vendors)) {
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));
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

    public function getRouteDataDetail(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


        $input = $request->all();
        $type = base64_decode($input['type']);
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
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
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));
            //$montreal_routeIds = AmazonEnteries::where(DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), 'like', $amazon_date . "%")->where(['creator_id' => 477260])->pluck('route_id')->toArray();
            $montreal_routeIds = JoeyRoutes::where('hub',$user->hub_id)->where('date', 'like', $date . "%")->pluck('id')->toArray();
        }
        if (in_array('477282', $vendors)) {
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));
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

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


        $input = $request->all();
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
        $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
        $setting = AlertSystem::where('hub_id', $hub_id)->first();

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
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
            $end_dt->setTimeZone(new DateTimezone('UTC'));
            $end = $end_dt->format('Y-m-d H:i:s');

            $montreal_tracking_ids = TorontoEntries::whereIn('creator_id', $data)->where('created_at','>',$start)->where('created_at','<',$end)
                ->pluck('tracking_id')->toArray();
            $Custom_tracking_ids = CustomerRoutingTrackingId::whereIn('tracking_id',$montreal_tracking_ids)->pluck('tracking_id')->toArray();
            $montreal_sort_TrackingIds = array_diff($montreal_tracking_ids,$Custom_tracking_ids);

         }
        if (in_array('477282', $vendors)) {
            $amazon_date  = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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

    public function getTopTenJoeys(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


        $input = $request->all();
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
        $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
        $sprint = new Sprint();
        $joeyList =[];
        $joeyTrackingListList =[];

        if (in_array('477260', $vendors)) {
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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

    public function getLeastTenJoeys(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");


        $input = $request->all();
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
        $ctcVendorIds = CtcVendor::pluck('vendor_id')->toArray();
        $sprint = new Sprint();
        $joeyList =[];
        $joeyTrackingListList =[];

        if (in_array('477260', $vendors)) {


            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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
            $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

            $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
            $start_dt->setTimeZone(new DateTimezone('UTC'));
            $start = $start_dt->format('Y-m-d H:i:s');

            $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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

    public function getGraph(Request $request)
    {

        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

        $input = $request->all();
        $type = $input['type'];
        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
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

                $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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

                $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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

    public function getBroker(Request $request)
    {
        //User Data to get the logged in details...
        $user = Auth::user();

        //Getting vendors_id from HubStore on the bases of hub_id...
        $data = HubStore::where(['hub_id' => $user->hub_id])->pluck("vendor_id");

        $input = $request->all();

        $date = $request->get('datepicker') ? $request->get('datepicker') : date('Y-m-d');
        $hub_id = $user->hub_id;
        $vendors = FinanceVendorCityDetail::where('vendor_city_realtions_id', $hub_id)->pluck('vendors_id')->toArray();
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
                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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
                $amazon_date = date('Y-m-d', strtotime($date . ' -1 days'));

                $start_dt = new DateTime($amazon_date." 00:00:00", new DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new DateTime($amazon_date." 23:59:59", new DateTimezone('America/Toronto'));
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
        //dd($brookerList);
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
