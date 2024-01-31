<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JCUser extends Model
{

    public $table = 'jc_users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_address','user_phone','email_address'
    ];


}
