<?php

namespace App\Console\Commands\Benchmarks\EagerLoading\Factories;

use App\Console\Commands\Benchmarks\EagerLoading\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
        ];
    }
}
