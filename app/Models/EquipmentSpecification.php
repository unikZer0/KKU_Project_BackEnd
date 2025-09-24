<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentSpecification extends Model
{
    public $timestamps = false; // Disable timestamps since the table doesn't have created_at/updated_at columns
    
    protected $fillable = [
        'equipment_id',
        'spec_key',
        'spec_value_text',
        'spec_value_number',
        'spec_value_bool'
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'id');
    }
}
