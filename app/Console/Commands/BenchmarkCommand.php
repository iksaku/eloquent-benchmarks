<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BenchmarkCommand extends Command
{
    protected $signature = 'benchmark';

    public function handle()
    {
        DB::setDefaultConnection('sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

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

            $this->isolate($command);
        }
    }

    protected function isolate(Command $command)
    {
        DB::reconnect();
        DB::beginTransaction();

        $name = Str::of($command::class)
            ->classBasename()
            ->replace('Command', '')
            ->headline();

        $this->comment("[Benchmark] Preparing '{$name}'...");

        if (method_exists($command, 'migrate')) {
            $command->migrate();
        }

        if (method_exists($command, 'seed')) {
            $command->seed();
        }

        $this->info("[Benchmark] Running '{$name}'...\n");

        $this->call($command::class);

        DB::rollBack();
    }
}
