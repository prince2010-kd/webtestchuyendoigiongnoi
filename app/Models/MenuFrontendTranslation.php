<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuFrontendTranslation extends Model
{
    protected $fillable = ['menu_frontend_id', 'locale', 'title'];

    public function menu()
    {
        return $this->belongsTo(MenuFrontend::class);
    }
}
