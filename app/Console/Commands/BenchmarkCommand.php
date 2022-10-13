<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

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

            $name = Str::of($command::class)
                ->classBasename()
                ->replace('Command', '')
                ->headline();

            $this->info("[Benchmark] {$name}\n");

            Artisan::call($signature, outputBuffer: $outputBuffer);
        }
    }
}
