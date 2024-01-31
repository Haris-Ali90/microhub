<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Hub;
use App\Sprint;

class MicroHubBundles extends Model
{
    use SoftDeletes;
    public $table = 'microhub_bundle_scanning';

    protected $guarded = [];

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

    public function hubID($date)
    {
        $users = User::where('userType', 'admin')->where('hub_id', auth()->user()->hub_id)->pluck('id');

        return $this->hasMany(self::class, 'hub_id', 'hub_id')->where('scanned_by',$users)->whereBetween('created_at', $date)->get();

    }

}

