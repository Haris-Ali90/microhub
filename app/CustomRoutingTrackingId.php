<?php

namespace App;
use App\MerchantIds;
use App\User;
use Illuminate\Foundation\Auth\User as Authenticatable;


class CustomRoutingTrackingId extends Authenticatable
{
    protected $table = 'custom_routing_tracking_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function merchantid()
    {
        return  $this->belongsTo(new MerchantIds(),'tracking_id','tracking_id')->orderBy('id','desc');
    }
    public function scanUser()
    {
        return  $this->belongsTo(new User(),'user_id');
    }
}

