<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    public const RANKS = [
        'E' => 0,
        'D' => 100,
        'C' => 500,
        'B' => 2000,
        'A' => 5000,
        'S' => 15000,
        'Nation' => 300000,
    ];
}
