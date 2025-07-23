<?php

namespace App\Game\Domain\Model\Repository;

use App\Game\Domain\Model\Entity\World;

interface PendingLevelMessageRepositoryInterface
{
    public function push(World $world, string $levelName): void;

    public function pop(): ?string;
}
