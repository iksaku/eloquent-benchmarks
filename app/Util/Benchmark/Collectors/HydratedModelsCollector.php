<?php

namespace App\Util\Benchmark\Collectors;

use App\Util\Benchmark\BenchmarkResult;
use App\Util\Benchmark\Measurement;
use App\Util\Benchmark\MeasurementUnit;
use Closure;
use Illuminate\Support\Facades\Event;

class HydratedModelsCollector
{
    public function handle(Closure $callback, Closure $next): BenchmarkResult
    {
        $count = 0;

        Event::listen('eloquent.retrieved:*', function () use (&$count) {
            $count++;
        });

        return tap($next($callback), function (BenchmarkResult $result) use ($count) {
            $result->hydratedModels = new Measurement(value: $count, unit: MeasurementUnit::Raw);

            Event::forget('eloquent.retrieved:*');
        });
    }
}
