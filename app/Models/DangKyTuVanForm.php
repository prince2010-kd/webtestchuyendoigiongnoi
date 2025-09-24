<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DangKyTuVanForm extends Model
{
     protected $table = 'dang_ky_tu_van_forms';

    protected $fillable = [
        'hoten',
        'tuoi',
        'sdt',
        'email',
        'khuvuc'
    ];
}
