<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivityTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, LogsActivityTrait, SoftDeletes;
    protected $table = 'menus';
     
    protected $fillable = [
        'title',    
        'url',       
        'parent_id', 
        'active',
        'icon',
        'stt',
    ];
    public function children() {
        return $this->hasMany(Menu::class, 'parent_id')->where('active', 1);
    }

    public function childrenRecursive()
    {
         return $this->hasMany(Menu::class, 'parent_id')
        ->where('active', 1)
        ->with(['childrenRecursive' => function ($query) {
            $query->where('active', 1);
        }]);
    }

    public function childrenAll()
    {
        return $this->hasMany(Menu::class, 'parent_id')
                    ->with('childrenAll');
    }
}
