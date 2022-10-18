<?php

namespace App\Console\Commands\Benchmarks\CumulativeSum;

use App\Console\Commands\BenchmarkCommand;
use App\Console\Commands\Benchmarks\CumulativeSum\Models\Transaction;
use App\Util\Benchmark\Benchmark;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CumulativeSumBenchmark extends BenchmarkCommand
{
    protected $signature = 'benchmark:cumulative-sum';

    public function handle(): void
    {
        Benchmark::make('Obtain cumulative sum of transactions', $this)
            ->measure([
                'Calculate cumulative sum using code' => function () {
                    $running_balance = 0;

                    Transaction::query()
                        ->get()
                        ->each(function (Transaction $transaction) use (&$running_balance) {
                            $running_balance += $transaction->amount;

                            $transaction->running_balance = $running_balance;
                        });
                },
                'Calculate cumulative sum using query groups' => function () {
                    Transaction::query()
                        ->select('transactions.*')
                        ->selectRaw('sum(t2.amount) as running_balance')
                        ->join('transactions as t2', 'transactions.id', '>=', 't2.id')
                        ->groupBy('transactions.id')
                        ->get();
                },
                'Calculate cumulative sum using window functions' => function () {
                    Transaction::query()
                        ->select('*')
                        ->selectRaw('sum(amount) over (order by id rows unbounded preceding) as running_balance')
                        ->get();
                },
            ]);
    }

    protected function migrate(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->tinyText('description');
            $table->unsignedBigInteger('amount');

            $table->timestamps();
        });
    }

    protected function seed(): void
    {
        Transaction::factory()
            ->count(1000)
            ->create();
    }
}
