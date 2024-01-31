<?php

namespace App;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;
use App\Review;
use Illuminate\Support\Facades\Mail;

class ZonesTypes extends Authenticatable
{
    protected $table = 'zones_types';
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'id','title','amount',
    ];


}
