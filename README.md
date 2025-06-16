# Laravel Redis Streams

A Laravel package to **publish** and **consume** messages using **Redis Streams**, offering a simple and reliable alternative to traditional queues or pub/sub.

---

## 🚀 Features

- ✅ Stream-based messaging using Redis 5.0+ (`XADD`, `XREADGROUP`, `XACK`)
- ✅ Multiple consumer support with consumer groups
- ✅ Simple, Laravel-style API
- ✅ Automatic group creation
- ✅ Lightweight and queue-driver agnostic

---

## 📦 Installation

```bash
composer require iankibet/redis-stream
```

---

## ⚙️ Configuration

Optionally publish the config:

```bash
php artisan vendor:publish --tag=redis-stream-config
```

This will create `config/redis-stream.php`:

```php
return [
    'stream' => env('REDIS_STREAM_NAME', 'events'),
    'group' => env('REDIS_STREAM_GROUP', 'default'),
    'consumer' => env('REDIS_STREAM_CONSUMER', env('APP_NAME', 'laravel')),
];
```

---

## 🧱 Usage

### ✅ 1. Publish a Message to a Redis Stream

```php
use Iankibet\RedisStream\RedisStream;

$id = RedisStream::publish('user_activity', [
    'event' => 'login',
    'user_id' => 42,
]);
```

- Publishes to the `user_activity` stream
- Automatically generates a stream ID

---

### ✅ 2. Consume Messages from a Redis Stream (via Config)

```bash
php artisan redis:consume-stream
```

- Uses values from `config('redis-stream.handlers')`, `group`, and `consumer`
- Automatically acknowledges messages
- Load balanced between consumers in the same group

---

## 🧪 Testing

```bash
php artisan test
```

Tests include:
- Publishing to a stream
- Consuming via consumer group
- Config-based streaming

---

## 🛡 Requirements

- PHP 8.0+
- Laravel 9 or 10
- Redis 5.0+ (Streams support)
- `predis/predis` or `ext-redis`

---

## 📂 File Structure

```
src/
  RedisStream.php               # Main logic
  RedisStreamServiceProvider.php
  Console/RedisConsumeCommand.php
  config/redis-stream.php
tests/
  RedisStreamTest.php
  RedisStreamFromConfigTest.php
```

---

## 🧠 Why Redis Streams?

Redis Streams are durable, acknowledgeable, and perfect for **reliable message processing**. Unlike Pub/Sub, they:
- Store messages persistently
- Let you replay or retry failed jobs
- Load balance across consumers

---

## 🧑‍💻 Maintainer

- [Ian Kibet](mailto:kibethosea8@gmail.com)

---

## 📝 License

MIT
