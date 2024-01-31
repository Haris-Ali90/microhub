<?php

namespace App\Http\Controllers\Backend;

use App\Hub;
use App\HubStore;
use App\Slots;
use App\Sprint;
use App\Task;
use App\Vendor;

class FirstMileHubController extends BackendController
{
    // get first mile hub list with vendors
    public function fisrtMileHubList()
    {
        $hubId = auth()->user()->hub_id;
//        dd($hubId);
        $hubs = Hub::with('vendor')
            ->whereNull('deleted_at')
            ->find($hubId);
        return backend_view('first_mile.index', ['data'=> $hubs, 'id' => $hubId]);
    }

    //get first mile order count
    public function getFirstMileOrderCount($hub_id, $date)
    {
        $hubs = Hub::whereNull('deleted_at')->where('id', $hub_id)->first();
        $vendorIds = HubStore::where('hub_id', $hub_id)->WhereNull('deleted_at')->pluck('vendor_id');
        $created_at = date("Y-m-d", strtotime('-1 day', strtotime($date)));

        $vendorOrderCount = Sprint::whereIn('creator_id',$vendorIds)
            ->whereNotIn('status_id', [36])
            ->whereIn('status_id', [24,61,111])
//            ->whereDate('created_at', 'LIKE', $date.'%')
            ->whereNull('deleted_at')->count();

        $joeyCount = Slots::where('hub_id', '=',  $hub_id)
            ->WhereNull('slots.deleted_at')
            ->where('mile_type',1)
            ->sum('joey_count');

        $vehicleTyp = Slots::where('hub_id', '=',  $hub_id)
            ->join('vehicles', 'vehicles.id', '=', 'slots.vehicle')
            ->WhereNull('slots.deleted_at')
            ->where('mile_type',1)
            ->get(['vehicles.name', 'slots.joey_count']);

        if($joeyCount==null){
            $joeyCount=0;
        }

        if($vehicleTyp->isEmpty()){
            $vehicleTyp[0]=['name'=>'','joey_count'=>''];
        }

        $response = ['title'=>$hubs->title,'id'=>$hubs->id,'orders' => $vendorOrderCount, 'joeys_count' => $joeyCount, 'slots_detail' => $vehicleTyp];

        return json_encode($response);


    }

    // get slots list data and view
    public function slotsListData($id)
    {
        $slots = Slots::whereNull('deleted_at')->where('hub_id','=',$id)->where('mile_type',1)->orderBy('id' , 'DESC')->get();
        return backend_view('first_mile.slots.list', ['data'=> $slots, 'id'=> $id] );
    }
}
