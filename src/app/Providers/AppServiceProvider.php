<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Arr::macro('crossSum', function (array $a, array $b) {
            $keys = array_keys($a) + array_keys($b);

            foreach ($keys as $key) {
                $a[$key] ??= 0;
                $a[$key] += $b[$key] ?? 0;
            }

            return $a;
        });

        Arr::macro('columnAverage', function(array $arr, int $times) {
            $keys = array_keys($arr);

            foreach ($keys as $key) {
                $arr[$key] /= $times;
            }

            return $arr;
        });
    }
}
