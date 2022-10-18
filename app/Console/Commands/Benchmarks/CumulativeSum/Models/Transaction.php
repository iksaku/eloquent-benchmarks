<?php

namespace App\Console\Commands\Benchmarks\CumulativeSum\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];
}
