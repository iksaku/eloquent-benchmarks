<?php

namespace App\Console\Commands\Benchmarks\CountUsingConditionals\Util;

use Illuminate\Support\Arr;

enum TicketStatus: string
{
    case Requested = 'requested';
    case Planned = 'planned';
    case Completed = 'completed';

    public static function values(): array
    {
        return Arr::pluck(TicketStatus::cases(), 'value');
    }
}
