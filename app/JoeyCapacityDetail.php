<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoeyCapacityDetail extends Model
{

    use SoftDeletes;
    protected $table = 'custom_joey_detail';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'id', 'name', 'created_at','updated_at','deleted_at',
    // ];




}
