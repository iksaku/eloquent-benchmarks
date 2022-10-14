<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BenchmarkCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        DB::setDefaultConnection('sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::reconnect();

        DB::beginTransaction();

        $name = Str::of(static::class)
            ->classBasename()
            ->before('Command')
            ->headline();

        $this->comment("[Benchmark] Preparing '{$name}'...");

        if (method_exists($this, 'migrate')) {
            $this->migrate();
        }

        if (method_exists($this, 'seed')) {
            $this->seed();
        }

        $this->info("[Benchmark] Running '{$name}'...\n");

        return tap(parent::execute($input, $output), function () {
            DB::rollBack();
        });
    }
}
