<?php

use Illuminate\Support\Facades\Redis;
use Iankibet\RedisStream\RedisStream;

test('it publishes and consumes using config values', function () {
    config(['redis-stream.stream' => 'test-config-stream']);
    config(['redis-stream.group' => 'test-config-group']);
    config(['redis-stream.consumer' => 'test-consumer-1']);

    // Flush any existing stream
    Redis::del('test-config-stream');

    // Publish a message
    $payload = ['type' => 'test', 'message' => 'hello'];
    $id = RedisStream::publish('test-config-stream', $payload);
    expect($id)->toBeString();

    // Read and acknowledge via config
    $received = false;

    RedisStream::consume('test-config-stream', 'test-config-group', 'test-consumer-1', function ($msgId, $fields) use (&$received, $id) {
        expect($msgId)->toBe($id);
        expect($fields['type'])->toBe('test');
        expect($fields['message'])->toBe('hello');
        $received = true;

        // Break after one message
        throw new Exception("done");
    });

    expect($received)->toBeTrue();
});
