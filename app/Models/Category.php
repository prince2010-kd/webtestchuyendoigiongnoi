<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivityTrait;
class Category extends Model
{
    use SoftDeletes, LogsActivityTrait;
    protected $dates = ['deleted_at'];
    protected $table = 'category';
    
    protected $fillable = [
        'title',
        'seo-name',
        'image',
        'content',
        'stt',
        'active',
    ];
}
