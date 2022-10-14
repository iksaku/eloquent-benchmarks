<?php

namespace App\Console\Commands\Benchmarks\EagerLoading\Factories;

use App\Console\Commands\Benchmarks\EagerLoading\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
