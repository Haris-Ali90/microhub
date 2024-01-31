<?php

namespace App\Http\Controllers\Backend;

use App\Classes\Client;
use App\Hub;
use App\HubStore;
use App\RoutingZones;
use App\SlotJob;
use App\Slots;
use App\Task;
use App\ZonesTypes;
use Illuminate\Http\Request;

class FirstMileSlotController extends BackendController
{

    // get first mile slots list
    public function slotsdata($id)
    {
        $slots = Slots::whereNull('deleted_at')->where('hub_id','=',$id)->where('mile_type',1)->orderBy('id' , 'DESC')->get();
        return backend_view('hubstores.slot.list', ['data'=> $slots, 'id'=> $id] );
    }

    // store first mile slots data
    public function storeFirstMileSlot(Request $request)
    {
        $slot = new Slots();
        $slot->hub_id = $request->input('hub_id');
        $slot->vehicle = $request->input('vehicle');
        $slot->start_time = $request->input('start_time');
        $slot->end_time = $request->input('end_time');
        $slot->joey_count = $request->input('joey_count');
        $slot->custom_capacity = $request->input('custom_capacity');
        $slot->mile_type = 1;
        $slot->save();
        return back()->with('success','Slot has been added successfully!');
    }

    //get data of edit options
    public function getFirstMileEditSlot($id)
    {
        $data=Slots::where('id','=',$id)->first();
        $d=['data'=>$data];
        return json_encode($d);
    }

    //first mile slot update
    public function firstMileSlotUpdate(Request $request)
    {
        $id = $request->input('id_time');
        $slotsupdate = Slots::where('id', '=', $id)->first();
        $slotsupdate->vehicle = $request->input('vehicle_edit');
        $slotsupdate->start_time = $request->input('start_time_edit');
        $slotsupdate->end_time = $request->input('end_time_edit');
        $slotsupdate->joey_count = $request->input('joey_count_edit');
        $slotsupdate->custom_capacity = $request->input('custom_capacity_edit');
        $slotsupdate->save();
        return back()->with('success','Slot has been updated successfully!');

    }

    //delete first mile slot
    public function firstMileSlotDelete(Request $request)
    {
        $id = $request->input('delete_id');
        Slots::where('id','=',$id)->update(['deleted_at'=>date('Y-m-d h:i:s')]);
        return redirect()->back()->with('success','Slot has been deleted successfully!');
    }

    //get detail of slot
    public function getDetailOfFirstMile($id)
    {
        $data=Slots::where('id','=',$id)->first();
        $d=['data'=>$data];
        return json_encode($d);
    }

}