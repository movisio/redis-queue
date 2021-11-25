# Simple Redis Message Queue
This is simple queue implementation using Redis lists with help of [kdyby/redis](https://github.com/Kdyby/Redis).

## Installation:
Install with composer
```shell
composer require movisio/redis-queue
```

## Nette service configuration
Add this to config.neon:
```yaml
services:
  - Movisio\RedisQueue\Factory
```

For more complex setup with directory or directory:subdirectory, use:
```yaml
services:
    RedisQueue:
        factory: Movisio\RedisQueue\Factory
        setup:
          - setNamespace("MyQueues") # directory
          - setNamespace("Directory:%redisNamespace%") # subdirectory from parameter
```

## Usage:
First, create factory. Skip this step if you are using Nette service.
```php
/** @var \Kdyby\Redis\RedisClient $redisClient */

$factory = new \Movisio\RedisQueue\Factory($redisClient);
$factory->setNamespace("Queues:MyCustomQueues"); # optional
```

If using Nette service, inject Factory:
```php
class MyPresenter {
    /** @var \Movisio\RedisQueue\Factory @inject */
    public \Movisio\RedisQueue\Factory $queueFactory;
}
```

Then create queue and `push()` messages into it. Return value is the new length of the queue.
```php
$queue = $this->queueFactory->get("QueueName");
$queueLength = $queue->push("TestMessage");
```

For reading from queue, you can use `wait()` method which will remove and return the first value in queue, or block until one is available.
Parameter is timeout in seconds, defaults to 30. 
```php
while (true) {
    $message = $queue->wait(30); // wait 30 seconds
    if (is_null($message)) {
        echo "queue is empty";
        continue; // or some fallback load from database
    }
    echo "found message: " . $message;
}
```
If you want to check if queue is empty and do not wait for timeout, use `pop()` method which will remove and return the first value in queue.

```php
$message = $queue->pop();
if (is_null($message)) {
    echo "Queue is empty";
}
```