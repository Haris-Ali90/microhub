<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class TrackingHistory extends Authenticatable
{
    protected $connection = 'mysql3';
    /**
     * Table name.
     *
     * @var array
     */
    public $table = 'tracking_history';

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



}
