<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
    // Cho phép Laravel fill dữ liệu vào các cột này
    protected $fillable = [
        'name',
        'active',
        'stt',
    ];
}
