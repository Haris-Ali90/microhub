<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerIncidents extends Model
{

use SoftDeletes;

    protected $table = 'customer_flag_incidents';

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
    ];
	
}

