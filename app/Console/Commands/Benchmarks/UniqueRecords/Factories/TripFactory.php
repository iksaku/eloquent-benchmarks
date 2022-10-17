<?php

namespace App\Console\Commands\Benchmarks\UniqueRecords\Factories;

use App\Console\Commands\Benchmarks\UniqueRecords\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        return [
            'country' => $this->faker->country(),
            'visited_at' => $this->faker->dateTimeBetween('-10 years', 'now'),
        ];
    }
}
