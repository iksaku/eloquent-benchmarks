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

    public function normalizeValue(): void
    {
        if ($this->unit->isTimeUnit()) {
            while ($this->value < 1 && $this->unit->hasSmallerUnit()) {
                $this->value *= 1000;
                $this->unit = $this->unit->nextSmallerUnit();
            }

            $this->value = round($this->value, precision: 2);
        }
    }

    public function render(): string
    {
        $this->normalizeValue();

        return transform((string) $this->value, function (string $value) {
            if ($this->unit !== MeasurementUnit::Raw) {
                $value .= $this->unit->value;
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
