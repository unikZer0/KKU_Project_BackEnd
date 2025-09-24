<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentAccessory extends Model
{
    protected $table = 'equipment_accessories';

    protected $fillable = [
        'equipment_type_id',
        'equipment_item_id',
        'name',
        'serial_number',
        'condition_',
        'status',
        'description'
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_type_id', 'id');
    }
public function equipmentItem()
{
    return $this->belongsTo(EquipmentItem::class, 'equipment_item_id');
}
public function equipmentItems()
{
    return $this->belongsTo(Equipment::class, 'equipment_type_id')
                ->with('equipmentItems'); // optional for eager loading
}



}


