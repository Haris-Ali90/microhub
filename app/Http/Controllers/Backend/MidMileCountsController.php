<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\HubStore;
use App\JoeyRoute;
use App\MicroHubOrder;
use App\OrderActualHubModel;
use Illuminate\Support\Facades\Auth;

class MidMileCountsController extends Controller
{
    public function bundle_count()
    {

        $hub_id = Auth::user()->hub_id;
        $bundle_count = MicroHubOrder::where('hub_id', $hub_id)->WhereNull('deleted_at')->groupBy('bundle_id')->count();


        if (!empty($bundle_count)) {
            $response = json_encode(["title" => 'Order Count',
                'Total Orders ' => $bundle_count,
                "code" => 200]);
        } else {
            $response = json_encode(["message" => 'Not found', "code" => 200]);

        }

        return $response;
    }
    public function pickup_dropoff_count()
    {
        $hub_id = Auth::user()->hub_id;
        /*
                $date_today = date('Y-m-d');
                $start_dt = new \DateTime($date_today." 00:00:00", new \DateTimezone('America/Toronto'));
                $start_dt->setTimeZone(new \DateTimezone('UTC'));
                $start = $start_dt->format('Y-m-d H:i:s');

                $end_dt = new \DateTime($date_today." 23:59:59", new \DateTimezone('America/Toronto'));
                $end_dt->setTimeZone(new \DateTimezone('UTC'));
                $end = $end_dt->format('Y-m-d H:i:s');*/


        $pickup_count = JoeyRoute::where('hub', $hub_id)
            ->join('joey_route_locations', 'joey_route_locations.route_id', '=', 'joey_routes.id')
            ->leftjoin('mid_mile_pick_drop', 'mid_mile_pick_drop.route_id', '=', 'joey_routes.id')
            ->WhereNull('mid_mile_pick_drop.deleted_at')
            ->where('joey_routes.mile_type', 2)
            ->where('joey_routes.route_completed', 0)
            ->where('mid_mile_pick_drop.status_id', '=', 125)
//            ->where('created_at','>',$start)
//            ->where('created_at','<',$end)
            ->count();


        $dropoff_count = JoeyRoute::where('hub', $hub_id)
            ->join('joey_route_locations', 'joey_route_locations.route_id', '=', 'joey_routes.id')
            ->leftjoin('mid_mile_pick_drop', 'mid_mile_pick_drop.route_id', '=', 'joey_routes.id')
            ->WhereNotNull('mid_mile_pick_drop.deleted_at')
            ->where('joey_routes.mile_type', 2)
            ->where('joey_routes.route_completed', 0)
            ->where('mid_mile_pick_drop.status_id', '=', 124)
//            ->where('created_at','>',$start)
//            ->where('created_at','<',$end)
            ->count();

        if (!empty($dropoff_count) && !empty($dropoff_count)) {
            $response = json_encode([
                "response" =>
                    [
                        "title" => "Mid Mile Pick up and dropoff Details",
                        "Total Pickups " => $pickup_count,
                        "Total Dropoff " => $dropoff_count,
                        "code" => 200
                    ],
            ]);

        } else {
            $response = json_encode(["message" => 'Not found', "code" => 200]);
        }
        return $response;

    }

    public function mid_mile_scanning()
    {
        $hub_id = Auth::user()->hub_id;
        /*
    $date_today = date('Y-m-d');
    $start_dt = new \DateTime($date_today." 00:00:00", new \DateTimezone('America/Toronto'));
    $start_dt->setTimeZone(new \DateTimezone('UTC'));
    $start = $start_dt->format('Y-m-d H:i:s');

    $end_dt = new \DateTime($date_today." 23:59:59", new \DateTimezone('America/Toronto'));
    $end_dt->setTimeZone(new \DateTimezone('UTC'));
    $end = $end_dt->format('Y-m-d H:i:s');*/

        $ready_scan_count = JoeyRoute::where('hub', $hub_id)
            ->join('joey_route_locations', 'joey_route_locations.route_id', '=', 'joey_routes.id')
            ->leftjoin('mid_mile_pick_drop', 'mid_mile_pick_drop.route_id', '=', 'joey_routes.id')
            ->WhereNotNull('mid_mile_pick_drop.deleted_at')
            ->where('joey_routes.mile_type', 2)
            ->where('joey_routes.route_completed', 0)
            ->where('mid_mile_pick_drop.status_id', '=', 124)
//            ->where('created_at','>',$start)
//            ->where('created_at','<',$end)
            ->count();

        if (!empty($ready_scan_count)) {
            $response = json_encode([
                "response" =>
                    [
                        "title" => "Mid Mile Scanning",
                        "Ready to Scan "=> $ready_scan_count,
                        "code" => 200
                    ],
            ]);

        } else {
            $response = json_encode(["message" => 'Not found', "code" => 200]);
        }
        return $response;

    }
}
