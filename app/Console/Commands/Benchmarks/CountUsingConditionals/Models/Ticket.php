<?php

namespace App\Console\Commands\Benchmarks\CountUsingConditionals\Models;

use App\Console\Commands\Benchmarks\CountUsingConditionals\Util\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'status' => TicketStatus::class,
    ];
}
