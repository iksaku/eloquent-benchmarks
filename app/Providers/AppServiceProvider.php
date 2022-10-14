<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            $factoryDirectory = Str::before($modelName, 'Models\\') . 'Factories\\';

            return $factoryDirectory . class_basename($modelName) . 'Factory';
        });
    }
}
