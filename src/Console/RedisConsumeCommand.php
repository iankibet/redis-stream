<?php

namespace Iankibet\RedisStream\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
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

        $this->log("Initializing Redis Stream consumer...", 'info');
        $this->log("Group: {$group}, Consumer: {$consumer}", 'info');

        foreach ($handlersMap as $stream => $handlers) {
            $this->log("Listening on stream: {$stream}", 'info');

            RedisStream::consume($stream, $group, $consumer, function ($id, $payload) use ($stream, $group, $handlers) {
                $this->handleMessage($stream, $group, $id, $payload, $handlers);
            }, 1, 0);
        }
    }

    protected function handleMessage(string $stream, string $group, string $id, array $payload, array $handlers): void
    {
        try {
            foreach ($handlers as $handler) {
                if (!class_exists($handler)) {
                    $this->log("[$stream] Handler class not found: {$handler}", 'warning');
                    continue;
                }
                try {
                    $instance = new $handler($payload);

                    if (method_exists($instance, 'handle')) {
                        dispatch($instance);
                        $this->log("[$stream] ✅ Job dispatched: {$handler}", 'success');
                    } else {
                        event($instance);
                        $this->log("[$stream] ✅ Event dispatched: {$handler}", 'success');
                    }

                } catch (\Throwable $e) {
                    $this->log("[$stream] ❌ Handler failed: {$handler} — " . $e->getMessage(), 'error');
                    logger()->error("Redis Stream Handler Error", [
                        'stream' => $stream,
                        'handler' => $handler,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Redis::xAck($stream, $group, [$id]); // Acknowledge if all handlers processed
        } catch (\Throwable $e) {
            $this->log("[$stream] ❌ Redis message handling failed — " . $e->getMessage(), 'error');
            logger()->error("Redis handler failed", [
                'stream' => $stream,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function log(string $message, string $level = 'info'): void
    {
        $now = now()->toDateTimeString();

        $prefix = match ($level) {
            'success' => '[✓]',
            'warning' => '[!]',
            'error'   => '[✗]',
            default   => '[>]',
        };

        $this->line("[$now] {$prefix} {$message}");
    }
}
