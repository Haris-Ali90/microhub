<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\JoeyRoute;
use Illuminate\Support\Facades\Auth;

class LastMileCountsController extends Controller
{
    public function order_count()
    {
        $hub_id = Auth::user()->hub_id;
        $last_mile_count = JoeyRoute::where('hub', $hub_id)
            ->join('joey_route_locations', 'joey_route_locations.route_id', '=', 'joey_routes.id')
            ->WhereNull('joey_routes.deleted_at')
            ->where('joey_routes.route_completed', 0)
            ->count();

        if (!empty($last_mile_count)) {
            $response = json_encode(["title" => 'Order Count',
                'Total Orders ' => $last_mile_count,
                "code" => 200]);

        } else {
            $response = json_encode(["message" => 'Not found', "code" => 200]);

        }

        return $response;
    }
}
