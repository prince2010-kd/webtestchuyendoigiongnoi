<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id_stage', 'code', 'title', 'des', 'active', 'stt'
    ];

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'id_stage');
    }
}
