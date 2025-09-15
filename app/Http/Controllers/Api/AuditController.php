<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OwenIt\Auditing\Models\Audit;
use Carbon\Carbon;

class AuditController extends Controller
{
    /**
     * Get paginated audits
     */
    public function index(Request $request): JsonResponse
    {
        $query = Audit::with(['user', 'auditable']);

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by auditable type
        if ($request->has('auditable_type')) {
            $query->where('auditable_type', $request->auditable_type);
        }

        // Filter by event
        if ($request->has('event')) {
            $query->where('event', $request->event);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Search in old_values and new_values
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('old_values', 'like', "%{$search}%")
                  ->orWhere('new_values', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $audits = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => AuditResource::collection($audits),
            'meta' => [
                'current_page' => $audits->currentPage(),
                'last_page' => $audits->lastPage(),
                'per_page' => $audits->perPage(),
                'total' => $audits->total(),
            ],
        ]);
    }

    /**
     * Get audit statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $startDate = $request->has('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->subDays(30);

        $endDate = $request->has('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now();

        $query = Audit::whereBetween('created_at', [$startDate, $endDate]);

        $stats = [
            'total_audits' => $query->count(),
            'unique_users' => $query->distinct('user_id')->count('user_id'),
            'audits_by_event' => $query->selectRaw('event, COUNT(*) as count')
                ->groupBy('event')
                ->pluck('count', 'event'),
            'audits_by_auditable_type' => $query->selectRaw('auditable_type, COUNT(*) as count')
                ->groupBy('auditable_type')
                ->pluck('count', 'auditable_type'),
            'audits_by_hour' => $query->selectRaw('strftime("%H", created_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderBy('hour')
                ->pluck('count', 'hour'),
        ];

        return response()->json([
            'data' => $stats,
            'date_range' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
        ]);
    }

    /**
     * Get recent audits
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 20);
        $audits = Audit::with(['user', 'auditable'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => AuditResource::collection($audits),
        ]);
    }

    /**
     * Get audits for a specific model
     */
    public function modelAudits(Request $request, string $modelType, int $modelId): JsonResponse
    {
        $modelClass = "App\\Models\\{$modelType}";

        if (!class_exists($modelClass)) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        $model = $modelClass::findOrFail($modelId);
        $limit = $request->get('limit', 50);

        $audits = $model->audits()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => AuditResource::collection($audits),
            'model' => [
                'type' => $modelType,
                'id' => $model->id,
                'name' => $model->name ?? $model->title ?? "{$modelType} #{$model->id}",
            ],
        ]);
    }

    /**
     * Get a specific audit
     */
    public function show(Audit $audit): JsonResponse
    {
        $audit->load(['user', 'auditable']);

        return response()->json([
            'data' => new AuditResource($audit),
        ]);
    }

    /**
     * Clean old audits
     */
    public function clean(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $days = $request->days;
        $deletedCount = Audit::where('created_at', '<', Carbon::now()->subDays($days))->delete();

        return response()->json([
            'message' => "Deleted {$deletedCount} old audits",
            'deleted_count' => $deletedCount,
        ]);
    }
}
