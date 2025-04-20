<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    protected $fillable = [
        'email',
        'name',
        'verification_token',
        'verified',
        'verified_at',
    ];
}
