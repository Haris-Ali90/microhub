<?php

namespace App;


use App\Models\Interfaces\QuizAnswerInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class QuizAnswers extends Model
{
    use SoftDeletes;
    /**
     * Table name.
     *
     * @var array
     */

    public $table = 'quiz_answers';

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
    ];


}