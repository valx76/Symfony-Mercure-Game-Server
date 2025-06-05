<?php

namespace App\SharedContext\Infrastructure\Uuid;

use App\SharedContext\Domain\Service\UuidGeneratorInterface;
use Symfony\Component\Uid\Uuid;

class SymfonyUuidGenerator implements UuidGeneratorInterface
{
    public function generate(): string
    {
        return Uuid::v7()->toString();
    }
}
