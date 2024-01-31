<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManifestField extends Model
{
    // use HasFactory;
    use SoftDeletes;
    public $table = 'mainfest_fields';



    protected $guarded = [];





}
