<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRequest extends Model
{
    protected $table = 'borrow_requests';
    protected $fillable = [
    'users_id',     
    'equipments_id', 
    'start_at',      
    'end_at',       
    'status',
    'reject_reason'
];

public function user()
{
    return $this->belongsTo(User::class, 'users_id', 'id');
}

public function equipment()
{
    return $this->belongsTo(Equipment::class, 'equipments_id', 'id');
}

}
