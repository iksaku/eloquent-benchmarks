<?php

namespace App\Console\Commands\Benchmarks;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Util\Benchmark\Benchmark;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RelationshipLoadingCommand extends Command
{
    protected $signature = 'benchmark:eager-loading';

    public function handle()
    {
        Benchmark::make('Load all Users and Posts', $this)
            ->measure([
                'Lazy-load Posts' => function () {
                    $users = User::query()->get();

                    foreach ($users as $user) {
                        $user->load('posts');
                    }
                },
                'Eager-load Posts' => function () {
                    User::query()
                        ->with('posts')
                        ->get();
                }
            ])
            ->render();

        Benchmark::make('Load all Users, Posts and Comments', $this)
            ->measure([
                'Lazy-load Posts, then Comments' => function () {
                    $users = User::query()->get();

                    foreach ($users as $user) {
                        foreach ($user->posts as $post) {
                            $post->load('comments');
                        }
                    }
                },
                'Lazy-load Posts+Comments' => function () {
                    $users = User::query()->get();

                    foreach ($users as $user) {
                        $user->load('posts.comments');
                    }
                },
                'Eager-load Posts+Comments' => function () {
                    User::query()
                        ->with('posts.comments')
                        ->get();
                }
            ])
            ->render();
    }

    public function migrate(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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

    public function seed(): void
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
