<?php

namespace Iankibet\RedisStream;

use Illuminate\Support\Facades\Redis;

class RedisStream
{
    public static function publish(string $stream, array $payload): string
    {
        return Redis::xAdd($stream, '*', $payload);
    }

    public static function consume(string $stream, string $group, string $consumer, callable $callback, int $count = 1, int $block = 0): void
    {
        // Try to create the group if it doesn't exist
        try {
            Redis::xGroup('CREATE', $stream, $group, '0', true);
        } catch (\Exception $e) {
            // Group likely exists
        }

        while (true) {
            $messages = Redis::xReadGroup($group, $consumer, [$stream => '>'], $count, $block);

            if (!empty($messages[$stream])) {
                foreach ($messages[$stream] as $id => $fields) {
                    $callback($id, $fields);
                    Redis::xAck($stream, $group, [$id]);
                }
            }

            // Prevent CPU overuse in no-block mode
            if ($block === 0) {
                usleep(500000); // 0.5s
            }
        }
    }
}
