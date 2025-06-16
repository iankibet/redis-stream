<?php

use Iankibet\RedisStream\RedisStream;
use Illuminate\Support\Facades\Redis;

test('it publishes and consumes a redis stream message', function () {
    $stream = 'test-stream';
    $group = 'test-group';
    $consumer = 'test-consumer';

    // Flush old data
    Redis::del($stream);

    // Publish message
    $payload = ['event' => 'test', 'user_id' => 123];
    $id = RedisStream::publish($stream, $payload);

    expect($id)->toBeString();

    // Consume message
    $received = false;

    $callback = function ($msgId, $fields) use (&$received, $id) {
        expect($msgId)->toBe($id);
        expect($fields['event'])->toBe('test');
        expect($fields['user_id'])->toBe('123');
        $received = true;
    };

    // Only run one iteration
    RedisStream::consume($stream, $group, $consumer, function ($id, $fields) use ($callback) {
        $callback($id, $fields);
        throw new Exception("Stop after one message");
    });
});
