<?php

namespace App\Tests\_Helper;

use App\SharedContext\Infrastructure\Database\RedisDatabase;

trait RedisHelperTrait
{
    public function deleteTestKeys(RedisDatabase $redisDatabase): void
    {
        $testKeys = $redisDatabase->findKeysByPattern('*');

        foreach ($testKeys as $testKeyStr) {
            $redisDatabase->deleteKey(
                str_replace('test-', '', $testKeyStr)
            );
        }
    }
}
