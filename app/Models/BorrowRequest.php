<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRequest extends Model
{
    protected $table = 'borrow_requests';
    protected $fillable = [
        'user_id',
        'equipment_id',
        'start_date',
        'end_date',
        'status',
        'reject_reason'
    ];

    protected function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id');
    }
}
