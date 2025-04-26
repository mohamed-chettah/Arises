<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionFocus extends Model
{
    protected $fillable = [
        'user_id',
        'started_at',
        'paused_at',
        'finished_at',
        'total_paused_time',
        'xp_earned',
        'expected_duration',
        'status',
        'is_valid'
    ];
}
