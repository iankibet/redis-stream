<?php

namespace Iankibet\RedisStream;

use Illuminate\Support\ServiceProvider;

class RedisStreamServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register()
    {
        // Register any bindings or services here
    }

    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Iankibet\RedisStream\Console\RedisConsumeCommand::class,
            ]);
            $this->publishes([
                __DIR__ . '/config/redis-stream.php' => config_path('redis-stream.php'),
            ], 'redis-stream');
        }
    }
}
