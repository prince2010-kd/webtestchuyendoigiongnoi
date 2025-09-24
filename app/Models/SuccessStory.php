<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuccessStory extends Model
{
    use SoftDeletes;
    protected $table = 'success_story';
    protected $fillable = [
        'name',
        'school',
        'ielts_score',
        'image',
        'content',
        'stt'
    ];
}
