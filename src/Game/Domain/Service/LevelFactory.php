<?php

namespace App\Game\Domain\Service;

use App\Game\Domain\Exception\LevelNotFoundException;
use App\Game\Domain\Model\Entity\Level\Level1;
use App\Game\Domain\Model\Entity\Level\Level2;
use App\Game\Domain\Model\Entity\Level\LevelInterface;

final readonly class LevelFactory
{
    /**
     * @throws LevelNotFoundException
     */
    public function create(string $levelName): LevelInterface
    {
        return match ($levelName) {
            Level1::class => new Level1(),
            Level2::class => new Level2(),
            default => throw new LevelNotFoundException(),
        };
    }
}
