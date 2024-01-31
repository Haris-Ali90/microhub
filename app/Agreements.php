<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agreements extends Model
{

    protected $table = 'agreements';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'target','effective_at','copy'
    ];
}