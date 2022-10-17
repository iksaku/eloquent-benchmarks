<?php

namespace App\Util\Benchmark;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class Benchmark
{
    /**
     * @var Collection<int|string, BenchmarkResult>
     */
    protected Collection $benchmarks;

    protected function __construct(public string $title, protected Command $command)
    {
    }

    public static function make(string $title, Command $command): static
    {
        return new static($title, $command);
    }

    /**
     * @param array<int|string, Closure> $callbacks
     */
    public function measure(array $callbacks): static
    {
        $this->benchmarks = Collection::make($callbacks)
            ->map(BenchmarkResult::make(...))
            ->tap(BenchmarkResult::highlightBestMeasurements(...));

        return $this;
    }

    protected function render(): void
    {
        $this->command->line($this->title);

        $this->command->table(
            headers: [
                'Test',
                ...BenchmarkResult::getHeaders()
            ],
            rows: $this->benchmarks
                ->map(fn (BenchmarkResult $result, int|string $name) => [
                    $name,
                    ...$result->toArray(),
                ]),
        );

        $this->command->newLine();
    }

    public function __destruct()
    {
        $this->render();
    }
}
