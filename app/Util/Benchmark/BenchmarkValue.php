<?php

namespace App\Util\Benchmark;

/**
 * @template T
 */
class BenchmarkValue
{
    public bool $hasBestValue = false;

    /**
     * @param T $value
     */
    public function __construct(public mixed $value, public ?string $unit = null)
    {
    }

    public function markAsBestValue(): void
    {
        $this->hasBestValue = true;
    }

    public function render(): string
    {
        return transform((string) $this->value, function (string $value) {
            if (isset($this->unit)) {
                $value .= " {$this->unit}";
            }

            if ($this->hasBestValue) {
                $value = "<info>{$value}</info>";
            }

            return $value;
        });
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
