<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiKhoaHoc extends Model
{
     protected $table = 'loai_khoahoc';

    protected $fillable = [
        'ten',
        'slug', // Hoặc các cột khác trong bảng của bạn
    ];

}
