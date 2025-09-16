<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use App\Models\BorrowRequest;

trait ClearsDashboardCache
{
    protected function clearDashboardCache(): void
    {
        $year = now()->year;
        Cache::forget("admin_dashboard_{$year}");

        $availableYears = BorrowRequest::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year');

        foreach ($availableYears as $y) {
            Cache::forget("admin_dashboard_{$y}");
        }

        Cache::forget('borrow_requests_pending');
        Cache::forget('borrow_requests_recent');
    }
}
