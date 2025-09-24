<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LevelMapTarget extends Model
{
    use SoftDeletes;

    protected $table = 'level_map_target'; // bảng có tên custom

    public $timestamps = true; // nếu có created_at, updated_at

    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'id_level', 'id_target'
    ];

    public function level()
    {
        return $this->belongsTo(Level::class, 'id_level');
    }

    public function target()
    {
        return $this->belongsTo(Target::class, 'id_target');
    }
}
