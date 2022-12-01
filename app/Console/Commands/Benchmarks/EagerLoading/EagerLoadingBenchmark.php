<?php

namespace App\Console\Commands\Benchmarks\EagerLoading;

use App\Console\Commands\BenchmarkCommand;
use App\Console\Commands\Benchmarks\EagerLoading\Models\Comment;
use App\Console\Commands\Benchmarks\EagerLoading\Models\Post;
use App\Console\Commands\Benchmarks\EagerLoading\Models\User;
use App\Util\Benchmark\Benchmark;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EagerLoadingBenchmark extends BenchmarkCommand
{
    protected $signature = 'benchmark:eager-loading';

    public function handle(): void
    {
        Benchmark::make('Simple Relationship Loading', $this)
            ->measure([
                'Lazy-load User\'s Posts' => function () {
                    $users = User::query()->get();

                    foreach ($users as $user) {
                        foreach ($user->posts as $post) {
                            // ...
                        }
                    }
                },
                'Eager-load User\'s Posts' => function () {
                    $users = User::query()
                        ->with(relations: 'posts')
                        ->get();

                    foreach ($users as $user) {
                        foreach ($user->posts as $post) {
                            // ...
                        }
                    }
                }
            ]);

        Benchmark::make('Deep Relationship Loading', $this)
            ->measure([
                'Lazy-load User\'s Posts, then Comments' => function () {
                    $users = User::query()->get();

                    foreach ($users as $user) {
                        foreach ($user->posts as $post) {
                            foreach ($post->comments as $comment) {
                                // ...
                            }
                        }
                    }
                },
                'Eager-load User\'s Posts with Comments' => function () {
                    $users = User::query()
                        ->with('posts.comments')
                        ->get();

                    foreach ($users as $user) {
                        foreach ($user->posts as $post) {
                            foreach ($post->comments as $comment) {
                                // ...
                            }
                        }
                    }
                }
            ]);
    }

    protected function migrate(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();

            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users');
            $table->foreignId('post_id')->constrained();

            $table->text('body');
            $table->timestamps();
        });
    }

    protected function seed(): void
    {
        User::factory()
            ->count(100)
            ->afterCreating(function (User $user) {
                Post::factory()
                    ->count(10)
                    ->for($user)
                    ->has(
                        Comment::factory()
                            ->count(10)
                            ->for($user, 'author')
                    )
                    ->create();
            })
            ->create();
    }
}
