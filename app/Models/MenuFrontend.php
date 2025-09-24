<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivityTrait;
class MenuFrontend extends Model
{
    use SoftDeletes, LogsActivityTrait;

    protected $table = 'menus_frontend';

    protected $fillable = [
        'title',
        'url',
        'parent_id',
        'position',
        'stt',
        'active',
        'footer_column',
    ];

    public function children()
    {
        return $this->hasMany(MenuFrontend::class, 'parent_id')->where('active', 1)->orderBy('stt');
    }

    public function parent()
    {
        return $this->belongsTo(MenuFrontend::class, 'parent_id');
    }

    public function subMenus()
    {
        return $this->hasMany(MenuFrontend::class, 'parent_id')
            ->where('active', 1)
            ->orderBy('stt')
            ->with('subMenus');
    }

    public function scopeFooterColumn2($query)
    {
        return $query->where('footer_column', 2);
    }

    // Scope lọc menu cột 3
    public function scopeFooterColumn3($query)
    {
        return $query->where('footer_column', 3);
    }


}
