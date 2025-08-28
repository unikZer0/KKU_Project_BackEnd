<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'uid',
        'username',
        'age',
        'email',
        'phonenumber',
        'password',
        'role',
    ];

    public function borrowRequests()
    {
        return $this->hasMany(BorrowRequest::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uid = self::generateUid();

            if (request()) {
                $model->ip_address = request()->ip();
            }
        });
    }

    protected static function generateUid()
    {
        do {
            $uid = 'uid' . strtoupper(Str::random(10));
        } while (self::where('uid', $uid)->exists());

        return $uid;
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
