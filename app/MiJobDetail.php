<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiJobDetail extends Model
{

    use SoftDeletes;
    protected $table = 'mi_job_details';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mi_job_id', 'locationid', 'location_type','type','start_time', 'end_time'
    ];




}
