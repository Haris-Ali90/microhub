<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\HubStore;
use App\JoeyRoute;
use Illuminate\Support\Facades\Auth;

class FirstMileCountsController extends Controller
{
    public function firstMileRouteActivity()
    {
        $hub_id = Auth::user()->hub_id;
        $routes = JoeyRoute::where('hub', $hub_id)->where('mile_type', 1)->where('route_completed', 0)->where('date', 'LIKE', date('Y-m-d').'%')->exists();
        $store_sount = HubStore::where('hub_id', $hub_id)->WhereNull('deleted_at')->count();

        if ($routes == false) {
            $response = json_encode([
                "message" => 'Creating First Mile Routes',
                'counts' => $store_sount,
                "code" => 200]);
        } else {
            $response = json_encode([
                "message" => 'No Routes found', "code" => 201
            ]);
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
            ->leftjoin('joey_storepickup', 'joey_storepickup.route_id', '=', 'joey_routes.id')
            ->WhereNull('joey_routes.deleted_at')
            ->where('joey_routes.mile_type', 1)
            ->where('joey_routes.route_completed', 0)
            ->where('joey_routes.date', 'LIKE', date('Y-m-d').'%')
            ->where('joey_storepickup.status_id', '=', 125)
            ->whereNull('joey_storepickup.deleted_at')
            ->count();

        dd($pickup_count);

        $dropoff_count = JoeyRoute::where('hub', $hub_id)
            ->join('joey_route_locations', 'joey_route_locations.route_id', '=', 'joey_routes.id')
            ->leftjoin('joey_storepickup', 'joey_storepickup.route_id', '=', 'joey_routes.id')
            ->WhereNotNull('joey_storepickup.deleted_at')
            ->where('joey_routes.mile_type', 1)
            ->where('joey_routes.route_completed', 0)
            ->where('joey_storepickup.status_id', '=', 124)
//            ->where('created_at','>',$start)
//            ->where('created_at','<',$end)
            ->count();

        if (!empty($dropoff_count) && !empty($dropoff_count)) {
            $response = json_encode([
                "response" =>
                    array(
                        "title" => "First Mile Pick up and dropoff Details",
                        "total_pickup_count" => $pickup_count,
                        "total_drop_off_count" => $dropoff_count,
                        "code" => 200
                    ),
            ]);

        } else {
            $response = json_encode(["message" => 'Not found', "code" => 200]);
        }
        return $response;

    }

    public function first_mile_scanning()
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
            ->leftjoin('joey_storepickup', 'joey_storepickup.route_id', '=', 'joey_routes.id')
            ->WhereNotNull('joey_storepickup.deleted_at')
            ->where('joey_routes.mile_type', 1)
            ->where('joey_routes.route_completed', 0)
            ->where('joey_storepickup.status_id', '=', 124)
//            ->where('created_at','>',$start)
//            ->where('created_at','<',$end)
            ->count();

        if (!empty($ready_scan_count)) {
            $response = json_encode([
                "response" =>
                    [
                        "title" => "First Mile Scanning",
                        "Ready to Scan " => $ready_scan_count,
                        "code" => 200
                    ],
            ]);

        } else {
            $response = json_encode(["message" => 'Not found', "code" => 200]);
        }
        return $response;

    }
}
