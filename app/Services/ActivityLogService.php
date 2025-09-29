<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as LaravelLog;

class ActivityLogService
{
    /**
     * Log user activity with comprehensive details
     */
    public static function log(string $action, array $data = []): void
    {
        try {
            $request = request();
            $user = Auth::user();
            
            $logData = [
                'users_id' => $user?->id,
                'action' => $action,
                'ip_address' => $request?->ip() ?? 'unknown',
                'user_agent' => $request?->userAgent() ?? 'unknown',
                'session_id' => $request?->session()?->getId() ?? 'unknown',
                'request_method' => $request?->method() ?? 'unknown',
                'request_url' => $request?->fullUrl() ?? 'unknown',
                'request_data' => $request?->except(['password', 'password_confirmation', '_token']) ?? [],
                'severity' => $data['severity'] ?? 'info',
                'module' => $data['module'] ?? 'unknown',
                'target_type' => $data['target_type'] ?? null,
                'target_id' => $data['target_id'] ?? null,
                'target_name' => $data['target_name'] ?? null,
                'description' => $data['description'] ?? self::generateDescription($action, $data),
                'old_values' => $data['old_values'] ?? null,
                'new_values' => $data['new_values'] ?? null,
                'notes' => $data['notes'] ?? null,
            ];

            Log::create($logData);
        } catch (\Exception $e) {
            // Fallback to Laravel's logging system if database logging fails
            LaravelLog::error('Activity logging failed: ' . $e->getMessage(), [
                'action' => $action,
                'data' => $data,
                'user_id' => Auth::id()
            ]);
        }
    }

    /**
     * Log authentication events
     */
    public static function logAuth(string $action, array $data = []): void
    {
        self::log($action, array_merge($data, [
            'module' => 'authentication',
            'severity' => 'info'
        ]));
    }

    /**
     * Log equipment management events
     */
    public static function logEquipment(string $action, $equipment = null, array $data = []): void
    {
        $logData = [
            'module' => 'equipment',
            'target_type' => 'Equipment',
            'target_id' => $equipment?->id,
            'target_name' => $equipment?->name ?? $equipment?->code,
        ];

        self::log($action, array_merge($logData, $data));
    }

    /**
     * Log user management events
     */
    public static function logUser(string $action, $user = null, array $data = []): void
    {
        $logData = [
            'module' => 'user_management',
            'target_type' => 'User',
            'target_id' => $user?->id,
            'target_name' => $user?->name ?? $user?->email,
        ];

        self::log($action, array_merge($logData, $data));
    }

    /**
     * Log borrow request events
     */
    public static function logBorrowRequest(string $action, $request = null, array $data = []): void
    {
        $logData = [
            'module' => 'borrow_request',
            'target_type' => 'BorrowRequest',
            'target_id' => $request?->id,
            'target_name' => $request?->req_id ?? 'Request #' . $request?->id,
        ];

        self::log($action, array_merge($logData, $data));
    }

    /**
     * Log category management events
     */
    public static function logCategory(string $action, $category = null, array $data = []): void
    {
        $logData = [
            'module' => 'category',
            'target_type' => 'Category',
            'target_id' => $category?->id,
            'target_name' => $category?->name,
        ];

        self::log($action, array_merge($logData, $data));
    }

    /**
     * Log system events
     */
    public static function logSystem(string $action, array $data = []): void
    {
        self::log($action, array_merge($data, [
            'module' => 'system',
            'severity' => $data['severity'] ?? 'info'
        ]));
    }

    /**
     * Log data changes (for updates)
     */
    public static function logDataChange(string $action, $model, array $oldData = [], array $newData = []): void
    {
        $logData = [
            'old_values' => $oldData,
            'new_values' => $newData,
            'description' => "Data changed from: " . json_encode($oldData) . " to: " . json_encode($newData)
        ];

        // Determine module and target type based on model
        $module = strtolower(class_basename($model));
        $targetType = class_basename($model);
        
        $logData['module'] = $module;
        $logData['target_type'] = $targetType;
        $logData['target_id'] = $model->id;
        $logData['target_name'] = $model->name ?? $model->title ?? $model->code ?? "ID: {$model->id}";

        self::log($action, $logData);
    }

    /**
     * Generate description based on action and data
     */
    private static function generateDescription(string $action, array $data): string
    {
        $descriptions = [
            'login' => 'User logged in',
            'logout' => 'User logged out',
            'create' => 'New record created',
            'update' => 'Record updated',
            'delete' => 'Record deleted',
            'approve' => 'Request approved',
            'reject' => 'Request rejected',
            'cancel' => 'Request cancelled',
            'export' => 'Data exported',
            'import' => 'Data imported',
            'view' => 'Record viewed',
            'search' => 'Search performed',
            'filter' => 'Data filtered',
        ];

        $baseDescription = $descriptions[$action] ?? ucfirst($action) . ' action performed';
        
        if (isset($data['target_name'])) {
            $baseDescription .= " for {$data['target_name']}";
        }

        return $baseDescription;
    }

    /**
     * Get log statistics
     */
    public static function getStats(): array
    {
        return [
            'total_logs' => Log::count(),
            'today_logs' => Log::whereDate('created_at', today())->count(),
            'this_week_logs' => Log::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_logs' => Log::whereMonth('created_at', now()->month)->count(),
            'top_actions' => Log::selectRaw('action, COUNT(*) as count')
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'top_modules' => Log::selectRaw('module, COUNT(*) as count')
                ->groupBy('module')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
        ];
    }
}
