<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QualityCheck extends Model
{
    use SoftDeletes;
    protected $table = 'quality_checks';
    protected $fillable = [
        'title',
        'image',
        'content',
        'stt',
        'active',
        'question_set_id',
        'slug'
    ];

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class);
    }

}
