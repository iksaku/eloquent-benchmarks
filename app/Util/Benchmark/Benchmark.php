<?php

namespace App\Util\Benchmark;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class Benchmark
{
    /**
     * @var Collection<int|string, PendingBenchmark>
     */
    protected Collection $pendingBenchmarks;

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
        $this->pendingBenchmarks = Collection::make($callbacks)
            ->mapInto(PendingBenchmark::class);

        return $this;
    }

    /**
     * @return Collection<int|string, BenchmarkResult>
     */
    protected function evaluate(): Collection
    {
        return $this->pendingBenchmarks
            ->mapWithKeys(
                fn(PendingBenchmark $pendingBenchmark) => $pendingBenchmark->evaluate()
            )
            ->tap(function (Collection $results) {
                foreach (['codeTime', 'databaseTime', 'queryCount'] as $category) {
                    $results
                        ->reduce(
                            fn (?BenchmarkValue $min, BenchmarkResult $current): BenchmarkValue =>
                                is_null($min) || $current->{$category}->value < $min->value
                                    ? $current->{$category}
                                    : $min
                        )
                        ->markAsBestValue();
                }
            });
    }

    public function render(): void
    {
        $this->command->line($this->title);

        $this->command->table(
            headers: [
                'Test',
                'Query Count',
                'Database Time',
                'Code Time',
            ],
            rows: $this->evaluate()
                ->map(fn (BenchmarkResult $result, int|string $name) => [
                    $name,
                    $result->queryCount,
                    $result->databaseTime,
                    $result->codeTime,
                ])
                ->toArray(),
        );

        $this->command->newLine();
    }
}
