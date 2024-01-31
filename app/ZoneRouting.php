<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZoneRouting extends Model
{
    use SoftDeletes;

    /**
     * Table name.
     *
     * @var array
     */
    public $table = 'zones_routing';

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
    protected $casts = [

    ];

}

