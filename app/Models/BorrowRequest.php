<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BorrowRequest extends Model
{
    protected $table = 'borrow_requests';

    protected $fillable = [
        'users_id',
        'req_id',
        'start_at',
        'end_at',
        'status',
        'request_reason',
        'reject_reason',
        'cancel_reason',
        'photo_path',
        'email',
        'phonenumber',
        'code',
        'name',
        'description',
        'categories_id',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function equipment()
    {
        return $this->belongsTo(BorrowRequestItem::class, 'equipment_id');
    }

    // Access equipment via items if needed
    public function items()
    {
        return $this->hasMany(BorrowRequestItem::class, 'borrow_request_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }

    public function transaction()
    {
        return $this->hasOne(BorrowTransaction::class, 'borrow_requests_id');
    }

    // Generate unique req_id
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

    protected static function booted()
    {
        static::created(function ($request) {
            $request->transaction()->create([
                'penalty_amount' => 0,
                'checked_out_at' => null,
                'checked_in_at'   => null,
                'notes'         => null,
            ]);
        });
    }
}
