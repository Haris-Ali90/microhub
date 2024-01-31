<?php

namespace App;


use App\Interfaces\JCAttemptedQuizInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Quiz;
use App\QuizAnswers;

class JCAttemptedQuizDetail extends Model
{
    use SoftDeletes;
    /**
     * Table name.
     *
     * @var array
     */

    public $table = 'microhub_attempted_quiz_details';

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
    ];

    public function question()
    {
        return $this->belongsTo(Quiz::class,'question_id','id');
    }

    public function answer()
    {
        return $this->belongsTo(QuizAnswers::class,'answer_id','id');
    }

}