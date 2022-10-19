<?php

namespace App\Util\Benchmark;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionProperty;

class BenchmarkResult implements Arrayable
{
    /** @var Measurement<float> */
    public Measurement $codeTime;

    /** @var Measurement<float> */
    public Measurement $databaseTime;

    /** @var Measurement<int> */
    public Measurement $queryCount;

    /** @var Measurement<int> */
    public Measurement $hydratedModels;

    /** @var Measurement<float> */
    public Measurement $peakMemoryUsage;

    protected static ?Collection $categories = null;

    public static function make(Closure $callback): static
    {
        return app(Pipeline::class)
            ->send($callback)
            ->through([
                Collectors\HydratedModelsCollector::class,
                Collectors\DatabaseQueryCollector::class,
                Collectors\CodeTimeCollector::class,
                Collectors\PeakMemoryCollector::class,
            ])
            ->thenReturn();
    }

    public static function getMeasurementCategories(): Collection
    {
        return static::$categories ??= collect((new ReflectionClass(static::class))->getProperties())
            ->filter(fn (ReflectionProperty $property) =>
                $property->isPublic()
                && ! $property->isStatic()
                && $property->getType()->getName() === Measurement::class
            )
            ->map(fn (ReflectionProperty $property) => $property->getName());
    }

    public static function getHeaders(): array
    {
        return static::getMeasurementCategories()
            ->map(Str::headline(...))
            ->toArray();
    }

    public function toArray(): array
    {
        return static::getMeasurementCategories()
            ->map(fn (string $category) => (string) $this->{$category})
            ->toArray();
    }
}
