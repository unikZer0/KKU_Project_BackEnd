<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowRequestAccessory extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrow_request_item_id',
        'accessory_id',
        'condition_out',
        'condition_in',
    ];

    public function borrowRequestItem()
    {
        return $this->belongsTo(BorrowRequestItem::class);
    }
}
