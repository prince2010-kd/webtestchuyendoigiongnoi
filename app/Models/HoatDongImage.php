<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HoatDongImage extends Model
{
    use SoftDeletes;
    protected $table = 'hoatdong_images';
    protected $fillable = [
        'image_path',
        'alt_text',
        'stt',
        'active'
    ];
}
