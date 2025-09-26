<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BorrowRequest;
use App\Models\VerificationRequest;
use App\Notifications\BorrowRequestPickupReminder;
use App\Notifications\BorrowRequestAutoCancelled;
use App\Notifications\BorrowRequestAutoRejected;
use App\Notifications\VerificationRequestAutoRejected;
use Carbon\Carbon;

class ProcessExpiredBorrowRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'borrow:process-expired {--dry-run : Show what would be processed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process expired borrow requests - send reminders, cancel overdue requests, and auto-reject pending requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run') !== null;
        
        if ($dryRun) {
            $this->info(' DRY RUN MODE - No changes will be made');
        }

        $now = now();
        
        // Increase memory limit for this command
        ini_set('memory_limit', '256M');
        
        // Find approved requests that haven't been checked out
        $approvedRequests = BorrowRequest::where('status', 'approved')
            ->where('is_checked_out', false)
            ->with(['user:id,name,email', 'equipment:id,name'])
            ->get();

        $this->info("Found {$approvedRequests->count()} approved requests that haven't been checked out");

        $reminderCount = 0;
        $cancelledCount = 0;
        $autoRejectedBorrowCount = 0;
        $autoRejectedVerificationCount = 0;

        foreach ($approvedRequests as $request) {
            // Set pickup deadline if not set (for existing requests)
            if (!$request->pickup_deadline) {
                $request->pickup_deadline = $request->updated_at->addDays(3);
                if (!$dryRun) {
                    $request->save();
                }
            }

            $daysSinceApproval = $request->updated_at->diffInDays($now);
            $daysUntilDeadline = $now->diffInDays($request->pickup_deadline, false);

            // Send reminder notifications
            if ($daysSinceApproval >= 1 && $daysSinceApproval < 3) {
                $daysRemaining = max(0, 3 - $daysSinceApproval);
                
                if (!$dryRun) {
                    $request->user->notify(new BorrowRequestPickupReminder($request, $daysRemaining));
                }
                
                $reminderCount++;
                $this->line(" Would send reminder to {$request->user->name} for request #{$request->req_id} ({$daysRemaining} days left)");
            }

            // Cancel requests that are overdue
            if ($daysUntilDeadline <= 0 && $daysSinceApproval >= 3) {
                if (!$dryRun) {
                    $request->update([
                        'status' => 'cancelled',
                        'reject_reason' => 'ยกเลิกอัตโนมัติ: ไม่มารับอุปกรณ์ภายใน 3 วัน'
                    ]);
                    
                    $request->user->notify(new BorrowRequestAutoCancelled($request));
                }
                
                $cancelledCount++;
                $this->line(" Would cancel request #{$request->req_id} for {$request->user->name} (overdue by " . abs($daysUntilDeadline) . " days)");
            }
        }

        // Process auto-rejection for pending borrow requests (more than 3 days old)
        $pendingBorrowRequests = BorrowRequest::where('status', 'pending')
            ->where('created_at', '<', $now->subDays(3))
            ->with(['user:id,name,email', 'equipment:id,name'])
            ->get();

        $this->info("Found {$pendingBorrowRequests->count()} pending borrow requests older than 3 days");

        foreach ($pendingBorrowRequests as $request) {
            if (!$dryRun) {
                $request->update([
                    'status' => 'rejected',
                    'reject_reason' => 'ปฏิเสธอัตโนมัติ: ไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน'
                ]);
                
                if ($request->user) {
                    $request->user->notify(new BorrowRequestAutoRejected($request));
                }
            }
            
            $autoRejectedBorrowCount++;
            $this->line(" Would auto-reject borrow request #{$request->req_id} for {$request->user->name} (pending for " . $request->created_at->diffInDays($now) . " days)");
        }

        // Process auto-rejection for pending verification requests (more than 3 days old)
        $pendingVerificationRequests = VerificationRequest::where('status', 'pending')
            ->where('created_at', '<', $now->subDays(3))
            ->with(['user:id,name,email'])
            ->get();

        $this->info("Found {$pendingVerificationRequests->count()} pending verification requests older than 3 days");

        foreach ($pendingVerificationRequests as $request) {
            if (!$dryRun) {
                $request->update([
                    'status' => 'rejected',
                    'admin_notes' => 'ปฏิเสธอัตโนมัติ: ไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน',
                    'processed_at' => now(),
                ]);
                
                if ($request->user) {
                    $request->user->notify(new VerificationRequestAutoRejected($request));
                }
            }
            
            $autoRejectedVerificationCount++;
            $this->line(" Would auto-reject verification request #{$request->id} for {$request->user->name} (pending for " . $request->created_at->diffInDays($now) . " days)");
        }

        if ($dryRun) {
            $this->info(" SUMMARY (DRY RUN):");
            $this->info("- Reminders to send: {$reminderCount}");
            $this->info("- Requests to cancel: {$cancelledCount}");
            $this->info("- Borrow requests to auto-reject: {$autoRejectedBorrowCount}");
            $this->info("- Verification requests to auto-reject: {$autoRejectedVerificationCount}");
        } else {
            $this->info(" PROCESSING COMPLETE:");
            $this->info("- Reminders sent: {$reminderCount}");
            $this->info("- Requests cancelled: {$cancelledCount}");
            $this->info("- Borrow requests auto-rejected: {$autoRejectedBorrowCount}");
            $this->info("- Verification requests auto-rejected: {$autoRejectedVerificationCount}");
        }

        return 0;
    }
}
