<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        "id",
        "name",
    ];

    // Relationship with Equipment
    public function equipments()
    {
        return $this->hasMany(\App\Models\Equipment::class, 'categories_id'); 
        // 'categories_id' is the foreign key in the equipments table
    }
}
