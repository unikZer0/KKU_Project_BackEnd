<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentItem extends Model
{
    protected $table = 'equipment_items';

    protected $fillable = [
        'equipment_id',
        'serial_number',
        'condition',
        'status',
        'notes',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id');
    }
    public function accessories()
    {
        return $this->hasMany(EquipmentAccessory::class, 'equipment_item_id');
    }
    public function equipmentType()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
}
