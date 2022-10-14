<?php

namespace App\Console\Commands\Benchmarks\EagerLoading\Factories;

use App\Console\Commands\Benchmarks\EagerLoading\Models\Comment;
use App\Console\Commands\Benchmarks\EagerLoading\Models\Post;
use App\Console\Commands\Benchmarks\EagerLoading\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'post_id' => Post::factory(),
            'body' => $this->faker->paragraph(),
        ];
    }
}
