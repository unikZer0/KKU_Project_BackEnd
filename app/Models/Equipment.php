<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Equipment extends Model
{
    protected $table = 'equipments';
    protected $fillable = [
        "id",
        "code",
        "name",
        "description",
        "categories_id",
        "status",
        "photo_path"
    ];

    protected static function generateEcode()
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->code = self::generateEcode();
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id', 'id');
    }
}
