<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class ActivityLog extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'status_code',
        'execution_time',
        'event',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the subject that the activity was performed on.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the causer that performed the activity.
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include activities for a specific log name.
     */
    public function scopeForLog(Builder $query, string $logName): Builder
    {
        return $query->where('log_name', $logName);
    }

    /**
     * Scope a query to only include activities for a specific causer.
     */
    public function scopeForCauser(Builder $query, $causer): Builder
    {
        return $query->where('causer_type', get_class($causer))
                    ->where('causer_id', $causer->getKey());
    }

    /**
     * Scope a query to only include activities for a specific subject.
     */
    public function scopeForSubject(Builder $query, $subject): Builder
    {
        return $query->where('subject_type', get_class($subject))
                    ->where('subject_id', $subject->getKey());
    }

    /**
     * Scope a query to only include activities for a specific event.
     */
    public function scopeForEvent(Builder $query, string $event): Builder
    {
        return $query->where('event', $event);
    }

    /**
     * Scope a query to only include activities within a date range.
     */
    public function scopeInDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get the formatted description with user information.
     */
    public function getFormattedDescriptionAttribute(): string
    {
        $causerName = $this->causer ? $this->causer->name ?? $this->causer->email : 'Unknown User';
        return str_replace(':causer', $causerName, $this->description);
    }

    /**
     * Get the human readable time ago.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
