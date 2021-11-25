<?php
declare(strict_types = 1);

namespace Movisio\RedisQueue;

use Kdyby\Redis\RedisClient;

/**
 * Queues factory
 */
class Factory
{

    /**
     * Default second-level namespace (Redis folder:subfolder)
     */
    protected const DEFAULT_NAMESPACE = "Redis.queue:Default";

    /**
     * Namespace (Redis subfolder) from Nette cache
     * @var string
     */
    protected string $namespace = self::DEFAULT_NAMESPACE;

    /**
     * @var RedisClient
     */
    protected RedisClient $redisClient;

    /**
     * Queue instances
     * @var Queue[]
     */
    protected array $queues = [];

    /**
     * @param RedisClient $redisClient
     */
    public function __construct(RedisClient $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace(string $namespace) : void
    {
        $this->namespace = $namespace;
    }

    /**
     * Create or get queue instance
     * @param string $queueName
     * @return Queue
     */
    public function get(string $queueName) : Queue
    {
        if (!isset($this->queues[$queueName])) {
            $this->queues[$queueName] = new Queue(
                $this->redisClient,
                $this->namespace . ":" . $queueName
            );
        }
        return $this->queues[$queueName];
    }
}
