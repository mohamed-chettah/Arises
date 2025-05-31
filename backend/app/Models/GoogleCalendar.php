<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleCalendar extends Model
{
    protected $fillable = [
        'google_id',
        'access_token',
        'refresh_token',
        'expires_in',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
