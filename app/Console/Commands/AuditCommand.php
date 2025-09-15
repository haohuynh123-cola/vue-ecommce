<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OwenIt\Auditing\Models\Audit;
use Carbon\Carbon;

class AuditCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:manage
                            {action : Action to perform (list|stats|clean|model)}
                            {--model= : Model type for model-specific actions}
                            {--limit=20 : Number of records to show}
                            {--days=30 : Number of days for statistics}
                            {--clean-days=90 : Number of days to keep for clean action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage audit logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        return match($action) {
            'list' => $this->listAudits(),
            'stats' => $this->showStatistics(),
            'clean' => $this->cleanOldAudits(),
            'model' => $this->showModelAudits(),
            default => $this->error('Invalid action. Available actions: list, stats, clean, model'),
        };
    }

    /**
     * List recent audits
     */
    private function listAudits(): int
    {
        $limit = (int) $this->option('limit');

        $audits = Audit::with(['user', 'auditable'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        if ($audits->isEmpty()) {
            $this->info('No audits found.');
            return 0;
        }

        $this->info("Recent Audits (Last {$limit}):");
        $this->newLine();

        $headers = ['ID', 'User', 'Event', 'Model', 'Created At'];
        $rows = [];

        foreach ($audits as $audit) {
            $rows[] = [
                $audit->id,
                $audit->user ? $audit->user->name : 'System',
                $audit->event,
                $audit->auditable ? class_basename($audit->auditable) . ' #' . $audit->auditable->id : 'N/A',
                $audit->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $this->table($headers, $rows);
        return 0;
    }

    /**
     * Show audit statistics
     */
    private function showStatistics(): int
    {
        $days = (int) $this->option('days');
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        $query = Audit::whereBetween('created_at', [$startDate, $endDate]);

        $this->info("Audit Statistics (Last {$days} days):");
        $this->newLine();

        $this->info("Total Audits: {$query->count()}");
        $this->info("Unique Users: {$query->distinct('user_id')->count('user_id')}");
        $this->newLine();

        $this->info("Audits by Event:");
        $eventStats = $query->selectRaw('event, COUNT(*) as count')
            ->groupBy('event')
            ->pluck('count', 'event');

        foreach ($eventStats as $event => $count) {
            $this->line("  {$event}: {$count}");
        }
        $this->newLine();

        $this->info("Audits by Model Type:");
        $modelStats = $query->selectRaw('auditable_type, COUNT(*) as count')
            ->groupBy('auditable_type')
            ->pluck('count', 'auditable_type');

        foreach ($modelStats as $model => $count) {
            $this->line("  {$model}: {$count}");
        }

        return 0;
    }

    /**
     * Clean old audits
     */
    private function cleanOldAudits(): int
    {
        $days = (int) $this->option('clean-days');

        if (!$this->confirm("Are you sure you want to delete audits older than {$days} days?")) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $deletedCount = Audit::where('created_at', '<', Carbon::now()->subDays($days))->delete();

        $this->info("Deleted {$deletedCount} old audits.");
        return 0;
    }

    /**
     * Show audits for a specific model
     */
    private function showModelAudits(): int
    {
        $modelType = $this->option('model');

        if (!$modelType) {
            $this->error('Model type is required. Use --model=ModelName');
            return 1;
        }

        $limit = (int) $this->option('limit');

        $audits = Audit::where('auditable_type', "App\\Models\\{$modelType}")
            ->with(['user', 'auditable'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        if ($audits->isEmpty()) {
            $this->info("No audits found for model: {$modelType}");
            return 0;
        }

        $this->info("Audits for {$modelType} (Last {$limit}):");
        $this->newLine();

        $headers = ['ID', 'User', 'Event', 'Model ID', 'Created At'];
        $rows = [];

        foreach ($audits as $audit) {
            $rows[] = [
                $audit->id,
                $audit->user ? $audit->user->name : 'System',
                $audit->event,
                $audit->auditable_id,
                $audit->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $this->table($headers, $rows);
        return 0;
    }
}
