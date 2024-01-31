<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimReason extends Model
{
    public $table = 'claim_reasons';
    use SoftDeletes;

    protected $guarded = [];
}
