<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transcript extends Model
{
    protected $table = 'transcripts';

    protected $fillable = [
        'title',
        'text',
        'file_path',
        'stt',
        'active',
    ];
}
