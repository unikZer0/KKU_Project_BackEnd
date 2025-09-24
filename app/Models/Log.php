<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';
    
    protected $fillable = [
        'users_id',
        'action',
        'ip_address',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
