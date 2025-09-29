<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\BorrowRequest;
use App\Notifications\BorrowReturnReminder;
use App\Services\LateReturnService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send return reminders: one day before end_at and at 12:00 on end_at
Artisan::command('reminders:returns', function () {
    $now = now();
    $today = $now->toDateString();
    $tomorrow = $now->copy()->addDay()->toDateString();

    // Only consider active/approved/checked out requests
    $activeStatuses = ['approved', 'check_out', 'pending'];

    // Due tomorrow (any time of day)
    $dueTomorrow = BorrowRequest::with(['user', 'equipment'])
        ->whereDate('end_at', $tomorrow)
        ->whereIn('status', $activeStatuses)
        ->get();

    foreach ($dueTomorrow as $req) {
        if ($req->user) {
            $req->user->notify(new BorrowReturnReminder($req, 'due_tomorrow'));
        }
    }

    // Due today at 12:00 (noon). We run only around noon; rely on scheduler to run at 12:00
    if ((int) $now->format('H') === 12) {
        $dueToday = BorrowRequest::with(['user', 'equipment'])
            ->whereDate('end_at', $today)
            ->whereIn('status', $activeStatuses)
            ->get();

        foreach ($dueToday as $req) {
            if ($req->user) {
                $req->user->notify(new BorrowReturnReminder($req, 'due_today'));
            }
        }
    }

    $this->info('Return reminders processed at ' . $now);
})->purpose('Send reminders for equipment return deadlines');

// Process expired borrow requests (pickup reminders and auto-cancellation)
Artisan::command('borrow:process-expired', function () {
    $this->call('borrow:process-expired');
})->purpose('Process expired borrow requests - send reminders and cancel overdue requests')
->daily();

// Check for late returns and send notifications
Artisan::command('borrow:check-late-returns', function () {
    $lateReturnService = new LateReturnService();
    $lateCount = $lateReturnService->checkLateReturns();
    
    if ($lateCount > 0) {
        $this->info("Found {$lateCount} late returns and sent notifications.");
    } else {
        $this->info('No late returns found.');
    }
})->purpose('Check for late equipment returns and send penalty notifications')
->daily();
