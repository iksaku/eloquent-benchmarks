<?php

namespace App\Console\Commands\Benchmarks\CumulativeSum\Factories;

use App\Console\Commands\Benchmarks\CumulativeSum\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence,
            'amount' => $this->faker->numberBetween(1_00, 1000_00),
        ];
    }
}
