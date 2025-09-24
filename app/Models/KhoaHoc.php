<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivityTrait;
class KhoaHoc extends Model
{
     use SoftDeletes, LogsActivityTrait;
     protected $table = 'khoa_hocs';

    protected $fillable = [
       'tieu_de',
    'slug',
    'mo_ta_ngan',         
    'mo_ta',
    'hinh_anh',
    'noi_dung',           // Thêm
    'sections',           // Thêm
    'meta_title',         // Thêm
    'meta_keywords',      // Thêm
    'meta_description',   // Thêm
    'meta_new_keyword',   // Thêm
    'active',
    'stt',
    ];

    protected $casts = [
        'active' => 'boolean',
        'stt' => 'integer',
        'sections' => 'array',
    ];
}
