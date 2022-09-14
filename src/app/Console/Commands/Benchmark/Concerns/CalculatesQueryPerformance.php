<?php

namespace App\Console\Commands\Benchmark\Concerns;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

trait CalculatesQueryPerformance
{
    protected int $benchmarksRun = 0;

    public abstract static function benchmarkName(): string;

    protected function benchmark(string $title, array $callbacks): void
    {
        if (++$this->benchmarksRun > 1) {
            $this->newLine();
        }

        $this->line($title);

        $measurements = collect($this->compare($callbacks))
            ->map(fn (array $measurement, int|string $key) => [$key, ...$measurement]);

        $this->table(
            ['Run', 'Query Count', 'Database Time', 'Code Time'],
            $measurements
        );
    }

    protected function measure(callable $callback): array
    {
        $queries = [];

        DB::listen(function (QueryExecuted $event) use (&$queries) {
            $queries[] = $event->time;
        });

        $start = microtime(true);

        $callback();

        $end = microtime(true);

        $codeTime = round(($end - $start) * 1000, 2);

        Event::forget(QueryExecuted::class);

        $queryCount = count($queries);
        $queryTime = array_sum($queries);

        return compact('queryCount', 'queryTime', 'codeTime');
    }

    protected function compare(array $callbacks): array
    {
        $measurements = array_map(
            fn (callable $callback) => $this->measure($callback),
            $callbacks
        );

        $formatCount = fn(int $count, ?int $compare = null) => tap(
            $count,
            function (int &$count) use ($compare) {
                if (is_null($compare)) {
                    return;
                }

                $delta = $count - $compare;

                $style = match (true) {
                    $delta < 0 => 'comment',
                    $delta > 0 => 'error',
                    default => null,
                };

                if (is_null($style)) {
                    return;
                }

                $count = "{$count} (<{$style}>{$delta}</{$style}>)";
            }
        );

        $formatTime = fn(int|float $time, int|float|null $compare = null) => tap(
            number_format($time, 2) . 'ms',
            function (&$formatted) use ($time, $compare) {
                if (is_null($compare)) {
                    return;
                }

                $change = (($time * 100) / $compare) - 100;

                $style = match (true) {
                    $change < 0 => 'comment',
                    $change > 0 => 'error',
                    default => null,
                };

                $change = with(
                    number_format($change, 2) . '%',
                    fn(string $formatted) => $change > 0
                        ? "+{$formatted}"
                        : $formatted
                );

                if (! is_null($style)) {
                    $change = "<{$style}>{$change}</{$style}>";
                }

                $formatted .= " ({$change})";
            }
        );

        foreach(array_keys(Arr::first($measurements)) as $key) {
            $previous = null;

            $formatter = match (true) {
                str_ends_with($key, 'Count') => $formatCount,
                str_ends_with($key, 'Time') => $formatTime,
                default => value(...),
            };

            foreach ($measurements as &$measurement) {
                $measurement[$key] = tap(
                    $formatter($measurement[$key], $previous),
                    function () use (&$previous, $measurement, $key) {
                        $previous = $measurement[$key];
                    }
                );
            }
        }

        return $measurements;
    }
}
