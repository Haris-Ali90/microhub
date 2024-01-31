<?php

namespace App;


use App\Interfaces\JCAttemptedQuizInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class JCAttemptedQuiz extends Model
{
    use SoftDeletes;
    /**
     * Table name.
     *
     * @var array
     */

    public $table = 'microhub_attempted_quiz';

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
    ];

    public function category()
    {
        return $this->belongsTo(OrderCategory::class,'category_id','id');
    }


    public function attemptDetail()
    {
        return $this->hasMany(JCAttemptedQuizDetail::class, 'quiz_id','id');
    }


}
