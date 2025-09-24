<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;
    protected $table = 'questions';
    protected $fillable = [
        'content',
        'correct_answer',
        'explanation',
        'type',
        'stt',
        'active',
        'question_set_id',
        'cau'
    ];

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class);
    }

}
