<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionSet extends Model
{
    use SoftDeletes;
    protected $table = 'question_sets';
    protected $fillable = [
        'title',
        'description',
        'duration',
        'stt',
        'active'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function qualityChecks()
    {
        return $this->hasMany(QualityCheck::class);
    }

    public function activeQualityCheck()
    {
        return $this->hasOne(QualityCheck::class)->where('active', 1);
    }

}
