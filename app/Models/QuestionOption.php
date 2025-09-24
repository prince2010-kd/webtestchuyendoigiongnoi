<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionOption extends Model
{
    use SoftDeletes;
    protected $table = 'question_options';
    protected $fillable = [
        'question_id',
        'label',
        'text',
        'stt',
        'active'
    ];
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
