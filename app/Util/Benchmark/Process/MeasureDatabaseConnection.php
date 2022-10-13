<?php

namespace App\Util\Benchmark\Process;

use App\Util\Benchmark\BenchmarkResult;
use App\Util\Benchmark\Measurement;
use App\Util\Benchmark\MeasurementUnit;
use Closure;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class MeasureDatabaseConnection
{
    public function handle(Closure $callback, Closure $next): BenchmarkResult
    {
        $queryTime = [];

        DB::listen(function (QueryExecuted $event) use (&$queryTime) {
            $queryTime[] = $event->time;
        });

        return tap($next($callback), function (BenchmarkResult $result) use ($queryTime) {
            $result->queryCount = new Measurement(count($queryTime));
            $result->databaseTime = new Measurement(array_sum($queryTime), unit: MeasurementUnit::Milliseconds);

            Event::forget(QueryExecuted::class);
        });
    }
}
