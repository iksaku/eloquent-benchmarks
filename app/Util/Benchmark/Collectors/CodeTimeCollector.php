<?php

namespace App\Util\Benchmark\Collectors;

use App\Util\Benchmark\BenchmarkResult;
use App\Util\Benchmark\Measurement;
use App\Util\Benchmark\MeasurementUnit;
use Closure;

class CodeTimeCollector
{
    public function handle(Closure $callback, Closure $next): BenchmarkResult
    {
        $start = microtime(true);

        return tap($next($callback), function (BenchmarkResult $result) use ($start) {
            $end = microtime(true);

            $result->codeTime = new Measurement($end - $start, unit: MeasurementUnit::Seconds);
        });
    }
}
