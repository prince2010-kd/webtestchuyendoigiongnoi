<?php

namespace App\Models;

use App\Traits\ElasticSearchSync;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmtpSetting extends Model
{
    // use HasFactory;
    use ElasticSearchSync;
    protected $table = 'smtp_settings'; // ← tên bảng thật trong database
    protected $fillable = [
        'username', 'password', 'hostname', 'secure', 'port', 'active'
    ];
    protected $elasticIndex = 'smtp_settings';
}
