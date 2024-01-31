<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCategory extends Model
{
    use SoftDeletes;
    /**
     * Table name.
     *
     * @var array
     */

    public $table = 'order_category';

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
    ];

    public function trainings()
    {
        return $this->hasMany(Trainings::class, 'order_category_id','id');
    }

  

}