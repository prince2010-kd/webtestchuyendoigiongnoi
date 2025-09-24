<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivityTrait;

class GiaoVien extends Model
{
    use SoftDeletes, LogsActivityTrait;

    protected $dates = ['deleted_at'];
    protected $table = 'teachers';

    protected $fillable = [
        'name',
        'position',
        'image',
        'facebook_url',
        'linkedin_url',
        'active',
        'stt', 
    ];
}
