<?php

namespace App\Models;

use App\Traits\DienSoTuDong;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivityTrait;
class NhomQuyen extends Model
{
    use DienSoTuDong, SoftDeletes, LogsActivityTrait;
    
    protected $table = 'users_group';
    protected $fillable = ['name', 'stt', 'active'];
}
