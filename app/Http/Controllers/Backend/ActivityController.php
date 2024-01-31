<?php

namespace App\Http\Controllers\Backend;

use App\JoeyRoute;
use App\MicroHubOrder;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityController extends Controller
{

    function getindex(){
        return backend_view('zone.activity');
    }

    public function midMileRouteActivity()
    {
        $hubId = auth()->user()->hub_id;
        $routes = JoeyRoute::where('hub', $hubId)->where('mile_type', 2)->where('route_completed', 0)->where('date', 'LIKE', date('Y-m-d').'%')->exists();
        $users = User::where('userType', 'admin')->where('hub_id', $hubId)->pluck('id');

        $MicroHubScannedOrder = MicroHubOrder::whereIn('scanned_by',$users)
            ->groupBy('bundle_id')
            ->count();

        if($routes){
            return json_encode([
                'status' => 200,
                'data'=> [
                    'heading' => 'Mid Mile Route Already Created',
                    'bundle_count' => $MicroHubScannedOrder
                ]
            ]);
        }else{
            return json_encode([
                'status' => 200,
                'data'=> [
                    'heading' => 'Creating Mid Mile Route',
                    'bundle_count' => $MicroHubScannedOrder
                ]
            ]);
        }
    }

}