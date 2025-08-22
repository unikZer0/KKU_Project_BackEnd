<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipments';
    protected $fillable = [
        "id",
        "code",
        "name",
        "categories_id",
        "status",
        "photo_path"
    ];
    public function categories()
{
    return $this->belongsTo(Categories::class, 'categories_id');
}

}
