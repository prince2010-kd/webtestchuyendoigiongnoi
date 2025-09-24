<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Target extends Model
{
    use SoftDeletes;
    protected $table = 'targets';  // Khai báo rõ tên bảng

    protected $fillable = [
        'code',
        'title',
        'price',
        'des',
        'active',
        'stt'
    ];

    // Quan hệ: Target có thể có nhiều stage (target_map_stage)
    public function stages()
    {
        return $this->belongsToMany(Stage::class, 'target_map_stage', 'id_target', 'id_stage');
    }

    // Quan hệ: Target có thể thuộc nhiều level (level_map_target)
    public function levels()
    {
        return $this->belongsToMany(Level::class, 'level_map_target', 'id_target', 'id_level');
    }
}
