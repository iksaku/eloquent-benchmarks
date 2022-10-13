<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
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
