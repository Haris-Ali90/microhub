<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Amazon;
use App\Amazon_count;
use Illuminate\Support\Facades\DB;
use App\JoeyRoutes;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;

class MontrealController extends BackendController
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
        "60" => "Task failure");



    public function getMontreal(Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $amazon_count = Amazon_count::where(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"),'like',$today_date."%")
            ->where(['vendor_id' => 477260])
            ->orderBy('id','DESC')
            ->first();
        return backend_view('montrealdashboard.montreal_dashboard', compact('amazon_count'));
    }

    public function montrealExcel($date = null)
    {
        if($date == null)
        {
            $date = date('Y-m-d');
        }


        $montreal_data = Amazon::where(\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"),'like',$date."%")->where(['vendor_id' => 477260])->get();
        $montreal_array[] = array('JoeyCo Order 1 #', 'Route Number', 'Joey', 'Customer Address', 'Pickup From Hub', 'Sorter Time', 'Estimated Delivery ETA', 'Actual Arrival @ CX', 'Amazon tracking #', 'Status');

        foreach ($montreal_data as $montreal) {
            $montreal_array[] = array(
                'JoeyCo Order #' => $montreal->order_id,
                'Route Number' => $montreal->route,
                'Joey' => $montreal->joey,
                'Customer Address' => $montreal->address,
                'Pickup From Hub' => $montreal->picked_hub_time,
                'Sorter Time' => $montreal->sorter_time,
                'Estimated Delivery ETA' => $montreal->dropoff_eta,
                'Actual Arrival @ CX' => $montreal->delivery_time,
                'Amazon tracking #' => $montreal->tracking_id,
                'Status' => self::$status[$montreal->sprint_status]
            );
        }
        Excel::create('Montreal Data '.$date.'', function ($excel) use ($montreal_array) {
            $excel->setTitle('Montreal Data');
            $excel->sheet('Montreal Data', function ($sheet) use ($montreal_array) {
                $sheet->fromArray($montreal_array, null, 'A1', false, false);
            });
        })->download('csv');
    }

    public function montrealData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $query = Amazon::where(\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"),'like',$today_date."%")
            ->where(['vendor_id' => 477260])->orderBy('updated_at','DESC');
        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('sprint_status', static function ($record) {
                return self::$status[$record->sprint_status];
            })
            ->editColumn('image', static function ($record) {
                if (isset($record->image)) {
                    return '<a class="group1" href = "' . $record->image . '" ><img style = "width:50px;height:50px" src = "' . $record->image . '" /></a >';
                } else {
                    return '';
                }
            })
            ->addColumn('action', static function ($record) {
                return backend_view('montrealdashboard.action', compact('record'));
            })
            ->make(true);
    }

    public function montrealProfile(Request $request, $id)
    {
        $mont_id = base64_decode($id);
        $amazon_montreal = Amazon::where(['id' => $mont_id])->get();
        $amazon_montreal = $amazon_montreal[0];

        return backend_view('montrealdashboard.montreal_profile', compact('amazon_montreal'));
    }

    public function getSorter(Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $amazon_count = Amazon_count::where(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"), 'like', $today_date . "%")
            ->where(['vendor_id' => 477260])->orderBy('id','DESC')
            ->first();

        $title_name = 'Montreal';
        return backend_view('montrealdashboard.sorted_order', compact('amazon_count', 'title_name'));
    }

    public function montrealSortedData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $query = Amazon::where(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"), 'like', $today_date . "%")->where(['sprint_status' => 133, 'vendor_id' => 477260]);

        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('sprint_status', static function ($record) {
                return self::$status[$record->sprint_status];
            })
            ->editColumn('image', static function ($record) {
                if (isset($record->image)) {
                    return '<a class="group1" href = "' . $record->image . '" ><img style = "width:50px;height:50px" src = "' . $record->image . '" /></a >';
                } else {
                    return '';
                }
            })
            ->addColumn('action', static function ($record) {
                return backend_view('montrealdashboard.action', compact('record'));
            })
            ->make(true);
    }

    public function montrealSortedExcel($date = null)
    {
        if($date == null)
        {
            $date = date('Y-m-d');
        }
        $montreal_data = Amazon::where(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"),'like',$date."%")->where(['sprint_status' => 133, 'vendor_id' => 477260])->get();
        $montreal_array[] = array('JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Pickup From Hub', 'Sorter Time', 'Estimated Delivery ETA', 'Actual Arrival @ CX', 'Amazon tracking #', 'Status');
        foreach ($montreal_data as $montreal) {
            $montreal_array[] = array(
                'JoeyCo Order #' => $montreal->order_id,
                'Route Number' => $montreal->route,
                'Joey' => $montreal->joey,
                'Customer Address' => $montreal->address,
                'Pickup From Hub' => $montreal->picked_hub_time,
                'Sorter Time' => $montreal->sorter_time,
                'Estimated Delivery ETA' => $montreal->dropoff_eta,
                'Actual Arrival @ CX' => $montreal->delivery_time,
                'Amazon tracking #' => $montreal->tracking_id,
                'Status' =>   self::$status[$montreal->sprint_status]
            );
        }
        Excel::create('Montreal Sorted Data '.$date.'', function ($excel) use ($montreal_array) {
            $excel->setTitle('Montreal Sorted Data');
            $excel->sheet('Montreal Sorted Data', function ($sheet) use ($montreal_array) {
                $sheet->fromArray($montreal_array, null, 'A1', false, false);
            });
        })->download('csv');
    }

    public function getMontrealhub(Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $amazon_count = Amazon_count::where(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"), 'like', $today_date . "%")
            ->where(['vendor_id' => 477260])
            ->orderBy('id','DESC')
            ->first();
        $title_name = 'Montreal';
        return backend_view('montrealdashboard.pickup_hub', compact('amazon_count', 'title_name'));
    }

    public function montrealPickedUpData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $query = Amazon::where(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"), 'like', $today_date . "%")->where(['sprint_status' => 121, 'vendor_id' => 477260]);
        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('sprint_status', static function ($record) {
                return self::$status[$record->sprint_status];
            })
            ->editColumn('image', static function ($record) {
                if (isset($record->image)) {
                    return '<a class="group1" href = "' . $record->image . '" ><img style = "width:50px;height:50px" src = "' . $record->image . '" /></a >';
                } else {
                    return '';
                }
            })
            ->addColumn('action', static function ($record) {
                return backend_view('montrealdashboard.action', compact('record'));
            })
            ->make(true);
    }

    public function montrealPickedupExcel($date = null)
    {
        if($date == null)
        {
            $date = date('Y-m-d');
        }
        $montreal_data = Amazon::where(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"), 'like', $date . "%")->where(['sprint_status' => 121, 'vendor_id' => 477260])->get();
        $montreal_array[] = array('JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Pickup From Hub', 'Sorter Time', 'Estimated Delivery ETA', 'Actual Arrival @ CX', 'Amazon tracking #', 'Status');
        foreach ($montreal_data as $montreal) {
            $montreal_array[] = array(
                'JoeyCo Order #' => $montreal->order_id,
                'Route Number' => $montreal->route,
                'Joey' => $montreal->joey,
                'Customer Address' => $montreal->address,
                'Pickup From Hub' => $montreal->picked_hub_time,
                'Sorter Time' => $montreal->sorter_time,
                'Estimated Delivery ETA' => $montreal->dropoff_eta,
                'Actual Arrival @ CX' => $montreal->delivery_time,
                'Amazon tracking #' => $montreal->tracking_id,
                'Status' =>   self::$status[$montreal->sprint_status]
            );
        }
        Excel::create('Montreal Picked Up Data '.$date.'', function ($excel) use ($montreal_array) {
            $excel->setTitle('Montreal Picked Up Data');
            $excel->sheet('Montreal Picked Up Data', function ($sheet) use ($montreal_array) {
                $sheet->fromArray($montreal_array, null, 'A1', false, false);
            });
        })->download('csv');
    }

    public function getMontnotscan(Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $amazon_count = Amazon_count::where(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"), 'like', $today_date . "%")
            ->where(['vendor_id' => 477260])
            ->orderBy('id','DESC')
            ->first();
        $title_name = 'Montreal';
        return backend_view('montrealdashboard.not_scanned_orders', compact('amazon_count', 'title_name'));
    }

    public function montrealNotScanData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $query = Amazon::where(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"), 'like', $today_date . "%")->where(['sprint_status' => 61, 'vendor_id' => 477260]);
        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('sprint_status', static function ($record) {
                return self::$status[$record->sprint_status];
            })
            ->editColumn('image', static function ($record) {
                if (isset($record->image)) {
                    return '<a class="group1" href = "' . $record->image . '" ><img style = "width:50px;height:50px" src = "' . $record->image . '" /></a >';
                } else {
                    return '';
                }
            })
            ->addColumn('action', static function ($record) {
                return backend_view('montrealdashboard.action', compact('record'));
            })
            ->make(true);
    }

    public function montrealNotscanExcel($date = null)
    {
        if($date == null)
        {
            $date = date('Y-m-d');
        }
        $montreal_data = Amazon::where(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"), 'like', $date . "%")->where(['sprint_status' => 61, 'vendor_id' => 477260])->get();
        $montreal_array[] = array('JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Pickup From Hub', 'Sorter Time', 'Estimated Delivery ETA', 'Actual Arrival @ CX', 'Amazon tracking #', 'Status');
        foreach ($montreal_data as $montreal) {
            $montreal_array[] = array(
                'JoeyCo Order #' => $montreal->order_id,
                'Route Number' => $montreal->route,
                'Joey' => $montreal->joey,
                'Customer Address' => $montreal->address,
                'Pickup From Hub' => $montreal->picked_hub_time,
                'Sorter Time' => $montreal->sorter_time,
                'Estimated Delivery ETA' => $montreal->dropoff_eta,
                'Actual Arrival @ CX' => $montreal->delivery_time,
                'Amazon tracking #' => $montreal->tracking_id,
                'Status' =>   self::$status[$montreal->sprint_status]
            );
        }
        Excel::create('Montreal Not Scan Data '.$date.'', function ($excel) use ($montreal_array) {
            $excel->setTitle('Montreal Not Scan Data');
            $excel->sheet('Montreal Not Scan Data', function ($sheet) use ($montreal_array) {
                $sheet->fromArray($montreal_array, null, 'A1', false, false);
            });
        })->download('csv');
    }

    public function getMontdelivered(Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $amazon_count = Amazon_count::where(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"), 'like', $today_date . "%")
            ->where(['vendor_id' => 477260])
            ->orderBy('id','DESC')
            ->first();
        $title_name = 'Montreal';
        return backend_view('montrealdashboard.delivered_orders', compact('amazon_count', 'title_name'));
    }

    public function montrealDeliveredData(Datatables $datatables, Request $request)
    {
        $today_date = !empty($request->get('datepicker')) ? $request->get('datepicker') : date("Y-m-d");
        $query = Amazon::where(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"), 'like', $today_date . "%")->whereNotIn('sprint_status', [133, 121, 61])->where('vendor_id', [477260]);
        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('sprint_status', static function ($record) {
                return self::$status[$record->sprint_status];
            })
            ->editColumn('image', static function ($record) {
                if (isset($record->image)) {
                    return '<a class="group1" href = "' . $record->image . '" ><img style = "width:50px;height:50px" src = "' . $record->image . '" /></a >';
                } else {
                    return '';
                }
            })
            ->addColumn('action', static function ($record) {
                return backend_view('montrealdashboard.action', compact('record'));
            })
            ->make(true);
    }

    public function montrealDeliveredExcel($date = null)
    {
        if($date == null)
        {
            $date = date('Y-m-d');
        }
        $montreal_data = Amazon::where(\DB::raw("CONVERT_TZ(updated_at,'UTC','America/Toronto')"), 'like', $date . "%")->whereNotIn('sprint_status', [133, 121, 61])->where('vendor_id', [477260])->get();
        $montreal_array[] = array('JoeyCo Order #', 'Route Number', 'Joey', 'Customer Address', 'Pickup From Hub', 'Sorter Time', 'Estimated Delivery ETA', 'Actual Arrival @ CX', 'Amazon tracking #', 'Status');
        foreach ($montreal_data as $montreal) {
            $montreal_array[] = array(
                'JoeyCo Order #' => $montreal->order_id,
                'Route Number' => $montreal->route,
                'Joey' => $montreal->joey,
                'Customer Address' => $montreal->address,
                'Pickup From Hub' => $montreal->picked_hub_time,
                'Sorter Time' => $montreal->sorter_time,
                'Estimated Delivery ETA' => $montreal->dropoff_eta,
                'Actual Arrival @ CX' => $montreal->delivery_time,
                'Amazon tracking #' => $montreal->tracking_id,
                'Status' =>   self::$status[$montreal->sprint_status]
            );
        }
        Excel::create('Montreal Delivered Data '.$date.'', function ($excel) use ($montreal_array) {
            $excel->setTitle('Montreal Delivered Data');
            $excel->sheet('Montreal Delivered Data', function ($sheet) use ($montreal_array) {
                $sheet->fromArray($montreal_array, null, 'A1', false, false);
            });
        })->download('csv');
    }

    public function getRouteinfo(Request $request)
    {
        $date = $request->input('datepicker');
        // dd($date);
        if($date==null){
            $date = date("Y-m-d");
        }

        $montreal_info = JoeyRoutes::where('created_at','like',$date."%")->where('hub',16)->get();

        return backend_view('montrealdashboard.montreal_route_info', compact('montreal_info'));
    }

    public function montrealRouteinfoExcel($date = null)
    {
        //setting up current date if null
        if($date == null)
        {
            $date = date('Y-m-d');
        }

        /*getting csv file data*/
        $montreal_route_data = JoeyRoutes::where('created_at', 'like', $date . "%")->where('hub',16)->get();

        //checking if data is null then return null
        if(count($montreal_route_data) <= 0)
        {
            // if the data null ten return empty array
            return [];
        }

        // init data variable
        $data  = [];
        $csv_header = ['Route No', 'No of drops', 'No of picked', 'No of drops completed', 'No of Returns', 'No of unattempted'];
        $data[0] =  $csv_header;

        $iteration = 1;
        foreach($montreal_route_data as $montreal_route)
        {

            $data[$iteration] = [
                $montreal_route->id,
                $montreal_route->TotalOrderDropsCount(),
                $montreal_route->TotalOrderPickedCount(),
                $montreal_route->TotalOrderDropsCompletedCount(),
                $montreal_route->TotalOrderReturnCount(),
                $montreal_route->TotalOrderUnattemptedCount()
            ];
            $iteration++;
        }
        return $data;
    }

}
