<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes;
    protected $table = 'videos';
    protected $fillable = [
        'youtube_url',
        'youtube_thumbnail',
        'youtube_id',
        'local_path',
        'original_name',
        'stt',
        'active',
        'title',
    ];
}
