<?php

namespace App\Tests\Behat;

use App\SharedContext\Infrastructure\Database\RedisDatabase;
use App\Tests\_Helper\RedisHelperTrait;
use Behat\Behat\Context\Context;

final class RedisContext implements Context
{
    use RedisHelperTrait;

    public function __construct(
        private readonly RedisDatabase $redisDatabase,
    ) {
    }

    public function cleanup(): void
    {
        $this->deleteTestKeys($this->redisDatabase);
    }
}
