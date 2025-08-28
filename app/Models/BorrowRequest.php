<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BorrowRequest extends Model
{
    protected $table = 'borrow_requests';
    protected $fillable = [
        'id',
        'users_id',
        'req_id',
        'equipments_id',
        'start_at',
        'end_at',
        'status',
        'reject_reason',
        'cancel_reason',
        'uid',
        'username',
        'age',
        'photo_path',
        'email',
        'phonenumber',
        "code",
        "name",
        "description",
        "categories_id",
    ];
protected $casts = [
    'start_at' => 'datetime',
    'end_at' => 'datetime',
];
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipments_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id', 'id');
    }
    protected static function generatereq_id()
    {
        do {
            $req_id = 'REQ' . strtoupper(Str::random(10));
        } while (self::where('req_id', $req_id)->exists());

        return $req_id;
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->req_id = self::generatereq_id();
        });
    }
}
