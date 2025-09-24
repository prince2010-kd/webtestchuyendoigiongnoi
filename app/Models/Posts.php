<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivityTrait;
class Posts extends Model
{
    use SoftDeletes, LogsActivityTrait;
    protected $dates = ['deleted_at'];
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'url',
        'image',
        'active',
        'stt',
        'category_id',
        'short_description',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_new_keyword',
        'type',
        'user_id',
        'is_featured',
        'youtube_url',
        'youtube_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected $elasticIndex = 'posts';

    // Quan hệ đến bảng category (nếu có model Category)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }
}
