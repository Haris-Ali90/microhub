<?php

namespace App\Http\Controllers\Backend;

use App\Classes\Fcm;
use App\Classes\JoyFlagLoginValidationsHandler;
use App\CustomerFlagCategories;
use App\CustomerFlagCategoryValues;
use App\CustomerIncidents;
use App\FinanceVendorCity;
use App\FlagHistory;
use App\Http\Traits\BasicModelFunctions;
use App\Joey;
use App\HubZones;
use App\JoeyRoutes;
use App\JoyFlagLoginValidations;
use App\UserDevice;
use App\UserNotification;
use Illuminate\Http\Request;
use App\JoeyPerformanceHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Auth;


class FlagOrdersController extends BackendController
{
    use BasicModelFunctions;

    /**
     * Get Flag Orders
     */
    public function FlagOrderList(Request $request)
    {
        $data = $request->all();
        //dd($data);
        $start_date = !empty($request->start_date) ? $request->start_date . " 00:00:00" : date("Y-m-d 00:00:00");
        $end_date = !empty($request->end_date) ? $request->end_date . " 23:59:59" : date("Y-m-d 23:59:59");
        $start_date_converted = ConvertTimeZone($start_date,'UTC','America/Toronto');
        $end_date_converted = ConvertTimeZone($end_date,'UTC','America/Toronto');
        $user = Auth::user();

        $statistics_id = implode(',',FinanceVendorCity::pluck('id')->toArray());

        $hubIds = HubZones:://whereIn('zone_id',DB::raw('select zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id = '.$data.') '))
        whereIn('zone_id', function ($query) use ($statistics_id) {
            $query->select(
                DB::raw('zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id in (' . $statistics_id . ')) ')
            );
        })
            ->pluck('hub_id')->toArray();

        //Getting all joeys flag marked
        $all_flag_joey = FlagHistory::where('unflaged_by', 0)
            ->whereIn('hub_id', $hubIds)
            ->groupBy('joey_id')
            ->whereBetween('created_at', [$start_date_converted, $end_date_converted])
            ->get();
        //Getting all flag marked orders
        $all_flag_mark = FlagHistory::where('unflaged_by', 0)
            ->whereIn('hub_id', $hubIds)
            ->whereBetween('created_at', [$start_date_converted, $end_date_converted])
            ->count();
        //Getting all flag marked orders
        $all_un_flag_mark = FlagHistory::where('unflaged_by', '>', 0)
            ->whereIn('hub_id', $hubIds)
            ->whereBetween('created_at', [$start_date_converted, $end_date_converted])
            ->count();
        //Getting all approved flagged orders
        $all_approved_flag = FlagHistory::where('is_approved', 1)
            ->whereIn('hub_id', $hubIds)
            ->whereBetween('created_at', [$start_date_converted, $end_date_converted])
            ->count();
        //Getting all un-approved flag orders
        $all_un_approved_flag = FlagHistory::where('unflaged_by', 0)
            ->whereIn('hub_id', $hubIds)
            ->where('is_approved', 0)
            ->whereBetween('created_at', [$start_date_converted, $end_date_converted])
            ->count();
        //Getting All Blocked Joeys By Flag
        $blocked_joeys_by_flag = JoyFlagLoginValidations::where('is_blocked', 1)
            ->whereNull('deleted_at')
            ->groupBy('joey_id')
            ->whereBetween('created_at', [$start_date_converted, $end_date_converted])
            ->get();

        $selectjoey = isset($data['joey']) ? $request->get('joey') : '';
        $all_joeys_accept_selected = Joey::where('is_enabled', '=', 1)->where('deleted_at', null)->limit(10)->get();

        return backend_view('flag-orders.index',
            compact(
                'all_joeys_accept_selected',
                'selectjoey',
                'all_flag_joey',
                'all_flag_mark',
                'all_approved_flag',
                'all_un_approved_flag',
                'all_un_flag_mark',
                'blocked_joeys_by_flag'
            )
        );
    }

    /**
     * @param Datatables $datatables
     * @param Request $request
     * @return mixed
     */
    public function FlagOrderListData(Datatables $datatables, Request $request)
    {
        $start_date = !empty($request->start_date) ? $request->start_date . " 00:00:00" : date("Y-m-d 00:00:00");
        $end_date = !empty($request->end_date) ? $request->end_date . " 23:59:59" : date("Y-m-d 23:59:59");
        $start_date_converted = ConvertTimeZone($start_date,'UTC','America/Toronto');
        $end_date_converted = ConvertTimeZone($end_date,'UTC','America/Toronto');

        $data = Auth::user();

        $statistics_id = implode(',',FinanceVendorCity::pluck('id')->toArray());

        $hubIds = HubZones:://whereIn('zone_id',DB::raw('select zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id = '.$data.') '))
        whereIn('zone_id', function ($query) use ($statistics_id) {
            $query->select(
                DB::raw('zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id in (' . $statistics_id . ')) ')
            );
        })
            ->pluck('hub_id')->toArray();

        $query = FlagHistory::whereNull('deleted_at')
            ->whereIn('hub_id', $hubIds)
            ->whereBetween('created_at', [$start_date_converted, $end_date_converted]);

        // filters
        if ($request->joeys != '') {
            $query->where('joey_id', $request->joeys);
        }

        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {

                return $record->id;
            })
            ->editColumn('joey_name', static function ($record) {

                return $record->joeyName->FullName;
            })
            ->editColumn('flag_by', static function ($record) {
                return $record->flagByName->full_name;
            })
            ->editColumn('created_at', static function ($record) {
                return ConvertTimeZone($record->created_at, 'UTC', 'America/Toronto');
            })

            ->editColumn('flagged_type', static function ($record) {
                if ($record->flagged_type == 'order') {
                    return 'On-Order';
                } else {
                    return 'On-Route';
                }
            })
            ->editColumn('joey_performance_status', static function ($record) {
                return backend_view('flag-orders.joey-performance-status-action', compact('record'));
            })
            ->addColumn('action', static function ($record) {
                return backend_view('flag-orders.action', compact('record'));
            })
            ->make(true);

    }

    public function FlagOrderListPieChartData(Request $request)
    {

        $start_date = !empty($request->start_date) ? $request->start_date . " 00:00:00" : date("Y-m-d 00:00:00");
        $end_date = !empty($request->end_date) ? $request->end_date . " 23:59:59" : date("Y-m-d 23:59:59");
        $start_date_converted = ConvertTimeZone($start_date,'UTC','America/Toronto');
        $end_date_converted = ConvertTimeZone($end_date,'UTC','America/Toronto');
        // creating return data


        $statistics_id = implode(',',FinanceVendorCity::pluck('id')->toArray());

        $hubIds = HubZones:://whereIn('zone_id',DB::raw('select zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id = '.$data.') '))
        whereIn('zone_id', function ($query) use ($statistics_id) {
            $query->select(
                DB::raw('zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id in (' . $statistics_id . ')) ')
            );
        })
            ->pluck('hub_id')->toArray();

        $return_data = [
            'legend' => [],
            'data' => [],
        ];
        // getting data
        $FlagHistoryData = FlagHistory::whereNull('deleted_at')
            ->whereBetween('created_at', [$start_date_converted, $end_date_converted])
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

    public function FlagOrderDetails($id)
    {
        $JoeyPerformanceHistory = JoeyPerformanceHistory::where('flag_history_ref_id', $id)->orderBy('id', 'DESC')->first();

        $AllFlagsOrderJoeys = $JoeyPerformanceHistory->where('joey_id', $JoeyPerformanceHistory->joey_id)->get();

        return backend_view('flag-orders.details', compact(
            'JoeyPerformanceHistory',
            'AllFlagsOrderJoeys'
        ));
    }

    /**
     * Get Approved Flag Orders
     */
    public function ApprovedFlagList()
    {

        $all_joeys_accept_selected = Joey::where('is_enabled', '=', 1)->where('deleted_at', null)->limit(10)->get();
        return backend_view('flag-orders.approved-list', compact('all_joeys_accept_selected'));

    }

    /**
     * Datatable Approved Order List
     */
    public function ApprovedFlagListData(Datatables $datatables, Request $request)
    {
        $start_date = !empty($request->start_date) ? $request->start_date . " 00:00:00" : date("Y-m-d 00:00:00");
        $end_date = !empty($request->end_date) ? $request->end_date . " 23:59:59" : date("Y-m-d 23:59:59");
        $start_date_converted = ConvertTimeZone($start_date,'UTC','America/Toronto');
        $end_date_converted = ConvertTimeZone($end_date,'UTC','America/Toronto');
        $data = Auth::user();

        $statistics_id = implode(',',FinanceVendorCity::pluck('id')->toArray());

        $hubIds = HubZones::whereIn('zone_id', function ($query) use ($statistics_id) {
            $query->select(
                DB::raw('zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id in (' . $statistics_id . ')) ')
            );
        })
            ->pluck('hub_id')->toArray();

        $query = FlagHistory::whereIn('hub_id', $hubIds)
            ->whereBetween('created_at', [$start_date_converted, $end_date_converted])
            ->where('is_approved', 1)
            ->whereNull('deleted_at');

        // filters
        if ($request->joeys != '') {
            $query->whereIn('joey_id', $request->joeys);
        }

        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('joey_name', static function ($record) {
                return $record->joeyName->FullName;
            })
            ->editColumn('flag_by', static function ($record) {
                return $record->flagByName->full_name;
            })
            ->editColumn('created_at', static function ($record) {
                return ConvertTimeZone($record->created_at, 'UTC', 'America/Toronto');
            })
            ->addColumn('action', static function ($record) {
                return backend_view('flag-orders.action', compact('record'));
            })
            ->make(true);

    }

    /**
     * Get Un-Approved Flag Orders
     */
    public function UnApprovedFlagList()
    {

        $all_joeys_accept_selected = Joey::where('is_enabled', '=', 1)->where('deleted_at', null)->limit(10)->get();
        return backend_view('flag-orders.un-approved-list', compact('all_joeys_accept_selected'));
    }

    /**
     * Datatable Un-Approved Order List
     */
    public function UnApprovedFlagListData(Datatables $datatables, Request $request)
    {
        $start_date = !empty($request->start_date) ? $request->start_date . " 00:00:00" : date("Y-m-d 00:00:00");
        $end_date = !empty($request->end_date) ? $request->end_date . " 23:59:59" : date("Y-m-d 23:59:59");
        $start_date_converted = ConvertTimeZone($start_date,'UTC','America/Toronto');
        $end_date_converted = ConvertTimeZone($end_date,'UTC','America/Toronto');
        $data = Auth::user();

        $statistics_id = implode(',',FinanceVendorCity::pluck('id')->toArray());

        $hubIds = HubZones::whereIn('zone_id', function ($query) use ($statistics_id) {
            $query->select(
                DB::raw('zone_id from zone_vendor_relationship where vendor_id in (select vendors_id from finance_vendor_city_relations_detail where vendor_city_realtions_id in (' . $statistics_id . ')) ')
            );
        })
            ->pluck('hub_id')->toArray();

        $query = FlagHistory::whereIn('hub_id', $hubIds)
            ->whereBetween('created_at', [$start_date_converted, $end_date_converted])
            ->where('is_approved', 0)
            ->whereNull('unflaged_date');

        // filters
        if ($request->joeys != '') {
            $query->whereIn('joey_id', $request->joeys);
        }

        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('joey_name', static function ($record) {
                return $record->joeyName->FullName;
            })
            ->editColumn('flag_by', static function ($record) {
                return $record->flagByName->full_name;
            })
            ->editColumn('created_at', static function ($record) {
                return ConvertTimeZone($record->created_at, 'UTC', 'America/Toronto');
            })
            ->editColumn('joey_performance_status', static function ($record) {
                return backend_view('flag-orders.joey-performance-status-action', compact('record'));
            })
            ->addColumn('action', static function ($record) {
                return backend_view('flag-orders.action', compact('record'));
            })
            ->make(true);

    }

    /**
     * Get Block Joeys By Flag
     */
    public function BlockJoeyFlagList(Request $request)
    {
        return backend_view('flag-orders.block-joey-flag-list');
    }

    /**
     *
     * block flag joeys list
     */
    public function BlockJoeyFlagListData(Datatables $datatables, Request $request)
    {
        $query = JoyFlagLoginValidations::orderby('created_at', 'asc')
            ->whereNull('deleted_at')
            ->get();
        $group_joeys_ids = [];
        foreach ($query as $data) {
            $group_joeys_ids[$data->joey_id] = $data->id;
        }

        $query = JoyFlagLoginValidations::join('joey_performance_history', 'joey_flag_login_validations.joey_performance_history_id', '=', 'joey_performance_history.id')
            ->select(['joey_flag_login_validations.*', 'joey_performance_history.incident_value_applied as incident_value', 'joey_performance_history.id as joey_performance_id'])
            ->whereIn('joey_flag_login_validations.id', $group_joeys_ids);

        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('joey_id', static function ($record) {
                return $record->joeyName->id;
            })
            ->editColumn('joey_name', static function ($record) {
                return $record->joeyName->FullName;
            })
            ->editColumn('joey_email', static function ($record) {
                return $record->joeyName->email;
            })
            ->editColumn('joey_phone', static function ($record) {
                return $record->joeyName->phone;
            })
            ->editColumn('created_at', static function ($record) {
                return ConvertTimeZone($record->created_at, 'UTC', 'America/Toronto');
            })
            ->editColumn('suspension_date', static function ($record) {
                // checking the date is exist
                if (!is_null($record->window_start) && !is_null($record->window_end)) {
                    return $record->window_start . ' to ' . $record->window_end;
                } else {
                    return 'Not defined';
                }

            })
            ->editColumn('incident_value', static function ($record) {
                return ucwords(str_replace("_", " ", $record->incident_value));
            })
            ->addColumn('action', static function ($record) {
                return backend_view('flag-orders.block-list-action', compact('record'));
            })
            ->make(true);
    }

    /**
     *
     * Blocked Joeys
     */
    public function UnblockJoeyFlag($id)
    {

        $current_date = date("Y-m-d H:i:s");
        $remove = JoyFlagLoginValidations::where('joey_id', $id)
            ->whereNull('deleted_at')
            ->update(["deleted_at" => $current_date]);
        return response()->json(['status' => true, 'message' => 'Joey unblock successfully']);

    }

    /**
     *
     * Approve joey performance status
     */
    public function JoeyPerformanceStatus($id)
    {
        $extra_message = '';
        $extra_info = [];
        $current_date = date('Y-m-d');
        $do_logout = "not_logout";
        DB::beginTransaction();
        try {
            $flag_data = FlagHistory::where('id', $id)->whereNull('unflaged_date')->first();

            if (is_null($flag_data)) {
                return response()->json(['status' => false, 'message' => 'Someone Already Un-flag ']);
            }
            $flag_data->is_approved = 1;
            $flag_data->save();


            $incident_count = JoeyPerformanceHistory::where('joey_id', $flag_data->joey_id)
                    ->where('flag_cat_id', $flag_data->flag_cat_id)
                    ->where('unflaged_by', '=', 0)
                    ->where(function ($query) use($current_date) {
                        $query->where('refresh_date', '>=', $current_date)
                            ->orWhereNull('refresh_date');
                    })
                    ->count() + 1;

            // flag cat incident value should applied
            $flag_incident_values = CustomerFlagCategoryValues::where('category_ref_id', $flag_data->flag_cat_id)->first()->toArray();


            // geting incident label
            $incident_label = '';
            $incident_label_finance = '';
            $rating_label = '';
            $incident_id = 1;

            //$refresh_date = $current_date;
            $refresh_date = $current_date;

            // checking the incident is on conclusion or not
            if ($incident_count < 4) // for incident value
            {

                $incident_id = $flag_incident_values['incident_' . $incident_count . '_ref_id'];

                $finance_incident_value = $flag_incident_values['finance_incident_' . $incident_count];
                $finance_incident_operator = $flag_incident_values['finance_incident_' . $incident_count . '_operator'];
                $incident_label = CustomerIncidents::where('id', $incident_id)->pluck('label')->first();
                $incident_label_finance = '{"value":"' . $finance_incident_value . '","operator":"' . $finance_incident_operator . '"}';
                $rating_value = $flag_incident_values['rating_' . $incident_count];
                $rating_operator = $flag_incident_values['rating_' . $incident_count . '_operator'];
                $rating_label = '{"value":"' . $rating_value . '","operator":"' . $rating_operator . '"}';
                $refresh_date = strval($flag_incident_values['refresh_rate_incident_' . $incident_count]);

            } elseif ($incident_count == 4) // for conclusion
            {
                $incident_id = $flag_incident_values['conclusion_ref_id'];
                $finance_incident_value = $flag_incident_values['finance_conclusion'];
                $finance_incident_operator = $flag_incident_values['finance_conclusion_operator'];
                $incident_label = CustomerIncidents::where('id', $incident_id)->pluck('label')->first();
                $incident_label_finance = '{"value":"' . $finance_incident_value . '","operator":"' . $finance_incident_operator . '"}';

                $rating_value = $flag_incident_values['rating_' . $incident_count];
                $rating_operator = $flag_incident_values['rating_' . $incident_count . '_operator'];
                $rating_label = '{"value":"' . $rating_value . '","operator":"' . $rating_operator . '"}';
                $refresh_date = strval($flag_incident_values['refresh_rate_conclusion']);
            } else // for termination
            {
                $incident_id = $flag_incident_values['conclusion_ref_id']; // this id for termination label
                $finance_incident_value = $flag_incident_values['finance_conclusion'];
                $finance_incident_operator = $flag_incident_values['finance_conclusion_operator'];
                $incident_label = CustomerIncidents::where('id', $incident_id)->pluck('label')->first();
                $incident_label_finance = '{"value":"' . $finance_incident_value . '","operator":"' . $finance_incident_operator . '"}';

                $rating_value = $flag_incident_values['rating_4'];
                $rating_operator = $flag_incident_values['rating_4_operator'];
                $rating_label = '{"value":"' . $rating_value . '","operator":"' . $rating_operator . '"}';
                $refresh_date = strval($flag_incident_values['refresh_rate_conclusion']);
            }

            // calculating refresh rate
            $RefreshRateValueLabels = JoeyPerformanceHistory::RefreshRateValueLabels;

            if (array_key_exists($refresh_date, $RefreshRateValueLabels)) // checking the value exist in labels
            {
                $refresh_date = date('Y-m-d', strtotime($current_date . $RefreshRateValueLabels[$refresh_date]));
            }
            else
            {
                $refresh_date = null;
            }

            $Joey_performance_history_data = JoeyPerformanceHistory::create([
                'flag_history_ref_id' => $id,
                'route_id' => $flag_data->route_id,
                'joey_id' => $flag_data->joey_id,
                'tracking_id' => $flag_data->tracking_id,
                'sprint_id' => $flag_data->sprint_id,
                'hub_id' => $flag_data->hub_id,
                'flag_cat_id' => $flag_data->flag_cat_id,
                'flag_cat_name' => $flag_data->flag_cat_name,
                'flaged_by' => $flag_data->flaged_by,
                'portal_type' => 'dashboard',
                'flagged_type' => $flag_data->flagged_type,
                'incident_value_applied' => $incident_label,
                'finance_incident_value_applied' => $incident_label_finance,
                'rating_value' => $rating_label,
                'refresh_date' => $refresh_date
            ]);

            //checking logout condition push
            if ($incident_label != 'warning') {
                $do_logout = "logout";
            }
            //Getting joeys details to send notification
            $joey_data = Joey::where('id', '=', $flag_data->joey_id)
                ->first();
            if ($joey_data == null) {
                return response()->json(['status' => false, 'message' => 'This order has no joey for flag']);
            }
            //base64 convert
            $email = base64_encode($joey_data->email);

            $joey_flag = ["route_id" => $flag_data->route_id, "sprint_no" => $flag_data->sprint_id, "flag_name" => $flag_data->flag_cat_name];
            //Sen mail to joey on assign flag

            if (empty($joey_flag['sprint_no'])) {
                $message = 'You are receiving this notification because Joeyco take action on you against this route number "' . $joey_flag['route_id'] . '" and marked flaged ' . $joey_flag['flag_name'];
            } else {
                $message = 'You are receiving this notification because Joeyco take action on you against this order number "' . $joey_flag['sprint_no'] . '" and marked flaged ' . $joey_flag['flag_name'];
            }
            //Checking condition phone num exist or not



            // set login validation
            $login_validation = new JoyFlagLoginValidationsHandler();
            $login_validation->setValues($flag_data->joey_id, $incident_id, $Joey_performance_history_data->id);
            $login_validation->applyAction();

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Joey flag approved successfully ' . $extra_message . ' !', 'extra_info' => $extra_info]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Something went wrong ']);
        }

    }

    //Create Flag
    public function createFlag($flag_cat_id, Request $request)
    {

        DB::beginTransaction();
        try {
            //check if route already flag or not
            $route_flag = FlagHistory::where('route_id', $request->route_id)->where('flagged_type', 'route')->whereNull('unflaged_date')->first();

            if (isset($route_flag)) {
                return response()->json(['status' => false, 'message' => 'This route is already flagged']);
            }

            // getting category data
            $flag_category = CustomerFlagCategories::where('id', $flag_cat_id)->first();

            // checking joey id is exist
            $joey_id = $request->joey_id;
            if ($joey_id <= 0) {
                $joey_id = JoeyRoutes::where('id', $request->route_id)
                    ->latest()
                    ->first();
                // checking the route is exist
                if (is_null($joey_id)) {
                    return response()->json(['status' => false, 'message' => 'Route is not assigned ']);
                }
                // setting joey_id
                $joey_id = $joey_id->joey_id;
            }
            //check joey exits on this route or not
            if (is_null($joey_id)) {
                return response()->json(['status' => false, 'message' => 'Joey has not assigned in this route , you are not able to mark a flag.']);
            }
            //Mark Flag Against Joey
            $Joey_flag_history_data = FlagHistory::create([
                'joey_id' => $joey_id,
                'route_id' => $request->route_id,
                'tracking_id' => $request->tracking_id,
                'sprint_id' => $request->sprint,
                'hub_id' => $request->hub_id,
                'flag_cat_id' => $flag_category->id,
                'flag_cat_name' => $flag_category->category_name,
                'flaged_by' => Auth::user()->id,
                'flagged_type' => $request->flag_type,
                'portal_type' => 'dashboard',
            ]);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'This ' . $request->flag_type . ' flagged successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Something went wrong ']);
        }
    }

    //un-flag order
    public function unFlag($unFlag_id)
    {

        $unflag = FlagHistory::where('id', $unFlag_id)->first();

        if (is_null($unflag)) {
            return redirect()->back()
                ->with('error', 'The data dose not  exist');

        } elseif ($unflag->is_approved == 1) {
            return redirect()->back()
                ->with('error', 'This flag already approved');
        }

        //Getting joeys details to send notification
        $joey_data = Joey::where('id', '=', $unflag->joey_id)
            ->first();

        //checking condition data exist or not
        if (is_null($unflag)) {
            return redirect()->back()
                ->with('alert-danger', 'The id does`nt exist');
        }

        //Update Sprint For Return Order
        $unflag->unflaged_by = Auth::user()->id;
        $unflag->unflaged_date = date('Y-m-d H:i:s');
        $unflag->save();

        //base64 convert email
        $email = base64_encode($joey_data->email);

        //getting flag details
        $joey_flag = ["route_id" => $unflag->route_id, "sprint_no" => $unflag->sprint_id, "flag_name" => $unflag->flag_cat_name];


        //Mail send to joeys on un-flag

        if (isset($joey_data->id)) {
            if (empty($joey_flag['sprint_no'])) {
                $message = 'You are receiving this notification because Joeyco remove flag "' . $joey_flag['flag_name'] . '" on you against this route number "' . $joey_flag['route_id'] . '".';
            } else {
                $message = 'You are receiving this notification because Joeyco remove flag "' . $joey_flag['flag_name'] . '" on you against this order number "' . $joey_flag['sprint_no'] . '".';
            }
            $deviceIds = UserDevice::where('user_id', $joey_data->id)->where('is_deleted_at', 0)->pluck('device_token');
            $subject = 'Hi ' . $joey_data->first_name . ' ' . $joey_data->last_name;
            $payload = ['notification' => ['title' => $subject, 'body' => $message, 'click_action' => 'itinerary'],
                'data' => ['data_title' => $subject, 'data_body' => $message, 'data_click_action' => 'itinerary']];
            $createNotification = [
                'user_id' => $joey_data->id,
                'user_type' => 'Joey',
                'notification' => $subject,
                'notification_type' => 'itinerary',
                'notification_data' => json_encode(["body" => $message]),
                'payload' => json_encode($payload),
                'is_silent' => 0,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            UserNotification::create($createNotification);
        }


        return redirect()->back()
            ->with('alert-success', 'This ' . $unflag->flagged_type . ' is un-flag successfully');

    }
}
