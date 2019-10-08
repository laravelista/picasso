<?php

namespace Laravelista\Picasso;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/picasso.php' => config_path('picasso.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/picasso.php',
            'picasso'
        );
    }
}