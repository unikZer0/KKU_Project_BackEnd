<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentItem extends Model
{
    protected $table = 'equipment_items';

    protected $fillable = [
        'equipment_type_id',
        'serial_number',
        'condition',
        'status',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_type_id', 'id');
    }
    public function accessories()
{
    return $this->hasMany(EquipmentAccessory::class, 'equipment_item_id');
}
public function equipmentType()
    {
        // This assumes your 'equipment_items' table has a foreign key
        // named 'equipment_type_id' that links to the 'id' on the 'equipment' table.
        return $this->belongsTo(Equipment::class, 'equipment_type_id');
    }

}


