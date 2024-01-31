<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemporaryPassword extends Model
{

    protected $table = 'temporary_password';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id','password','is_valid', 'created_at','updated_at'
    ];


}
