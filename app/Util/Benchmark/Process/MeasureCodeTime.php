<?php

namespace App\Util\Benchmark\Process;

use App\Util\Benchmark\BenchmarkResult;
use App\Util\Benchmark\Measurement;
use App\Util\Benchmark\MeasurementUnit;
use Closure;

class MeasureCodeTime
{
    public function handle(Closure $callback): BenchmarkResult
    {
        return tap(new BenchmarkResult(), function (BenchmarkResult $result) use ($callback) {
            $start = microtime(true);

            $callback();

            $end = microtime(true);

            $result->codeTime = new Measurement($end - $start, unit: MeasurementUnit::Seconds);
        });
    }
}
