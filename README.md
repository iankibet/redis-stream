# Laravel Redis Stream Package
[![Latest Stable Version](https://poser.pugx.org/iankibet/redis-stream/v/stable)](https://packagist.org/packages/iankibet/redis-stream)
[![Total Downloads](https://poser.pugx.org/iankibet/redis-stream/downloads)](https://packagist.org/packages/iankibet/redis-stream)
[![License](https://poser.pugx.org/iankibet/redis-stream/license)](https://packagist.org/packages/iankibet/redis-stream)

A Laravel package to use Redis Streams for lightweight, scalable inter-service communication or job/event dispatching.

---

## 🚀 Features

- Simple Redis Streams integration
- Stream-to-handler mapping via config
- Supports multiple consumers under the same group
- Automatically dispatches Jobs or Events
- Clean CLI command with optional consumer override

---

## 🛠 Installation

```bash
composer require iankibet/redis-stream
```

Publish the config:

```bash
php artisan vendor:publish --tag=redis-stream
```

---

## ⚙️ Configuration

Edit the generated config file at `config/redis-stream.php`:

```php
return [
    'group' => env('REDIS_STREAM_GROUP', env('APP_NAME', 'laravel')),

    'consumer' => env('REDIS_STREAM_CONSUMER', env('REDIS_STREAM_GROUP', env('APP_NAME', 'laravel'))),

    'handlers' => [
        'user_activity' => [
            App\Jobs\ProcessUserActivity::class,
        ],
        'order_events' => [
            App\Events\OrderCreated::class,
        ],
    ],
];
```

---

## 📥 Publishing to a Stream

You can publish messages using the `RedisStream` facade:

```php
use Iankibet\RedisStream\RedisStream;

RedisStream::publish('order_events', [
    'order_id' => 123,
    'status' => 'created',
]);
```

---

## 🧑‍💻 Consuming Messages

Run the consumer using:

```bash
php artisan redis:consume-stream
```

Or provide a specific consumer name:

```bash
php artisan redis:consume-stream worker-1
```

> Messages are read from all streams defined in the config and dispatched automatically to jobs or events.

---

## 🧪 Testing

Run the tests:

```bash
php artisan test
```

---

## 🧠 Notes

- Each message is delivered to **only one consumer** in a group.
- Redis automatically tracks pending/unacknowledged messages.
- You should monitor or use `XPENDING` and `XCLAIM` to handle stuck messages in production.

---

## 📄 License

MIT © Ian Kibet
