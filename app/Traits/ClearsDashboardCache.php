<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait ClearsDashboardCache
{
    protected static function bootClearsDashboardCache()
    {
        static::saved(function ($model) {
            static::invalidateDashboardCache($model);
        });

        static::deleted(function ($model) {
            static::invalidateDashboardCache($model);
        });
    }

    protected static function invalidateDashboardCache($model)
    {
        $userId = $model->user_id ?? ($model->user?->id ?? auth()->id());

        if ($userId) {
            Cache::increment("user_{$userId}_dashboard_version");
        }
    }
}
