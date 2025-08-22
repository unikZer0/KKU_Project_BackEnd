<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipments extends Model
{
    protected $fillable = [
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
