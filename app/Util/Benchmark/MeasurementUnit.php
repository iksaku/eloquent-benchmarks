<?php

namespace App\Util\Benchmark;

enum MeasurementUnit: string
{
    case Raw = '';

    case Seconds = 's';
    case Milliseconds = 'ms';
    case Microseconds = 'μs';

    public function isTimeUnit(): bool
    {
        return in_array($this, [
            self::Seconds,
            self::Milliseconds,
            self::Microseconds,
        ]);
    }

    public function nextSmallerUnit(): ?MeasurementUnit
    {
        return match (true) {
            $this->isTimeUnit() => match ($this) {
                self::Seconds => self::Milliseconds,
                self::Milliseconds => self::Microseconds,
                default => null,
            },

            default => null,
        };
    }

    public function hasSmallerUnit(): bool
    {
        return ! is_null($this->nextSmallerUnit());
    }
}
