<?php

namespace App\Game\Infrastructure\Repository;

use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\PendingLevelMessageRepositoryInterface;
use App\SharedContext\Application\Database\DatabaseInterface;
use App\SharedContext\Domain\Model\DatabaseKeys;

final readonly class PendingLevelMessageRepository implements PendingLevelMessageRepositoryInterface
{
    public function __construct(
        private DatabaseInterface $database,
    ) {
    }

    public function push(World $world, string $levelName): void
    {
        $value = sprintf(DatabaseKeys::PENDING_MESSAGE_FORMAT, $world->id, $levelName);
        $this->database->pushValueToSet(DatabaseKeys::PENDING_MESSAGE_KEY, $value);
    }

    public function pop(): ?string
    {
        return $this->database->popValueFromSet(DatabaseKeys::PENDING_MESSAGE_KEY);
    }
}
