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
        'name',
        'description',
    ];

    public function borrowRequestItem()
    {
        return $this->belongsTo(BorrowRequestItem::class, 'borrow_request_item_id', 'id');
    }

    public function accessory()
    {
        return $this->belongsTo(EquipmentAccessory::class, 'accessory_id', 'id');
    }
}
