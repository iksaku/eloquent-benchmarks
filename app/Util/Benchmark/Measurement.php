<?php

namespace App\Util\Benchmark;

/**
 * @template T
 */
class Measurement
{
    public bool $hasBestValue = false;

    /**
     * @param T $value
     */
    public function __construct(public mixed $value, public MeasurementUnit $unit = MeasurementUnit::Raw)
    {
    }

    public function render(): string
    {
        // Deferred so we can properly highlight the best value.
        MeasurementUnit::normalize($this->value, $this->unit);

        return transform((string) $this->value, function (string $value) {
            if ($this->unit !== MeasurementUnit::Raw) {
                $value .= $this->unit->value;
            }

            if ($this->unit->isMemoryUnit()) {
                $value = "~{$value}";
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
