<?php
declare(strict_types = 1);

namespace Movisio\RedisQueue;

use Kdyby\Redis\RedisClient;

/**
 * Single Queue instance
 */
class Queue
{
    /** @var RedisClient */
    protected RedisClient $redisClient;

    protected string $queueName;

    /**
     * @param RedisClient $redisClient
     * @param string      $queueName
     */
    public function __construct(RedisClient $redisClient, string $queueName)
    {
        $this->redisClient = $redisClient;
        $this->queueName = $queueName;
    }

    /**
     * Push value to the end of queue
     * @param string $value
     * @return int
     */
    public function push(string $value) : int
    {
        return $this->redisClient->rPush($this->queueName, $value);
    }

    /**
     * @param int $timeout
     * @return string|null
     */
    public function wait(int $timeout = 30) : ?string
    {
        $response = $this->redisClient->blPop($this->queueName, null, $timeout);
        return (count($response) && isset($response[1])) ? $response[1] : null;
    }

    /**
     * @return string|null
     */
    public function pop() : ?string
    {
        $value = $this->redisClient->lPop($this->queueName);
        return $value === false ? null : $value;
    }
}
