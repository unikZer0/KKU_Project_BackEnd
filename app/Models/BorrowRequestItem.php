<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRequestItem extends Model
{
    protected $table = 'borrow_request_items';

    protected $fillable = [
        'borrow_request_id',
        'equipment_item_id',
        'quantity',
        'condition_out',
        'condition_in',
    ];

    public function request()
    {
        return $this->belongsTo(BorrowRequest::class, 'borrow_request_id', 'id');
    }

    public function equipmentItem()
    {
        return $this->belongsTo(EquipmentItem::class, 'equipment_item_id', 'id');
    }

    public function accessories()
    {
        return $this->hasMany(BorrowRequestAccessory::class, 'borrow_request_item_id', 'id');
    }
}


