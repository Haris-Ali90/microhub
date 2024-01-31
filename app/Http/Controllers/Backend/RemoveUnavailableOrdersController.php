<?php

namespace App\Http\Controllers\Backend;

use App\JoeyRoute;
use App\JoeyRouteLocations;
use Illuminate\Http\Request;

class RemoveUnavailableOrdersController extends BackendController
{
    public function getMarkIncomplete(Request $request)
    {
        $route_ids=[];

        $date = !empty($request->get('date')) ? $request->get('date') : date("Y-m-d");
        $postData = $request->all();
        $tracking_id_data=[];
        if(!empty($postData['hub']))
        {
            $tracking_id_data=JoeyRoute::join('joey_route_locations', 'joey_route_locations.route_id', '=', 'joey_routes.id')
                ->join('sprint__tasks', 'sprint__tasks.id', '=', 'joey_route_locations.task_id')
                ->join('sprint__sprints', 'sprint__sprints.id', '=', 'sprint__tasks.sprint_id')
                ->join('merchantids','merchantids.task_id','=','sprint__tasks.id')
                ->join('locations','locations.id','=','sprint__tasks.location_id')
                ->whereIn('sprint__sprints.status_id', [133,13, 61, 124,121,104,105,106,107,108,109,110,111,112,131,135,136,101,102,103])
                ->where('merchantids.tracking_id','NOT LIKE',"old%")
                ->whereNull('joey_route_locations.is_unattempted')
                ->whereNull('joey_route_locations.deleted_at')
                //->where('joey_routes.is_incomplete', 0)
                //->where("joey_routes.date",'like', $date ."%")
                ->where('joey_routes.hub',$postData['hub']);
            if(isset($postData['route_id']) && $postData['route_id'])
            {
                $tracking_id_data=$tracking_id_data->where('route_id','=',$postData['route_id']);
            }
            if(isset($postData['status_id']) && $postData['status_id'])
            {
                $tracking_id_data=$tracking_id_data->where('sprint__sprints.status_id','=',$postData['status_id']);
            }
            $route_ids=$tracking_id_data;
            $tracking_id_data=$tracking_id_data->get(['joey_route_locations.id','joey_route_locations.ordinal','joey_route_locations.route_id','sprint__sprints.status_id','merchantids.tracking_id','locations.address','locations.postal_code']);
            $route_ids=$route_ids->groupBy('route_id')->get(['route_id']);


        }
        return backend_view('remove-unavailable-order.incomplete',compact('tracking_id_data','route_ids'));
    }

    public function markIncomplete(Request $request)
    {

        $ids = $request->get('ids');
        JoeyRouteLocations::whereIn('id',$ids)->update(['is_unattempted'=>1]);
        return response()->json( ['status_code'=>200,'success'=>'Route Mark Incomplete Successfully!']);

    }
}