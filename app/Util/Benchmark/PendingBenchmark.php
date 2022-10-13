<?php

namespace App\Util\Benchmark;

use Closure;

class PendingBenchmark
{
    public function __construct(public Closure $callback, public int|string $name)
    {
    }

    /**
     * @return array<int|string, BenchmarkResult>
     */
    public function evaluate(): array
    {
        return [
            $this->name => BenchmarkResult::process($this->callback),
        ];
    }
}
