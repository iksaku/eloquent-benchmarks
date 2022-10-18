<?php

namespace App\Util\Benchmark;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class Benchmark
{
    /**
     * @var BenchmarkResultCollection
     */
    protected BenchmarkResultCollection $benchmarks;

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
        $this->benchmarks = BenchmarkResultCollection::make($callbacks);

        return $this;
    }

    protected function render(): void
    {
        if (! isset($this->benchmarks)) {
            return;
        }

        $this->command->line($this->title);

        $this->command->table(
            headers: [
                'Test',
                ...BenchmarkResult::getHeaders()
            ],
            rows: $this->benchmarks->compileResults(),
        );

        $this->command->newLine();
    }

    public function __destruct()
    {
        $this->render();
    }
}
