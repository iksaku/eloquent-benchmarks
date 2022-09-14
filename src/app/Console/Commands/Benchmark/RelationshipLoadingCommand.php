<?php

namespace App\Console\Commands\Benchmark;

use App\Console\Commands\Benchmark\Concerns\CalculatesQueryPerformance;
use App\Models\User;
use Illuminate\Console\Command;

class RelationshipLoadingCommand extends Command
{
    use CalculatesQueryPerformance;

    protected $signature = 'benchmark:eager-loading';

    public function handle()
    {
        $this->benchmark(
            title: 'Load all Users and Posts',
            callbacks: [
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
            ]
        );

        $this->benchmark(
            title: 'Load all Users, Posts and Comments',
            callbacks: [
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
            ]
        );
    }
}
