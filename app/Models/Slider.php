<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivityTrait;

class Slider extends Model
{
     use SoftDeletes, LogsActivityTrait;
    protected $table = 'sliders';

    protected $fillable = [
        'title', 
        'image', 
        'content', 
        'stt', 
        'active', 
    ];

    // protected $elasticIndex = 'sliders';

    // public function getSearchableData()
    // {
    //     return [
    //         'title' => $this->title,
    //         'image' => $this->image,
    //         'content' => $this->content,
    //         'active' => $this->active,
    //         'stt' => $this->stt,
    //         'deleted_at' => $this->deleted_at ? $this->deleted_at->toDateTimeString() : null,
    //     ];
    // }

}
