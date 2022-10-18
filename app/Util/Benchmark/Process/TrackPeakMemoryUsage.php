<?php

namespace App\Util\Benchmark\Process;

use App\Util\Benchmark\BenchmarkResult;
use App\Util\Benchmark\Measurement;
use App\Util\Benchmark\MeasurementUnit;
use Closure;

class TrackPeakMemoryUsage
{
    public function handle(Closure $callback, Closure $next): BenchmarkResult
    {
        memory_reset_peak_usage();
        $start = memory_get_usage();

        return tap($next($callback), function (BenchmarkResult $result) use ($start) {
            $end = memory_get_peak_usage();

            $result->peakMemoryUsage = new Measurement($end - $start, unit: MeasurementUnit::Bytes);
        });
    }
}
