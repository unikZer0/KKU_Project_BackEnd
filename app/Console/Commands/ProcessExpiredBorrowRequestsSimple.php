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

class ProcessExpiredBorrowRequestsSimple extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'borrow:process-simple {--dry-run : Show what would be processed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simple version: Process expired borrow requests with memory optimization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info(' DRY RUN MODE - No changes will be made');
        }

        $now = now();
        $threeDaysAgo = $now->copy()->subDays(3);
        
        $this->info("Processing requests older than: {$threeDaysAgo}");
        $this->line("");

        // Process borrow requests in chunks to avoid memory issues
        $this->processBorrowRequests($now, $threeDaysAgo, $dryRun);
        
        // Process verification requests in chunks
        $this->processVerificationRequests($now, $threeDaysAgo, $dryRun);

        $this->info('Processing completed successfully!');
        return 0;
    }

    private function processBorrowRequests($now, $threeDaysAgo, $dryRun)
    {
        $this->info('📋 Processing borrow requests...');
        
        // Process approved requests that need pickup reminders
        $approvedCount = BorrowRequest::where('status', 'approved')
            ->where('is_checked_out', false)
            ->whereNotNull('pickup_deadline')
            ->where('pickup_deadline', '<', $now)
            ->count();
            
        if ($approvedCount > 0) {
            $this->info("Found {$approvedCount} approved requests that need pickup reminders");
        }

        // Process pending requests for auto-rejection
        $pendingCount = BorrowRequest::where('status', 'pending')
            ->where('created_at', '<', $threeDaysAgo)
            ->count();
            
        if ($pendingCount > 0) {
            $this->info("Found {$pendingCount} pending requests eligible for auto-rejection");
            
            if (!$dryRun) {
                // Process in small batches
                BorrowRequest::where('status', 'pending')
                    ->where('created_at', '<', $threeDaysAgo)
                    ->chunk(10, function ($requests) {
                        foreach ($requests as $request) {
                            $this->autoRejectBorrowRequest($request);
                        }
                    });
            }
        }
    }

    private function processVerificationRequests($now, $threeDaysAgo, $dryRun)
    {
        $this->info('🔐 Processing verification requests...');
        
        $pendingCount = VerificationRequest::where('status', 'pending')
            ->where('created_at', '<', $threeDaysAgo)
            ->count();
            
        if ($pendingCount > 0) {
            $this->info("Found {$pendingCount} pending verification requests eligible for auto-rejection");
            
            if (!$dryRun) {
                // Process in small batches
                VerificationRequest::where('status', 'pending')
                    ->where('created_at', '<', $threeDaysAgo)
                    ->chunk(10, function ($requests) {
                        foreach ($requests as $request) {
                            $this->autoRejectVerificationRequest($request);
                        }
                    });
            }
        }
    }

    private function autoRejectBorrowRequest($request)
    {
        try {
            $request->update([
                'status' => 'rejected',
                'reject_reason' => 'ปฏิเสธอัตโนมัติ: ไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน'
            ]);
            
            if ($request->user) {
                $request->user->notify(new BorrowRequestAutoRejected($request));
            }
            
            $this->line("✅ Auto-rejected borrow request #{$request->req_id}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to auto-reject borrow request #{$request->req_id}: " . $e->getMessage());
        }
    }

    private function autoRejectVerificationRequest($request)
    {
        try {
            $request->update([
                'status' => 'rejected',
                'admin_notes' => 'ปฏิเสธอัตโนมัติ: ไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน',
                'processed_at' => now(),
            ]);
            
            if ($request->user) {
                $request->user->notify(new VerificationRequestAutoRejected($request));
            }
            
            $this->line("✅ Auto-rejected verification request #{$request->id}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to auto-reject verification request #{$request->id}: " . $e->getMessage());
        }
    }
}
