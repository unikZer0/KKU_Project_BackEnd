<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\BorrowRequest;

class BorrowTransaction extends Model
{
    protected $table = 'borrow_transactions';

    protected $fillable = [
        'borrow_requests_id',
        'checked_out_at',
        'checked_in_at',
        'penalty_amount',
        'notes'
    ];

    public function borrowRequest()
    {
        return $this->belongsTo(BorrowRequest::class, 'borrow_requests_id');
    }

    public function calculatePenalty($penaltyRate = 50)
    {
        if (!$this->checked_in_at || !$this->borrowRequest) {
            return 0;
        }

        $endAt = $this->borrowRequest->end_at;
        $checkInAt = $this->checked_in_at;

        if ($checkInAt->greaterThan($endAt)) {
            // Late seconds
            $secondsLate = $checkInAt->timestamp - $endAt->timestamp;

            // Convert to days, round up
            $lateDays = ceil($secondsLate / 86400);

            return $lateDays * $penaltyRate;
        }

        return 0;
    }
}
