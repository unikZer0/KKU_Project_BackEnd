<?php

namespace App\Traits;

use App\Services\ActivityLogService;

trait LogsActivity
{
    /**
     * Log activity with automatic context detection
     */
    protected function logActivity(string $action, $model = null, array $data = []): void
    {
        if ($model) {
            $data['target_id'] = $model->id;
            $data['target_name'] = $model->name ?? $model->title ?? $model->code ?? "ID: {$model->id}";
            $data['target_type'] = class_basename($model);
        }

        ActivityLogService::log($action, $data);
    }

    /**
     * Log authentication events
     */
    protected function logAuth(string $action, array $data = []): void
    {
        ActivityLogService::logAuth($action, $data);
    }

    /**
     * Log equipment events
     */
    protected function logEquipment(string $action, $equipment = null, array $data = []): void
    {
        ActivityLogService::logEquipment($action, $equipment, $data);
    }

    /**
     * Log user management events
     */
    protected function logUser(string $action, $user = null, array $data = []): void
    {
        ActivityLogService::logUser($action, $user, $data);
    }

    /**
     * Log borrow request events
     */
    protected function logBorrowRequest(string $action, $request = null, array $data = []): void
    {
        ActivityLogService::logBorrowRequest($action, $request, $data);
    }

    /**
     * Log category events
     */
    protected function logCategory(string $action, $category = null, array $data = []): void
    {
        ActivityLogService::logCategory($action, $category, $data);
    }

    /**
     * Log system events
     */
    protected function logSystem(string $action, array $data = []): void
    {
        ActivityLogService::logSystem($action, $data);
    }

    /**
     * Log data changes
     */
    protected function logDataChange(string $action, $model, array $oldData = [], array $newData = []): void
    {
        ActivityLogService::logDataChange($action, $model, $oldData, $newData);
    }
}
