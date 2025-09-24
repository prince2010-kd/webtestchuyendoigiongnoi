<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TargetMapStage extends Model
{
    use SoftDeletes;

    protected $table = 'target_map_stage';

    public $timestamps = true;
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'id_target', 'id_stage'
    ];

    public function target()
    {
        return $this->belongsTo(Target::class, 'id_target');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'id_stage');
    }
}
