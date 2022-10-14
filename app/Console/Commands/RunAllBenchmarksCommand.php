<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunAllBenchmarksCommand extends Command
{
    protected $signature = 'benchmark {benchmark? : The name of the benchmark to run}';

    public function handle()
    {
        $firstRun = true;

        foreach (Artisan::all() as $command) {
            if (! ($command instanceof BenchmarkCommand)) {
                continue;
            }

            if ($firstRun) {
                $firstRun = false;
            } else {
                $this->line(str_repeat('-', 80));
                $this->newLine();
            }

            $this->call($command::class);
        }
    }
}
