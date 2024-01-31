<?php

namespace App\Http\Controllers\Backend;

use App\Client;
use App\Hub;
use App\HubStore;
use App\MicroHubPostalCodes;
use App\RoutingZones;
use App\SlotJob;
use App\Slots;
use App\SlotsPostalCode;
use App\Sprint;
use App\Task;
use App\ZonesTypes;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LastMileController extends Controller
{

    //get last mile zones list
    public function getZonesLastMile(Request $request)
    {

        $id = auth()->user()->hub_id;
        $data = RoutingZones::with('zoneType')
            ->whereNull('deleted_at')
            ->where('hub_id', $id)
            ->whereNull('is_custom_routing')
            ->orderBy('id', 'DESC')
            ->get();

        $zoneType = ZonesTypes::whereNull('deleted_at')->get();

        $hubData = Hub::where('id', $id)->first();
        // dd($hubData->HubPostalCode);

        $date = ($request->date != null) ? $request->date : date("Y-m-d");

        return backend_view('last_mile.index', compact('data', 'date', 'id', 'zoneType', 'hubData'));
    }

    // create zone of last mile
    public function lastMileZonesCreation(Request $request)
    {

        $zone = new RoutingZones();
        $zone->hub_id = auth()->user()->hub_id;
        $zone->title = $request->input('title');
        $zone->zone_type = $request->input('zone_type');
        $zone->save();

        for ($i = 0; $i < count($request->input('postal_code')); $i++) {

            $slotPostalCode = new SlotsPostalCode();
            $slotPostalCode->zone_id = $zone->id;
            $slotPostalCode->postal_code = $request->input('postal_code')[$i];
            $slotPostalCode->save();
        }

        return back()->with('success', 'Zone has been created successfully!');
    }

    // update zone of last mile
    public function getLastMileZoneInModal($id)
    {
        $data = RoutingZones::where('id', '=', $id)->first();

        $dataPostalCode = SlotsPostalCode::whereNull('slots_postal_code.deleted_at')
            ->where('zone_id', '=', $id)
            ->pluck('postal_code')
            ->toArray();
        $hubPostalCode = MicroHubPostalCodes::where('hub_id', auth()->user()
            ->hub_id)->whereNull('deleted_at')
            ->pluck('postal_code')
            ->toArray();

        $d = ['data' => $data, 'postalcodedata' => $dataPostalCode, 'hubPostalCode' => $hubPostalCode];

        return json_encode($d);
    }

    //update last mile zone
    public function lastMileZoneUpdate(Request $request)
    {

        $id = $request->input('id_time');
        $zoneupdate = RoutingZones::where('id', '=', $id)->first();
        $zoneupdate->title = $request->input('title_edit');
        $zoneupdate->zone_type = $request->input('zone_type');
        $zoneupdate->save();


        SlotsPostalCode::where('zone_id', '=', $request->input('id_time'))->update(['deleted_at' => date('Y-m-d H:i:s')]);
        foreach ($request->input('postal_code_edit') as $value) {
            $slotPostalCode_update = new SlotsPostalCode();
            $slotPostalCode_update->zone_id = $request->input('id_time');
            $slotPostalCode_update->postal_code = $value;
            $slotPostalCode_update->save();
        }
        return back()->with('success', 'Zone has been updated successfully!');

    }

    public function lastMileZoneDelete(Request $request)
    {
        $id = $request->input('delete_id');

        RoutingZones::
        where('id', '=', $id)->
        update(['deleted_at' => date('Y-m-d h:i:s')]);

        return redirect()->back()->with('success', 'Zone has been deleted successfully!');
    }

    public function lastMileZoneOrderCount($date, $zone_id)
    {

        $hubStores = HubStore::whereNull('deleted_at')->pluck('vendor_id');

        $zones = RoutingZones::whereNull('deleted_at')->where('id', $zone_id)->first();
        $SlotsPostalCode = SlotsPostalCode::where('zone_id', '=', $zone_id)->WhereNull('slots_postal_code.deleted_at')->get();

        $vendors = implode(',', $hubStores->toArray());

        $totalCounts = $this->ctcCount($SlotsPostalCode);



        $joeyCount = Slots::where('zone_id', '=', $zone_id)
            ->WhereNull('slots.deleted_at')
            ->sum('joey_count');

        $vehicleTyp = Slots::where('zone_id', '=', $zone_id)
            ->join('vehicles', 'vehicles.id', '=', 'slots.vehicle')
            ->WhereNull('slots.deleted_at')
            ->get(['vehicles.name', 'slots.joey_count']);


        if ($totalCounts['not_in_route'] == null) {
            $d_orders = 0;
        }
        if ($joeyCount == null) {

            $joeyCount = 0;
        }

        if ($vehicleTyp->isEmpty()) {
            $vehicleTyp[0] = ['name' => '', 'joey_count' => ''];
        }

        $response = ['title' => $zones->title, 'id' => $zones->id, 'orders' => $totalCounts['orders'], 'd_orders' => $totalCounts['not_in_route'], 'joeys_count' => $joeyCount, 'slots_detail' => $vehicleTyp];

        return json_encode($response);

    }

    public function ctcCount($postals=array()){

        $orders=0;
        $d_orders=0;

        foreach($postals as $postalcode){

            $ordercountQury = "SELECT 
          COUNT(*) AS counts,
          SUM(CASE WHEN in_hub_route = 0  THEN 1 ELSE 0 END) AS d_counts
          FROM sprint__sprints 
          join sprint__tasks ON(sprint_id=sprint__sprints.id  AND type='dropoff')
          join merchantids on(task_id=sprint__tasks.id and tracking_id IS NOT NULL and tracking_id!='')
          right join locations on(location_id=locations.id)
          WHERE locations.postal_code like '".$postalcode->postal_code."%'
          AND sprint__sprints.status_id IN(13,147)
          AND sprint__sprints.status_id NOT IN(36)
          AND sprint__sprints.deleted_at IS NULL";

            $ordercount = DB::select($ordercountQury);

            $orders += $ordercount[0]->counts;
            $d_orders += $ordercount[0]->d_counts;

        }

        return array('orders'=>$orders,'not_in_route'=>$d_orders);

    }

    public function listMileTotalOrderNotInRoute(Request $request)
    {
        $date=$request->get('date');


        $id=$request->get('id');
        $hub=Hub::where('id',$id)->first();
        if($hub!=null)
        {
            $id=$hub->parent_hub_id;
        }
        if($id==16)
        {
            $vendor=[477260];
            $date=date('Y-m-d',strtotime("-1 day", strtotime($date)));
        }
        else if($id==19)
        {
            $vendor=[475874,477282,476592,477340,477341,477342,477343,477344,477345,477346];
            $date=date('Y-m-d',strtotime("-1 day", strtotime($date)));
        }
        else if($id==20)
        {
            $vendor=[476674];
        }
        else
        {
            $vendor=[475874,477255,477254,477283,477284,477286,477287,477288,477289,477307,477308,477309,477310,477311,477312,477313,477314,477292,477294,477315,477317,477316,477295,
                477302,477303,477304,477305,477306,477296,477290,477297,477298,477299,477300,477320,477301,477318,477171];
        }


        $count=Sprint::whereIn('creator_id',$vendor)->where('sprint__sprints.created_at','like',$date."%")
            ->whereNull('in_hub_route')->whereNull('deleted_at')->count();
        return response()
            ->json(['status_code' => 200,'count'=>$count]);
    }



}
