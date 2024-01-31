<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Backend\StoreImageRequest;
use App\Joey;
use Illuminate\Support\Facades\Auth;
use App\Classes\Fcm;
use App\Claim;
use App\Sprint;
use App\JoeyRoute;
use App\BrookerJoey;
use App\MerchantIds;
use App\UserDevice;
use App\ClaimReason;
use App\TaskHistory;
use Illuminate\Http\Request;
use App\ManifestField;
use App\SprintReattempt;
use App\UserNotification;
use Yajra\Datatables\Datatables;
use App\JoeyRouteLocations;
use Illuminate\Http\JsonResponse;
use App\SprintConfirmation;

class JoeycoClaimController extends BackendController
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
        "145" => 'Returned To Merchant',
        "146" => "Delivery Missorted, Incorrect Address",
        '153' => 'Miss sorted to be reattempt',
        '154' => 'Joey unable to complete the route', '155' => 'To be re-attempted tomorrow');
    public function index()
    {
    }

    public function show()
    {
    }

    public function create()
    {
        return backend_view('joeyco-claim.createform');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'tracking_id' => "required|array",
            'amount' => "required|array",
            'claim_on' => 'required'
        ], [
                'claim_on.required' => 'Type is required',
            ]
        );
        $claimdate = [];
        $data = $request->all();
        $count = 0;
        foreach ($data['tracking_id'] as $tracking_id) {
            $claim = Claim::where('tracking_id', $tracking_id)->where('type', $data['claim_on'])->first();
            if (!empty($claim)) {
                Claim::where('id', $claim->id)->update(['amount' => $data['amount'][$count] ?? 0]);
            } else {
                $claimdate[$count] = [
                    'user_id' => Auth::user()->id,
                    'tracking_id' => $tracking_id ?? '',
                    'amount' => $data['amount'][$count] ?? null,
                    'task_id' => $data['task_id'][$count] ?? null,
                    'vendor_id' => $data['vendor_id'][$count] ?? null,
                    'sprint_id' => $data['sprint_id'][$count] ?? null,
                    'sprint_status_id' => $data['sprint_status_id'][$count] ?? null,
                    'route_id' => null,
                    'joey_id' => null,
                    'brooker_id' => null,
                    'type' => $data['claim_on'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'ordinal' => null,
                    'image' => ''
                ];

                $claimdate[$count]['task_id'] = ($data['task_id'][$count] == null || $data['task_id'][$count] === "undefined") ? null : $data['task_id'][$count];
                $claimdate[$count]['vendor_id'] = ($data['vendor_id'][$count] == null || $data['vendor_id'][$count] === "undefined") ? null : $data['vendor_id'][$count];
                $claimdate[$count]['sprint_id'] = ($data['sprint_id'][$count] == null || $data['sprint_id'][$count] === "undefined") ? null : $data['sprint_id'][$count];
                $claimdate[$count]['sprint_status_id'] = ($data['sprint_status_id'][$count] == null || $data['sprint_status_id'][$count] === "undefined") ? null : $data['sprint_status_id'][$count];

                if ($data['task_id'][$count] != null) {
                    $image = SprintConfirmation::where('task_id', '=', $data['task_id'][$count])->whereNotNull('attachment_path')->orderBy('id', 'desc')->first();
                    if ($image) {
                        $claimdate[$count]['image'] = $image->attachment_path;
                    }
                }

                if ($data['sprint_id'][$count] != null) {
                    $sprint = Sprint::where('id', $data['sprint_id'][$count])->whereNull('deleted_at')->first();
                    if (!empty($sprint)) {
                        if ($sprint->in_hub_route != null) {
                            $joeyRouteLocation = JoeyRouteLocations::where('task_id', $data['task_id'][$count])->whereNull('deleted_at')->first();
                            if ($joeyRouteLocation) {
                                $claimdate[$count]['ordinal'] = $joeyRouteLocation->ordinal ?? null;
                                $joeyRoute = JoeyRoute::where('id', $joeyRouteLocation->route_id)->whereNull('deleted_at')->first();
                                if ($joeyRoute) {
                                    $claimdate[$count]['route_id'] = $joeyRoute->id ?? null;
                                    $claimdate[$count]['joey_id'] = $joeyRoute->joey_id ?? null;
                                    if ($joeyRoute->joey_id) {
                                        $brokerJoey = BrookerJoey::where('joey_id', $joeyRoute->joey_id)->first();
                                        if ($brokerJoey) {
                                            $claimdate[$count]['brooker_id'] = $brokerJoey->brooker_id ?? null;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }
            $count++;
        }

        if (count($claimdate) > 0) {
            $claimdate = array_values($claimdate);
            Claim::insert($claimdate);
        }
        return redirect()
            ->route('claims.pendingList')
            ->with('success', 'Claim saved successfully!');
    }

    public function destroy($id)
    {
        $ids = explode(',', $id);
        Claim::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success', 'Claim deleted successfully!');
    }

    public function validateTrackingId(Request $request)
    {
        $tracking_ids = trim($request->input('id'));
        $type = trim($request->input('type'));
        $return = [];


        if (strpos($tracking_ids, ',') !== false) {
            $id = explode(",", $tracking_ids);
        } else {
            $id = explode("\n", $tracking_ids);
        }
        $i = 0;
        $ids = [];
        foreach ($id as $trackingid) {
            if (!empty(trim($trackingid))) {
                $pattern = "/^[a-zA-Z0-9@#$&*_-]*/i";
                preg_match($pattern, trim($trackingid), $matche);
                $ids[$i] = $matche[0];
                $i++;
            }

        }
        $ids = array_unique($ids);
        $delivered_and_return_status = [17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 131, 135, 136, 140];
        $amazon_vendors = [477260, 477282];
        $count = 0;
        foreach ($ids as $trackingids) {
            if ($type == 'tracking_id') {
                $merchantid = MerchantIds::where('tracking_id', $trackingids)->whereNull('deleted_at')->first();
            } elseif ($type == 'merchant_order_num') {
                $merchantid = MerchantIds::where('merchant_order_num', $trackingids)->whereNull('deleted_at')->first();
            }
            $return[$count]['tracking_id'] = $trackingids;
            $return[$count]['is_valid'] = (empty($merchantid)) ? 0 : 1;
            $return[$count]['amount'] = 0;
            $return[$count]['msg'] = '';
            $return[$count]['is_required'] = 0;
            $return[$count]['amount_readonly'] = '';
            if ($return[$count]['is_valid'] == 1) {
                $claim = Claim::where('tracking_id', $trackingids)->where('type', $type)->first();

                if ($merchantid) {
                    if ($merchantid->sprintTaskDetail) {
                        $return[$count]['task_id'] = $merchantid->sprintTaskDetail->id ?? '';
                        if ($merchantid->sprintTaskDetail->sprintDetail) {
                            $return[$count]['vendor_id'] = $merchantid->sprintTaskDetail->sprintDetail->creator_id ?? '';
                            $return[$count]['sprint_id'] = $merchantid->sprintTaskDetail->sprintDetail->id ?? '';
                            $return[$count]['sprint_status_id'] = $merchantid->sprintTaskDetail->sprintDetail->status_id ?? '';
                            if (in_array($merchantid->sprintTaskDetail->sprintDetail->status_id, $delivered_and_return_status)) {
                                $return[$count]['is_required'] = 1;
                            }
                            if ($return[$count]['vendor_id'] != '') {
                                if (in_array($return[$count]['vendor_id'], $amazon_vendors)) {
                                    if ($return[$count]['sprint_id'] != '') {
                                        $manifestField = ManifestField::where('sprint_id', $return[$count]['sprint_id'])->whereNull('deleted_at')->first();
                                        if (!empty($manifestField)) {
                                            // $return[$count]['amount']= ($manifestField->column_name==null || $manifestField->column_name=='' )?0:$manifestField->column_name;
                                            $return[$count]['amount'] = 0;
                                            if ($return[$count]['amount'] > 0) {
                                                $return[$count]['amount_readonly'] = 'readonly';
                                            }

                                        }
                                    }


                                }
                            }
                        }
                    }
                }
                if (!empty($claim)) {
                    $return[$count]['amount'] = $claim->amount;
                    // $return[$count]['msg']='Already exist in system';
                    if ($claim->status == 0) {//pending
                        $return[$count]['msg'] = 'Pending';
                    } elseif ($claim->status == 1) {//approved
                        $return[$count]['msg'] = 'Approved';
                        $return[$count]['amount_readonly'] = 'readonly';
                    } elseif ($claim->status == 2) {//not approved
                        $return[$count]['msg'] = 'Not Approved';
                        $return[$count]['amount_readonly'] = 'readonly';
                    } elseif ($claim->status == 3) { //re-submited
                        $return[$count]['msg'] = 'Re-Submitted';
                    }

                }
            }

            $count++;
        }

        return $return;

    }

    public function pendingList(Request $request)
    {
        $input = $request->all();
        $start = isset($input['datepicker1']) ? $input['datepicker1'] : date('Y-m-d');
        $end = isset($input['datepicker2']) ? $input['datepicker2'] : date('Y-m-d');
        $start = ConvertTimeZone($start . ' 00:00:00', 'America/Toronto', 'UTC');
        $end = ConvertTimeZone($end . ' 23:59:59', 'America/Toronto', 'UTC');
        $query = Claim::where('status', 0)->whereBetween('created_at', [$start, $end])->get();
        $filterVendor = isset($input['vendor_id']) ? $input['vendor_id'] : "";
        $filterBroker = isset($input['brooker_id']) ? $input['brooker_id'] : "";
        $filterSprintStatus = isset($input['sprint_status_id']) ? $input['sprint_status_id'] : "";
        $filterJoey = isset($input['joey_id']) ? $input['joey_id'] : "";
        $filterpos = isset($input['pos']) ? $input['pos'] : "";
        $vendors = [];
        $brokers = [];
        $sprint_statuses = [];
        $joeys = [];
        if (count($query) > 0) {
            foreach ($query as $query_key => $query_value) {
                if ($query_value->joey_id != null) {
                    $joeys[$query_value->joey_id] = (!empty($query_value->joey)) ? $query_value->joey->first_name . ' ' . $query_value->joey->last_name . " (" . $query_value->joey_id . ")" : '';
                }
                if ($query_value->vendor_id != null) {
                    $vendors[$query_value->vendor_id] = (!empty($query_value->vendor)) ? $query_value->vendor->name . " (" . $query_value->vendor_id . ")" : '';
                }
                if ($query_value->sprint_status_id != null) {
                    $sprint_statuses[$query_value->sprint_status_id] = self::$status[$query_value->sprint_status_id] ?? '';
                    // $record->sprint_status_id?self::$status[$record->sprint_status_id]:'';
                }
                if ($query_value->brooker_id != null) {
                    $brokers[$query_value->brooker_id] = (!empty($query_value->brookersUsers)) ? $query_value->brookersUsers->name . " (" . $query_value->brooker_id . ")" : '';
                }
            }
        }

        return backend_view('joeyco-claim.pending-listing', compact('joeys', 'vendors', 'sprint_statuses', 'brokers', 'filterpos', 'filterVendor', 'filterBroker', 'filterSprintStatus', 'filterJoey'));
    }

    public function pendingListData(DataTables $datatables, Request $request): JsonResponse
    {
        $input = $request->all();
        $reasonsList = ClaimReason::where('slug', 'Pending')->get();
        $start = ConvertTimeZone($input['datepicker1'] . ' 00:00:00', 'America/Toronto', 'UTC');
        $end = ConvertTimeZone($input['datepicker2'] . ' 23:59:59', 'America/Toronto', 'UTC');


        $query = Claim::where('status', 0)->whereBetween('created_at', [$start, $end]);
        if (!empty($input['vendor_id'])) {
            $query = $query->where('vendor_id', $input['vendor_id']);
        }
        if (!empty($input['brooker_id'])) {
            $query = $query->where('brooker_id', $input['brooker_id']);
        }
        if (!empty($input['sprint_status_id'])) {
            $query = $query->where('sprint_status_id', $input['sprint_status_id']);
        }

        if (isset($input['pos'])) {
            // dd($input['pos']);
            if ($input['pos'] != '' && ($input['pos'] == 1 || $input['pos'] == 0)) {
                if ($input['pos'] == 1) {
                    $query = $query->whereNotNull('image');
                } else {
                    $query = $query->whereNull('image');
                }
            }
        }
        if (!empty($input['joey_id'])) {
            $query = $query->where('joey_id', $input['joey_id']);
        }
        return $datatables->eloquent($query)
            ->setRowId(static function ($record) use ($reasonsList) {

                return $record->id;
            })
            ->addColumn('checkbox', static function ($record) {

                return backend_view('joeyco-claim.checkbox', compact('record'));
            })
            ->editColumn('tracking_id', static function ($record) {
                return $record->tracking_id;
            })
            ->addColumn('vendors', static function ($record) {
                // return $record->vendors->name." (".$record->vendors->id.")";
                if ($record->vendor) {
                    return $record->vendor->name . " (" . $record->vendor->id . ")";
                } else {
                    return "";
                }
            })
            ->editColumn('status', static function ($record) {
                return backend_view('joeyco-claim.status', compact('record'));
            })
            ->editColumn('reason', static function ($record) use ($reasonsList) {
                return backend_view('joeyco-claim.reason', compact('record', 'reasonsList'));
            })
            ->editColumn('sprint_status_id', static function ($record) {
                return $record->sprint_status_id ? self::$status[$record->sprint_status_id] : '';
            })
            ->editColumn('brookersUsers', static function ($record) {
                return $record->brookersUsers ? $record->brookersUsers->name . ' (' . $record->brooker_id . ')' : '';
            })
            ->editColumn('joey_id', static function ($record) {
                if ($record->joey_id != null) {
                    return $record->joey_id ? $record->joey_id : '';
                } else {
                    $joeyRouteLocation = JoeyRouteLocations::where('task_id', $record->task_id)->whereNull('deleted_at')->first();
                    if ($joeyRouteLocation) {
                        $joeyRoute = JoeyRoute::where('id', $joeyRouteLocation->route_id)->whereNull('deleted_at')->first();
                        if ($joeyRoute) {
                            return $joeyRoute->joey_id ?? null;
                        }
                    }
                }
            })
            ->editColumn('joey', static function ($record) {
                if ($record->joey_id != null) {
                    return $record->joey ? $record->joey->first_name . ' ' . $record->joey->last_name : '';
                } else {
                    $joeyRouteLocation = JoeyRouteLocations::where('task_id', $record->task_id)->whereNull('deleted_at')->first();
                    if ($joeyRouteLocation) {
                        $joeyRoute = JoeyRoute::where('id', $joeyRouteLocation->route_id)->whereNull('deleted_at')->first();
                        if ($joeyRoute) {
                            if ($joeyRoute->joey_id) {
                                $joey = Joey::where('id', $joeyRoute->joey_id)->first();
                                return $joey->first_name . ' ' . $joey->last_name;
                            }
                        }
                    }
                }

            })
            ->editColumn('image', static function ($record) {
                return backend_view('joeyco-claim.image', compact('record'));
            })
            ->editColumn('amount', static function ($record) {
                return ($record->amount != null) ? "$" . $record->amount : "$0";
            })
            ->addColumn('route_ordinal', static function ($record) {
                $r = '';
                //    return "R-".$record->route_id.'-'.$record->ordinal;
                if ($record->route_id != null) {
                    $r .= "R-" . $record->route_id;
                    if ($record->ordinal != null) {
                        $r .= '-' . $record->ordinal;
                    }
                } else {
                    $joeyRouteLocation = JoeyRouteLocations::where('task_id', $record->task_id)->whereNull('deleted_at')->first();
                    if ($joeyRouteLocation) {
                        $r = $joeyRouteLocation->route_id . '-' . $joeyRouteLocation->ordinal;
                    }
                }

                return $r;
            })
            ->addColumn('action', static function ($record) {
                return backend_view('joeyco-claim.status', compact('record'));
            })
            //->rawColumns(['tracking_id', 'vendors'])
            ->make(true);
    }

    public function approvedList(Request $request)
    {
        $input = $request->all();
        $start = isset($input['datepicker1']) ? $input['datepicker1'] : date('Y-m-d');
        $end = isset($input['datepicker2']) ? $input['datepicker2'] : date('Y-m-d');
        $start = ConvertTimeZone($start . ' 00:00:00', 'America/Toronto', 'UTC');
        $end = ConvertTimeZone($end . ' 23:59:59', 'America/Toronto', 'UTC');
        $query = Claim::where('status', 1)->whereBetween('created_at', [$start, $end])->get();
        $filterVendor = isset($input['vendor_id']) ? $input['vendor_id'] : "";
        $filterBroker = isset($input['brooker_id']) ? $input['brooker_id'] : "";
        $filterSprintStatus = isset($input['sprint_status_id']) ? $input['sprint_status_id'] : "";
        $filterJoey = isset($input['joey_id']) ? $input['joey_id'] : "";
        $filterpos = isset($input['pos']) ? $input['pos'] : "";
        $vendors = [];
        $brokers = [];
        $sprint_statuses = [];
        $joeys = [];
        if (count($query) > 0) {
            foreach ($query as $query_key => $query_value) {
                if ($query_value->joey_id != null) {
                    $joeys[$query_value->joey_id] = (!empty($query_value->joey)) ? $query_value->joey->first_name . ' ' . $query_value->joey->last_name . " (" . $query_value->joey_id . ")" : '';
                }
                if ($query_value->vendor_id != null) {
                    $vendors[$query_value->vendor_id] = (!empty($query_value->vendor)) ? $query_value->vendor->name . " (" . $query_value->vendor_id . ")" : '';
                }
                if ($query_value->sprint_status_id != null) {
                    $sprint_statuses[$query_value->sprint_status_id] = self::$status[$query_value->sprint_status_id] ?? '';
                    // $record->sprint_status_id?self::$status[$record->sprint_status_id]:'';
                }
                if ($query_value->brooker_id != null) {
                    $brokers[$query_value->brooker_id] = (!empty($query_value->brookersUsers)) ? $query_value->brookersUsers->name . " (" . $query_value->brooker_id . ")" : '';
                }
            }
        }
        return backend_view('joeyco-claim.approved-listing', compact('joeys', 'vendors', 'sprint_statuses', 'brokers', 'filterpos', 'filterVendor', 'filterBroker', 'filterSprintStatus', 'filterJoey'));
    }

    public function approvedListData(DataTables $datatables, Request $request): JsonResponse
    {
        $input = $request->all();
        $reasonsList = ClaimReason::where('slug', 'Approved')->get();
        $start = ConvertTimeZone($input['datepicker1'] . ' 00:00:00', 'America/Toronto', 'UTC');
        $end = ConvertTimeZone($input['datepicker2'] . ' 23:59:59', 'America/Toronto', 'UTC');

        $query = Claim::where('status', 1)->whereBetween('created_at', [$start, $end]);
        if (!empty($input['vendor_id'])) {
            $query = $query->where('vendor_id', $input['vendor_id']);
        }
        if (!empty($input['brooker_id'])) {
            $query = $query->where('brooker_id', $input['brooker_id']);
        }
        if (!empty($input['sprint_status_id'])) {
            $query = $query->where('sprint_status_id', $input['sprint_status_id']);
        }
        if (isset($input['pos'])) {
            // dd($input['pos']);
            if ($input['pos'] != '' && ($input['pos'] == 1 || $input['pos'] == 0)) {
                if ($input['pos'] == 1) {
                    $query = $query->whereNotNull('image');
                } else {
                    $query = $query->whereNull('image');
                }
            }
        }
        if (!empty($input['joey_id'])) {
            $query = $query->where('joey_id', $input['joey_id']);
        }
        return $datatables->eloquent($query)
            ->setRowId(static function ($record) use ($reasonsList) {

                return $record->id;
            })
            ->editColumn('tracking_id', static function ($record) {
                return $record->tracking_id;
            })
            ->addColumn('vendors', static function ($record) {
                // return $record->vendors->name." (".$record->vendors->id.")";
                if ($record->vendors) {
                    return $record->vendors->name . " (" . $record->vendors->id . ")";
                } else {
                    return "";
                }
            })
            ->editColumn('status', static function ($record) {
                return backend_view('joeyco-claim.status', compact('record'));
            })
            ->editColumn('reason', static function ($record) use ($reasonsList) {
                // return backend_view('joeyco-claim.reason', compact('record','reasonsList') );
                return ($record->getReason) ? $record->getReason->title : '';
            })
            ->editColumn('sprint_status_id', static function ($record) {
                return $record->sprint_status_id ? self::$status[$record->sprint_status_id] : '';
            })
            ->editColumn('brookersUsers', static function ($record) {
                return $record->brookersUsers ? $record->brookersUsers->name . ' (' . $record->brooker_id . ')' : '';
            })
            ->editColumn('joey_id', static function ($record) {
                if ($record->joey_id != null) {
                    return $record->joey_id ? $record->joey_id : '';
                } else {
                    $joeyRouteLocation = JoeyRouteLocations::where('task_id', $record->task_id)->whereNull('deleted_at')->first();
                    if ($joeyRouteLocation) {
                        $joeyRoute = JoeyRoute::where('id', $joeyRouteLocation->route_id)->whereNull('deleted_at')->first();
                        if ($joeyRoute) {
                            return $joeyRoute->joey_id ?? null;
                        }
                    }
                }
            })
            ->editColumn('joey', static function ($record) {
                if ($record->joey_id != null) {
                    return $record->joey ? $record->joey->first_name . ' ' . $record->joey->last_name : '';
                } else {
                    $joeyRouteLocation = JoeyRouteLocations::where('task_id', $record->task_id)->whereNull('deleted_at')->first();
                    if ($joeyRouteLocation) {
                        $joeyRoute = JoeyRoute::where('id', $joeyRouteLocation->route_id)->whereNull('deleted_at')->first();
                        if ($joeyRoute) {
                            if ($joeyRoute->joey_id) {
                                $joey = Joey::where('id', $joeyRoute->joey_id)->first();
                                return $joey->first_name . ' ' . $joey->last_name;
                            }
                        }
                    }
                }

            })
            ->editColumn('image', static function ($record) {
                return backend_view('joeyco-claim.image', compact('record'));
            })
            ->editColumn('amount', static function ($record) {
                return ($record->amount != null) ? "$" . $record->amount : "$0";
            })
            ->addColumn('route_ordinal', static function ($record) {
                $r = '';
                //    return "R-".$record->route_id.'-'.$record->ordinal;
                if ($record->route_id != null) {
                    $r .= "R-" . $record->route_id;
                    if ($record->ordinal != null) {
                        $r .= '-' . $record->ordinal;
                    }
                } else {
                    $joeyRouteLocation = JoeyRouteLocations::where('task_id', $record->task_id)->whereNull('deleted_at')->first();
                    if ($joeyRouteLocation) {
                        $r = $joeyRouteLocation->route_id . '-' . $joeyRouteLocation->ordinal;
                    }
                }

                return $r;
            })
            ->addColumn('action', static function ($record) {
                return backend_view('joeyco-claim.status', compact('record'));
            })
            ->make(true);
    }

    public function notApprovedList(Request $request)
    {
        $input = $request->all();
        $start = isset($input['datepicker1']) ? $input['datepicker1'] : date('Y-m-d');
        $end = isset($input['datepicker2']) ? $input['datepicker2'] : date('Y-m-d');
        $start = ConvertTimeZone($start . ' 00:00:00', 'America/Toronto', 'UTC');
        $end = ConvertTimeZone($end . ' 23:59:59', 'America/Toronto', 'UTC');
        $query = Claim::where('status', 2)->whereBetween('created_at', [$start, $end])->get();
        $filterVendor = isset($input['vendor_id']) ? $input['vendor_id'] : "";
        $filterBroker = isset($input['brooker_id']) ? $input['brooker_id'] : "";
        $filterSprintStatus = isset($input['sprint_status_id']) ? $input['sprint_status_id'] : "";
        $filterJoey = isset($input['joey_id']) ? $input['joey_id'] : "";
        $filterpos = isset($input['pos']) ? $input['pos'] : "";
        $vendors = [];
        $brokers = [];
        $sprint_statuses = [];
        $joeys = [];
        if (count($query) > 0) {
            foreach ($query as $query_key => $query_value) {
                if ($query_value->joey_id != null) {
                    $joeys[$query_value->joey_id] = (!empty($query_value->joey)) ? $query_value->joey->first_name . ' ' . $query_value->joey->last_name . " (" . $query_value->joey_id . ")" : '';
                }
                if ($query_value->vendor_id != null) {
                    $vendors[$query_value->vendor_id] = (!empty($query_value->vendor)) ? $query_value->vendor->name . " (" . $query_value->vendor_id . ")" : '';
                }
                if ($query_value->sprint_status_id != null) {
                    $sprint_statuses[$query_value->sprint_status_id] = self::$status[$query_value->sprint_status_id] ?? '';
                }
                if ($query_value->brooker_id != null) {
                    $brokers[$query_value->brooker_id] = (!empty($query_value->brookersUsers)) ? $query_value->brookersUsers->name . " (" . $query_value->brooker_id . ")" : '';
                }
            }
        }
        return backend_view('joeyco-claim.not-approved-listing', compact('joeys', 'vendors', 'sprint_statuses', 'brokers', 'filterpos', 'filterVendor', 'filterBroker', 'filterSprintStatus', 'filterJoey'));
    }

    public function notApprovedListData(DataTables $datatables, Request $request): JsonResponse
    {
        $input = $request->all();
        $reasonsList = ClaimReason::where('slug', 'Not Approved')->get();
        $start = ConvertTimeZone($input['datepicker1'] . ' 00:00:00', 'America/Toronto', 'UTC');
        $end = ConvertTimeZone($input['datepicker2'] . ' 23:59:59', 'America/Toronto', 'UTC');

        $query = Claim::where('status', 2)->whereBetween('created_at', [$start, $end]);
        if (!empty($input['vendor_id'])) {
            $query = $query->where('vendor_id', $input['vendor_id']);
        }
        if (!empty($input['brooker_id'])) {
            $query = $query->where('brooker_id', $input['brooker_id']);
        }
        if (!empty($input['sprint_status_id'])) {
            $query = $query->where('sprint_status_id', $input['sprint_status_id']);
        }

        if (isset($input['pos'])) {
            // dd($input['pos']);
            if ($input['pos'] != '' && ($input['pos'] == 1 || $input['pos'] == 0)) {
                if ($input['pos'] == 1) {
                    $query = $query->whereNotNull('image');
                } else {
                    $query = $query->whereNull('image');
                }
            }
        }
        if (!empty($input['joey_id'])) {
            $query = $query->where('joey_id', $input['joey_id']);
        }
        return $datatables->eloquent($query)
            ->setRowId(static function ($record) use ($reasonsList) {

                return $record->id;
            })
            ->editColumn('tracking_id', static function ($record) {
                return $record->tracking_id;
            })
            ->editColumn('status', static function ($record) {
                return backend_view('joeyco-claim.status', compact('record'));
            })
            ->addColumn('vendors', static function ($record) {
                // return $record->vendors->name." (".$record->vendors->id.")";
                if ($record->vendors) {
                    return $record->vendors->name . " (" . $record->vendors->id . ")";
                } else {
                    return "";
                }
            })
            ->editColumn('reason', static function ($record) use ($reasonsList) {
                // return backend_view('joeyco-claim.reason', compact('record','reasonsList') );
                return ($record->getReason) ? $record->getReason->title : '';
            })
            ->editColumn('sprint_status_id', static function ($record) {
                return $record->sprint_status_id ? self::$status[$record->sprint_status_id] : '';
            })
            ->editColumn('brookersUsers', static function ($record) {
                return $record->brookersUsers ? $record->brookersUsers->name . ' (' . $record->brooker_id . ')' : '';
            })
            ->editColumn('joey_id', static function ($record) {
                if ($record->joey_id != null) {
                    return $record->joey_id ? $record->joey_id : '';
                } else {
                    $joeyRouteLocation = JoeyRouteLocations::where('task_id', $record->task_id)->whereNull('deleted_at')->first();
                    if ($joeyRouteLocation) {
                        $joeyRoute = JoeyRoute::where('id', $joeyRouteLocation->route_id)->whereNull('deleted_at')->first();
                        if ($joeyRoute) {
                            return $joeyRoute->joey_id ?? null;
                        }
                    }
                }
            })
            ->editColumn('joey', static function ($record) {
                if ($record->joey_id != null) {
                    return $record->joey ? $record->joey->first_name . ' ' . $record->joey->last_name : '';
                } else {
                    $joeyRouteLocation = JoeyRouteLocations::where('task_id', $record->task_id)->whereNull('deleted_at')->first();
                    if ($joeyRouteLocation) {
                        $joeyRoute = JoeyRoute::where('id', $joeyRouteLocation->route_id)->whereNull('deleted_at')->first();
                        if ($joeyRoute) {
                            if ($joeyRoute->joey_id) {
                                $joey = Joey::where('id', $joeyRoute->joey_id)->first();
                                return $joey->first_name . ' ' . $joey->last_name;
                            }
                        }
                    }
                }

            })
            ->editColumn('image', static function ($record) {
                return backend_view('joeyco-claim.image', compact('record'));
            })
            ->editColumn('amount', static function ($record) {
                return ($record->amount != null) ? "$" . $record->amount : "$0";
            })
            ->addColumn('route_ordinal', static function ($record) {
                $r = '';
                //    return "R-".$record->route_id.'-'.$record->ordinal;
                if ($record->route_id != null) {
                    $r .= "R-" . $record->route_id;
                    if ($record->ordinal != null) {
                        $r .= '-' . $record->ordinal;
                    }
                } else {
                    $joeyRouteLocation = JoeyRouteLocations::where('task_id', $record->task_id)->whereNull('deleted_at')->first();
                    if ($joeyRouteLocation) {
                        $r = $joeyRouteLocation->route_id . '-' . $joeyRouteLocation->ordinal;
                    }
                }

                return $r;
            })
            ->addColumn('action', static function ($record) {
                return backend_view('joeyco-claim.status', compact('record'));
            })
            ->make(true);
    }

    public function reSubmittedList(Request $request)
    {
        $input = $request->all();
        $start = isset($input['datepicker1']) ? $input['datepicker1'] : date('Y-m-d');
        $end = isset($input['datepicker2']) ? $input['datepicker2'] : date('Y-m-d');
        $start = ConvertTimeZone($start . ' 00:00:00', 'America/Toronto', 'UTC');
        $end = ConvertTimeZone($end . ' 23:59:59', 'America/Toronto', 'UTC');
        $query = Claim::where('status', 3)->whereBetween('created_at', [$start, $end])->get();
        $filterVendor = isset($input['vendor_id']) ? $input['vendor_id'] : "";
        $filterBroker = isset($input['brooker_id']) ? $input['brooker_id'] : "";
        $filterSprintStatus = isset($input['sprint_status_id']) ? $input['sprint_status_id'] : "";
        $filterJoey = isset($input['joey_id']) ? $input['joey_id'] : "";
        $filterpos = isset($input['pos']) ? $input['pos'] : "";
        $vendors = [];
        $brokers = [];
        $sprint_statuses = [];
        $joeys = [];
        if (count($query) > 0) {
            foreach ($query as $query_key => $query_value) {
                if ($query_value->joey_id != null) {
                    $joeys[$query_value->joey_id] = (!empty($query_value->joey)) ? $query_value->joey->first_name . ' ' . $query_value->joey->last_name . " (" . $query_value->joey_id . ")" : '';
                }
                if ($query_value->vendor_id != null) {
                    $vendors[$query_value->vendor_id] = (!empty($query_value->vendor)) ? $query_value->vendor->name . " (" . $query_value->vendor_id . ")" : '';
                }
                if ($query_value->sprint_status_id != null) {
                    $sprint_statuses[$query_value->sprint_status_id] = self::$status[$query_value->sprint_status_id] ?? '';
                    // $record->sprint_status_id?self::$status[$record->sprint_status_id]:'';
                }
                if ($query_value->brooker_id != null) {
                    $brokers[$query_value->brooker_id] = (!empty($query_value->brookersUsers)) ? $query_value->brookersUsers->name . " (" . $query_value->brooker_id . ")" : '';
                }
            }
        }
        return backend_view('joeyco-claim.re-submitted-listing', compact('joeys', 'vendors', 'sprint_statuses', 'brokers', 'filterpos', 'filterVendor', 'filterBroker', 'filterSprintStatus', 'filterJoey'));
    }

    public function reSubmittedListData(DataTables $datatables, Request $request): JsonResponse
    {
        $input = $request->all();
        $reasonsList = ClaimReason::where('slug', 'Re-Submitted')->get();
        $start = ConvertTimeZone($input['datepicker1'] . ' 00:00:00', 'America/Toronto', 'UTC');
        $end = ConvertTimeZone($input['datepicker2'] . ' 23:59:59', 'America/Toronto', 'UTC');

        $query = Claim::where('status', 3)->whereBetween('created_at', [$start, $end]);
        if (!empty($input['vendor_id'])) {
            $query = $query->where('vendor_id', $input['vendor_id']);
        }
        if (!empty($input['brooker_id'])) {
            $query = $query->where('brooker_id', $input['brooker_id']);
        }

        if (!empty($input['sprint_status_id'])) {
            $query = $query->where('sprint_status_id', $input['sprint_status_id']);
        }

        if (isset($input['pos'])) {
            // dd($input['pos']);
            if ($input['pos'] != '' && ($input['pos'] == 1 || $input['pos'] == 0)) {
                if ($input['pos'] == 1) {
                    $query = $query->whereNotNull('image');
                } else {
                    $query = $query->whereNull('image');
                }
            }
        }
        if (!empty($input['joey_id'])) {
            $query = $query->where('joey_id', $input['joey_id']);
        }
        return $datatables->eloquent($query)
            ->setRowId(static function ($record) use ($reasonsList) {

                return $record->id;
            })
            ->editColumn('tracking_id', static function ($record) {
                return $record->tracking_id;
            })
            ->addColumn('vendors', static function ($record) {
                if ($record->vendors) {
                    return $record->vendors->name . " (" . $record->vendors->id . ")";
                } else {
                    return "";
                }
            })
            ->editColumn('reason', static function ($record) use ($reasonsList) {
                // return backend_view('joeyco-claim.reason', compact('record','reasonsList') );
                return ($record->getReason) ? $record->getReason->title : '';
            })
            ->editColumn('status', static function ($record) {
                return backend_view('joeyco-claim.status', compact('record'));
            })
            ->editColumn('sprint_status_id', static function ($record) {
                return $record->sprint_status_id ? self::$status[$record->sprint_status_id] : '';
            })
            ->editColumn('brookersUsers', static function ($record) {
                return $record->brookersUsers ? $record->brookersUsers->name . ' (' . $record->brooker_id . ')' : '';
            })
            ->editColumn('joey_id', static function ($record) {
                if ($record->joey_id != null) {
                    return $record->joey_id ? $record->joey_id : '';
                } else {
                    $joeyRouteLocation = JoeyRouteLocations::where('task_id', $record->task_id)->whereNull('deleted_at')->first();
                    if ($joeyRouteLocation) {
                        $joeyRoute = JoeyRoute::where('id', $joeyRouteLocation->route_id)->whereNull('deleted_at')->first();
                        if ($joeyRoute) {
                            return $joeyRoute->joey_id ?? null;
                        }
                    }
                }
            })
            ->editColumn('joey', static function ($record) {
                if ($record->joey_id != null) {
                    return $record->joey ? $record->joey->first_name . ' ' . $record->joey->last_name : '';
                } else {
                    $joeyRouteLocation = JoeyRouteLocations::where('task_id', $record->task_id)->whereNull('deleted_at')->first();
                    if ($joeyRouteLocation) {
                        $joeyRoute = JoeyRoute::where('id', $joeyRouteLocation->route_id)->whereNull('deleted_at')->first();
                        if ($joeyRoute) {
                            if ($joeyRoute->joey_id) {
                                $joey = Joey::where('id', $joeyRoute->joey_id)->first();
                                return $joey->first_name . ' ' . $joey->last_name;
                            }
                        }
                    }
                }

            })
            ->editColumn('image', static function ($record) {
                return backend_view('joeyco-claim.image', compact('record'));
            })
            ->editColumn('amount', static function ($record) {
                return ($record->amount != null) ? "$" . $record->amount : "$0";
            })
            ->addColumn('route_ordinal', static function ($record) {
                $r = '';
                //    return "R-".$record->route_id.'-'.$record->ordinal;
                if ($record->route_id != null) {
                    $r .= "R-" . $record->route_id;
                    if ($record->ordinal != null) {
                        $r .= '-' . $record->ordinal;
                    }
                } else {
                    $joeyRouteLocation = JoeyRouteLocations::where('task_id', $record->task_id)->whereNull('deleted_at')->first();
                    if ($joeyRouteLocation) {
                        $r = $joeyRouteLocation->route_id . '-' . $joeyRouteLocation->ordinal;
                    }
                }

                return $r;
            })
            ->addColumn('action', static function ($record) {
                return backend_view('joeyco-claim.status', compact('record'));
            })
            ->make(true);
    }

    public function statusUpdate(Request $request)
    {
        $post = $request->all();
        if ($post['status_id'] == 1) {
            $claim = Claim::where('id', $post['claim_id'])->first();
            if ($claim->joey_id != null) {
                $deviceIds = UserDevice::where('user_id', $claim->joey_id)->where('is_deleted_at', 0)->pluck('device_token');
                $subject = 'Customer Support';
                $message = 'Claim on tracking id / merchant order no. "' . $claim->tracking_id . '" is approved.';
                // echo count($deviceIds);die;
                if (count($deviceIds) > 0) {
                    Fcm::sendPush($subject, $message, 'approved-claim', null, $deviceIds);
                    $payload = ['notification' => ['title' => $subject, 'body' => $message, 'click_action' => 'approved-claim'],
                        'data' => ['data_title' => $subject, 'data_body' => $message, 'data_click_action' => 'approved-claim']];
                    $createNotification = [
                        'user_id' => $claim->joey_id,
                        'user_type' => 'Joey',
                        'notification' => $subject,
                        'notification_type' => 'approved-claim',
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
        Claim::where('id', $post['claim_id'])->update(['status' => $post['status_id'], 'reason_id' => $post['reason']]);
        return redirect()->back()->with('success', 'Status Updated successfully!');


    }

    public function getReasons(Request $request)
    {
        $slug = $request->input('slug');
        $reasons = ClaimReason::where('slug', $slug)->get();
        return $reasons;
    }

    public function uploadImage(StoreImageRequest $request)
    {
        $post = $request->all();
        $return_data = ['status' => 200, 'message' => 'Status Updated Successfully!'];
        $id = $request->input('claim_id');

        $image_base64 = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
        $data = ['image' => $image_base64];//$base64Data];
        $response = $this->sendData('POST', '/', $data);
        // checking responce
        if (!isset($response->url)) {
            $message = $response->http->message;
            $return_data = ['status' => 400, 'message' => $message];
        } else {
            Claim::where('id', $id)->update(['image' => $response->url, 'status' => $post['status_id'], 'reason_id' => $post['reason']]);
        }
        return redirect()->back()->with('success', $return_data['message']);
    }

    public function sendData($method, $uri, $data = [])
    {
        $host = 'assets.joeyco.com';

        $json_data = json_encode($data);
        $headers = [
            'Accept-Encoding: utf-8',
            'Accept: application/json; charset=UTF-8',
            'Content-Type: application/json; charset=UTF-8',
            'User-Agent: JoeyCo',
            'Host: ' . $host,
        ];

        if (!empty($json_data)) {

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

                $this->response = (object)[
                    'copyright' => 'Copyright © ' . date('Y') . ' JoeyCo Inc. All rights reserved.',
                    'http' => (object)[
                        'code' => 500,
                        'message' => json_last_error_msg(),
                    ],
                    'response' => new \stdClass()
                ];
            }
        } else {
            dd(['error' => $error, 'responce' => $this->originalResponse]);
        }

        return $this->response;
    }

    public function get_trackingorderdetails($sprintId)
    {
        $sprintId = base64_decode($sprintId);
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
            ->get(array('sprint__tasks.*', 'joey_routes.id as route_id', 'locations.address', 'locations.suite', 'locations.postal_code', 'sprint__contacts.name', 'sprint__contacts.phone', 'sprint__contacts.email',
                'joeys.first_name as joey_firstname', 'joeys.id as joey_id',
                'joeys.last_name as joey_lastname', 'vendors.id as merchant_id', 'vendors.first_name as merchant_firstname', 'vendors.last_name as merchant_lastname', 'merchantids.scheduled_duetime'
            , 'joeys.id as joey_id', 'merchantids.tracking_id', 'joeys.phone as joey_contact', 'joey_route_locations.ordinal as stop_number', 'merchantids.merchant_order_num', 'merchantids.address_line2', 'sprint__sprints.creator_id', 'sprint__sprints.is_hub'));

        $i = 0;

        $data = [];
        $sprint_id = 0;
        foreach ($result as $tasks) {
            $sprint_id = $tasks->sprint_id;
            $status2 = array();
            $status = array();
            $status1 = array();
            $data[$i] = $tasks;
            $taskHistory = TaskHistory::where('sprint_id', '=', $tasks->sprint_id)->WhereNotIn('status_id', [17, 38, 0])->orderBy('date')
                ->get(['status_id', \DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto') as created_at")]);

            $returnTOHubDate = SprintReattempt::
            where('sprint_reattempts.sprint_id', '=', $tasks->sprint_id)->orderBy('created_at')
                ->first();

            if (!empty($returnTOHubDate)) {
                $taskHistoryre = TaskHistory::where('sprint_id', '=', $returnTOHubDate->reattempt_of)->WhereNotIn('status_id', [17, 38, 0])->orderBy('date')
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
                    $taskHistoryre = TaskHistory::where('sprint_id', '=', $returnTO2->reattempt_of)->WhereNotIn('status_id', [17, 38, 0])->orderBy('date')
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

                $status1[$history->status_id]['id'] = $history->status_id;

                if ($history->status_id == 13) {
                    $status1[$history->status_id]['description'] = 'At hub - processing';
                } else {
                    $status1[$history->status_id]['description'] = $this->statusmap($history->status_id);
                }
                $status1[$history->status_id]['created_at'] = $history->created_at;

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

        if (!empty($data)) {
            $status_array = [17, 113, 114, 116, 117, 118, 132, 138, 139, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 131, 135, 136, 144, 143];

            return backend_view('joeyco-claim.orderdetailswtracknigid')->with('data', $data)->with('sprintId', $sprintId)->with('status_array', $status_array);

        } else {
            return redirect()->back()->with('success', 'No Data Found!');
        }


    }

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
            "146" => "Delivery Missorted, Incorrect Address");
        return $statusid[$id];
    }
}
