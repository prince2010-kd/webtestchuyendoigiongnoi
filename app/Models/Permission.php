<?php

namespace App\Models;

use App\Traits\DienSoTuDong;
use App\Traits\DIenSttVaActiveTuDong;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Permission extends Model
{
    use HasFactory, DienSoTuDong, DIenSttVaActiveTuDong;

    protected $table = 'permissions';

    protected $fillable = [
        'group_id',
        'menu_id',
        'can_view',
        'can_add',
        'can_edit',
        'can_delete',
        'can_export',
    ];

    // public function group()
    // {
    //     return $this->belongsTo(Group::class, 'group_id');
    // }

    // Quan hệ tới Menu
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
