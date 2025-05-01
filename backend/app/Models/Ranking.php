<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    public const RANKS = [
        'E' => 0,
        'D' => 500,
        'C' => 2000,
        'B' => 3000,
        'A' => 5000,
        'S' => 15000,
        'Nation' => 300000,
    ];
}
