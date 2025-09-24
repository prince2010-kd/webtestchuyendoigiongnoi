<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivityTrait;
class General extends Model
{
    use LogsActivityTrait;
    protected $table = 'general';

    protected $fillable = [
        'keyword',
        'label',
        'val',
        'created',
        'stt',
        'public',
        'type',
        'group_conf',
        'page_key'
    ];

    public $timestamps = false; // vì không có created_at, updated_at
}
