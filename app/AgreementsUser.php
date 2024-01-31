<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgreementsUser extends Model
{

    protected $table = 'agreements_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'agreement_id','user_id','user_type','signed_at'
    ];
}