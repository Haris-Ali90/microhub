<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MicroHubAssign extends Model {

    protected $table = 'microhub_manager_assign';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id','hub_id','created_at','updated_at','deleted_at'
    ];

}
