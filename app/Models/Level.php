<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
    use SoftDeletes;
    protected $table = 'levels';  // Khai báo rõ tên bảng

    protected $fillable = [
        'code',
        'title',
        'des',
        'active',
        'stt'
    ];

    public function targets()
    {
        return $this->belongsToMany(Target::class, 'level_map_target', 'id_level', 'id_target');
    }
}
