<?php

namespace App\SharedContext\Application\Database;

use App\SharedContext\Domain\Exception\DatabaseFieldNotFoundException;
use App\SharedContext\Domain\Exception\DatabaseKeyNotFoundException;

interface DatabaseInterface
{
    public function setValue(string $key, string $value): void;

    /**
     * @throws DatabaseKeyNotFoundException
     */
    public function getValue(string $key): string;

    public function setHashValue(string $key, string $field, string $value): void;

    /**
     * @throws DatabaseKeyNotFoundException
     * @throws DatabaseFieldNotFoundException
     */
    public function getHashValue(string $key, string $field): string;

    public function hasKey(string $key): bool;

    public function hasHashField(string $key, string $field): bool;

    public function pushValueToSet(string $key, string $value): void;

    public function popValueFromSet(string $key): ?string;

    /**
     * @throws DatabaseKeyNotFoundException
     */
    public function deleteKey(string $key): void;

    /**
     * @throws DatabaseKeyNotFoundException
     * @throws DatabaseFieldNotFoundException
     */
    public function deleteHashField(string $key, string $field): void;

    /**
     * @return string[]
     */
    public function findKeysByPattern(string $pattern): array;
}
