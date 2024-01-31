<?php

namespace App\Http\Controllers\Backend;

use App\Sprint;
use App\Task;
use App\Vendor;
use Config;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use App\UserEntities;

class ReportingController extends BackendController
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
        "60" => "Task failure",
        '153' => 'Miss sorted to be reattempt',
        '154' => 'Joey unable to complete the route', '155' => 'To be re-attempted tomorrow');

    public function getReporting(Request $request)
    {


        $vendors = Vendor::whereNull('vendors.deleted_at')->selectRaw('id,name')
            ->get()->pluck('name', 'id')->toArray();

        return backend_view('reporting.orders_dashboard',compact('vendors'));
    }

    public function reportingData(Datatables $datatables, Request $request)
    {

        $vendors =$request->get('vendors');
        $from_date = !empty($request->get('fromdatepicker')) ? $request->get('fromdatepicker') : date("Y-m-d");
        $to_date = !empty($request->get('todatepicker')) ? $request->get('todatepicker') : date("Y-m-d");
        $from_date = $from_date." 00:00:00";
        $to_date = $to_date." 23:59:59";

        $query = Sprint::with(['sprintTasks'=> function ($query) {
             $query->where(['sprint__tasks.type' => 'dropoff'])->whereNull('sprint__tasks.deleted_at');
        }])->where(['creator_id' => $vendors])->whereBetween(\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), [$from_date, $to_date])->whereNull('sprint__sprints.deleted_at');


        return $datatables->eloquent($query)
        ->setRowId(static function ($query) {
            return $query->id;
        })->editColumn('status_id', static function ($query) {
                return self::$status[$query->status_id];
            })
            ->addColumn('sprintTasks', static function ($query) {
                if ($query->sprintTasks)
                    return self::$status[$query->sprintTasks->status_id];
            })
              ->addColumn('tracking_id', static function ($query) {
                  if ($query->sprintTasks) {
                      if ($query->sprintTasks->taskMerchants) {
                          return $query->sprintTasks->taskMerchants->tracking_id;
                      }
                  }
                  else
                      return '';

              })
              ->editColumn('route_id', static function ($query) {
                  if ($query->in_hub_route == 1) {
                      if ($query->sprintTasks) {
                          if ($query->sprintTasks->taskRouteLocation) {
                              return 'yes in route ( ' . $query->sprintTasks->taskRouteLocation->route_id . ')';
                          }
                      }
                  }
                  else
                      return 'no in route   ';
              })
            ->make(true);
    }





public function reportingExcel($vendor,$fromdate,$todate)
    {

         $vendors =$vendor;
        $from_date = $fromdate;
        $to_date = $todate;
        $from_date = $from_date." 00:00:00";
        $to_date = $to_date." 23:59:59";

        $query = Sprint::with(['sprintTasks'=> function ($query) {
            $query->where(['sprint__tasks.type' => 'dropoff'])->whereNull('sprint__tasks.deleted_at');
        }])
        ->where(['creator_id' => $vendors])
        ->whereBetween(\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), [$from_date, $to_date])
        ->whereNull('sprint__sprints.deleted_at')->get();

        $csv_hrader =array('sprint_id', 'tracking_id', 'created_at', 'updated_at', 'status_id', 'task_id', 'route_id');

        $reporting_array[] = array('sprint_id', 'tracking_id', 'created_at', 'updated_at', 'status_id', 'task_id', 'route_id');

        $file = fopen(public_path()."/vendereport.csv","w");

        fputcsv($file, $csv_hrader );

        foreach ($query as $reporting) {

            fputcsv($file, [
                'sprint_id' => strval($reporting->id),
                'tracking_id' => $reporting->sprintTasks?$reporting->sprintTasks->taskMerchants?$reporting->sprintTasks->taskMerchants->tracking_id:'' :'',//$reporting->tracking_id,
                'created_at' => $reporting->created_at,
                'updated_at' => $reporting->updated_at,
                'status_id' =>self::$status[$reporting->status_id],
                'task_id' => $reporting->sprintTasks? self::$status[$reporting->sprintTasks->status_id]:'',
                'route_id' => ($reporting->in_hub_route == 1)?$reporting->sprintTasks?$reporting->sprintTasks->taskRouteLocation?'yes in route (' . $reporting->sprintTasks->taskRouteLocation->route_id . ')':'':'':'not  in route ',

            ]);
        }


        fclose($file);

        $files= public_path(). "/vendereport.csv";

        $headers = array(
            'Content-Type: application/csv',
        );

        return Response::download($files, 'filename.csv', $headers);

	}
}
