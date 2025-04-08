<?php

namespace App\Services;

use App\Models\Waitlist;
class WaitlistService
{
    public static function create(array $data)
    {
        return Waitlist::create([
            'email' => $data['email'],
            'name' => $data['name'],
        ]);
    }
}
