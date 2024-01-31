<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Hub;
use App\Sprint;

class MicroHubOrder extends Model
{
    use SoftDeletes;
    public $table = 'orders_actual_hub';

    protected $guarded = [];

//    protected $hidden = ['created_at','updated_at'];

    /**
     * The attributes that should be append to toArray.
     *
     * @var array
     */
    protected $appends = [];

    public function hub()
    {
        return $this->belongsTo(Hub::class, 'hub_id', 'id');
    }

    public function sprint()
    {
        return $this->belongsTo(Sprint::class, 'sprint_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'sprint_id', 'sprint_id')->whereIn('ordinal',[2,4]);
    }

    public function bundleOrderID()
    {
        $users = User::where('userType', 'admin')->where('hub_id', auth()->user()->hub_id)->pluck('id');

        return $this->hasMany(self::class, 'bundle_id', 'bundle_id')->where('scanned_by',$users);
    }

    public function sprintID()
    {
        return $this->belongsTo(Sprint::class, 'sprint_id', 'id');
    }

    public static function getHubIds($hubId, $user){

        $microHubOrder = MicroHubOrder::join('sprint__sprints','sprint__sprints.id','=','orders_actual_hub.sprint_id')
            ->whereIn('sprint__sprints.status_id', [148])
            ->whereNotIn('sprint__sprints.status_id', [36])
            ->where('orders_actual_hub.is_my_hub', 0)
            ->whereIn('orders_actual_hub.scanned_by',$user)
            ->whereNull('orders_actual_hub.deleted_at')
            ->groupBy('orders_actual_hub.hub_id')
            ->pluck('hub_id')->toArray();
        $sprintIds = CurrentHubOrder::where('hub_id', $hubId)->where('is_actual_hub', 0)->pluck('sprint_id');

        $hubBundleOther = MicroHubOrder::join('sprint__sprints','sprint__sprints.id','=','orders_actual_hub.sprint_id')
            ->where('sprint__sprints.status_id', 150)
            ->whereNotIn('sprint__sprints.status_id', [36])
            ->whereIn('orders_actual_hub.sprint_id',$sprintIds)
            ->groupBy('orders_actual_hub.bundle_id')
            ->pluck('hub_id')->toArray();

        $matchHubId = array_merge($microHubOrder, $hubBundleOther);

        return $matchHubId;

    }

}

