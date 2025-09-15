<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActivityService
{
    /**
     * Log a custom activity
     */
    public function log(
        string $description,
        ?Model $subject = null,
        ?Model $causer = null,
        string $logName = 'default',
        ?string $event = null,
        array $properties = []
    ): ActivityLog {
        $causer = $causer ?? Auth::user();

        return ActivityLog::create([
            'log_name' => $logName,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'causer_type' => $causer ? get_class($causer) : null,
            'causer_id' => $causer?->getKey(),
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => request()->method(),
            'url' => request()->fullUrl(),
            'event' => $event,
        ]);
    }

    /**
     * Log model creation
     */
    public function logCreated(Model $model, ?Model $causer = null, string $logName = 'default'): ActivityLog
    {
        $modelName = class_basename($model);
        $description = ":causer created {$modelName} #{$model->getKey()}";

        return $this->log($description, $model, $causer, $logName, 'created');
    }

    /**
     * Log model update
     */
    public function logUpdated(Model $model, array $oldValues = [], ?Model $causer = null, string $logName = 'default'): ActivityLog
    {
        $modelName = class_basename($model);
        $description = ":causer updated {$modelName} #{$model->getKey()}";

        $properties = [
            'old_values' => $oldValues,
            'new_values' => $model->getChanges(),
        ];

        return $this->log($description, $model, $causer, $logName, 'updated', $properties);
    }

    /**
     * Log model deletion
     */
    public function logDeleted(Model $model, ?Model $causer = null, string $logName = 'default'): ActivityLog
    {
        $modelName = class_basename($model);
        $description = ":causer deleted {$modelName} #{$model->getKey()}";

        return $this->log($description, $model, $causer, $logName, 'deleted');
    }

    /**
     * Log user login
     */
    public function logLogin(Model $user, Request $request): ActivityLog
    {
        return $this->log(
            ":causer logged in",
            $user,
            $user,
            'auth',
            'login',
            [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'login_at' => Carbon::now()->toISOString(),
            ]
        );
    }

    /**
     * Log user logout
     */
    public function logLogout(Model $user): ActivityLog
    {
        return $this->log(
            ":causer logged out",
            $user,
            $user,
            'auth',
            'logout'
        );
    }

    /**
     * Log failed login attempt
     */
    public function logFailedLogin(string $email, Request $request): ActivityLog
    {
        return $this->log(
            "Failed login attempt for email: {$email}",
            null,
            null,
            'auth',
            'failed_login',
            [
                'email' => $email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'attempted_at' => Carbon::now()->toISOString(),
            ]
        );
    }

    /**
     * Get activities for a specific user
     */
    public function getActivitiesForUser(Model $user, int $limit = 50): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return ActivityLog::forCauser($user)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    /**
     * Get activities for a specific model
     */
    public function getActivitiesForModel(Model $model, int $limit = 50): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return ActivityLog::forSubject($model)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    /**
     * Get activities by log name
     */
    public function getActivitiesByLogName(string $logName, int $limit = 50): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return ActivityLog::forLog($logName)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    /**
     * Get activities in date range
     */
    public function getActivitiesInDateRange(Carbon $startDate, Carbon $endDate, int $limit = 50): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return ActivityLog::inDateRange($startDate, $endDate)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity statistics
     */
    public function getStatistics(Carbon $startDate = null, Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->subDays(30);
        $endDate = $endDate ?? Carbon::now();

        $query = ActivityLog::inDateRange($startDate, $endDate);

        return [
            'total_activities' => $query->count(),
            'unique_users' => $query->distinct('causer_id')->count('causer_id'),
            'activities_by_log_name' => $query->selectRaw('log_name, COUNT(*) as count')
                ->groupBy('log_name')
                ->pluck('count', 'log_name'),
            'activities_by_event' => $query->selectRaw('event, COUNT(*) as count')
                ->groupBy('event')
                ->pluck('count', 'event'),
            'activities_by_hour' => $query->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderBy('hour')
                ->pluck('count', 'hour'),
        ];
    }

    /**
     * Clean old activities
     */
    public function cleanOldActivities(int $days = 90): int
    {
        $cutoffDate = Carbon::now()->subDays($days);

        return ActivityLog::where('created_at', '<', $cutoffDate)->delete();
    }
}
