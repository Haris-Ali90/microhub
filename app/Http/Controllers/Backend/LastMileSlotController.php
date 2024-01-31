<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Slots;
use Illuminate\Http\Request;

class LastMileSlotController extends Controller
{
    public function lastMileSlotList($id, $zoneid)
    {
        $slots = Slots::whereNull('deleted_at')->where('hub_id','=',$id)->where('zone_id','=',$zoneid)->orderBy('id' , 'DESC')->get();
        return backend_view('last_mile.slot.index', ['data'=> $slots, 'id'=> $id, 'zoneid'=> $zoneid] );
    }

    public function createLastMileSlot(Request $request)
    {
        $slot = new Slots();
        $slot->hub_id = $request->input('hub_id');
        $slot->zone_id = $request->input('zone_id');
        $slot->vehicle = $request->input('vehicle');
        $slot->start_time = $request->input('start_time');
        $slot->end_time = $request->input('end_time');
        $slot->joey_count = $request->input('joey_count');
        $slot->custom_capacity = $request->input('custom_capacity');
        $slot->save();
        return back()->with('success','Slot has been added successfully!');
    }

    public function getLastMileEdit($id)
    {
        $data=Slots::where('id','=',$id)->first();
        $d=['data'=>$data];
        return json_encode($d);
    }


    public function lastMileSlotUpdate(Request $request)
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

    public function getDetailLastMile($id)
    {
        $data=Slots::where('id','=',$id)->first();
        $d=['data'=>$data];
        return json_encode($d);

    }

    public function deleteLastMileSlot(Request $request)
    {
        $id = $request->input('delete_id');
        Slots::where('id','=',$id)->update(['deleted_at'=>date('Y-m-d h:i:s')]);
        return redirect()->back()->with('success','Slot has been deleted successfully!');
    }

}
