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
        'categories_id',
        'brand',
        'model',
        'specification',
        'photo_path'
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id', 'id');
    }
}
