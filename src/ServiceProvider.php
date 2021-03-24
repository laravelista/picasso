<?php

namespace Laravelista\Picasso;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function boot(): void
    {
        $this->publishes(
            paths: [
                __DIR__ . '/../config/picasso.php' => $this->app->configPath(path: 'picasso.php'),
            ],
            groups: 'config'
        );
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            path: __DIR__ . '/../config/picasso.php',
            key: 'picasso'
        );
    }
}
