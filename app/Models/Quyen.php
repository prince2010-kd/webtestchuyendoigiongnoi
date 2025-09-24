<?php

namespace App\Models;

use App\Traits\DienSoTuDong;
use App\Traits\DIenSttVaActiveTuDong;
use Illuminate\Database\Eloquent\Model;

class Quyen extends Model
{
    use DienSoTuDong, DIenSttVaActiveTuDong;
    protected $table = 'permissions';
    protected $fillable = ['group_id', 'menu_id', 'can_view', 'can_add', 'can_edit', 'can_delete', 'can_export'];
}
