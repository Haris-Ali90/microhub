<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HubRequest extends Model
{

    public $table = 'micro_hub_request';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'jc_user_id','own_joeys'
    ];


}
