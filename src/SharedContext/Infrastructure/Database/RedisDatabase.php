<?php

namespace App\SharedContext\Infrastructure\Database;

use App\SharedContext\Application\Database\DatabaseInterface;
use App\SharedContext\Domain\Exception\DatabaseFieldNotFoundException;
use App\SharedContext\Domain\Exception\DatabaseKeyNotFoundException;
use Predis\ClientInterface;

final readonly class RedisDatabase implements DatabaseInterface
{
    public function __construct(
        private ClientInterface $client,
        private string $keyPrefix = '',
    ) {
    }

    public function setValue(string $key, string $value): void
    {
        $this->client->set($this->keyPrefix.$key, $value);
    }

    /**
     * @throws DatabaseKeyNotFoundException
     */
    public function getValue(string $key): string
    {
        $this->throwExceptionIfKeyDoesNotExist($key);

        /** @var string $value */
        $value = $this->client->get($this->keyPrefix.$key);

        return $value;
    }

    public function setHashValue(string $key, string $field, string $value): void
    {
        $this->client->hset($this->keyPrefix.$key, $field, $value);
    }

    /**
     * @throws DatabaseKeyNotFoundException
     * @throws DatabaseFieldNotFoundException
     */
    public function getHashValue(string $key, string $field): string
    {
        $this->throwExceptionIfFieldDoesNotExist($key, $field);

        /** @var string $value */
        $value = $this->client->hget($this->keyPrefix.$key, $field);

        return $value;
    }

    public function hasKey(string $key): bool
    {
        return 1 === $this->client->exists($this->keyPrefix.$key);
    }

    public function hasHashField(string $key, string $field): bool
    {
        return 1 === $this->client->hexists($this->keyPrefix.$key, $field);
    }

    /**
     * @throws DatabaseKeyNotFoundException
     */
    public function deleteKey(string $key): void
    {
        $this->throwExceptionIfKeyDoesNotExist($key);

        $this->client->del($this->keyPrefix.$key);
    }

    /**
     * @throws DatabaseKeyNotFoundException
     * @throws DatabaseFieldNotFoundException
     */
    public function deleteHashField(string $key, string $field): void
    {
        $this->throwExceptionIfFieldDoesNotExist($key, $field);

        $this->client->hdel($this->keyPrefix.$key, [$field]);
    }

    /** @return string[] */
    public function findKeysByPattern(string $pattern): array
    {
        /** @var string[] $keys */
        $keys = $this->client->keys($this->keyPrefix.$pattern);

        return $keys;
    }

    /**
     * @throws DatabaseKeyNotFoundException
     */
    private function throwExceptionIfKeyDoesNotExist(string $key): void
    {
        if ($this->hasKey($key)) {
            return;
        }

        throw new DatabaseKeyNotFoundException($key);
    }

    /**
     * @throws DatabaseFieldNotFoundException
     * @throws DatabaseKeyNotFoundException
     */
    private function throwExceptionIfFieldDoesNotExist(string $key, string $field): void
    {
        $this->throwExceptionIfKeyDoesNotExist($key);

        if ($this->hasHashField($key, $field)) {
            return;
        }

        throw new DatabaseFieldNotFoundException($field);
    }
}
