<?php

namespace App\Console\Commands\Benchmarks\UniqueRecords\Factories;

use App\Console\Commands\Benchmarks\UniqueRecords\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
