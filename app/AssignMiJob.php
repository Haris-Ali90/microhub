<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignMiJob extends Model
{

    use SoftDeletes;

    protected $table = 'microhub_assign_mi_jobs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'hub_id', 'mi_job_id'
    ];


}
