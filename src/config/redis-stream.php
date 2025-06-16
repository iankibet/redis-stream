<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Redis Stream Group
    |--------------------------------------------------------------------------
    |
    | The name of the consumer group. This enables message delivery tracking
    | and multiple consumers. Defaults to the application name.
    |
    */
    'group' => env('REDIS_STREAM_GROUP', env('APP_NAME', 'laravel')),

    /*
    |--------------------------------------------------------------------------
    | Redis Stream Consumer
    |--------------------------------------------------------------------------
    |
    | The name of the consumer within the group. By default, it uses the same
    | value as the group to keep things simple.
    |
    */
    'consumer' => env('REDIS_STREAM_CONSUMER', env('REDIS_STREAM_GROUP', env('APP_NAME', 'laravel'))),

    /*
    |--------------------------------------------------------------------------
    | Stream Handlers
    |--------------------------------------------------------------------------
    |
    | Define which Redis stream maps to which job or event handler(s).
    | The system will automatically subscribe and dispatch them.
    |
    */
    'handlers' => [
        'user_activity' => [
            // \App\Jobs\ProcessUserActivity::class,
        ],
        'order_events' => [
            // \App\Events\OrderCreated::class,
        ],
    ],
];
