<?php

namespace App\Console\Commands;

use App\Console\Commands\Benchmark\Concerns\CalculatesQueryPerformance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class BenchmarkCommand extends Command
{
    protected $signature = 'benchmark';

    public function handle()
    {
        $outputBuffer = $this->getOutput()->getOutput();

        $firstRun = true;

        foreach (Artisan::all() as $signature => $command) {
            if (! str_starts_with($signature, 'benchmark:')) {
                continue;
            }

            if ($firstRun) {
                $firstRun = false;
            } else {
                $this->newLine();
                $this->line(str_repeat('-', 80));
                $this->newLine();
            }

            /** @var CalculatesQueryPerformance $command */
            $this->info("[Benchmark] {$command::benchmarkName()}\n");

            Artisan::call($signature, outputBuffer: $outputBuffer);
        }
    }
}
