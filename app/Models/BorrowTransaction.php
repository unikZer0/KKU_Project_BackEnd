<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowTransaction extends Model
{
    protected $table = 'borrow_transactions';

    protected $fillable = [
        'borrow_request_id',
        'checkout_at',
        'check_in_at',
        'penalty_amount',
        'notes'
    ];
}
