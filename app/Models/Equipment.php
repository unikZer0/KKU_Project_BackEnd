<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Equipment extends Model
{
    protected $table = 'equipments';
    protected $fillable = [
        'code',
        'name',
        'description',
        'category_id',
        'status',
        'photo_path'
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(EquipmentItem::class, 'equipment_id', 'id');
    }

    public function accessories()
    {
        return $this->hasMany(EquipmentAccessory::class, 'equipment_id', 'id');
    }
    public function equipmentItems()
    {
        return $this->hasMany(EquipmentItem::class, 'equipment_id');
    }


    public function getAvailabilityStatusAttribute(): string
    {
        // If no items exist, treat as unavailable
        $totalItems = $this->items_count ?? $this->items()->count();
        if ($totalItems === 0) {
            return 'unavailable';
        }

        $availableItems = $this->available_items_count ?? $this->items()
            ->where('status', 'available')
            ->count();

        // Any available -> available
        if ($availableItems > 0) {
            return 'available';
        }

        // None available -> unavailable
        return 'unavailable';
    }
}
