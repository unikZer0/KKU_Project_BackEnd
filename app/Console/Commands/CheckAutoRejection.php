<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BorrowRequest;
use App\Models\VerificationRequest;
use Carbon\Carbon;

class CheckAutoRejection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:auto-rejection {req_id? : Specific request ID to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check which requests would be auto-rejected due to admin inactivity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reqId = $this->argument('req_id');
        $now = now();
        $threeDaysAgo = $now->subDays(3);

        $this->info("🔍 Checking auto-rejection criteria...");
        $this->info("Current time: {$now}");
        $this->info("3 days ago: {$threeDaysAgo}");
        $this->line("");

        if ($reqId) {
            // Check specific request
            $this->checkSpecificRequest($reqId, $now, $threeDaysAgo);
        } else {
            // Check all pending requests
            $this->checkAllPendingRequests($now, $threeDaysAgo);
        }

        return 0;
    }

    private function checkSpecificRequest($reqId, $now, $threeDaysAgo)
    {
        $this->info("📋 Checking specific request: {$reqId}");
        $this->line("");

        // Check borrow request
        $borrowRequest = BorrowRequest::where('req_id', $reqId)->first();
        if ($borrowRequest) {
            $this->displayBorrowRequestInfo($borrowRequest, $now, $threeDaysAgo);
        } else {
            $this->warn("❌ Borrow request not found: {$reqId}");
        }

        // Check verification requests for the same user
        if ($borrowRequest) {
            $verificationRequests = VerificationRequest::where('user_id', $borrowRequest->users_id)
                ->where('status', 'pending')
                ->get();

            if ($verificationRequests->count() > 0) {
                $this->line("");
                $this->info("🔐 User's verification requests:");
                foreach ($verificationRequests as $verificationRequest) {
                    $this->displayVerificationRequestInfo($verificationRequest, $now, $threeDaysAgo);
                }
            }
        }
    }

    private function checkAllPendingRequests($now, $threeDaysAgo)
    {
        $this->info("📊 Checking all pending requests...");
        $this->line("");

        // Check borrow requests
        $pendingBorrowRequests = BorrowRequest::where('status', 'pending')
            ->with(['user', 'equipment'])
            ->get();

        $this->info("📋 Pending Borrow Requests ({$pendingBorrowRequests->count()}):");
        foreach ($pendingBorrowRequests as $request) {
            $this->displayBorrowRequestInfo($request, $now, $threeDaysAgo);
        }

        $this->line("");

        // Check verification requests
        $pendingVerificationRequests = VerificationRequest::where('status', 'pending')
            ->with(['user'])
            ->get();

        $this->info("🔐 Pending Verification Requests ({$pendingVerificationRequests->count()}):");
        foreach ($pendingVerificationRequests as $request) {
            $this->displayVerificationRequestInfo($request, $now, $threeDaysAgo);
        }
    }

    private function displayBorrowRequestInfo($request, $now, $threeDaysAgo)
    {
        $daysSinceCreation = $request->created_at->diffInDays($now);
        $shouldAutoReject = $request->created_at->lt($threeDaysAgo);
        
        $status = $shouldAutoReject ? "WOULD BE AUTO-REJECTED" : " Still within 3-day limit";
        
        $this->line("   Request #{$request->req_id}");
        $this->line("     User: {$request->user->name}");
        $this->line("     Equipment: {$request->equipment->name}");
        $this->line("     Created: {$request->created_at->format('Y-m-d H:i:s')}");
        $this->line("     Days since creation: " . abs($daysSinceCreation));
        $this->line("     Status: {$status}");
        $this->line("");
    }

    private function displayVerificationRequestInfo($request, $now, $threeDaysAgo)
    {
        $daysSinceCreation = $request->created_at->diffInDays($now);
        $shouldAutoReject = $request->created_at->lt($threeDaysAgo);
        
        $status = $shouldAutoReject ? " WOULD BE AUTO-REJECTED" : " Still within 3-day limit";
        
        $this->line("   Verification Request #{$request->id}");
        $this->line("     User: {$request->user->name}");
        $this->line("     Created: {$request->created_at->format('Y-m-d H:i:s')}");
        $this->line("     Days since creation: " . abs($daysSinceCreation));
        $this->line("     Status: {$status}");
        $this->line("");
    }
}
