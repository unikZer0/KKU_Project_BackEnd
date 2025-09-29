<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    protected $table = 'logs';
    
    protected $fillable = [
        'users_id',
        'action',
        'ip_address',
        'module',
        'target_type',
        'target_id',
        'target_name',
        'description',
        'old_values',
        'new_values',
        'user_agent',
        'session_id',
        'request_method',
        'request_url',
        'request_data',
        'severity',
        'notes',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'request_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Scope for filtering by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for filtering by module
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope for filtering by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope for filtering by target type
     */
    public function scopeByTargetType($query, $targetType)
    {
        return $query->where('target_type', $targetType);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('users_id', $userId);
    }

    /**
     * Get formatted description
     */
    public function getFormattedDescriptionAttribute(): string
    {
        if ($this->description) {
            return $this->description;
        }

        $action = ucfirst($this->action);
        $target = $this->target_name ? " for {$this->target_name}" : '';
        
        return "{$action} action performed{$target}";
    }

    /**
     * Get severity badge class
     */
    public function getSeverityBadgeClassAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'bg-red-100 text-red-800',
            'error' => 'bg-red-100 text-red-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'info' => 'bg-blue-100 text-blue-800',
            'success' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get action badge class
     */
    public function getActionBadgeClassAttribute(): string
    {
        return match($this->action) {
            'create' => 'bg-green-100 text-green-800',
            'update' => 'bg-blue-100 text-blue-800',
            'delete' => 'bg-red-100 text-red-800',
            'login' => 'bg-green-100 text-green-800',
            'logout' => 'bg-gray-100 text-gray-800',
            'approve' => 'bg-green-100 text-green-800',
            'reject' => 'bg-red-100 text-red-800',
            'cancel' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
