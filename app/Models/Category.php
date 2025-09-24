<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'cate_id',
        "name",
        "description"
    ];

    protected static function generateEcode()
    {
        do {
            $cate_id = strtoupper(Str::random(10));
        } while (self::where('cate_id', $cate_id)->exists());

        return $cate_id;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->cate_id = self::generateEcode();
        });
    }

    // Add this relationship
    public function equipments()
    {
        return $this->hasMany(Equipment::class, 'category_id', 'id');
    }
}
