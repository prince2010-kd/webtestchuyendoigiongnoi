<?php
namespace App\Traits;

use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Log; 

trait LogsActivityTrait
{
    protected static function bootLogsActivityTrait()
    {
        static::created(function ($model) {
            $description = 'thêm mới: ' . ($model->title ?? '');
            // Log::info('Log created: ' . $description); 
            app(ActivityLogService::class)->log($model, $description);
        });

        static::updated(function ($model) {
            $description = 'cập nhật: ' . ($model->title ?? '');
            // Log::info('Log updated: ' . $description);
            app(ActivityLogService::class)->log($model, $description);
        });

        static::deleted(function ($model) {
            $description = 'xóa: ' . ($model->title ?? '');
            // Log::info('Log deleted: ' . $description);
            app(ActivityLogService::class)->log($model, $description);
        });
    }
}
