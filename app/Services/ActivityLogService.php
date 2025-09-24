<?php
namespace App\Services;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public function log(Model $model, string $description, ?array $properties = null)
    {
        /** @var \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard $auth */
        $auth = auth();

        /** @var \App\Models\User|null $user */
        $user = $auth->user();

        $activity = activity()
            ->performedOn($model)
            ->causedBy($user)
            ->withProperties($properties ?? $model->getChanges())
            ->log($description);

        return $activity;
    }
}
