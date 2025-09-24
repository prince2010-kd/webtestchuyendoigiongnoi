<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'title',
        'des',
        'active',
        'stt'
    ];

    // Quan hệ: Một stage có thể có nhiều course
    public function courses()
    {
        return $this->hasMany(Course::class, 'id_stage');
    }

    // Quan hệ: Một stage có thể thuộc nhiều target (target_map_stage)
    public function targets()
    {
        return $this->belongsToMany(Target::class, 'target_map_stage', 'id_stage', 'id_target');
    }
}
