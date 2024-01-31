<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{

    protected $table = 'vehicles';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'id', 'name', 'created_at','updated_at','deleted_at',
    // ];

    public function getId() {
        return (int) $this->attributes['id'];
    }


}
