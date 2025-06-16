<?php

namespace Iankibet\RedisStream\Console;

use Illuminate\Console\Command;
use Iankibet\RedisStream\RedisStream;

class RedisConsumeCommand extends Command
{
    protected $signature = 'redis:consume-stream {consumer?}';
    protected $description = 'Consume messages from configured Redis Streams and dispatch handlers';

    public function handle()
    {
        $group = config('redis-stream.group');
        $consumer = $this->argument('consumer') ?? config('redis-stream.consumer');
        $handlersMap = config('redis-stream.handlers');

        $this->log("Initializing Redis Stream consumer...");
        $this->log("Group: {$group}, Consumer: {$consumer}");

        foreach ($handlersMap as $stream => $handlers) {
            $this->log("Listening on stream: {$stream}");

            RedisStream::consume($stream, $group, $consumer, function ($id, $fields) use ($handlers, $stream) {
                foreach ($handlers as $handler) {
                    if (class_exists($handler)) {
                        try {
                            $instance = new $handler($fields);
                            if (method_exists($instance, 'handle')) {
                                dispatch($instance);
                                $this->log("[$stream] ✅ Job dispatched: {$handler}");
                            } else {
                                event($instance);
                                $this->log("[$stream] ✅ Event dispatched: {$handler}");
                            }
                        } catch (\Throwable $e) {
                            $this->log("[$stream] ❌ Handler failed: {$handler} — " . $e->getMessage());
                            logger()->error("Redis Stream Handler Error", [
                                'stream' => $stream,
                                'handler' => $handler,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    } else {
                        $this->log("[$stream] ⚠ Handler class not found: {$handler}");
                    }
                }
            }, 1, 0);
        }
    }

    protected function log(string $message)
    {
        $now = now()->toDateTimeString();
        $this->line("[{$now}] {$message}");
    }
}
