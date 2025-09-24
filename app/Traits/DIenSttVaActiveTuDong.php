<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait DIenSttVaActiveTuDong
{
    public static function bootDIenSttVaActiveTuDong()
    {
        static::creating(function ($model){
            if(!$model->isDirty('stt') || is_null($model->stt))
            {
                $model->stt = method_exists($model, 'laySttTiepTheo')
                    ? $model::laySttTiepTheo('stt')
                    : 0;
            }

            if (!$model->isDirty('active') || is_null($model->active)) {
                $model->active = 1;
            }
            Log::info('Creating model with attributes:', $model->getAttributes());
            // return false;
        });
    }
}