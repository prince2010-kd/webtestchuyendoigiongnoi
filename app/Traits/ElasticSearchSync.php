<?php

namespace App\Traits;

use App\Services\ElasticService;
use Illuminate\Support\Facades\Log;

trait ElasticSearchSync
{
    protected static function bootElasticSearchSync()
    {
        static::created(function ($model) {
            try {
                $elastic = new ElasticService();
                $index = $model->getElasticIndexName();
                // Log::info("Trait: created event for {$index} id {$model->getKey()}");

                $elastic->index($index, (string)$model->getKey(), $model->getSearchableData());
            } catch (\Throwable $e) {
                Log::error("Elasticsearch create error: " . $e->getMessage());
            }
        });

        static::updated(function ($model) {
            try {
                $elastic = new ElasticService();
                $index = $model->getElasticIndexName();
                // Log::info("Trait: updated event for {$index} id {$model->getKey()}");

                $elastic->update($index, (string)$model->getKey(), $model->getSearchableData());
            } catch (\Throwable $e) {
                Log::error("Elasticsearch update error: " . $e->getMessage());
            }
        });

        static::deleted(function ($model) {
            try {
                $elastic = new ElasticService();
                $index = $model->getElasticIndexName();
                // Log::info("Trait: deleted event for {$index} id {$model->getKey()}");

               $data = $model->getSearchableData();
        $data['deleted_at'] = now()->toDateTimeString();

        $elastic->update($index, (string)$model->getKey(), $data);
            } catch (\Throwable $e) {
                Log::error("Elasticsearch delete error: " . $e->getMessage());
            }
        });
    }

    /**
     * Bắt buộc model phải định nghĩa tên index Elasticsearch.
     */
    public function getElasticIndexName(): string
    {
        return property_exists($this, 'elasticIndex') ? $this->elasticIndex : strtolower(class_basename($this));
    }
}
