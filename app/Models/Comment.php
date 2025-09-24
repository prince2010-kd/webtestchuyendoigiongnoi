<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'post_id',
        'name',
        'email',
        'website',
        'comment',
        'approved',
        'active',
        'stt'
    ];

    public function post()
    {
        return $this->belongsTo(Posts::class, 'post_id');
    }
}
