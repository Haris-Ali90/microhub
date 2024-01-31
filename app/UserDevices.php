<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDevices extends Model {

    protected $connection = 'mysql3';
    /**
     * Table name.
     *
     * @var array
     */
    public $table = 'user_devices';

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
