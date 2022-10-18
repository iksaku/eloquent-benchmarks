<?php

namespace App\Util\Benchmark;

enum MeasurementUnit: string
{
    case Raw = '';

    case Microseconds = 'μs';
    case Milliseconds = 'ms';
    case Seconds = 's';

    case Bytes = 'B';
    case Kibibytes = 'KiB';
    case Mebibytes = 'MiB';

    public function isTimeUnit(): bool
    {
        return in_array($this, [
            self::Microseconds,
            self::Milliseconds,
            self::Seconds,
        ]);
    }

    public function isMemoryUnit(): bool
    {
        return in_array($this, [
            self::Bytes,
            self::Kibibytes,
            self::Mebibytes,
        ]);
    }

    public static function normalize(mixed &$value, MeasurementUnit &$unit): void
    {
        $convert = function () use (&$value, &$unit) {
            return match ($unit) {
                MeasurementUnit::Raw => null,

                MeasurementUnit::Microseconds => match (true) {
                    $value >= 1000 => [$value / 1000, MeasurementUnit::Milliseconds],
                    default => null,
                },
                MeasurementUnit::Milliseconds => match (true) {
                    $value >= 1000 => [$value / 1000, MeasurementUnit::Seconds],
                    $value < 1 => [$value * 1000, MeasurementUnit::Microseconds],
                    default => null,
                },
                MeasurementUnit::Seconds => match (true) {
                    $value < 1 => [$value * 1000, MeasurementUnit::Milliseconds],
                    default => null,
                },

                MeasurementUnit::Bytes => match (true) {
                    $value >= 1024 => [$value / 1024, MeasurementUnit::Kibibytes],
                    default => null,
                },
                MeasurementUnit::Kibibytes => match (true) {
                    $value >= 1024 => [$value / 1024, MeasurementUnit::Mebibytes],
                    $value < 1 => [$value * 1024, MeasurementUnit::Bytes],
                    default => null,
                },
                MeasurementUnit::Mebibytes => match (true) {
                    $value < 1 => [$value * 1024, MeasurementUnit::Kibibytes],
                    default => null,
                },
            };
        };

        while (! is_null($next = $convert())) {
            [$value, $unit] = $next;
        }

        $value = round($value, precision: 2);
    }
}
