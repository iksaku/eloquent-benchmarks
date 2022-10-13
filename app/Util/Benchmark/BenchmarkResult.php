<?php

namespace App\Util\Benchmark;

use Closure;
use Illuminate\Pipeline\Pipeline;

class BenchmarkResult
{
    /** @var BenchmarkValue<float> */
    public BenchmarkValue $codeTime;

    /** @var BenchmarkValue<float> */
    public BenchmarkValue $databaseTime;


    /** @var BenchmarkValue<int> */
    public BenchmarkValue $queryCount;

    public static function process(Closure $callback): static
    {
        return app(Pipeline::class)
            ->send($callback)
            ->through([
                Process\MeasureDatabaseConnection::class,
                Process\MeasureCodeTime::class,
            ])
            ->thenReturn();
    }
}
