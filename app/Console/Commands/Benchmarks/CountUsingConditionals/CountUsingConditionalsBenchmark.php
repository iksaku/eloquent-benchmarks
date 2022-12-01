<?php

namespace App\Console\Commands\Benchmarks\CountUsingConditionals;

use App\Console\Commands\BenchmarkCommand;
use App\Console\Commands\Benchmarks\CountUsingConditionals\Models\Ticket;
use App\Console\Commands\Benchmarks\CountUsingConditionals\Util\TicketStatus;
use App\Util\Benchmark\Benchmark;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CountUsingConditionalsBenchmark extends BenchmarkCommand
{
    protected $signature = 'benchmark:count-using-conditionals';

    public function handle(): void
    {
        Benchmark::make('Count using Conditionals', $this)
            ->measure([
                'Apply count conditions in code' => function () {
                    $counter = [
                        TicketStatus::Requested->value => 0,
                        TicketStatus::Planned->value => 0,
                        TicketStatus::Completed->value => 0,
                    ];

                    Ticket::query()
                        ->get('status')
                        ->each(function (Ticket $ticket) use (&$counter) {
                            $counter[$ticket->status->value]++;
                        });
                },
                'Apply count conditions with multiple queries ' => function () {
                    $counter = [
                        TicketStatus::Requested->value => Ticket::query()
                            ->where('status', TicketStatus::Requested->value)
                            ->count(),
                        TicketStatus::Planned->value => Ticket::query()
                            ->where('status', TicketStatus::Planned->value)
                            ->count(),
                        TicketStatus::Completed->value => Ticket::query()
                            ->where('status', TicketStatus::Completed->value)
                            ->count(),
                    ];
                },
                'Mimic count conditions using row grouping' => function () {
                    $counter = Ticket::query()
                        ->toBase()
                        ->selectRaw('status, count(*) as count')
                        ->groupBy('status')
                        ->pluck('count', 'status')
                        ->toArray();
                },
                'Apply count conditions using Case statement' => function () {
                    $counter = (array) Ticket::query()
                        ->toBase()
                        ->selectRaw("count(case when status = 'requested' then 1 end) as requested")
                        ->selectRaw("count(case when status = 'planned' then 1 end) as planned")
                        ->selectRaw("count(case when status = 'completed' then 1 end) as completed")
                        ->first();
                },
                'Apply count conditions using Filter statement' => function () {
                    $counter = (array) Ticket::query()
                        ->toBase()
                        ->selectRaw("count(*) filter (where status = 'requested') as requested")
                        ->selectRaw("count(*) filter (where status = 'planned') as planned")
                        ->selectRaw("count(*) filter (where status = 'completed') as completed")
                        ->first();
                },
            ]);
    }

    protected function migrate(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->enum('status', TicketStatus::values());

            $table->timestamps();
        });
    }

    protected function seed(): void
    {
        Ticket::factory()
            ->count(1000)
            ->create();
    }
}
