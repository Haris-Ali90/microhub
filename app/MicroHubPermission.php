<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MicroHubPermission extends Model
{
    use SoftDeletes;
    /**
     * Table name.
     *
     * @var array
     */
    public $table = 'micro_hub_permissions';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hub_id', 'process_id'
    ];


    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];


    public function getHubProcess(){
        return $this->belongsToMany(HubProcess::class, 'micro_hub_permissions');
    }
}
