<?php

namespace App\Console\Commands\Benchmarks\UniqueRecords;

use App\Console\Commands\BenchmarkCommand;
use App\Console\Commands\Benchmarks\UniqueRecords\Models\Trip;
use App\Console\Commands\Benchmarks\UniqueRecords\Models\User;
use App\Util\Benchmark\Benchmark;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UniqueRecordsBenchmark extends BenchmarkCommand
{
    protected $signature = 'benchmark:unique-records';

    public function handle(): void
    {
        $user_id = 1;

        Benchmark::make('Display Unique Records', $this)
            ->measure([
                'Filter using arrays' => function () use ($user_id) {
                    $countries = [];

                    Trip::query()
                        ->where('user_id', $user_id)
                        ->get()
                        ->each(function (Trip $trip) use (&$countries) {
                            if (! in_array($trip->country, $countries)) {
                                $countries[] = $trip->country;
                            }
                        });
                },
                'Filter using collections' => function () use ($user_id) {
                    Trip::query()
                        ->where('user_id', $user_id)
                        ->get()
                        ->unique('country');
                },
                'Filter using database' => function () use ($user_id) {
                    Trip::query()
                        ->distinct('country')
                        ->where('user_id', $user_id)
                        ->pluck('country');
                },
            ]);
    }

    protected function migrate(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->timestamps();
        });

        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained();

            $table->string('country');
            $table->timestamp('visited_at');
        });
    }

    protected function seed(): void
    {
        User::factory()
            ->has(
                Trip::factory()
                    ->count(1000)
            )
            ->create();
    }
}
