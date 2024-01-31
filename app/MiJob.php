<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiJob extends Model
{

    use SoftDeletes;

    protected $table = 'mi_jobs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'start_address', 'execution_time','type', 'start_latitude', 'start_longitude'
    ];

    public function jobDetails()
    {
        return $this->hasMany(MiJobDetail::class);
    }

//    public function hub()
//    {
//        return $this->belongsToMany(Hub::class, 'assign_mi_jobs');
//    }




}
