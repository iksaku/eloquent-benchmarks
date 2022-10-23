<?php

namespace App\Util\Benchmark;

use Illuminate\Support\Collection;

/**
 * @extends Collection<int|string, BenchmarkResult>
 */
class BenchmarkResultCollection extends Collection
{
    public static function make($items = [])
    {
        return tap(parent::make($items), function (BenchmarkResultCollection $collection) {
            $collection->transform(BenchmarkResult::make(...));

            $collection->highlightValues();
        });
    }

    protected function highlightValues(): void
    {
        BenchmarkResult::getMeasurementCategories()
            ->each(function (string $category) {
                /** @var Measurement $best */
                $best = $this
                    ->pluck($category)
                    ->reduce(function (?Measurement $min, Measurement $current): Measurement {
                        if (is_null($min) || $current->value < $min->value) {
                            return $current;
                        }

                        if ($current->value === $min->value) {
                            $current->hasBestValue = &$min->hasBestValue;
                        }

                        return $min;
                    });

                $best->hasBestValue = true;
            });
    }

    public function compileResults(): array
    {
        return $this
            ->map(fn (BenchmarkResult $result, string $name) => [$name, ...$result->toArray()])
            ->toArray();
    }
}
