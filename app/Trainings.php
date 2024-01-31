<?php

namespace App;

use App\Interfaces\TrainingInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Trainings extends Model
{
    use SoftDeletes;
    /**
     * Table name.
     *
     * @var array
     */

    public $table = 'trainings';

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
    ];




}