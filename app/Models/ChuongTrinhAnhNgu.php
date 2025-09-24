<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ChuongTrinhAnhNgu extends Model
{
    use SoftDeletes;
    protected $table = 'chuong_trinh_anh_ngu';
    protected $fillable = [
        'title',
        'slug',
        'image',
        'stt',
        'active',
        'sections',
    ];
}
