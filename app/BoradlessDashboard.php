<?php

namespace App;

use App\Http\Traits\BasicModelFunctions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BoradlessDashboard extends Model
{

    use BasicModelFunctions,SoftDeletes;

    protected $table = 'boradless_dashboard';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id' , 'sprint_id' , 'task_id' , 'creator_id' , 'route_id' , 'ordinal' , 'tracking_id' , 'joey_id' ,'eta_time','store_name','customer_name','weight' ,'joey_name' , 'picked_up_at' , 'sorted_at' , 'delivered_at' , 'returned_at' , 'hub_return_scan' , 'task_status_id' , 'order_image' , 'address_line_1' , 'address_line_2' , 'address_line_3' , 'created_at' , 'updated_at' , 'deleted_at' , 'is_custom_route'

    ];

    public function sprintBoradlessTasks()
    {
        return $this->hasone(Task::class, 'sprint_id', 'sprint_id')->where('type','dropoff')->orderby('id','DESC')->select('id','status_id','ordinal','location_id','contact_id',\DB::raw("CONVERT_TZ(FROM_UNIXTIME(eta_time),'UTC','America/Toronto') as eta_time"));
    }
	
	 public function JoeyNameRelation()
    {
        return $this->belongsTo( JoeyStorePickup::class,'tracking_id', 'tracking_id');
    }

    public function getFirstMileSprintCounts($sprintIds){

        $counts['total'] = $this->firstMileTotalOrders($sprintIds);
        $counts['picked-up'] = $this->firstMilePickedUp($sprintIds);
        $counts['at-hub'] = $this->firstMileAtHub($sprintIds);
        $counts['at-store'] = $this->firstMileAtStore($sprintIds);
        $counts['sprint_id'] = $sprintIds;
        return $counts;
    }

    public function firstMileTotalOrders($sprintIds)
    {
        //$totalOrders = DB::table('sprint__tasks_history')->whereIn('sprint_id',$sprintIds)->whereIn('status_id',[125,124,13,120,61])->pluck('id');
        return count($sprintIds);
    }

    public function firstMilePickedUp($sprintIds)
    {
        $picked_up = DB::table('sprint__tasks_history')->whereIn('sprint_id',$sprintIds)->whereNotIn('status_id', [38, 36])->where('status_id',125)->distinct('sprint_id')->pluck('sprint_id');
        return count($picked_up);
    }

    public function firstMileAtHub($sprintIds)
    {

        $at_hub = DB::table('sprint__tasks_history')->whereIn('sprint_id',$sprintIds)->whereNotIn('status_id', [38, 36])->whereIn('status_id',[124])->distinct('sprint_id')->pluck('sprint_id');

        return count($at_hub);
    }

    public function firstMileAtStore($sprintIds)
    {
        //$picked_up = DB::table('sprint__tasks_history')->whereIn('sprint_id',$sprintIds)->where('status_id',125)->distinct('sprint_id')->pluck('sprint_id');
        $picked_up = DB::table('sprint__sprints')->whereIn('id',$sprintIds)->whereNotIn('status_id', [38, 36])->whereIn('status_id',[24,61])->distinct('id')->pluck('id');
        //$at_store = [count($sprintIds) - count($picked_up)];
        //$at_store = [$picked_up,$sprintIds];
        //$at_store = DB::table('sprint__tasks_history')->whereIn('sprint_id',$sprintIds)->where('status_id',61)->distinct('sprint_id')->pluck('sprint_id');
        return count($picked_up);
    }

    /**
     * Get Sprint Task History.
     */
    public function SprintTaskHistory()
    {
        return $this->hasMany( SprintTaskHistory::class,'sprint_id', 'sprint_id');
    }

    public function pickupFromStore()
    {
        // gating current routs tasks ids
        return $this->SprintTaskHistory()
            ->select((DB::raw("MAX(CASE WHEN status_id=125 THEN CONVERT_TZ(created_at,'UTC','America/Toronto') ELSE NULL END) as pickup")))->where('status_id',125)->orderBy('date','ASC')->limit(2)->first();
    }

    public function atHubProcessing()
    {
        // gating current routs tasks ids
        return $this->SprintTaskHistory()
            ->select((DB::raw("MAX(CASE WHEN status_id IN (133) THEN CONVERT_TZ(created_at,'UTC','America/Toronto') ELSE NULL END) as athub")))->first();
    }

    public function outForDelivery()
    {
        // gating current routs tasks ids
        return $this->SprintTaskHistory()
            ->select((DB::raw("MAX(CASE WHEN status_id=121 THEN CONVERT_TZ(created_at,'UTC','America/Toronto') ELSE NULL END) as outdeliver")))->first();
    }

    public function deliveryTime()
    {
        return $this->SprintTaskHistory()
            ->select((DB::raw('MAX(CASE WHEN status_id IN(17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143,141) THEN CONVERT_TZ(created_at,"UTC","America/Toronto") ELSE NULL END) as delivery_time')))->first();
    }

    public function actualDeliveryTime()
    {
        return $this->SprintTaskHistory()
            ->select((DB::raw('MAX(CASE WHEN status_id IN (17, 113, 114, 116, 117, 118, 132, 138, 139, 144) THEN CONVERT_TZ(created_at,"UTC","America/Toronto") ELSE NULL END) as actual_delivery')),
                (DB::raw('MAX(CASE WHEN status_id IN ( 113, 114, 116, 117, 118, 132, 138, 139, 144) THEN status_id ELSE NULL END) as status_id')))->first();
    }

    public function sprintReattempts()
    {
        return $this->belongsTo(SprintReattempt::class,'sprint_id','sprint_id');
    }

    public function pickupFromStoreOtd($otd_date)
    {
        // gating current routs tasks ids
        return $this->SprintTaskHistory()
            ->select((DB::raw("MAX(CASE WHEN status_id=125 THEN CONVERT_TZ(created_at,'UTC','America/Toronto') ELSE NULL END) as pickup")))->where('status_id',125)->orderBy('date','ASC')->limit(2)->first();
    }


    public function atHubProcessingOtd()
    {
        // gating current routs tasks ids
        return $this->SprintTaskHistory()
            ->select((DB::raw("MAX(CASE WHEN status_id IN (124) THEN CONVERT_TZ(created_at,'UTC','America/Toronto') ELSE NULL END) as athub")))->where('status_id',124)->orderBy('date','ASC')->limit(2)->first();
    }

    public function deliveryTimeOTD()
    {
        // gating current routs tasks ids
        return $this->SprintTaskHistory()
            ->select((DB::raw('MAX(CASE WHEN status_id IN(17, 113, 114, 116, 117, 118, 132, 138, 139, 144, 131, 101, 102, 103, 104, 105, 106, 107, 108, 109, 111, 112, 131, 135, 136, 143) THEN CONVERT_TZ(created_at,"UTC","America/Toronto") ELSE NULL END) as delivery_time')))->first();
    }

    public function sprintReattemptsOTD()
    {
        return $this->belongsTo(SprintReattempt::class,'sprint_id','reattempt_of');
    }

    public function atHubProcessingFirst()
    {
        // gating current routs tasks ids
        return $this->SprintTaskHistory()
            ->select((DB::raw("MAX(CASE WHEN status_id IN (124) THEN CONVERT_TZ(created_at,'UTC','America/Toronto') ELSE NULL END) as athub")))->where('status_id',124)->orderBy('date','ASC')->limit(2)->first();
    }

    public function boradlessAtHubProcessingFirst()
    {
        // gating current routs tasks ids
        return $this->SprintTaskHistory()
            ->select((DB::raw("MAX(CASE WHEN status_id IN (124) THEN CONVERT_TZ(created_at,'UTC','America/Toronto') ELSE NULL END) as athub")))->where('status_id',124)->orderBy('date','ASC')->limit(2)->first();
    }

    public function getInprogressOrders($taskIds, $type)
    {
        $totalRecord = DB::table('boradless_dashboard')->whereIn('task_id', $taskIds)->whereIn('task_status_id',[133,121])
            ->get(['route_id','task_status_id']);
        $total = 0;
        $remaining_sorted = 0;
        $remaining_pickup = 0;
        $remaining_route = [];
        $routes = [];
        foreach ($totalRecord as $record)
        {
            if ($record->task_status_id == 133){
                $remaining_sorted = $remaining_sorted + 1 ;
            }
            if ($record->task_status_id == 121){
                $remaining_pickup = $remaining_pickup + 1 ;
            }
            if ($record->task_status_id == 121 ){
                $routes[] = $record->route_id;
            }
        }

        $counts['remaining_sorted'] = $remaining_sorted;
        $counts['remaining_pickup'] = $remaining_pickup;
        $counts['remaining_route'] = count(array_unique($routes));
        return $counts;
    }


    public function getSprintCounts($sprintIds){

        $counts['total'] = $this->totalOrders($sprintIds);
        $counts['picked-up'] = $this->picked_up($sprintIds);
        $counts['at-hub'] = $this->at_hub($sprintIds);
        $counts['at-store'] = $this->at_store($sprintIds);
        $counts['sorted-order'] = $this->sorted_order($sprintIds);
        $counts['out-for-delivery'] = $this->out_for_delivery($sprintIds);
        $counts['delivered-order'] = $this->delivery_order($sprintIds);
        $counts['returned'] = $this->returned($sprintIds);
        $counts['returned-to-merchant'] = $this->returned_to_merchant($sprintIds);
        return $counts;
    }

    public function totalOrders($sprintIds)
    {
        $totalOrders = DB::table('boradless_dashboard')->whereIn('id',$sprintIds)->pluck('id');
        return count($totalOrders);
    }

    public function picked_up($sprintIds)
    {
        $picked_up = DB::table('boradless_dashboard')->whereIn('id',$sprintIds)->where('task_status_id',125)->pluck('id');
        return count($picked_up);
    }

    public function at_hub($sprintIds)
    {
        $at_hub = DB::table('boradless_dashboard')->whereIn('id',$sprintIds)->whereIn('task_status_id',[124,13,120])->pluck('id');
        return count($at_hub);
    }

    public function at_store($sprintIds)
    {
        $at_store = DB::table('boradless_dashboard')->whereIn('id',$sprintIds)->where('task_status_id',61)->pluck('id');
        return count($at_store);
    }

    public function sorted_order($sprintIds)
    {
        $sorted_order = DB::table('boradless_dashboard')->whereIn('id',$sprintIds)->where('task_status_id',133)->pluck('id');
        return count($sorted_order);
    }

    public function out_for_delivery($sprintIds)
    {
        $out_for_delivery = DB::table('boradless_dashboard')->whereIn('id',$sprintIds)->where('task_status_id',121)->pluck('id');
        return count($out_for_delivery);
    }

    public function delivery_order($sprintIds)
    {
        $delivery_order = DB::table('boradless_dashboard')->whereIn('id',$sprintIds)->whereIn('task_status_id',$this->getStatusCodes('competed'))->pluck('id');
        return count($delivery_order);
    }

    public function returned($sprintIds)
    {
        $returned = DB::table('boradless_dashboard')->whereIn('id',$sprintIds)->whereIn('task_status_id',$this->getStatusCodes('return'))
            ->where('task_status_id','!=',111)->pluck('id');
        return count($returned);
    }

    public function returned_to_merchant($sprintIds)
    {
        $returned_to_merchant = DB::table('boradless_dashboard')->whereIn('id',$sprintIds)->where('task_status_id',111)->pluck('id');
        return count($returned_to_merchant);
    }


    public function getBoradlessCounts($taskIds, $type)
    {
        if (in_array($type, ['all','total'])) {
            $counts['total'] = $this->boradlesstotalOrders($taskIds);
        }
        if (in_array($type, ['all', 'sorted'])) {
            $counts['sorted'] = $this->boradlesssorted($taskIds);
        }
        if (in_array($type, ['all', 'picked'])) {
            $counts['pickup'] = $this->boradlesspickup($taskIds);
        }
        if (in_array($type, ['all', 'delivered'])) {
            $counts['delivered_order'] = $this->boradlessdelivery_order($taskIds);
        }
        if (in_array($type, ['all', 'return'])) {
            $counts['return_orders'] = $this->boradlessreturn_orders($taskIds);
            $counts['hub_return_scan'] = $this->boradlesshub_return_scan($taskIds);
        }
        if (in_array($type, ['all', 'scan'])) {
            $counts['notscan'] = $this->boradlessnotscan($taskIds);
            $counts['reattempted'] = $this->boradlessreattempted($taskIds);
        }

        if (in_array($type, ['all', 'scan'])){
            if ($this->boradlesspickup($taskIds) > 0 ){
                $counts['completion_ratio'] = round(($this->boradlessdelivery_order($taskIds)/$this->boradlesspickup($taskIds))*100,2);
            }
        }
        return $counts;
    }

    public function boradlesstotalOrders($taskIds)
    {
        $total = DB::table('boradless_dashboard')->where('is_custom_route', 0)->whereIn('task_id', $taskIds)->pluck('task_id');
        return count($total);
    }

    public function boradlesssorted($taskIds)
    {
        $sorted = DB::table('boradless_dashboard')->where('is_custom_route', 0)->whereIn('task_id', $taskIds)->whereNotNull('sorted_at')->pluck('task_id');
        return count($sorted);
    }

    public function boradlesspickup($taskIds)
    {
        $pickup = DB::table('boradless_dashboard')->where('is_custom_route', 0)->whereIn('task_id', $taskIds)->whereNotNull('picked_up_at')->pluck('task_id');
        return count($pickup);
    }

    public function boradlessdelivery_order($taskIds)
    {
        return $delivery_order = count(DB::table('boradless_dashboard')->where('is_custom_route', 0)->whereIn('task_id', $taskIds)->whereIn('task_status_id', $this->getStatusCodes('competed'))->pluck('task_id'));
    }

    public function boradlessreturn_orders($taskIds)
    {
        return $return_orders = count(DB::table('boradless_dashboard')->where('is_custom_route', 0)->whereIn('task_id', $taskIds)->whereIn('task_status_id', $this->getStatusCodes('return'))->pluck('task_id'));
    }

    public function boradlesshub_return_scan($taskIds)
    {
        return $hub_return_scan = count(DB::table('boradless_dashboard')->where('is_custom_route', 0)->whereIn('task_id', $taskIds)->whereIn('task_status_id', $this->getStatusCodes('return'))->whereNotNull('hub_return_scan')->where('is_custom_route', 0)->pluck('task_id'));;
    }

    public function boradlessnotscan($taskIds)
    {
        return $notscan = count(DB::table('boradless_dashboard')->whereIn('task_id', $taskIds)->whereIn('task_status_id', [61])->where('is_custom_route', 0)->pluck('task_id'));
    }

    public function boradlessreattempted($taskIds)
    {
        return $notscan = count(DB::table('boradless_dashboard')->whereIn('task_id', $taskIds)->whereIn('task_status_id', [ 13])->where('is_custom_route', 0)->pluck('task_id'));
    }


    public function getBoradlessCountsWithCustom($taskIds, $type)
    {
        if (in_array($type, ['all','total'])) {
            $counts['total'] = $this->boradlesstotalOrdersWithCustom($taskIds);
        }
        if (in_array($type, ['all', 'sorted'])) {
            $counts['sorted'] = $this->boradlesssortedWithCustom($taskIds);
        }
        if (in_array($type, ['all', 'picked'])) {
            $counts['pickup'] = $this->boradlesspickupWithCustom($taskIds);
        }
        if (in_array($type, ['all', 'delivered'])) {
            $counts['delivered_order'] = $this->boradlessdelivery_orderWithCustom($taskIds);
        }
        if (in_array($type, ['all', 'return'])) {
            $counts['return_orders'] = $this->boradlessreturn_ordersWithCustom($taskIds);
            $counts['hub_return_scan'] = $this->boradlesshub_return_scanWithCustom($taskIds);
        }
        if (in_array($type, ['all', 'scan'])) {
            $counts['notscan'] = $this->boradlessnotscanWithCustom($taskIds);
        }
        return $counts;
    }

    public function boradlesstotalOrdersWithCustom($taskIds)
    {
        $total = DB::table('boradless_dashboard')->whereIn('task_id', $taskIds)->pluck('task_id');
        return count($total);
    }

    public function boradlesssortedWithCustom($taskIds)
    {
        $sorted = DB::table('boradless_dashboard')->whereIn('task_id', $taskIds)->whereNotNull('sorted_at')->pluck('task_id');
        return count($sorted);
    }

    public function boradlesspickupWithCustom($taskIds)
    {
        $pickup = DB::table('boradless_dashboard')->whereIn('task_id', $taskIds)->whereNotNull('picked_up_at')->pluck('task_id');
        return count($pickup);
    }

    public function boradlessdelivery_orderWithCustom($taskIds)
    {
        return $delivery_order = count(DB::table('boradless_dashboard')->whereIn('task_id', $taskIds)->whereIn('task_status_id', $this->getStatusCodes('competed'))->pluck('task_id'));
    }

    public function boradlessreturn_ordersWithCustom($taskIds)
    {
        return $return_orders = count(DB::table('boradless_dashboard')->whereIn('task_id', $taskIds)->whereIn('task_status_id', $this->getStatusCodes('return'))->pluck('task_id'));
    }

    public function boradlesshub_return_scanWithCustom($taskIds)
    {
        return $hub_return_scan = count(DB::table('boradless_dashboard')->whereIn('task_id', $taskIds)->whereIn('task_status_id', $this->getStatusCodes('return'))->whereNotNull('hub_return_scan')->pluck('task_id'));
    }

    public function boradlessnotscanWithCustom($taskIds)
    {
        return $notscan = count(DB::table('boradless_dashboard')->whereIn('task_id', $taskIds)->whereIn('task_status_id', [61, 13])->pluck('task_id'));
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function onOrderCreationBoradlessEntries($sprint,$is_custom_route)
    {

        if (!$sprint instanceof Sprint) {
            return false;
        }
        $this->sprint_id=$sprint->id;
        $task=$sprint->dropoffTask;
        $vendor=$sprint->Vendor;
        $merchantid=$task->taskMerchant;
        $sprint_contact=$task->sprint_contact;
        if (!$task instanceof Task) {
            return false;
        }
        if (!$merchantid instanceof Merchantids) {

            return false;
        }
        $location=$task->location;
        if (!$location instanceof Locations) {

            return false;
        }

        $this->task_id=$task->id;
        $this->eta_time=$task->eta_time;
        $this->creator_id=$sprint->creator_id;
        $this->store_name=$vendor->name;
        $this->tracking_id=$merchantid->tracking_id;
        $this->weight=$merchantid->weight;
        $this->customer_name=$sprint_contact->name;
        $this->address_line_1=$location->address;
        $this->address_line_2=$merchantid->address_line2;
        $this->is_custom_route=$is_custom_route;
        return true;
    }


    public function getCtcCounts($taskIds, $type)
    {
        if (in_array($type, ['all','total'])) {
            $counts['total'] = $this->ctctotalOrders($taskIds);
        }
        if (in_array($type, ['all', 'sorted'])) {
            $counts['sorted'] = $this->ctcsorted($taskIds);
        }
        if (in_array($type, ['all', 'picked'])) {
            $counts['pickup'] = $this->ctcpickup($taskIds);
        }
        if (in_array($type, ['all', 'delivered'])) {
            $counts['delivered_order'] = $this->ctcdelivery_order($taskIds);
        }
        if (in_array($type, ['all', 'return'])) {
            $counts['return_orders'] = $this->ctcreturn_orders($taskIds);
            $counts['hub_return_scan'] = $this->ctchub_return_scan($taskIds);
        }
        if (in_array($type, ['all', 'scan'])) {
            $counts['notscan'] = $this->ctcnotscan($taskIds);
            $counts['reattempted'] = $this->ctcreattempted($taskIds);
        }

        if (in_array($type, ['all', 'scan'])){
            if ($this->ctcpickup($taskIds) > 0 ){
                $counts['completion_ratio'] = round(($this->ctcdelivery_order($taskIds)/$this->ctcpickup($taskIds))*100,2);
            }
        }
        return $counts;
    }



}
