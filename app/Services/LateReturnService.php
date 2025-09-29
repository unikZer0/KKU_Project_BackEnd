<?php

namespace App\Services;

use App\Models\BorrowRequest;
use App\Models\BorrowTransaction;
use App\Notifications\BorrowRequestLateReturn;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LateReturnService
{
    /**
     * Check for late returns and send notifications
     */
    public function checkLateReturns()
    {
        $lateRequests = $this->getLateRequests();
        
        foreach ($lateRequests as $request) {
            $this->handleLateReturn($request);
        }
        
        return count($lateRequests);
    }

    /**
     * Get all requests that are late
     */
    private function getLateRequests()
    {
        $today = Carbon::today();
        
        return BorrowRequest::with(['user', 'equipment', 'transaction'])
            ->where('status', 'check_out')
            ->where('end_at', '<', $today)
            ->whereDoesntHave('transaction', function ($query) {
                $query->whereNotNull('checked_in_at');
            })
            ->get();
    }

    /**
     * Handle a specific late return
     */
    private function handleLateReturn(BorrowRequest $request)
    {
        $daysLate = $this->calculateDaysLate($request);
        $penaltyAmount = $this->calculatePenalty($request, $daysLate);
        
        // Update penalty amount in transaction
        $this->updatePenalty($request, $penaltyAmount);
        
        // Send notification to user
        $this->sendLateNotification($request, $daysLate, $penaltyAmount);
        
        // Log the late return
        Log::info("Late return detected", [
            'request_id' => $request->req_id,
            'user_id' => $request->users_id,
            'days_late' => $daysLate,
            'penalty_amount' => $penaltyAmount
        ]);
    }

    /**
     * Calculate how many days late the return is
     */
    private function calculateDaysLate(BorrowRequest $request)
    {
        $endDate = Carbon::parse($request->end_at);
        $today = Carbon::today();
        
        return $today->diffInDays($endDate);
    }

    /**
     * Calculate penalty amount based on days late
     */
    private function calculatePenalty(BorrowRequest $request, $daysLate)
    {
        // Base penalty rate (configurable)
        $penaltyRate = 50; // ฿50 per day
        
        // Calculate base penalty
        $basePenalty = $daysLate * $penaltyRate;
        
        // Progressive penalty for very late returns
        if ($daysLate > 7) {
            $basePenalty += ($daysLate - 7) * 100; // Additional ฿100 per day after 7 days
        }
        
        // Maximum penalty cap
        $maxPenalty = 2000; // ฿2000 maximum
        
        return min($basePenalty, $maxPenalty);
    }

    /**
     * Update penalty amount in transaction
     */
    private function updatePenalty(BorrowRequest $request, $penaltyAmount)
    {
        $transaction = $request->transaction;
        
        if (!$transaction) {
            $transaction = new BorrowTransaction();
            $transaction->borrow_requests_id = $request->id;
        }
        
        $transaction->penalty_amount = $penaltyAmount;
        $transaction->save();
    }

    /**
     * Send late return notification to user
     */
    private function sendLateNotification(BorrowRequest $request, $daysLate, $penaltyAmount)
    {
        $user = $request->user;
        
        if ($user) {
            $user->notify(new BorrowRequestLateReturn($request, $daysLate, $penaltyAmount));
        }
    }

    /**
     * Get penalty rate configuration
     */
    public function getPenaltyRate()
    {
        return [
            'daily_rate' => 50, // ฿50 per day
            'progressive_rate' => 100, // ฿100 per day after 7 days
            'max_penalty' => 2000, // ฿2000 maximum
            'progressive_threshold' => 7 // days
        ];
    }

    /**
     * Calculate penalty for a specific request
     */
    public function calculatePenaltyForRequest(BorrowRequest $request)
    {
        $daysLate = $this->calculateDaysLate($request);
        return $this->calculatePenalty($request, $daysLate);
    }

    /**
     * Check if a request is late
     */
    public function isLate(BorrowRequest $request)
    {
        if ($request->status !== 'check_out') {
            return false;
        }
        
        $endDate = Carbon::parse($request->end_at);
        $today = Carbon::today();
        
        return $today->gt($endDate);
    }

    /**
     * Get days late for a specific request
     */
    public function getDaysLate(BorrowRequest $request)
    {
        if (!$this->isLate($request)) {
            return 0;
        }
        
        return $this->calculateDaysLate($request);
    }
}
