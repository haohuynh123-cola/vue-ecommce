<?php

namespace App\Traits;

use App\Services\ActivityService;
use Illuminate\Database\Eloquent\Model;

trait Trackable
{
    /**
     * Boot the trait
     */
    protected static function bootTrackable(): void
    {
        static::created(function (Model $model) {
            app(ActivityService::class)->logCreated($model);
        });

        static::updated(function (Model $model) {
            app(ActivityService::class)->logUpdated($model, $model->getOriginal());
        });

        static::deleted(function (Model $model) {
            app(ActivityService::class)->logDeleted($model);
        });
    }

    /**
     * Get all activities for this model
     */
    public function activities()
    {
        return $this->morphMany(\App\Models\ActivityLog::class, 'subject');
    }

    /**
     * Get activities performed by this model
     */
    public function performedActivities()
    {
        return $this->morphMany(\App\Models\ActivityLog::class, 'causer');
    }

    /**
     * Log a custom activity for this model
     */
    public function logActivity(string $description, string $event = null, array $properties = []): \App\Models\ActivityLog
    {
        return app(ActivityService::class)->log(
            $description,
            $this,
            auth()->user(),
            'model',
            $event,
            $properties
        );
    }

    /**
     * Get the last activity for this model
     */
    public function getLastActivityAttribute(): ?\App\Models\ActivityLog
    {
        return $this->activities()->latest()->first();
    }

    /**
     * Get activity count for this model
     */
    public function getActivityCountAttribute(): int
    {
        return $this->activities()->count();
    }
}
