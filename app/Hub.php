<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{

    protected $table = 'hubs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title','parent_hub_id','address', 'created_at','updated_at','deleted_at',
    ];


    public Static function getHubAdressFromVendorId($vendorId)
    {
        
        return VendorZone::where('vendor_id', '=', $vendorId)
        ->join('hub_zones', 'zone_vendor_relationship.zone_id', '=', 'hub_zones.zone_id')
        ->join('hubs', 'hub_zones.hub_id', '=', 'hubs.id')
        ->leftjoin('vendors','vendor_id','=','vendors.id')
        // ->where('with_hub','=',1)
        ->whereNull('hubs.deleted_at')
        ->first();
    }
    public function HubProcess()
    {
        return $this->hasMany(HubProcess::class,'hub_id');
    }
    public function HubPostalCode()
    {
        return $this->hasMany(MicroHubPostalCodes::class,'hub_id')->whereNull('deleted_at');
    }

    public function vendor()
    {
        return $this->belongsToMany(Vendor::class, 'hub_stores')->whereNull('hub_stores.deleted_at');
    }

    public function sprint()
    {
        return $this->belongsToMany(sprint::class, 'orders_actual_hub')->whereNull('orders_actual_hub.deleted_at')->where('is_my_hub',0);
    }

    public function getHubOrdersCount($date, $hubId)
    {
        $hub = $this->where('id', $hubId)->where('is_consolidated', '!=', 1)->pluck('id');
        return $this->belongsToMany(sprint::class, 'orders_actual_hub')
            ->whereNull('orders_actual_hub.deleted_at')->where('is_my_hub',0)->whereIn('hub_id', $hub)->count();
    }

//    public function job()
//    {
//        return $this->belongsToMany(MiJob::class, 'assign_mi_jobs');
//    }
}
